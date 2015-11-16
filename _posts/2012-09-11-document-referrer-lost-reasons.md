---
layout: post
title: "document.referrer丢失的几个原因"
date: "Tue, 11 Sep 2012 11:29:29 +0800"
categories: javascript
---

修改window.location对象进行页面导航
-----

`window.location`对象是一个用于页面导航的非常实用的对象。因为他允许你只变更url的其中一部分。

例如从cn域名切换到com域名，其他部分不变：

{% highlight javascript %}
window.location.hostname = "example.com";
{% endhighlight %}

ie丢失referrer，其他浏览器均正常返回`document.referrer`。

window.open方式打开新窗口
-----

{% highlight html %}
<a href="#" onclick="window.open('http://www.google.com')">访问Google</a>
{% endhighlight %}

ie丢失referrer，其他浏览器均正常返回`document.referrer`。

鼠标拖拽打开新窗口
-----

通过这种方式打开的页面，全都丢失referrer。

https跳转到http
-----

从https的网站跳转到http的网站时，浏览器是不会发送referrer的。

References
-----

1. [Document.Referrer丢失的几个原因](http://www.imkevinyang.com/2010/01/document-referrer%E4%B8%A2%E5%A4%B1%E7%9A%84%E5%87%A0%E4%B8%AA%E5%8E%9F%E5%9B%A0.html)
