---
layout: post
title: "linux命令查看当前网速"
date: "Tue, 12 Jun 2012 11:01:54 +0800"
categories: linux
---

ubuntu
-----

{% highlight bash %}
$> sudo apt-get install nload
$> nload -u H
{% endhighlight %}

centos
-----

{% highlight bash %}
$> sar -n DEV 1 100
{% endhighlight %}

其他未测试的命令
-----

1. iptraf
2. sniffer
3. iftop

流量查看
-----

{% highlight bash %}
$> watch -n 1 "/sbin/ifconfig eth0 | grep bytes"
{% endhighlight %}
