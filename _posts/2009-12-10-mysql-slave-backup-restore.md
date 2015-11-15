---
layout: post
title: "mysql-5.0.37同步备份恢复的三种方法"
date: "Thu Dec 10 2009 12:39:00 GMT+0800 (CST)"
categories: mysql
---

第一种
-----

最简单的方法适用于数据库文件比较小，能在停止主数据库服务后几分钟内打完tar包的情况，这种情况与第一次做slave同步的方法一样：

{% highlight sql %}
mysql> FLUSH TABLES WITH READ LOCK;
mysql> SHOW MASTER STATUS;
{% endhighlight %}

记录状态后进入命令行将数据库原生数据文件打个tar包，再

{% highlight sql %}
mysql> UNLOCK TABLES;
{% endhighlight %}

将tar包重布署到mysql slave上即可，注意将tar包中的日志文件，master.info，relay-log.info要删除之后再change master to。

第二种
-----

此方法是利用mysql的二进制日志文件。

首先重启一次主数据库。

然后在slave上用以下命令查看二进制日志文件执行的最后SQL语句，其中start-datetime时间为此二进制日志文件最后的修改时间，或者提前30秒方便查看最后执行完成的SQL语句：

{% highlight bash %}
$> mysqlbinlog -S /tmp/mysql.sock hostname-relay-bin.000002 --start-datetime='2009-12-11 11:12:00'
{% endhighlight %}

然后在主数据库上用命令筛选出符合时间范围的日志：

{% highlight bash %}
$> mysqlbinlog --start-datetime='2009-12-11 11:12:00' -S /tmp/mysql.sock mysql-bin.000007 > /home/username/bin-log.sql
{% endhighlight %}

再用grep命令，查找到bin-log.sql中与之前在slave上查到的sql行号，并用sed命令删除1到此行号的全部行(举例查到行号为1234)：

{% highlight bash %}
$> grep -n "last executed sql statement" /home/username/bin-log.sql
$> sed '1,1234d' /home/username/bin-log.sql > /home/username/bin-log-sed.sql
{% endhighlight %}

将生成的bin-log-sed.sql文件传到slave数据库上用mysql命令行工具导入数据。

{% highlight bash %}
$> mysql -uroot -p < bin-log-sed.sql
{% endhighlight %}

如果碰到报错，用sed再删除sql文件的前几行导入。
导入完成后，进行mysql控制台，下面的master_log_file是同步中断的主数据库日志文件mysql-bin.000007的后继日志文件名mysql-bin.000008：

{% highlight sql %}
mysql> stop slave;
mysql> change master to master_log_file='mysql-bin.000008', master_log_pos=98;
mysql> start slave;
mysql> show slave status\G
{% endhighlight %}

第三种
-----

此方法也是利用二进制日志文件恢复。

与第二种方法类似，只是在grep查到行号之后，用awk命令找到日志中断的位置，重新调整slave上的`master_log_pos`即可。
举例查到行号为1234：

{% highlight bash %}
$> cat /home/username/bin-log.sql |awk 'NR >= 1234 {print $0}' |more
{% endhighlight %}

可以找到最后执行完的那个SQL之后，服务器的二进制日志文件位置：

{% highlight text %}
last executed sql statuement;
{% endhighlight %}

之后会看到类似：

{% highlight text %}
# at 1023411738
{% endhighlight %}

这样的内容，用这个值更新slave上的master信息：

{% highlight sql %}
mysql> stop slave;
mysql> change master to master_log_file='mysql-bin.000007', master_log_pos=1023411738;
mysql> start slave;
mysql> show slave status\G
{% endhighlight %}

在slave的二进制日志或者是hostname-relay-log.info，hostname.err中也会有master上的end_log_pos，可以先尝试用一下，一般因为master/slave异常才导致同步失败，在slave上的这些信息已经不正确，所以需要用awk找到服务器上的二进制日志位置。
