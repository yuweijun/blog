---
layout: post
title: "classical inheritance in javascript"
date: "Fri Aug 10 2007 02:00:00 GMT+0800 (CST)"
categories: javascript
---

[Douglas Crockford](http://www.crockford.com/javascript/inheritance.html)用javascript模拟实现java的类继承。

{% highlight javascript %}
Function.prototype.method = function(name, func) {
    this.prototype[name] = func;
    return this;
};
Function.method('inherits', function(parent) {
    var d = {}, p = (this.prototype = new parent());
    this.method('uber', function uber(name) {
        if (!(name in d)) {
            d[name] = 0;
        }
        var f, r, t = d[name],
            v = parent.prototype;
        if (t) {
            while (t) {
                v = v.constructor.prototype;
                t -= 1;
            }
            f = v[name];
        } else {
            f = p[name];
            if (f == this[name]) {
                f = v[name];
            }
        }
        d[name] += 1;
        r = f.apply(this, Array.prototype.slice.apply(arguments, [1]));
        d[name] -= 1;
        return r;
    });
    return this;
});
Function.method('swiss', function(parent) {
    for (var i = 1; i < arguments.length; i += 1) {
        var name = arguments[i];
        this.prototype[name] = parent.prototype[name];
    }
    return this;
});
{% endhighlight %}

{% highlight javascript %}
function Parenizor(value) {
    this.setValue(value);
}

Parenizor.method('setValue', function(value) {
    this.value = value;
    return this;
});

Parenizor.method('getValue', function() {
    return this.value;
});

Parenizor.method('toString', function() {
    return '(' + this.getValue() + ')';
});

var myParenizor = new Parenizor(0);
var myString = myParenizor.toString();
console.log(myParenizor.value);
console.log(myString);

function ZParenizor(value) {
    this.setValue(value);
}

ZParenizor.inherits(Parenizor);

ZParenizor.method('toString', function() {
    if (this.getValue()) {
        return this.uber('toString');
    }
    return "-0-";
});
{% endhighlight %}

There is another way to write ZParenizor. Instead of inheriting from Parenizor, we write a constructor that calls the Parenizor constructor, passing off the result as its own. And instead of adding public methods, the constructor adds privileged methods.

{% highlight javascript %}
function ZParenizor2(value) {
     var that = new Parenizor(value);
     that.toString = function () {
         if (this.getValue()) {
             return this.uber('toString');
         }
         return "-0-"
     };
     return that;
}

var myZParenizor = new ZParenizor(0);
var myString = myZParenizor.toString();
console.log(myZParenizor.value);
console.log(myString);
{% endhighlight %}
