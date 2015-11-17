---
layout: post
title: "ie6 a:hover bug"
date: "Tue Nov 17 2015 17:04:40 GMT+0800 (CST)"
categories: web
---

ie6下a标签href="#"时a:hover失效
-----

{% highlight html %}
<style>
.entry-content a:link, .entry-content a:visited {
    color: #57A3E8;
    text-decoration: none;
}
.entry-content a:hover {
    color: #F90;
    text-decoration: underline;
}
</style>

<a href="#">这问题的解决方案只需要给a标签加个链接就可以解决这个bug或者是写成href值给个空格也能避免:hover失效问题</a>
{% endhighlight %}

在ie6下"颜色"根本就不会变成红色，其他浏览器都是好的，要解决这个问题就必须触发`a:hover`的`hasLayout`，例如`a:hover{display:inline-block}`或者`a:hover{zoom:1}`。

References
-----

1. [http://www.css88.com/archives/1335](http://www.css88.com/archives/1335)
2. [http://kayosite.com/ie6-hover-bug.html](http://kayosite.com/ie6-hover-bug.html)
