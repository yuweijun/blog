---
layout: post
title: "mysql error row size too large"
date: "Mon Dec 24 2007 12:56:00 GMT+0800 (CST)"
categories: mysql
---

Row size too large. The maximum row size for the used table type, not counting BLOBs, is 65535. You have to change some columns to TEXT or BLOBs

一个表有130个varchar(255)字段，gbk编码，建表报以上错误，原因如下:

{% highlight text %}
130 * 255 * 2 = 66560 > 66535 (GBK 2字节)
{% endhighlight %}

表字段长度总和mysql有限制。如果表是utf-8的话，按3字节计算.
