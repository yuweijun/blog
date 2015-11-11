---
layout: post
title: "javascript number tostring方法使用"
date: "Tue Jun 10 2008 13:28:00 GMT+0800 (CST)"
categories: javascript
---

说明
-----

{% highlight tex %}
Number.toString(radix)
Converts a number to a string, using a specified radix (base), and returns the string. radix must be between 2 and 36. If omitted, base 10 is used.
{% endhighlight %}

比如中文`一`字的unicode编码为`\u4e00`,要想得到对应的unicode十进制编码,在js中不必用`4 * 16 * 16 * 16 + 14 * 16 + 0 + 0 = 19968`来获得，可以直接用`Number.toString()`方法得到:

使用
-----

{% highlight javascript %}
(0x4e00).toString(10) = "19968"
{% endhighlight %}

这样得到的值可以再用js加上前后缀成为`"&#" + "19968" + ";" = "一"`后可以直接从网页中输出。

反过来，如果知道十进制的一个数字，也可以很容易转换成16进制的值:

{% highlight javascript %}
(19968).toString(16) = "4e00"
{% endhighlight %}
