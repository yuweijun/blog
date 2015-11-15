---
layout: post
title: "img is an inline element"
date: "Wed Aug 27 2008 00:13:00 GMT+0800 (CST)"
categories: css
---

img标签是一个内联元素。

css
-----

{% highlight css %}
#div1, #div2 {
    border: #333 1px solid;
    background-color: #333;
    height: 300px;
}
#img1 {
    display: block;
}
#img2 {
    vertical-align: sub; /* bottom, middle */
}
{% endhighlight %}

html
-----

{% highlight html %}
<div id="div1"><img id="img1" src="/images/puzzle1.jpg"/></div>
 如果img对象不设置一下display或者vertical-align等属性，会在div底部多出一点空白，
 这个就是因为img是一个inline element。
<div id="div2"><img id="img2" src="/images/puzzle2.jpg"/></div>
{% endhighlight %}
