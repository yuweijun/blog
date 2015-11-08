---
layout: post
title: "one_to_one relationship explain and notice"
date: "Sun Aug 26 2007 18:10:00 GMT+0800 (CST)"
categories: ruby
---

以Agile Web Development with Rails书中关于`has_one`关系的order和invoice关系做个例子，其中设了一个total_price字段在invoice表中，不能为空，当做以下操作时：

{% highlight ruby %}
o = Order.find(1)
i = Invoice.new
o.invoice = i
{% endhighlight %}

这个时候Rails会自动保存i这个Invoice对象，保存过和书里有插图说明，先将原来记录的order_id置为NULL,再插入些记录。而此记录在数据库里是设定了非空，所以不会插入成功。
得到的log如下：

{% highlight sql %}
Order Load (0.002154)   SELECT * FROM orders WHERE (orders.`id` = 1)
Invoice Load (0.002144)   SELECT * FROM invoices WHERE (invoices.order_id = 1) LIMIT 1
SQL (0.000859)   BEGIN
Invoice Update (0.002496)   UPDATE invoices SET `created_at` = '2007-08-26 17:21:30', `total_price` = '9.0', `order_id` = NULL WHERE `id` = 1
SQL (0.003602)   COMMIT
SQL (0.000605)   BEGIN
SQL (0.000000)   Mysql::Error: Column 'total_price' cannot be null: INSERT INTO invoices (`order_id`, `total_price`, `created_at`) VALUES(1, NULL, '2007-08-26 18:07:21')
SQL (0.000865)   ROLLBACK
{% endhighlight %}

这就造成了数据问题，在这里是数据库做限制，所以程序出错还可以知道出错，如果是通过validate或者before_save过滤器过滤的话，就很难查觉数据没有写入库中，所以这种写法书里不推荐，而是先强制保存子对象i再保存父对象o。
或者写入事务中：

{% highlight ruby %}
 Order.transaction do
    o.invoice = i
 end
{% endhighlight %}

程序还是会报错，因为数据库写不进去的。可以再begin..end捕获错误。

{% highlight ruby %}
>> o = Order.new do |x|
?> x.name = 'a'
>> x.email = 'a@test.com'
>> x.address = 'Shanghai'
>> end
=> #"a", "updated_at"=>nil, "pay_type"=>nil, "address"=>"Shanghai", "created_at"=>nil, "email"=>"a@test.com"}>
>> i = Invoice.new
=> #nil, "total_price"=>nil, "created_at"=>nil}>
>> i.order = o
=> #"a", "updated_at"=>nil, "pay_type"=>nil, "address"=>"Shanghai", "created_at"=>nil, "email"=>"a@test.com"}>
>> i.save
ActiveRecord::StatementInvalid: Mysql::Error: Column 'total_price' cannot be null: INSERT INTO invoices (`order_id`, `total_price`, `created_at`) VALUES(5, NULL, '2007-08-26 18:36:24')
{% endhighlight %}

one_to_one relationship中保存子对象i时会先保存其父对象o，而保存父对象o则子对象不会一起保存。以上例子产生的log如下:

{% highlight sql %}
SQL (0.000750)   BEGIN
SQL (0.080562)   INSERT INTO orders (`name`, `updated_at`, `pay_type`, `address`, `created_at`, `email`) VALUES('a', '2007-08-26 18:36:23', NULL, 'Shanghai', '2007-08-26 18:36:23', 'a@test.com')
SQL (0.000000)   Mysql::Error: Column 'total_price' cannot be null: INSERT INTO invoices (`order_id`, `total_price`, `created_at`) VALUES(5, NULL, '2007-08-26 18:36:24')
SQL (0.005971)   ROLLBACK
{% endhighlight %}

Rails 是将其包装在一个事务里处理的，所以不会生成一条新的order记录。

BTW:
在has_many关系中与上面has_one是相反的，当保存了父对象之后会遍历父对象中的全部子对象并保存，如果父对象和子对象都是新建对象，不能先保存子对象，因为无法取到父对象的id。如下例子说明：

{% highlight javascript %}
order = Order.new
[1, 2, 3].each do |prd_id|
  product = Product.find(prd_id)
  order.line_items << LineItem.new(:product =>product, :quantity => 2, :total_price => 3.22)
end
order.save
{% endhighlight %}

产生Log如下：

{% highlight sql %}
Product Load (0.002137)   SELECT * FROM products WHERE (products.`id` = 1)
SQL (0.023867)   BEGIN
SQL (0.002860)   COMMIT
Product Load (0.001754)   SELECT * FROM products WHERE (products.`id` = 2)
SQL (0.066274)   BEGIN
SQL (0.001553)   COMMIT
Product Load (0.002514)   SELECT * FROM products WHERE (products.`id` = 3)
SQL (0.001669)   BEGIN
SQL (0.000442)   COMMIT
SQL (0.000661)   BEGIN
SQL (0.003558)   INSERT INTO orders (`name`, `updated_at`, `pay_type`, `address`, `created_at`, `email`) VALUES(NULL, '2007-08-26 19:30:18', NULL, NULL, '2007-08-26 19:30:18', NULL)
SQL (0.002258)   INSERT INTO line_items (`order_id`, `updated_at`, `total_price`, `product_id`, `quantity`, `created_at`) VALUES(9, '2007-08-26 19:30:18', '3.22', 1, 2, '2007-08-26 19:30:18')
SQL (0.002302)   INSERT INTO line_items (`order_id`, `updated_at`, `total_price`, `product_id`, `quantity`, `created_at`) VALUES(9, '2007-08-26 19:30:18', '3.22', 2, 2, '2007-08-26 19:30:18')
SQL (0.004991)   INSERT INTO line_items (`order_id`, `updated_at`, `total_price`, `product_id`, `quantity`, `created_at`) VALUES(9, '2007-08-26 19:30:18', '3.22', 3, 2, '2007-08-26 19:30:18')
SQL (0.069437)   COMMIT
{% endhighlight %}

{% highlight ruby %}
order = Order.new
product = Product.find(1)
li = LineItem.new(:product =>product, :quantity => 2, :total_price => 3.22)
order.line_items << li
li.save
{% endhighlight %}

产生Log如下：

{% highlight sql %}
Product Load (0.002695)   SELECT * FROM products WHERE (products.`id` = 1)
SQL (0.023173)   BEGIN
SQL (0.000987)   COMMIT
SQL (0.000777)   BEGIN
SQL (0.000000)   Mysql::Error: Column 'order_id' cannot be null: INSERT INTO line_items (`order_id`, `updated_at`, `total_price`, `product_id`, `quantity`, `created_at`) VALUES(NULL, '2007-08-26 19:34:15', '3.22', 1, 2, '2007-08-26 19:34:15')
SQL (0.002174)   ROLLBACK
SQL (0.003055)   BEGIN
SQL (0.001734)   COMMIT
{% endhighlight %}
