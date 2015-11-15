---
layout: post
title: "mysql slave同步报错server_errno=1236"
date: "Mon May 24 2010 14:11:00 GMT+0800 (CST)"
categories: mysql
---

mysql日志报错内容：

{% highlight text %}
100519 19:35:33 [Note] Slave I/O thread: connected to master 'repl@master:3306', replication started in log 'mysql-bin.000176' at position 615525147
100519 19:35:33 [ERROR] Error reading packet from server: Could not find first log file name in binary log index file ( server_errno=1236)
{% endhighlight %}

从日志信息上可知，slave已经连接到master，并且准备从指定的binlog文件指定位置开始同步，但后面错误提示日志文件找不到，而master服务器上日志文件是存在的。

这种情况有二种处理方法：

1. 重启主数据库(master)之后，然后slave上`stop slave;start slave`，再检查同步的状态。
2. 不重启主数据库(master)，则用`mysqlbinlog`根据`start-position`或者`start-datetime`，将日志分析出来后，将分析结果在slave上用mysql命令导入，导入完成后，再用`change master to`语句，从下一个日志文件`master_log_position=98`开始同步。

另外在使用`mysqlbinlog`工具进行日志导入时，需要注意以下问题，下面内容转自mysql官方手册：

如果mysql服务器上有多个要执行的二进制日志，安全的方法是在一个连接中处理它们。

下面是一个说明什么是不安全的例子，千万不要这么操作：

{% highlight bash %}
$> mysqlbinlog hostname-bin.000001 | mysql # DANGER!!
$> mysqlbinlog hostname-bin.000002 | mysql # DANGER!!
{% endhighlight %}

使用与服务器的不同连接来处理二进制日志时，如果第1个日志文件包含一个`create temporary table`语句，第2个日志包含一个使用该临时表的语句，则会造成问题。当第1个mysql进程结束时，服务器撤销临时表。当第2个mysql进程想使用该表时，服务器报告`不知道该表`。

要想避免此类问题，使用一个连接来执行想要处理的所有二进制日志中的内容。下面提供了一种方法：

{% highlight bash %}
$> mysqlbinlog hostname-bin.000001 hostname-bin.000002 | mysql -u root -ppassword
{% endhighlight %}

References
-----

1. [http://yuweijun.blogspot.com/2009/12/mysql50.html](http://yuweijun.blogspot.com/2009/12/mysql50.html)
