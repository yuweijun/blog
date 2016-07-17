---
layout: post
title: "mysql error 1071 specified key was too long"
date: "Fri Sep 21 2007 14:00:00 GMT+0800 (CST)"
categories: mysql
---

[1071]Mysql::Error: Specified key was too long; max key length is 1000 bytes
-----

在Windows XP下用Mysql5.1.20创建一个表索引碰到这个错误，错误号1071，表为GBK编码，MyISAM引擎。Google了一下，这个在Mysql5.2.0之前是个Bug，改用默认的Latin1字符集就可以避过这个问题，未验证，但是在CentOS 5.0下安装的Mysql5.0.45这个错误并不会发生，具体跟操作系统还有些关系。

错误原因说明及解决方法如下：
-----

建立索引时，数据库计算key的长度是累加所有Index用到的字段的char长度后再按下面比例乘起来不能超过限定的key长度`1000`字节：

{% highlight text %}
latin1 = 1 byte = 1 character
uft8 = 3 byte = 1 character
gbk = 2 byte = 1 character
{% endhighlight %}

举例能看得更明白些，以GBK为例：

{% highlight sql %}
CREATE UNIQUE INDEX `unique_record` ON reports (`report_name`, `report_client`, `report_city`);
{% endhighlight %}

其中：

1. report_name varchar(200)
2. report_client varchar(200)
3. report_city varchar(200)

GBK字符集中一个字符为2个字节：

> (200 + 200 +200) * 2 = 1200 > 1000

所以就会报1071错误，只要将`report_city`改为`varchar(100)`那么索引就能成功建立。

如果表是UTF8字符集，那索引还是建立不了。
