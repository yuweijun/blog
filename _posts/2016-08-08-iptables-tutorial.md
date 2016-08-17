---
layout: post
title: "iptables tutorial"
date: Mon, 08 Aug 2016 21:57:49 +0800
categories: linux
---

本文是关于`iptables`使用的一些简单说明以及例子，更详细的使用可参考[鸟哥的私房菜](http://linux.vbird.org/linux_server/0250simple_firewall.php)和这篇[iptables指南](https://www.frozentux.net/iptables-tutorial/cn/iptables-tutorial-cn-1.1.19.html)。

iptables数据包处理流程示意图
-----

![iptables-chain-en]({{ site.baseurl }}/img/linux/iptables/iptables-chain-en.png)

iptables的一些基本概念
-----

1. tables: raw/filter/nat/mangle/security，与本机最有关系的就是filter表，filter表是用于存放所有与防火墙相关操作的默认表。
2. chains: 表由链组成，链是一些按顺序排列的规则的列表。默认的filter表包含`INPUT`，`OUTPUT`和`FORWARD`3条内建的链，这3条链作用于数据包过滤过程中的不同时间点，如上流程图所示。
3. rules: 数据包的过滤基于rule。rule由一个target目标（数据包匹配所有条件后的动作）和很多匹配（导致该规则可以应用的数据包所需要满足的条件）指定。
4. targe: 目标使用`-j`或者`--jump`选项指定，target常用的是`ACCEPT`，`DROP`，`REJECT`和`LOG`。

如果目标是`REJECT`，数据包的命运会被立刻决定，并且当前表的数据包的处理过程会停止，也就是说`REJECT`会拦阻该数据包，并返回数据包通知对方，可以返回的数据包有几个选择：`ICMP port-unreachable`、`ICMP echo-reply`或是`tcp-reset`（这个数据包会要求对方关闭连接），进行完此处理动作后，将不再比对其它规则，直接中断过滤程序。

iptables常用参数说明
-----

| 参数                 | 说明                                       |
|:---------------------|:-------------------------------------------|
| -P  \--policy         |  定义默认策略                              |
| -L  \--list           |  查看iptables规则列表                      |
| -A  \--append         |  在规则列表的最后增加1条规则               |
| -I  \--insert         |  在指定的位置插入1条规则                   |
| -D  \--delete         |  从规则列表中删除1条规则                   |
| -R  \--replace        |  替换规则列表中的某条规则                  |
| -F  \--flush          |  删除表中所有规则                          |
| -Z  \--zero           |  将表中数据包计数器和流量计数器归零        |
| -X  \--delete-chain   |  删除自定义链                              |
| -v  \--verbose        |  与-L他命令一起使用显示更多更详细的信息    |

rules匹配参数说明
-----

| 匹配参数             | 说明                                        |
|:---------------------|:--------------------------------------------|
| -i \--in-interface    | 指定数据包从哪个网络接口进入               |
| -o \--out-interface   | 指定数据包从哪个网络接口输出               |
| -p \--proto           | 指定数据包匹配的协议，如TCP、UDP和ICMP等   |
| -s \--source          | 指定数据包匹配的源地址                     |
|    \--sport           | 指定数据包匹配的源端口号，结合参数`-p`使用 |
|    \--dport           | 指定数据包匹配的目的端口号                 |
| -m \--match           | 指定数据包规则所使用的过滤模块，如state    |
| \--state              | 数据包的状态，如ESTABLISHED                |
| \--icmp-type          | 后面必须要接ICMP的数据包类型，例如8        |

因为只有`tcp`和`udp`的数据包才有端口号，因此要使用`--sport`和`--dport`时，要加上`-p tcp`或`-p udp`参数才可执行。

过滤模块`-m`最常用的方式为：

{% highlight bash %}
$> iptables -A INPUT -m state --state RELATED,ESTABLISHED -j ACCEPT
{% endhighlight %}

`iptables`包括一个模块，它允许管理员使用`connection tracking`方法来检查和限制到内部网络中可用服务的连接，连接跟踪把所有连接都保存在一个表格内，它令管理员能够根据以下连接状态来允许或拒绝连接：

1. NEW — 请求新连接的分组，如 HTTP 请求。
2. ESTABLISHED — 属于当前连接的一部分的分组。
3. RELATED — 请求新连接的分组，但是它也是当前连接的一部分，如被动模式的 FTP 连接，其连接端口是 20，但是其传输端口却是 1024 以上的未使用端口。
4. INVALID — 不属于连接跟踪表内任何连接的分组。

你可以和任何网络协议一起使用 iptables 连接跟踪的状态功能，即便协议本身可能是无状态的（如 UDP）。

iptables命令行使用示例
-----

上述提到`iptables`的5个表，最常用的是`filter`表，以下命令不指定参数`-t`，都是针对`filter`表操作。

使用以下命令查看当前规则和匹配数：

{% highlight bash %}
$> iptables -nvL
{% endhighlight %}

本机操作可将`INPUT`链的策略设置为`DROP`，如下指令，远程SSH操作服务器测试时，使用下面那个指令。

{% highlight bash %}
$> iptables -P INPUT DROP
{% endhighlight %}

如果在远程服务器上测试`iptables`时，将`INPUT`链过滤数据包的默认策略改成`ACCEPT`，表示`filter`表的`INPUT`链默认接受一切请求。

{% highlight bash %}
$> iptables -P INPUT ACCEPT
{% endhighlight %}

将`iptables`计数器置0：

{% highlight bash %}
$> iptables -Z
{% endhighlight %}

没有任何参数的`-F`命令在当前表中刷新所有链，在SSH远程服务器上测试这个命令时要注意`INPUT`默认策略不要是`DROP`，会被服务器关在防火墙外的，同样的，`-X`命令删除表中所有非默认链，使用这些命令刷新和重置`iptables`到默认状态：

{% highlight bash %}
$> iptables -X
$> iptables -F
{% endhighlight %}

允许来自于`lo`接口的数据包，如果没有此规则，你将不能通过`127.0.0.1`访问本地服务：

{% highlight bash %}
$> iptables -A INPUT -i lo -j ACCEPT
{% endhighlight %}

开放ssh端口和web端口：

{% highlight bash %}
$> iptables -A INPUT -p tcp --dport 22 -j ACCEPT
$> iptables -A INPUT -p tcp --dport 80 -j ACCEPT
{% endhighlight %}

允许内网访问mysql的3306端口：

{% highlight bash %}
$> iptables -A INPUT -p tcp -s 192.168.1.0/24 --dport 3306 -j ACCEPT
$> iptables -A INPUT -p tcp -s 10.0.0.0/8 --dport 3306 -j ACCEPT
{% endhighlight %}

允许`icmp`包通过,也就是允许`ping`：

{% highlight bash %}
$> iptables -A INPUT -p icmp -m icmp --icmp-type 8 -j ACCEPT
{% endhighlight %}

允许所有已经建立的和相关的连接：

{% highlight bash %}
$> iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
$> iptables -A OUTPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
{% endhighlight %}

上述命令在新版本中写法：

{% highlight bash %}
$> iptables -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
{% endhighlight %}

所有其他不符合上述规则的数据包全部拒绝：

{% highlight bash %}
$> iptables -A INPUT -p udp -j REJECT --reject-with icmp-port-unreachable
$> iptables -A INPUT -p tcp -j REJECT --reject-with tcp-reset
$> iptables -A INPUT -j REJECT --reject-with icmp-proto-unreachable
{% endhighlight %}

注意: 这里使用`REJECT`而不是`DROP`，因为`RFC 1122 3.3.8`要求主机尽可能返回`ICMP`错误而不是`DROP`数据包。

> 3.3.8  Error Reporting
>
> Wherever practical, hosts MUST return ICMP error datagrams on
>
> detection of an error, except in those cases where returning an
>
> ICMP error message is specifically prohibited.
>
> DISCUSSION:
>
> A common phenomenon in datagram networks is the "black
>
> hole disease": datagrams are sent out, but nothing comes
>
> back.  Without any error datagrams, it is difficult for
>
> the user to figure out what the problem is.

iptables相关的其他命令
-----

`iptables`是一个`Systemd`服务，因此可以这样启动：

{% highlight bash %}
$> systemctl enable iptables.service
$> systemctl start iptables.service
{% endhighlight %}

通过命令行添加规则，配置文件不会自动改变，所以必须手动保存：

{% highlight bash %}
$> iptables-save > /etc/iptables/iptables.rules
$> service iptables save
{% endhighlight %}

修改配置文件后，需要重新加载服务：

{% highlight bash %}
$> systemctl reload iptables
{% endhighlight %}

或者通过`iptables`直接加载：

{% highlight bash %}
$> iptables-restore < /etc/iptables/iptables.rules
{% endhighlight %}

iptables示例脚本一
-----

{% highlight bash %}
iptables -P INPUT DROP
iptables -P OUTPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -F
iptables -X
iptables -Z

iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A INPUT -p icmp -j ACCEPT
iptables -A INPUT -i lo -j ACCEPT

iptables -A INPUT -p tcp --dport 22 -j ACCEPT
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j ACCEPT
iptables -A INPUT -p tcp --dport 3306 -j ACCEPT

iptables -A INPUT -p tcp -s 192.168.1.0/24 -j ACCEPT
iptables -A INPUT -p tcp -s 10.0.0.0/8 -j ACCEPT

iptables -A INPUT -j REJECT --reject-with icmp-host-prohibited
iptables -A FORWARD -j REJECT --reject-with icmp-host-prohibited
{% endhighlight %}

iptables示例脚本二
-----

{% highlight bash %}
#! /bin/sh
iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -P INPUT DROP
iptables -P OUTPUT ACCEPT
iptables -P FORWARD ACCEPT

iptables -A INPUT -i lo -j ACCEPT
iptables -A INPUT -p icmp -j ACCEPT
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A INPUT -p tcp -m state --state NEW -m tcp --dport 22 -j ACCEPT
iptables -A INPUT -p tcp -m state --state NEW -m tcp --dport 1723 -j ACCEPT
iptables -A INPUT -p tcp -m state --state NEW -m tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp -m state --state NEW -m tcp --dport 8388 -j ACCEPT
iptables -t nat -A POSTROUTING -o eth1 -s 192.168.0.0/24 -j SNAT --to-source 200.88.88.188

iptables -I FORWARD -p tcp --syn -i ppp+ -j TCPMSS --set-mss 1356

iptables -A INPUT -j REJECT --reject-with icmp-host-prohibited
iptables -A FORWARD -j REJECT --reject-with icmp-host-prohibited
{% endhighlight %}

/etc/sysconfit/iptables示例
-----

{% highlight bash %}
*filter
:INPUT DROP [0:0]
:FORWARD DROP [0:0]
:OUTPUT ACCEPT [0:0]
:TCP - [0:0]
:UDP - [0:0]
-A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
-A INPUT -i lo -j ACCEPT
-A INPUT -m conntrack --ctstate INVALID -j DROP
-A INPUT -p icmp -m icmp --icmp-type 8 -m conntrack --ctstate NEW -j ACCEPT
-A TCP -p tcp --dport 80 -j ACCEPT
-A TCP -p tcp --dport 443 -j ACCEPT
-A TCP -p tcp --dport 22 -j ACCEPT
-A INPUT -p udp -m conntrack --ctstate NEW -j UDP
-A INPUT -p tcp --tcp-flags FIN,SYN,RST,ACK SYN -m conntrack --ctstate NEW -j TCP
-A INPUT -p udp -j REJECT --reject-with icmp-port-unreachable
-A INPUT -p tcp -j REJECT --reject-with tcp-reset
-A INPUT -j REJECT --reject-with icmp-proto-unreachable
COMMIT
{% endhighlight %}

FORWARD and NAT
-----

按照默认设置，Linux内核中的`IPv4`策略禁用了对IP转发的支持，要启用IP转发，请运行以下命令：

{% highlight bash %}
$> sysctl -w net.ipv4.ip_forward=1
{% endhighlight %}

如果该命令是通过shell运行的，那么其设置在系统重启后就失效，可以通过编辑`/etc/sysctl.conf`文件来保存转发设置。

`Network Address Translation - NAT`一般情况下分为：

1. `SNAT`: 源地址转换，是指在数据包从网卡发送出去的时候，把数据包中的源地址部分替换为指定的IP，这样接收方就认为数据包的来源是被替换的那个IP的主机，MASQUERADE，即俗称的IP欺骗，是用发送数据的网卡上的IP来替换源IP，主要用于内部共享IP访问外部。
2. `DNAT`: 目的地址转换，就是指数据包从网卡进来的时候，修改数据包中的目的IP，转发给内网其他服务器，主要用于内部服务对外发布。

参考下图，因为路由发生在`PREROUTING`和`FORWARD`之间，并且路由是按目的地址来选择的，因此目标地址转换`DNAT`必然是在`PREROUTING`链上来进行的，而`SNAT`是在数据包发送出去的时候才进行，因此是在`POSTROUTING`链上进行的，更详细的说明可参考[鸟哥的私房菜](http://linux.vbird.org/linux_server/0250simple_firewall.php#nat_what)，其中画有数据包流向和处理示意图，并且关于`NAT`服务器与路由器的区别也讲得很清楚。

![iptables-flow-simple.png]({{ site.baseurl }}/img/linux/iptables/iptables-flow-simple.png)

例如，把HTTP请求转发到`172.31.0.23`上的HTTP服务器，运行以下命令：

{% highlight bash %}
$> iptables -t nat -A PREROUTING -i eth0 -p tcp --dport 80 -j DNAT --to 172.31.0.23:80
{% endhighlight %}

如果`FORWARD`链的数据包处理的默认策略是`DROP`，就必须再加一条规则来允许转发进入的HTTP请求，运行以下命令可以达到这个目的：

{% highlight bash %}
$> iptables -A FORWARD -i eth0 -p tcp --dport 80 -d 172.31.0.23 -j ACCEPT
{% endhighlight %}

把访问`172.31.0.23`的访问转发到`192.168.0.2`上：

{% highlight bash %}
$> iptables -t nat -A PREROUTING -d 172.31.0.23 -j DNAT --to-destination 192.168.0.2
{% endhighlight %}

把即将要流出本机的数据的`source ip address`修改为本机的外网地址`200.88.88.188`，数据包在达到目的机器以后，目的机器会将响应包返回到`200.88.88.188`也就是本机。

{% highlight bash %}
$> iptables -t nat -A POSTROUTING -s 192.168.0.0/24 -j SNAT --to-source 200.88.88.188
{% endhighlight %}

iptables指令参考示例
-----

{% highlight bash %}
iptables -A INPUT -p icmp -j ACCEPT
iptables -A INPUT -i lo -p all -j ACCEPT
iptables -A INPUT -p tcp -s 192.168.10.1 -j DROP
iptables -A OUTPUT -p tcp --sport 31337 -j DROP
iptables -A OUTPUT -p tcp --dport 31337 -j DROP
iptables -A INPUT -s 192.168.10.1 -p tcp --dport 22 -j ACCEPT
iptables -A INPUT -s 192.168.10.0/24 -p tcp --dport 22 -j ACCEPT
iptables -A OUTPUT -o eth0 -p tcp --sport 22 -m state --state ESTABLISHED -j ACCEPT
iptables -A INPUT -i eth0 -p tcp -s 192.168.10.0/24 --dport 22 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -A OUTPUT -o eth0 -p tcp --sport 22 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -A INPUT -m state --state INVALID -j DROP
iptables -A OUTPUT -m state --state INVALID -j DROP

iptables -A FORWARD -i eth0 -o eth1 -m state --state RELATED,ESTABLISHED -j ACCEPT
iptables -A FORWARD -s 192.168.1.0/24 -i eth0 -j DROP
iptables -A FORWARD -p TCP ! --syn -m state --state NEW -j DROP
iptables -A FORWARD -f -m limit --limit 100/s --limit-burst 100 -j ACCEPT
iptables -A FORWARD -p icmp -m limit --limit 1/s --limit-burst 10 -j ACCEPT
iptables -A FORWARD -m state --state INVALID -j DROP

iptables -t nat -A PREROUTING -i eth0 -s 10.0.0.0/8 -j DROP
iptables -t nat -A PREROUTING -i eth0 -s 172.16.0.0/12 -j DROP
iptables -t nat -A PREROUTING -i eth0 -s 192.168.0.0/16 -j DROP
iptables -t nat -A PREROUTING -d 192.168.10.1 -j DROP
iptables -t nat -A PREROUTING -p tcp --dport 80 -j DROP
iptables -t nat -A PREROUTING -p tcp --dport 21 -d 192.168.10.1 -j DROP
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
iptables -t nat -A POSTROUTING -o eth1 -s 192.168.0.0/24 -j SNAT --to-source 200.88.88.188
{% endhighlight %}

iptables数据包处理流程图参考
-----

1. [iptables-flow-chart.png]({{ site.baseurl }}/img/linux/iptables/iptables-flow-chart.png)
2. [iptables-flow-cn.png]({{ site.baseurl }}/img/linux/iptables/iptables-flow-cn.png)
3. [iptables-packet-flow.gif]({{ site.baseurl }}/img/linux/iptables/iptables-packet-flow.gif)
4. [iptables-process-flow.png]({{ site.baseurl }}/img/linux/iptables/iptables-process-flow.png)
5. [iptables-nat-snat.png]({{ site.baseurl }}/img/linux/iptables/iptables-nat-snat.png)
6. [iptables-nat-dnat.png]({{ site.baseurl }}/img/linux/iptables/iptables-nat-dnat.png)

References
-----

1. [鸟哥的Linux私房菜 - 防火墙与NAT伺服器](http://linux.vbird.org/linux_server/0250simple_firewall.php)
2. [iptables](https://wiki.archlinux.org/index.php/Iptables)
3. [Simple stateful firewall](https://wiki.archlinux.org/index.php/Simple_stateful_firewall)
4. [Requirements for Internet Hosts -- Communication Layers](https://tools.ietf.org/html/rfc1122#page-69)
5. [FORWARD 和 NAT 规则](http://man.chinaunix.net/linux/redhat/rhel-sg-zh_cn-4/s1-firewall-ipt-fwd.html)

