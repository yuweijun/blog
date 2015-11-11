---
layout: post
title: "test scope of this in closure and in anonymous functions"
date: "Wed May 07 2008 14:23:00 GMT+0800 (CST)"
categories: javascript
---

在`window.setTimeout`和全局环境中的匿名方法中的`this`是指向`window`对象的,所以在调用过程中可以将此匿名方法做为实际的某个对象(如`this`对象)的方法来调用.

{% highlight javascript %}
var foo = 'this is window.foo!';
var d = [1, 2, 3];
// timeout functions

function Constructor() {
    this.foo = 'this is Constructor.foo!';
    var that = this;
    this.timerId = window.setTimeout(function() {
        // alert(this); // will get [object Window]
        alert("this.foo = " + this.foo);
        alert("that.foo = " + that.foo);
    }, 1000);
}

// local functions
Constructor.prototype.getFoo = function() {
    alert(this); // [object Object]
    var getExternalFoo = (function() {
        // alert(this); // will get [object Window]
        return d.concat(this.foo)
    })();
    return getExternalFoo;
};

// using Function.call(object)
Constructor.prototype.getBar = function() {
    var getInternalFoo = (function() {
        // alert(this); // will get [object Object]
        return d.concat(this.foo)
    }).call(this);
    return getInternalFoo;
};
var f = new Constructor();
console.log(f.getFoo());
console.log("<br />");
console.log(f.getBar());
{% endhighlight %}

