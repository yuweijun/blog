---
layout: post
title: "find mysqld configure file position"
date: "Sat Jul 31 2010 12:43:00 GMT+0800 (CST)"
categories: mysql
---

检查mysqld读取配置文件的位置，可以用下面这个命令：

{% highlight bash %}
$> mysqld --verbose --help|grep -A 1 'Default options'
{% endhighlight %}

{% highlight text %}
Default options are read from the following files in the given order:
/etc/my.cnf /etc/mysql/my.cnf /usr/local/etc/my.cnf ~/.my.cnf
{% endhighlight %}

摘自《高性能MySQL》
