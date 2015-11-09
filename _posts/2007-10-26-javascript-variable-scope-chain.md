---
layout: post
title: "javascript 中变量的声明和变量的作用域说明"
date: "Fri Oct 26 2007 20:55:00 GMT+0800 (CST)"
categories: javascript
---

一、全局作用域和局部作用域
-----

在全局环境里:
`var a = 1;`
与
`a = 1;`
的作用是相同的。但是如果是在一个函数体内这二者就不同了，前者是声明了一个函数体内的局部变量，而后者在此函数被运行一次之后就会生成一个全局变量 a 。

一般在声明变量时尽可能的加上变量声明`var`。

二、delete与变量关系：
-----

按javascript权威指南书中所言，一个变量一旦被 `var` 声明之后(未初始化)就有一个默认值'undefined'，并`delete`运算符不能删除这些变量，不然会引发一个错误。不过在firefox中测试是可以对声明后的变量进行`delete`，并返回true，在操作之后再引用就会报未定义错误，说明变量正常删除。在ie7里进行`delete`是的确返回false，无法删除，不过也没有引发错误。

三、JavaScript没有块级作用域
-----

这个不同于c/c++/java，javascript的变量只要声明了就会在整个函数体中都有定义，而不管声明的前后位置，会覆盖全局的同名变量。

{% highlight javascript %}
function test(o) {
    var i = 0;                      // i is defined throughout function
    if (typeof o == "object") {
        var j = 0;                  // j is defined everywhere, not just block
        for(var k=0; k < 10; k++) { // k is defined everywhere, not just loop
            console.log(k);
        }
        console.log(k);          // k is still defined: prints 10
    }
    console.log(j);              // j is defined, but may not be initialized
}

var scope = "global";
function f( ) {
    console.log(scope);         // Displays "undefined", not "global"
    var scope = "local";  // Variable initialized here, but defined everywhere
    console.log(scope);         // Displays "local"
}
f();
{% endhighlight %}
