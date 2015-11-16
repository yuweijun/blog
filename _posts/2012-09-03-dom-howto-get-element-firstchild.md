---
layout: post
title: "如何获取元素下的第一个子元素"
date: "Mon, 03 Sep 2012 15:11:24 +0800"
categories: javascript
---

element.firstChild
-----

这个方法可能取到的应该是`TEXT_NODE`。

{% highlight javascript %}
var util = {};

util.first = function(element) {
    if (!element) return;

    var first = element.firstChild;
    // 处理 w3c 浏览器中第一个子元素是 TEXT_NODE
    // 并且需要考虑到有没有 COMMENT_NODE 的情况
    while (first && first.nodeType !== 1) first = first.nextSibling;
    return first;
}
{% endhighlight %}

element.firstElementChild
-----

{% highlight javascript %}
util.first = function(element) {
    if(!element) return;

    // 刚好 IE8 以下支持直接拿 firstChild
    return element[element.firstElementChild ? "firstElementChild" : "firstChild"];
}
{% endhighlight %}

element.querySelector and element.getElementsByTagName
-----

{% highlight javascript %}
// 通过 HTML5 的 querySelector，及 getElementsByTagName
util.first = function(element, tag) {
    if(!element) return;
    tag = tag || "*";;
    return element.querySelector ? element.querySelector(tag) : element.getElementsByTagName(tag)[0];
}
{% endhighlight %}

{% highlight javascript %}
// IE6 支持的 children
util.first = function(element) {
    return element && element.children[0];
}
{% endhighlight %}

References
-----

1. [如何取到ul下第一下li](https://gist.github.com/sofish/3549460)
