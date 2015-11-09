---
layout: post
title: "linux ifconfig 详解"
date: "Thu May 17 2007 23:59:00 GMT+0800 (CST)"
categories: linux
---

作用
-----

ifconfig用于查看和更改网络接口的地址和参数，包括IP地址、网络掩码、广播地址，使用权限是超级用户。

格式
-----

{% highlight bash %}
ifconfig -interface [options] address
{% endhighlight %}

主要参数
-----

{% highlight tex %}
-interface：指定的网络接口名，如eth0和eth1。
up：激活指定的网络接口卡。
down：关闭指定的网络接口。
broadcast address：设置接口的广播地址。
pointopoint：启用点对点方式。
address：设置指定接口设备的IP地址。
netmask address：设置接口的子网掩码。
更多可man ifconfig查看说明。
{% endhighlight %}

应用说明
-----

ifconfig是用来设置和配置网卡的命令行工具。为了手工配置网络，这是一个必须掌握的命令。使用该命令的好处是无须重新启动机器。要赋给eth0接口IP地址207.164.186.2，并且马上激活它，使用下面命令：

{% highlight bash %}
ifconfig eth0 192.168.1.11 netmask 255.255.255.0 broadcast 255.255.255.255
{% endhighlight %}

虚拟一块网卡：

{% highlight bash %}
ifconfig eth0:1 192.168.1.21 netmask 255.255.255.0 broadcast 255.255.255.255
{% endhighlight %}

该命令的作用是设置网卡eth0的IP地址、网络掩码和网络的本地广播地址。若运行不带任何参数的ifconfig命令，这个命令将显示机器所有激活接口的信息。带有“-a”参数的命令则显示所有接口的信息，包括没有激活的接口。注意，用ifconfig命令配置的网络设备参数，机器重新启动以后将会丢失，可设置启动运行此命令，也可以配置ifcfg-eth0文件，重启也就不会丢失了，虚拟的网卡也可以复制一份原文件ifcfg-eth0修改IPADDR即可。

如果要暂停某个网络接口的工作，可以使用down参数：
-----

{% highlight bash %}
ifconfig eth0 down
ifconfig eth0:1 up
{% endhighlight %}

另外Redhat里还有个`ifup`和`ifdown`命令与上面二个作用相似，只是暂时起作用。
