---
layout: post
title: "javascript string.replace for html tag"
date: "Wed Aug 13 2008 23:06:00 GMT+0800 (CST)"
categories: javascript
---

适用于所有浏览器，从script.aculo.us作者Thomas Fuchs一个ppt中看到此代码。

{% highlight javascript %}
var elem = "<div/><br/><p/>";
elem = elem.replace(/(<(\w+)[^>]*?)\/>/g, function(all, front, tag){ return tag.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i) ? all : front + "></" + tag + ">"; });
{% endhighlight %}
