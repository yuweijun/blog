---
layout: post
title: "mysql load apache2 logs"
date: "Thu May 27 2007 19:58:00 GMT+0800 (CST)"
categories: mysql
---

可以将apache2日志文件写入mysql数据库表。你可以将以下内容放到apache2配置文件中，更改apache2日志格式，使mysql更容易读取：

{% highlight bash %}
LogFormat "\"%h\",%{ %Y%m%d%H%M%S }t,%>s,\"%b\",\"%{Content-Type}o\", \"%U\",\"%{Referer}i\",\"%{User-Agent}i\""
{% endhighlight %}

要想将该格式的日志文件装载到MySQL，你可以使用以下语句:

{% highlight bash %}
LOAD DATA INFILE '/local/access_log' INTO TABLE tbl_name FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' ESCAPED BY '\\'
{% endhighlight %}

所创建的表中的列应与写入日志文件的LogFormat行对应。

第2个方法是在apache2 http.conf里在日志记录时利用管道命令直接将apache2 access log写入mysql中，还有个apache2的module可以直接实现此功能：mod_log_mysql，具体到其网站上查看设置使用方法，这种直接将access log写入mysql中的方法个人认为不是很妥当，还是用日志循回，再将日志导入mysql中比较好。

第3个方法就是利用php, perl, ruby写个脚本分析access_log后写入mysql中。

上面提到将apache2 access log写入mysql中的3个方法，但个人还是觉得最后自己写个脚本导入日志到mysql中比较好。
