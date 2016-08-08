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
3. rules: 数据包的过滤基于rule。rule由一个target目标（数据包包匹配所有条件后的动作）和很多匹配（导致该规则可以应用的数据包所满足的条件）指定。
4. targe: 目标使用`-j`或者`--jump`选项指定，target常用的是`ACCEPT`，`DROP`，`REJECT`。

如果目标是`REJECT`，数据包的命运会立刻被决定并且在当前表的数据包的处理过程会停止。

iptables常用参数说明
-----

| 参数                 | 说明                                       |
|:---------------------|:-------------------------------------------|
| -P  --policy         |  定义默认策略                              |
| -L  --list           |  查看iptables规则列表                      |
| -A  --append         |  在规则列表的最后增加1条规则               |
| -I  --insert         |  在指定的位置插入1条规则                   |
| -D  --delete         |  从规则列表中删除1条规则                   |
| -R  --replace        |  替换规则列表中的某条规则                  |
| -F  --flush          |  删除表中所有规则                          |
| -Z  --zero           |  将表中数据包计数器和流量计数器归零        |
| -X  --delete-chain   |  删除自定义链                              |
| -v  --verbose        |  与-L他命令一起使用显示更多更详细的信息    |

rules匹配参数说明
-----

| 匹配参数             | 说明                                       |
|:---------------------|:-------------------------------------------|
| -i --in-interface    | 指定数据包从哪个网络接口进入               |
| -o --out-interface   | 指定数据包从哪个网络接口输出               |
| -p ---proto          | 指定数据包匹配的协议，如TCP、UDP和ICMP等   |
| -s --source          | 指定数据包匹配的源地址                     |
|    --sport           | 指定数据包匹配的源端口号                   |
|    --dport           | 指定数据包匹配的目的端口号                 |
| -m --match           | 指定数据包规则所使用的过滤模块，如state    |
| --state              | 一些数据包的状态                           |

因为只有`tcp`和`udp`的数据包才有端口号，因此要使用`--sport`和`--dport`时，要加上`-p tcp`或`-p udp`参数才可执行。

过滤模块最常用的方式为`-A INPUT -m state --state RELATED,ESTABLISHED -j ACCEPT`。

数据包状态主要有：

1. INVALID
2. ESTABLISHED
3. NEW
4. RELATED

iptables命令行使用示例
-----

上述提到`iptables`的5个表，最常用的是`filter`表，以下命令不指定参数`-t`，都是针对`filter`表操作。

本机操作可将`INPUT`链的策略设置为`DROP`，如下指令，远程SSH操作服务器测试时，使用下面那个指令。
{% highlight bash %}
$> iptables -P INPUT DROP
{% endhighlight %}

如果在远程服务器上测试`iptables`时，将`INPUT`改成`ACCEPT`，表示`filter`表的`INPUT`链默认接受一切请求。
{% highlight bash %}
$> iptables -P INPUT ACCEPT
{% endhighlight %}

清空默认所有规则：

{% highlight bash %}
$> iptables -F
{% endhighlight %}

清空自定义的所有规则：

{% highlight bash %}
$> iptables -X
{% endhighlight %}

将`iptables`计数器置0：

{% highlight bash %}
$> iptables -Z
{% endhighlight %}

允许来自于lo接口的数据包，如果没有此规则，你将不能通过127.0.0.1访问本地服务：

{% highlight bash %}
$> iptables -A INPUT -i lo -j ACCEPT
{% endhighlight %}

开放ssh端口22和web服务端口80：

{% highlight bash %}
$> iptables -A INPUT -p tcp --dport 22 -j ACCEPT
$> iptables -A INPUT -p tcp --dport 80 -j ACCEPT
{% endhighlight %}

允许内网访问mysql的3306端口：

{% highlight bash %}
$> iptables -A INPUT -p tcp -s 192.168.1.0/24 --dport 3306 -j ACCEPT
$> iptables -A INPUT -p tcp -s 10.0.0.0/8 --dport 3306 -j ACCEPT
{% endhighlight %}


#允许所有对外请求的返回包：

iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A INPUT -p icmp -j ACCEPT

允许icmp包通过,也就是允许ping：

