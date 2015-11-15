---
layout: post
title: "get cookie value"
date: "Sat Aug 23 2008 18:28:00 GMT+0800 (CST)"
categories: javascript
---

javascript
-----

{% highlight javascript %}
function getCookie(cookie) {
    var reg = new RegExp("(?:^|\\s+)" + cookie + "=(.+?)(?:;|$)", "i");
    var match = document.cookie.match(reg);
    // if (match) return RegExp.$1;
    if (match) return match[1];
}
{% endhighlight %}
