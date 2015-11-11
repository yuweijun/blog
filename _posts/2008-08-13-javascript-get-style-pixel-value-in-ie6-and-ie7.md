---
layout: post
title: "javascript get style pixel value in ie6 and ie7"
date: "Wed Aug 13 2008 23:15:00 GMT+0800 (CST)"
categories: javascript
---

Computed vs Cascaded Style

{% highlight javascript %}
var PIXEL = /^\d+(px)?$/i;

function getPixelValue(element, value) {
    if (PIXEL.test(value)) return parseInt(value);
    var style = element.style.left;
    var runtimeStyle = element.runtimeStyle.left;
    element.runtimeStyle.left = element.currentStyle.left;
    element.style.left = value || 0;
    value = element.style.pixelLeft;
    element.style.left = style;
    element.runtimeStyle.left = runtimeStyle;
    return value;
};
{% endhighlight %}

References

1. [Computed vs Cascaded Style](http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291)
