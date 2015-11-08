---
layout: post
title: "ntpdate error: no server suitable for synchronization found"
date: "Mon Oct 22 2007 14:05:00 GMT+0800 (CST)"
categories: linux
---

主要是ntpd server的iptables把udp的123端口关了，局域网内其他机器无法访问到Server，所以才会报No Server suitable for synchronization found这个错误。

打开此udp端口重启iptables后，运行以下命令：

{% highlight bash %}
/usr/sbin/ntpdate ntp-server-ip-address; /sbin/hwclock -w
{% endhighlight %}

当然client端最好是做个crontab定时跑一下这个命令，同步时间。

