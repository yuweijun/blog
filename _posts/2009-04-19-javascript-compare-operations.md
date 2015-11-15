---
layout: post
title: "javascript compare operations"
date: "Sun Apr 19 2009 23:48:00 GMT+0800 (CST)"
categories: javascript
---

javascript
-----

{% highlight javascript %}
'' == '0'; // false
0 == ''; // true
0 == '0'; // true
false == 'false'; // false
false == '0'; // true
false == undefined; // false
false == null; // false
null == undefined; // true
' \t\r\n ' == 0; // true
{% endhighlight %}
