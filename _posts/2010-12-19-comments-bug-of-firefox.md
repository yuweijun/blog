---
layout: post
title: "comments including two dashes in a row render bug on firefox3"
date: "Sun Dec 19 2010 15:08:00 GMT+0800 (CST)"
categories: web
---

在一段注释代码中，如果出现了`--`这样二个连续的连字符，出导致firefox3解析出错。

{% highlight html %}
<body>
<!--

这段HTML注释代码因为中间多了2个连字符，在firefox中会导致解析出错，这段内容被做为正常的文本内容显示在浏览器中。
This entire comment -- will show in web browser

-->
</body>
{% endhighlight %}

References
-----

1. [(SGMLComment) Mozilla interprets a -- (two dashes in a row) inside of a comment or an include improperly](https://bugzilla.mozilla.org/show_bug.cgi?id=214476)
