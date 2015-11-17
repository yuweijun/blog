---
layout: post
title: "ie6下3px文本偏移bug"
date: "Tue Nov 17 2015 17:05:15 GMT+0800 (CST)"
categories: web
---

bug重现-haslayout引起
-----

{% highlight css %}
.float-left-div {
    float: left;
    width: 200px;
}
.margin-div {
    margin-left: 200px;
}
{% endhighlight %}

如下图所示，在文本和浮动元素之间就会出现一个莫名其妙的3像素间隙。

![ie6-text-3px-bug](/img/web/ie6-3px-bug.png)

bug fix
-----

{% highlight css %}
.float-left-div {
    _margin-right: -3px;
}
.margin-div {
    _height: 1%;
    _margin-left: 0;
}
{% endhighlight %}

另外一个解决方法是`float`元素同时加上`_display:inline`。

References
-----

1. [深入理解 IE haslayout](http://riny.net/2013/haslayout/)
1. [拥有布局 IE haslayout](http://adamghost.com/2009/03/ie-has-layout-and-bugs-zh/)
1. [On having layout](http://www.satzansatz.de/cssd/onhavinglayoutrev07-20060517.html)
1. [hasLayout && Block Formatting Contexts](http://www.smallni.com/haslayout-block-formatting-contexts/)
1. [一个display:none引起的3像素的bug](http://www.css88.com/archives/1797)
1. [9 Most Common IE Bugs and How to Fix Them](http://code.tutsplus.com/tutorials/9-most-common-ie-bugs-and-how-to-fix-them--net-7764)
