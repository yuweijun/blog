---
layout: post
title: "0.1 + 0.2 !== 0.3 in javascript"
date: "Sun Apr 19 2009 23:54:00 GMT+0800 (CST)"
categories: javascript
---

javascript浮点计算精度问题
-----

{% highlight javascript %}
console.log(0.1 + 0.2 !== 0.3);
// true
console.log(0.1 + 0.2);
// 0.30000000000000004
{% endhighlight %}
