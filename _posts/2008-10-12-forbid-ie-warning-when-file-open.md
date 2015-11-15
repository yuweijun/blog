---
layout: post
title: "如何禁止网页打开时ie弹出的安全警告"
date: "Sun Oct 12 2008 11:39:00 GMT+0800 (CST)"
categories: javascript
---

当直接双击打开一个有javascript代码的网页，ie6/ie7会在窗口顶部出现一条黄色警告信息，内容如下所示：

{% highlight text %}
为了有利于保护安全性，IE已限制此网页运行可以访问计算机的脚本或 ActiveX 控件。请单击这里获取选项...
{% endhighlight %}

要取消此信息，只要在网页源码顶部的`DOCTYPE`声明下面加一行代码，即可禁止此警告条出现：

{% highlight html %}
<!-- saved from url=(0022) -->
{% endhighlight %}

这句话的作用是让Internet Explorer 使用 Internet 区域的安全设置，而不是本地计算机区域的设置。

References
-----

1. [http://zhidao.baidu.com/question/33183687.html?fr=qrl&fr2=query&adt=0_88](http://zhidao.baidu.com/question/33183687.html?fr=qrl&fr2=query&adt=0_88)
1. [http://zhidao.baidu.com/question/3693592.html?fr=qrl](http://zhidao.baidu.com/question/3693592.html?fr=qrl)
