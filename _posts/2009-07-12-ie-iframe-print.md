---
layout: post
title: "打印ie中iframe内容"
date: "Sun Jul 12 2009 01:58:00 GMT+0800 (CST)"
categories: web
---

需要将被打印的iframe先`focus()`，不然打印仍然是最外面的窗体内容。如：

{% highlight javascript %}
window.frames[0].focus();
window.frames[0].print();
{% endhighlight %}
