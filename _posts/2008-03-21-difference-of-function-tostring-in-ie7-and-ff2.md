---
layout: post
title: "difference of function tostring() in ie7 and ff2"
date: "Fri Mar 21 2008 16:50:00 GMT+0800 (CST)"
categories: javascript
---

IE7:
-----

{% highlight javascript %}
alert(/x/.test(function(){'x';}))
// => true
(function(){'x';}).toString();
// => "(function(){'x';})"
{% endhighlight %}

FF2:
-----

{% highlight javascript %}
alert(/x/.test(function(){'x';}))
// => false
(function(){'x';}).toString();
// => "function () { }"
{% endhighlight %}
