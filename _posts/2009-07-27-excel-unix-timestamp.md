---
layout: post
title: "在excel中转换unix timestamp"
date: "Mon Jul 27 2009 15:22:00 GMT+0800 (CST)"
categories: linux
---

在excel中没有直接的函数可以将一个unix时间截，转换为一个可阅读的时间格式，
这个公式可以用来转换显示时间，转换出来的还是一个数字，需要调整其显示格式，选择一个日期格式。

如单元格`A1`的值为`1248255194`，那么`B1`可以使用此公式转换出普通的日期格式。

{% highlight text %}
=(A1+8*3600)/86400+70*365+19
{% endhighlight %}

