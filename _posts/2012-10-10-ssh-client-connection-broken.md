---
layout: post
title: "ssh不操作自动掉线问题"
date: "Wed, 10 Oct 2012 16:50:05 +0800"
categories: linux
---

在服务器端`/etc/ssh/sshd_config`文件里添加以下3行配置:

{% highlight bash %}
TCPKeepAlive yes
ClientAliveInterval 15
ClientAliveCountMax 45
{% endhighlight %}

重启sshd服务
-----

{% highlight bash %}
$> /etc/init.d/sshd restart
{% endhighlight %}

如果仍有问题，修改客户端`~/.ssh/config`配置文件，添加如下一行配置：

{% highlight bash %}
ServerAliveInterval 60
{% endhighlight %}

说明
-----

1. `ClientAliveInterval`：设置一个以秒记的时长，如果超过这么长时间没有收到客户端的任何数据，`sshd`将通过安全通道向客户端发送一个`alive`消息，并等候应答。默认值`0`表示不发送`alive`消息。这个选项仅对SSH-2有效。
2. `ClientAliveCountMax`：sshd在未收到任何客户端回应前最多允许发送多少个`alive`消息。默认值是`3`。
到达这个上限后，sshd将强制断开连接、关闭会话。
3. 需要注意的是，`alive`消息与`TCPKeepAlive`有很大差异。
4. `alive`消息是通过加密连接发送的，因此不会被欺骗；而`TCPKeepAlive`却是可以被欺骗的。
5.  如果`ClientAliveInterval`被设为`15`并且将`ClientAliveCountMax`保持为默认值，那么无应答的客户端大约会在`45秒`后被强制断开。这个指令仅可以用于SSH-2协议.

References
-----

1. [http://blog.tarotoast.com/2008/04/20/433/](http://blog.tarotoast.com/2008/04/20/433/)

