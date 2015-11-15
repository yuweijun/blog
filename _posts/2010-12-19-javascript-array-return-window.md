---
layout: post
title: "javascript中array方法调用返回window对象"
date: "Sun Dec 19 2010 15:06:00 GMT+0800 (CST)"
categories: javascript
---

array中的很多方法通过`call`和`apply`调用时会返回`window`对象，如下写法在firefox、chrome等浏览器中会取到window对象:

{% highlight javascript %}
window === ([]).sort.call();
window === ([]).reverse.call();
([]).concat.call()[0] === window
{% endhighlight %}

可以将这些array的方法重写，避免它在运行时的this指向window，如重写sort方法：

{% highlight javascript %}
Array.prototype.sort = (function(sort) {
    return function(callback) {
        return (this == window) ? null : (callback ? sort.call(this, function(a, b) {
            return callback(a, b)
        }) : sort.call(this));
    }
})(Array.prototype.sort);
{% endhighlight %}