{% highlight bash %}
$> iptables -A INPUT -p icmp -m icmp --icmp-type 8 -j ACCEPT
{% endhighlight %}


使用以下命令查看当前规则和匹配数：
{% highlight bash %}
$> iptables -nvL
{% endhighlight %}

使用这些命令刷新和重置`iptables`到默认状态：

{% highlight bash %}
$> iptables -X
$> iptables -F
{% endhighlight %}

iptables相关的命令
-----

`iptables`是一个`Systemd`服务，因此可以这样启动：

{% highlight bash %}
$> systemctl enable iptables.service
$> systemctl start iptables.service
{% endhighlight %}

通过命令行添加规则，配置文件不会自动改变，所以必须手动保存：

{% highlight bash %}
$> iptables-save > /etc/iptables/iptables.rules
{% endhighlight %}

修改配置文件后，需要重新加载服务：

{% highlight bash %}
$> systemctl reload iptables
{% endhighlight %}

或者通过`iptables`直接加载：

{% highlight bash %}
$> iptables-restore < /etc/iptables/iptables.rules
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

注意: 这里使用`REJECT`而不是`DROP`，因为`RFC 1122 3.3.8`要求主机尽可能返回`ICMP`错误而不是丢弃数据包。

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
{% endhighlight %}

iptables.rules示例
-----

{% highlight bash %}
# Generated by iptables-save v1.4.18 on Sun Mar 17 14:21:12 2013
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
# Completed on Sun Mar 17 14:21:12 2013
{% endhighlight %}

iptables其他指令示例
-----

{% highlight bash %}
iptables -A INPUT -p icmp -j ACCEPT
iptables -A INPUT -i lo -p all -j ACCEPT
iptables -A INPUT -p tcp -s 192.168.10.1 -j DROP
iptables -A OUTPUT -p tcp --sport 31337 -j DROP
iptables -A OUTPUT -p tcp --dport 31337 -j DROP
iptables -A INPUT -s 192.168.10.1 -p tcp --dport 22 -j ACCEPT
iptables -A INPUT -s 192.168.10.0/24 -p tcp --dport 22 -j ACCEPT
iptables -A INPUT -i eth0 -p tcp -s 192.168.10.0/24 --dport 22 -m state --state NEW,ESTABLESHED -j ACCEPT
iptables -A OUTPUT -o eth0 -p tcp --sport 22 -m state --state ESTABLISHED -j ACCEPT
iptables -A INPUT -i eth0 -p tcp -s 192.168.10.0/24 --dport 22 -m state --state ESTABLESHED -j ACCEPT
iptables -A OUTPUT -o eth0 -p tcp --sport 22 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -A INPUT -m state --state INVALID -j DROP
iptables -A OUTPUT -m state --state INVALID -j DROP

iptables -A FORWARD -i eth0 -o eth1 -m state --state RELATED,ESTABLISHED -j ACCEPT
iptables -A FORWARD -i eth1 -o eh0 -j ACCEPT
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
{% endhighlight %}

iptables数据包处理流程图参考
-----

1. [iptables-firewall-schema.png]({{ site.baseurl }}/img/linux/iptables/iptables-firewall-schema.png)
1. [iptables-flow-chart.png]({{ site.baseurl }}/img/linux/iptables/iptables-flow-chart.png)
1. [iptables-flow-cn.png]({{ site.baseurl }}/img/linux/iptables/iptables-flow-cn.png)
1. [iptables-packet-flow.gif]({{ site.baseurl }}/img/linux/iptables/iptables-packet-flow.gif)
1. [iptables-process-flow.png]({{ site.baseurl }}/img/linux/iptables/iptables-process-flow.png)
1. [iptables-flow-simple.png]({{ site.baseurl }}/img/linux/iptables/iptables-flow-simple.png)

References
-----

1. [鸟哥的Linux私房菜 - 防火墙与NAT伺服器](http://linux.vbird.org/linux_server/0250simple_firewall.php)
2. [iptables](https://wiki.archlinux.org/index.php/Iptables)
3. [Simple stateful firewall](https://wiki.archlinux.org/index.php/Simple_stateful_firewall)
4. [Requirements for Internet Hosts -- Communication Layers](https://tools.ietf.org/html/rfc1122#page-69)

