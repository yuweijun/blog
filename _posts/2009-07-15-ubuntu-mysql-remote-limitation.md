---
layout: post
title: "ubuntu上mysql远程访问限制"
date: "Wed Jul 15 2009 11:40:00 GMT+0800 (CST)"
categories: linux
---

用nestat命令查看3306端口状态

{% highlight bash %}
$> netstat -an | grep 3306
tcp        0      0 127.0.0.1:3306          0.0.0.0:*               LISTEN
{% endhighlight %}

从结果可以看出3306端口只是在IP`127.0.0.1`上监听，所以拒绝了其他IP的访问。

解决方法
-----

修改`/etc/mysql/my.cnf`文件，打开文件，找到下面内容：

{% highlight text %}
# Instead of skip-networking the default is now to listen only on
# localhost which is more compatible and is not less secure.
bind-address  = 127.0.0.1
{% endhighlight %}

把上面这一行注释掉，重新启动后，重新使用netstat检测：

{% highlight bash %}
$> netstat -an | grep 3306
tcp        0      0 0.0.0.0:3306            0.0.0.0:*               LISTEN
{% endhighlight %}

此时再从远程机器就可以telnet通3306端口了。

References
-----

1. [http://blog.csdn.net/mydeman/archive/2009/01/21/3847695.aspx](http://blog.csdn.net/mydeman/archive/2009/01/21/3847695.aspx)
