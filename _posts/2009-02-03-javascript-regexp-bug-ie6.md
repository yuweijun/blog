---
layout: post
title: "javascript expression '\\s'=='s' is true in ie6"
date: "Tue Feb 03 2009 19:51:00 GMT+0800 (CST)"
categories: javascript
---

在ie6中，以下表达式返回true，这是不正确的，需要多加注意。

{% highlight javascript %}
var exp = '\s' == 's';
// exp = true
{% endhighlight %}
