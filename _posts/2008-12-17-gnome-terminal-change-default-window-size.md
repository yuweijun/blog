---
layout: post
title: "修改gnome-terminal的默认窗口大小"
date: "Wed Dec 17 2008 14:12:00 GMT+0800 (CST)"
categories: linux
---

原来打开时为80*24的尺寸，如果需要调整默认的大小，可按下操作：

{% highlight bash %}
$> sudo vi /usr/share/vte/termcap/xterm
{% endhighlight %}

找到下面这行

{% highlight bash %}
:co#80:it#8:li#24\
{% endhighlight %}

修改其中的80和24，分别为terminal的高和宽。也可以用以下命令查看参数：

{% highlight bash %}
$> gnome-terminal --help-all
{% endhighlight %}
