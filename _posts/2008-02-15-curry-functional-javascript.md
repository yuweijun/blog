---
layout: post
title: "curry - functional javascript"
date: "Fri Feb 15 2008 11:37:00 GMT+0800 (CST)"
categories: javascript
---

curry function test

{% highlight javascript %}
Function.prototype.curry = function() {
    var fn = this, args = Array.prototype.slice.call(arguments);
    // alert(fn);
    // function slice() { native code }
    // alert(args);
    // 2
    return function() {
        // alert(this); // ['t', 'e', 's', 't']
        // alert(args.concat(Array.prototype.slice.call(arguments)));
        return fn.apply(this, args.concat(Array.prototype.slice.call(arguments)));
    };
};

var arr = 'test'.split('');
Array.prototype.yaslice = Array.prototype.slice.curry(2);
alert(arr.yaslice(3));
// arr.slice(2,3) => 's'
{% endhighlight %}

References:

1. [http://ejohn.org/blog/partial-functions-in-javascript/](http://ejohn.org/blog/partial-functions-in-javascript/)
2. [http://osteele.com/sources/javascript/functional/](http://osteele.com/sources/javascript/functional/)
