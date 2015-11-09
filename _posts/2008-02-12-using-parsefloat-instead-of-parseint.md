---
layout: post
title: "using parsefloat instead of parseint"
date: "Tue Feb 12 2008 21:27:00 GMT+0800 (CST)"
categories: javascript
---

parseInt() 在解析字符串为数字的时候，有时候会有点理解问题，如

{% highlight javascript %}
var value = parseInt("010");
{% endhighlight %}

因为字符串以"0"开头可能会被误解为8进制解析，一般正确的写法是`parseInt('08', 10)`这样的。

一般解析字符串为数字时可以用`parseFloat()`这个全局函数或者是`+`运算符。

{% highlight javascript %}
['2008', '02', '11', '06', '21', '03'].map(function(v) {
    return + v;
});
# [2008, 2, 11, 6, 21, 3]
{% endhighlight %}
