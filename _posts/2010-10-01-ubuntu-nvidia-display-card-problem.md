---
layout: post
title: "ubuntu-10.04下nvidia显卡不定时闪屏的解决"
date: "Fri Oct 01 2010 19:01:00 GMT+0800 (CST)"
categories: linux
---

解决方法
-----

在配置文件中添加一行:

{% highlight bash %}
$> sudo vi /etc/modprobe.d/nvidia-kernel-nkc
options nvidia NVreg_Mobile=1 NVreg_RegistryDwords="PerfLevelSrc=0x2222"
{% endhighlight %}

更多信息可查看下面链接中ubuntu的bug list。

References
-----

1. [解决Nvidia显卡不定时闪屏问题](http://forum.ubuntu.org.cn/viewtopic.php?f=42&t=152433&start=0)
2. [Occasional screen-wide "blink" when using opengl apps (compiz also) and Nvidia cards](https://bugs.launchpad.net/ubuntu/+source/nvidia-kernel-common/+bug/164589)
