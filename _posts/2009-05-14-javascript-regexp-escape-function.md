---
layout: post
title: "regexp.escape function of javascript"
date: "Thu May 14 2009 14:57:00 GMT+0800 (CST)"
categories: javascript
---

RegExp.escape() function example
-----

{% highlight javascript %}
RegExp.escape = (function() {
    var punctuationChars = /([.*+?|/(){}[\]\\])/g;
    return function(text) {
        return text.replace(punctuationChars, '\\$1');
    }
})();

var str = RegExp.escape('a+b/c*d$ ^{.}');
var reg = new RegExp(str);
{% endhighlight %}

References
-----

1. [http://simonwillison.net/2006/Jan/20/escape](http://simonwillison.net/2006/Jan/20/escape)
