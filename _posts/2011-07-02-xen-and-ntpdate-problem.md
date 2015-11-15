---
layout: post
title: "xen and ntpdate"
date: "Sat Jul 02 2011 15:26:00 GMT+0800 (CST)"
categories: linux
---

ntpdate在XEN虚拟机上执行后，不能更新当前时间。

修复方法
-----

将下面的指令加入`/etc/rc.local`文件后重启机器就可以，不重启的话手动在命令行中运行此命令。

{% highlight bash %}
echo 1 > /proc/sys/xen/independent_wallclock
ntpdate ntp.ubuntu.com > /dev/null
{% endhighlight %}
