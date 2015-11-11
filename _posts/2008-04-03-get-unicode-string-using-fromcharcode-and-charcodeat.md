---
layout: post
title: "get unicode string using fromcharcode and charcodeat in javascript"
date: "Thu Apr 03 2008 18:46:00 GMT+0800 (CST)"
categories: javascript
---

字符串和unicode编码、html character entity码之间的转换方式
-----

{% highlight javascript %}
// &#20013; => 中
console.log("&#20013;");
console.log("\u4e2d");
console.log("中国".charCodeAt(0));
// utf-8: 20013, GBK: 28051
console.log("中国".charCodeAt(0).toString(16));
// "4e2d"
console.log("&#" + "中国".charCodeAt(0) + ";");
// &#20013;
console.log(String.fromCharCode("&#20013;".replace(/[&#;]/g,"")));
// 中
console.log(escape("中"));
// "%u4E2D"
{% endhighlight %}

