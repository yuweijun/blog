---
layout: post
title: "利用 ruby 进行 udp 通信"
date: "Tue Dec 25 2007 10:06:00 GMT+0800 (CST)"
categories: ruby
---

收信端
-----

{% highlight ruby %}
require 'socket'
u1 = UDPSocket.open()
u1.bind("0.0.0.0", 10000)
p u1.recvfrom(65536)
{% endhighlight %}

发信端
----

{% highlight ruby %}
require 'socket'
u2 = UDPSocket.new()
u2.connect('localhost', 10000)
u2.send('Hello world!' , 0)
{% endhighlight %}

接收结果
-----

{% highlight tex %}
["Hello world!", ["AF_INET", 32818, "localhost", "127.0.0.1"]]
{% endhighlight %}

原文: [http://d.hatena.ne.jp/emergent/20071225/1198510691](http://d.hatena.ne.jp/emergent/20071225/1198510691)
