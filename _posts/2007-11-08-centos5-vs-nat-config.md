---
layout: post
title: "centos5.0 vs-nat config"
date: "Thu Nov 08 2007 12:10:00 GMT+0800 (CST)"
categories: linux
---

参考http://zh.linuxvirtualserver.org/node/26，环境如下：

* linux centos5.0 server: eth0 192.168.0.199(RIP); eth0:1 192.168.1.199(VIP); gateway 192.168.1.1(连外网)
* windowsXP: 192.168.0.109(RIP); gateway: 192.168.0.199(网关设置为Linux CentOS5.0机器的IP)
* windowsXP: 192.168.0.112(RIP); gateway: 192.168.0.199(网关设置为Linux CentOS5.0机器的IP)
* XP机器都有开80端口WEB服务，linux机器上80端口的服务不一定要开。

一、在CentOS5.0上搭建NAT
-----

{% highlight bash %}
$> vi /etc/sysctl.conf
net.ipv4.ip_forword = 1
$> /sbin/sysctl -p
{% endhighlight %}

需要安装iptables的模块,用这个命令检查:

{% highlight bash %}
$> modprobe ip_tables
{% endhighlight %}

也能用以下命令开启NAT:

{% highlight bash %}
$> echo "1" > /proc/sys/net/ipv4/ip_forword
{% endhighlight %}

二、关闭iptables service,测试NAT是否启用
-----

{% highlight bash %}
$> service iptables stop
{% endhighlight %}

将其中一台XP机器的网关设置为此CentOS5.0的VIP地址192.168.1.199,IP改为192.168.1.109(如果real server是linux机器，修改了ip和gateway需要重启网络: $> service network restart),正确的话就可以正常访问局域网内其他192.168.1.*网段的机器和外网。
测试NAT通过后：

{% highlight bash %}
$> iptables -t nat -A POSTROUTING -j MASQUERADE -s 192.168.1.0/24
# 可选
$> iptables -t nat -A POSTROUTING -j MASQUERADE -s 192.168.0.0/24
$> service iptables save
# 先stop再save会覆盖原来iptables规则，如果不想覆盖则在iptable开启状态进行这二个命令
$> service iptables start
{% endhighlight %}

再测试NAT是否正常，能否从XP机器联外网，如果正常那就把IP和gateway改回原来的设置。如果不能访问外网，需要调整一下iptables reject规则，具体可以看鸟哥的NAT设置相关文章。

三、安装ipvsadm
-----

{% highlight bash %}
$> yum install ipvsadm
$> chkconfig ipvsadm on
# linuxcommand info: http://www.linuxcommand.org/man_pages/ipvsadm8.html
$> vi /etc/sysconfig/ipvsadm
ipvsadm -A -t 192.168.1.199:80 -s rr
ipvsadm -a -t 192.168.1.199:80 -r 192.168.0.109:80 -m -w 1
ipvsadm -a -t 192.168.1.199:80 -r 192.168.0.112:80 -m -w 1
# ipvsadm -a -t 192.168.1.199:80 -r 192.168.0.199:80 -m -w 1

$> service ipvsadm start
$> ipvsadm -ln
IP Virtual Server version 1.2.1 (size=4096)
Prot LocalAddress:Port Scheduler Flags
-> RemoteAddress:Port Forward Weight ActiveConn InActConn
TCP 192.168.1.199:80 rr
-> 192.168.0.112:80 Masq 1 0 1
-> 192.168.0.109:80 Masq 1 0 0
{% endhighlight %}

四、安装heartbeat
-----

{% highlight bash %}
$> yum install heartbeat

$> yum install heartbeat-ldirector
$> chkconfig ldirectord on
$> vi /etc/ha.d/ldirectord.cf

# Global Directives
checktimeout=10
checkinterval=2
# fallback=127.0.0.1:80
autoreload=no
# logfile="/var/log/ldirectord.log"
logfile="local0"
quiescent=yes

# Virtual Server for HTTP
virtual=192.168.1.199:80
        fallback=127.0.0.1:80
        # real=192.168.0.199:80 masq
        real=192.168.0.109:80 masq
        real=192.168.1.112:80 masq
        service=http
        request="heartbeat.html"
        receive="Test Page"
        scheduler=rr
        # persistent=600
        protocol=tcp
        checktype=negotiate

{% endhighlight %}

在WEB Server的根目录下要有heartbeat.html这个文件，并且里面内容为Test Page字符串，Ldirector检测到这个文件并接收到的字符串能匹配上这个字符串即认为此Real Server为活跃的，不然会从集群中移除此server

五、测试结果
-----

在192.168.1.***网段内找一个client机器访问http://192.168.1.199/index.html，同时在LVS上用以下命令查看连接数：

{% highlight bash %}
$> ipvsadm -L -c
IPVS connection entries
pro expire state source virtual destination
{% endhighlight %}

可在上面的list中看到NAT将source发起的http请求调度到不同的destination上，正常的话应该能看到index.html页面内容。

需要注意的一点是请求的Client机器不能和集群的Real Server在同一个子网内，也不能在RealServer上发过Client请求，这样无法经NAT调度就不能正常显示index.html页面。
