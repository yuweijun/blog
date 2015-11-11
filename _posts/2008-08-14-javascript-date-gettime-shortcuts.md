---
layout: post
title: "javascript gettime() shortcuts"
date: "Thu Aug 14 2008 17:10:00 GMT+0800 (CST)"
categories: javascript
---

new Date().getTime()的简写
-----

{% highlight javascript %}
var time1 = (new Date()).getTime(), time2 = +new Date(), time3 = new Date() * 1;
// 1218645767082
console.log(time1);
// 1218645767082
console.log(time2);
// 1218645767082
console.log(time3);
// 1218645767082
{% endhighlight %}
