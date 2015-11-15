---
layout: post
title: "mysql replication server_errno=2020"
date: "Sat Sep 27 2008 15:38:00 GMT+0800 (CST)"
categories: mysql
---

mysql同步报错2020

{% highlight text %}
080927 15:28:42 [Note] Slave: connected to master 'test@localhost:3306',replication resumed in log 'mysql-bin.000003' at position 34699088
080927 15:28:42 [ERROR] Error reading packet from server: Got packet bigger than 'max_allowed_packet' bytes ( server_errno=2020)
080927 15:28:42 [Note] Slave I/O thread: Failed reading log event, reconnecting to retry, log 'mysql-bin.000003' position 34699088
{% endhighlight %}

修改my.cnf中`max_allowed_packet`这个属性的值为16M。

{% highlight bash %}
$> vi /etc/my.cnf
# max_allowed_packet = 1M
# modify it to
max_allowed_packet = 16M
{% endhighlight %}
