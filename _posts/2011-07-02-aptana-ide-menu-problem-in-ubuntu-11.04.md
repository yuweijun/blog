---
layout: post
title: "aptana ide menu problem in ubuntu-11.04"
date: "Sat Jul 02 2011 15:08:00 GMT+0800 (CST)"
categories: linux
---

ubuntu-11.04下显示aptana菜单有问题，创建一个aptana快捷键来避过这个问题。

{% highlight bash %}
#!/bin/bash
export UBUNTU_MENUPROXY=0
/path/to/AptanaStudio3
{% endhighlight %}
