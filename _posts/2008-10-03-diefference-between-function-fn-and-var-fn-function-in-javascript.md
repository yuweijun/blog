---
layout: post
title: "diefference between 'function func(){}' and 'var func = function(){}' in javascript"
date: "Fri Oct 03 2008 23:52:00 GMT+0800 (CST)"
categories: javascript
---

javascript function的二种定义方式的区别
-----

{% highlight javascript %}
console.log(f); // undefined
console.log(h()); // true

// This line of code defines an unnamed function and stores a reference to it
// in the variable f. It does not store a reference to the function into a variable
// named fact, but it does allow the body of the function to refer to itself using
// that name.
var f = function fact(x) {
    if (x <= 1) return 1;
    else return x * fact(x - 1);
};
try {
    console.log(f);
    console.log(fact);
} catch (e) {
    console.error(e.message)
}
console.log(f(1));
console.log(f(2));
console.log(f(3));
var g = function(x) {
    if (x <= 1) {
        return 1;
    } else {
        return x * arguments.callee(x - 1);
    }
};
console.log(g(4));

function h() {
    return true;
}
{% endhighlight %}

其中需要说明一下`var func = function(){}`与`function func(){}`这二者的区别是后者用function语句定义的variable会先于此function运行前被初始化，因此`h()`函数调用可以写在其定义语句之前，而前者用`var`声明的variable则只能在此变量声明之后才可以被调用，与其他用`var`声明的变量完全一样。

另`var f = function fact(x) { if (x <= 1) return 1; else return x * fact(x - 1); };`这个写法要注意是在`javascript 1.5`版本之后才被实现。
