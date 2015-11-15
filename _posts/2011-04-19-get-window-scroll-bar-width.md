---
layout: post
title: "get window scroll bar width"
date: "Tue Apr 19 2011 14:28:00 GMT+0800 (CST)"
categories: javascript
---

浏览器因为操作系统或者系统主题不同，导致当前窗口中的scrollbar的宽度不一致，在web应用中影响了页面布局，下述方法可以获取到当前浏览器的滚动条宽度：

{% highlight javascript %}
function getScrollBarWidth() {
    var inner = document.createElement('p');
    inner.style.width = "100%";
    inner.style.height = "200px";

    var outer = document.createElement('div');
    outer.style.position = "absolute";
    outer.style.top = "0px";
    outer.style.left = "0px";
    outer.style.visibility = "hidden";
    outer.style.width = "200px";
    outer.style.height = "150px";
    outer.style.overflow = "hidden";
    outer.appendChild(inner);

    document.body.appendChild(outer);
    var w1 = inner.offsetWidth;
    outer.style.overflow = 'scroll';
    var w2 = inner.offsetWidth;
    if (w1 == w2) w2 = outer.clientWidth;

    document.body.removeChild(outer);

    return (w1 - w2);
};
{% endhighlight %}

References
-----

1. [http://www.alexandre-gomes.com/?p=115 ](http://www.alexandre-gomes.com/?p=115 )
2. [http://www.softcomplex.com/docs/get_window_size_and_scrollbar_position.html](http://www.softcomplex.com/docs/get_window_size_and_scrollbar_position.html)
