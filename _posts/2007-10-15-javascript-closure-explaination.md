---
layout: post
title: "javascript中的闭包closures简单说明"
date: "Mon Oct 15 2007 15:56:00 GMT+0800 (CST)"
categories: javascript
---

javascript函数是将要执行的代码以及执行这些代码的作用域和作用域的arguments一起构成的一个综合体，即使函数包含相同的javascript代码，并且每段代码都是从相同的作用域调用的，还是可以返回不同的结果的。

因为javascript中的函数是在当时定义它们的作用域里运行的，而不是在执行它们的作用域里运行的。

这种代码和作用域的综合体叫`闭包`。所有的javascript函数都是`闭包`。

{% highlight javascript %}
uniqueID = (function() {
    // The call object of this function holds our value
    var id = 0;
    // This is the private persistent value
    // The outer function returns a nested function that has access
    // to the persistent value.  It is this nested function we're storing
    // in the variable uniqueID above.
    return function() { return id++; };
    // Return and increment
})();
// Invoke the outer function after defining it, and return a function: function() { return id++; }

console.log(uniqueID());
// alert(function() { return id++; }());
console.log(uniqueID());
console.log(uniqueID());
{% endhighlight %}


当一个嵌套函数被导出到它所定义的作用域外时，这种`闭包`才有意思。当一个嵌套的函数以这种方式使用时，通常被明确的叫做一个`闭包`。

uniqueID的函数体为`function() { return id++; }`，它是从一个function literal中返回得到，并包含了导出后的作用域，包含了变量名和值等，也就是从这个匿名函数是返回了一个`闭包`。

在uniqueID被`函数运算符()`调用时，已经在函数定义的作用域外，所有调用操作会影响`闭包`内的变量并仍会被此`闭包`继续保存。

Ruby和Perl中有个lambda方法也可以生成一个`闭包`。

更多关于javascript的闭包说明请查看[此处](http://yuweijun.blogspot.com/2010/06/javascript-closures-lexical-scope-and.html)
