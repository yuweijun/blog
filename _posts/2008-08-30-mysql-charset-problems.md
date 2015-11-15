---
layout: post
title: "mysql 数据库字符集问题"
date: "Sat Aug 30 2008 16:17:00 GMT+0800 (CST)"
categories: mysql
---

在rails项目开发中碰到一个mysql数据库字符集问题 ，一个`default charset utf8`的数据库中，有一个`engine=myisam default charset=utf8`的数据表，手工往其中插入中文，日文，韩文都没有问题，但通过rails添加数据记录时，中文，日文没有问题，但是插入韩文就一直乱码，rails和手工执行的sql相同，如下所示：

{% highlight sql %}
set names utf8;
insert table_name values('', '시험');
{% endhighlight %}

网上查不到什么相关资料，在rails中做了一些测试性的调整，都没有解决问题。

最后决定调整mysql server的默认字符集，虽然觉得这个应该不会造成rails无法插入韩文，因为在rails中加过`before_save`前置过滤器，在其中先运行`set names utf8;`，之后再执行insert操作，也一样是乱码。

在my.cnf中加入如下配置并重启mysql服务之后，rails就可以正常插入韩文了!!

{% highlight bash %}
# The MySQL server
[mysqld]
character-set-server = utf8
{% endhighlight %}

另摘录一段mysql server启动的参数选项说明
-----

1. --character-set-server=charset 使用charset作为默认服务器字符
2. --collation-server=collation 使用collation作为默认服务器校对规则
3. (DEPRECATED) --default-character-set=charset 使用char设置作为默认字符集。由于--character-set-server，反对使用该选项。
4. --default-collation=collation 使用collation作为默认校对规则。由于--collation-server，反对使用该选项。
