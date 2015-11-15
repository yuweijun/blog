---
layout: post
title: "when iframe src is too long in ie6/ie7"
date: "Mon Nov 03 2008 14:11:00 GMT+0800 (CST)"
categories: web
---

在ie中，如果页面内的iframe src的url长度过长(`>2083`)，ie会请求失败。

微软官方文档说明如下
----

{% highlight text %}
Microsoft Internet Explorer 具有最大统一资源定位符 (URL) 长度为 2,083 个字符。 Internet Explorer 也有最多 2,048 个字符的最大路径长度。 此限制适用于同时 POST 请求和 GET 请求 URL。
{% endhighlight %}

如果要使用`get`方法，您被限制为最多个最多`2,048`个字符，减去的实际路径中的字符数。

但是，`post`方法不受到用于提交`名称/值`对该`url`的大小。 这些对被传输在标头而不是在该`url`。

`rfc 2616`，超文本传输协议——`HTTP/1.1`未指定`url`长度的任何要求。

References
-----

1. [http://support.microsoft.com/kb/208427/zh-cn](http://support.microsoft.com/kb/208427/zh-cn)
