---
layout: post
title: "extending javascript function prototype object"
date: "Sun Oct 05 2008 20:31:00 GMT+0800 (CST)"
categories: javascript
---

javascript prototype extending
-----

{% highlight javascript %}
Function.prototype.twice = function() {
    var fn = this;
    return function() {
        return fn.call(null, fn.apply(null, arguments));
    };
};

Function.prototype.twice2 = function() {
    var fn = this;
    return function() {
        console.log(this); // Window Object
        return fn.call(this, fn.apply(this, arguments));
    };
};

function plus1(x) {
    return x + 1;
}
var plus2 = plus1.twice();
var plus3 = plus1.twice2();
console.log(plus2(10)); // 12
console.log(plus3(10)); // 12
{% endhighlight %}

References
-----

1. [http://osteele.com/talks/ajaxian-2008/samples/idioms-9.js.html](http://osteele.com/talks/ajaxian-2008/samples/idioms-9.js.html)
