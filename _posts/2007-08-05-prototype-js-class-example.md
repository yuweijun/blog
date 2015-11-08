---
layout: post
title: "prototype.js Class.create example"
date: "Sun Aug 05 2007 14:31:00 GMT+0800 (CST)"
categories: javascript
---

prototype.js中关于Class部分的源码学习。

{% highlight javascript %}
var Class = {
    create: function() {
        return function() {
            this.initialize.apply(this, arguments);
        }
    }
}
{% endhighlight %}

example
-----

{% highlight javascript %}
var MyClass = Class.create();
MyClass.prototype = {
    initialize: function(msg) {
        this.msg = msg;
        return msg;
    },

    showMsg: function() {
        console.log(this.msg);
    }
}

var c = new MyClass('test'); // Class.create('test')
c.showMsg();

var o = {
    name: 'test it',
    sex: 'man',
    info: function() {
        return this.name + " and " + this.sex;
    },
    ivk: function() {
        return this.info.apply(this); // invoke this.info function
    }
}

console.log(o.info());
console.log(o.ivk());
{% endhighlight %}
