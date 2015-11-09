---
layout: post
title: "linux route 详解"
date: "Mon May 21 2007 12:01:00 GMT+0800 (CST)"
categories: linux
---

linux中route命令说明：

{% highlight bash %}
$> ping -c 4 192.168.1.1

PING 192.168.1.1 (192.168.1.1) 56(84) bytes of data.
64 bytes from 192.168.1.1: icmp_seq=1 ttl=64 time=0.683 ms
64 bytes from 192.168.1.1: icmp_seq=2 ttl=64 time=0.671 ms
64 bytes from 192.168.1.1: icmp_seq=3 ttl=64 time=0.683 ms
64 bytes from 192.168.1.1: icmp_seq=4 ttl=64 time=0.670 ms

--- 192.168.1.1 ping statistics ---
4 packets transmitted, 4 received, 0% packet loss, time 3000ms
rtt min/avg/max/mdev = 0.670/0.676/0.683/0.032 ms
{% endhighlight %}

说明局域网是正常的。

{% highlight bash %}
$> ping 202.101.63.200
connect: Network is unreachable
{% endhighlight %}

这是因为路由没有设置，在ifcfg-eth0文件里设置GATEWAY即可，或者是利用route命令，在命令行里添加一个路由或者默认路由。

{% highlight bash %}
$> route add default gw 192.168.1.1
{% endhighlight %}


route命令详解：
-----

来源于iputils

{% highlight bash %}
语法 route [参数] 内部指令 [内部指令参数]

内部指令
del|add 删除增加路由
-net|-host 指定网段地址或主机地址
target 目的地址或者网络表示

内部指令参数：
netmask NM
gw IPADDR
metric M 设置路由表中的长度字段
mss M 设置TCP最大的区块长度为MB
refect 指定拒绝的路由。
def IF 指定该路由绑定特定的设备IF（如eth0)
{% endhighlight %}

范例：通过eth0的设备增加一个路由到192.168.1.0（子网掩码为255.255.255.0）网段：

{% highlight bash %}
$> route add -net 192.168.1.0 netmask 255.255.255.0 dev eth0
{% endhighlight %}


要把目标地址是202.101.x.x的数据包通过202.101.62.254路由出去，使用：

{% highlight bash %}
route add –net 202.101.0.0 netmask 255.255.0.0 gw 202.101.62.254
{% endhighlight %}


删除添加默认路由：

{% highlight bash %}
$> route del default gw 192.168.0.1
$> route add default gw 192.168.1.1
{% endhighlight %}

