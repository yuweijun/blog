---
layout: post
title: "prototype.js object.extend example"
date: "Sun Aug 05 2007 15:25:00 GMT+0800 (CST)"
categories: javascript
---

prototype.js中`Object.extend`源码学习。

{% highlight javascript %}
Object.extend = function(destination, source) {
    for (var property in source) {
        destination[property] = source[property];
    }
    return destination;
}
{% endhighlight %}

example
-----

{% highlight javascript %}
var o = {
    test: 'test it',
    sex: 'man',
    info: function() {
        return this.test + " and " + this.sex;
    },
    ivk: function() {
        return this.info.apply(this); // invoke this.info function
    }
}

for (var p in Object) {
    console.log('Object["' + p + '"] = ' + Object[p]);
}

Object.extend(Object, o);

for (var p in Object) {
    console.log('Object["' + p + '"] = ' + Object[p]);
}

console.log(Object.ivk());
{% endhighlight %}
