---
layout: post
title: "scope of javascript anonymous function"
date: "Fri Aug 08 2008 00:29:00 GMT+0800 (CST)"
categories: javascript
---

匿名函数中的作用对象是全局的`window`对象，一般是不需要注意这点，但当在匿名函数中使用使用`this`就要小心，这个`this`是指向`window`的，如上所示可以用`apply`或者`call`来指定匿名函数作用于哪个对象上，但匿名函数如果在`setTimeout/setInterval`中使用的话则需要将`this`对象用别名如`_self/self/_this/that`替代后在匿名方法中使用，如下面二个参考文章所示。

{% highlight javascript %}
var foo = "test in global window";

function Constructor() {
    this.foo = 'test in Constructor';
    this.local = (function() {
        alert(this.foo);
        return "local";
    }).apply(this);
    this.globals = (function() {
        alert(this.foo);
        return "global";
    })();
}
new Constructor();
{% endhighlight %}

注意
-----

In javascript, scope of anonymous functions is global.

References
-----

1. [http://yuweijun.blogspot.com/2008/05/test-scope-of-this-in-closure-and-in.html](http://yuweijun.blogspot.com/2008/05/test-scope-of-this-in-closure-and-in.html)
1. [http://www.dustindiaz.com/scoping-anonymous-functions/](http://www.dustindiaz.com/scoping-anonymous-functions/)
