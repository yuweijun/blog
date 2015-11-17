---
layout: post
title: "ie6下div高度显示的bug"
date: "Fri, 09 Sep 2011 14:56:41 +0800"
categories: web
---

ie6下默认的字体尺寸大致在`12px`和`14px`之间，当你试图定义一个高度小于这个默认值的div的时候，ie会固执的认为这个层的高度不应该小于字体的行高。所以即使你用 `height: 6px;`来定义了一个div的高度，实际在ie下显示的仍然是一个`12px`左右高度的层。

要解决这个问题，可以强制定义该div的字体尺寸，或者定义overflow属性来限制div高度的自动调整。比如

{% highlight html %}
<div style="height: 6px; font: 0px arial;"></div>
或者
<div style="height: 6px; overflow: hidden;"></div>
{% endhighlight %}

该问题可能与ie6的haslayout有关系，在ie7/firefox/opera下均不存在。
