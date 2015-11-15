---
layout: post
title: "ie6中select组件与css中z-index的冲突"
date: "Tue Apr 20 2010 14:13:00 GMT+0800 (CST)"
categories: web
---

在[Positioned Elements and OS Controls, Applets and Plug-ins](http://www.webreference.com/dhtml/diner/seethru/)这篇文章中，描述了当`position`为`absolute`一个div被拖到select组件上方的时候，在ie6中，select组件会透过div层显示出来，这并不是想要的效果，这个问题并非是一个bug，因为在ie6中，select元素是一个window控制组件，并不支持`z-index`属性，在ie7之后则开始支持`z-index`属性，更详细的说明可查看[msdn](http://msdn.microsoft.com/en-us/library/ms535893.aspx)关于select的文档：

{% highlight text %}
From Microsoft Internet Explorer 5 to Internet Explorer 6, This element is a windowed control and does not support the z-index attribute or zIndex property.

As of Internet Explorer 7, this element is windowless and supports the z-index attribute and the zIndex property. The SELECT element does not require a strict doctype to enable windowless functionality.

This element is an inline element.

This element requires a closing tag.
{% endhighlight %}
