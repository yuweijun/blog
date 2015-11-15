---
layout: post
title: "javascript: closures, lexical scope and scope chain"
date: "Tue Jun 08 2010 11:32:00 GMT+0800 (CST)"
categories: javascript
---

闭包的定义(javascript权威指南)如下：

javascript functions are a combination of code to be executed and the scope in which to execute them. this combination of code and scope is known as a closure in the computer science literature. all javascript functions are closures.

javascript的function定义了将要被执行的代码，并且指出在哪个作用域中执行这个方法，这种代码和作用域的组合体就是一个闭包，在代码中的变量是自由的未绑定的。闭包就像一个独立的生命体，有其自身要运行的代码，同时其自身携带了运行时所需要的环境。

javascript所有的function都是`闭包`。

`闭包`中包含了其代码运行的`作用域`，那这个`作用域`又是什么样子的呢，这就引入了`词法作用域`(lexical scope)的概念:

词法作用域是指方法运行的作用域是在方法定义时决定的，而不是方法运行时决定的。

所以在javascript中，function运行的`作用域`其实是一个`static scope`。但也有二个例外，就是`with`和`eval`，在这2者中的代码处于`dynamic scope`中，这给javascript带来额外的复杂度和计算量，因而也效率低下，避免使用。

当闭包在其词法作用域中运行过程中，如何检索其中的变量名？这就再引入了一个概念，`作用域链`(scope chain)。

当一个方法function定义完成，其`作用域链`就是`固定`的了，并被保存成为方法内部状态的一部分，只是这个`作用域链`中调用对象的属性值不是固定的。`作用域链`是"活"的。

当一个方法在被调用时，会生成一个`调用对象`(call object or activation object)，并将此call object加到其定义时确认下来的`作用域链`的顶端。

在这个call object上，方法的参数和方法内定义的局部变量名和值都会存在这个call object中，如果调用结束，这个call object会从`作用域链`的顶端移除，再没有被其他对象引用，内存也会被自动回收。

在此call object中使用的变量名会先从此方法局部变量和传入参数中检索，如果没有找到，就会向`作用域链`上的前一个对象查询，如此向上追溯，一直检索到global object(浏览器中即window对象上)，如果在整个作用域链上没有找到此变量名，则会返回undefined(没有指定对象直接查询变量名，没找到则抛出异常变量未定义)。

如此通过`作用域链`，javascript就实现了call object中变量名检索。

在全局对象中一个方法调用完成之后，生成的call object会被回收，这看不出`闭包`(即当前被调用的方法)有什么功用。但是当一个外部方法的内部返回一个嵌套方法，并且返回的嵌套方法被全局对象引用时，或者是外部方法内将嵌套方法赋给全局对象的属性(jquery构造方法就是在匿名方法内设置在window.jquery上)，外部方法调用生成的call object就会引用这个嵌套方法，而同时嵌套方法被全局对象引用，所以这个外部方法调用产生的call object及其属性就会继续生存在内存中，这时闭包(外部方法)的功用才被显示出来，下面以`jQuery.fn.animation()`方法调用过程为例进行说明:


{% highlight javascript %}
(function( window, undefined ) {
    // ......jQuery source code;
    // expose jQuery to the global object
    window.jQuery = window.$ = jQuery;
})(window);
{% endhighlight %}

1. 当载入整个jquery.js文件时，会运行最外面的匿名方法(通过这个匿名方法形成一个命名空间，所有的变量名都是匿名方法内部定义的局部变量名):
2. 因为匿名方法内部有一个内部方法jquery被全局对象window的属性jquery和$引用，这里变量名很搞，一个是匿名方法内嵌套的构造方法jquery，另一个window对象的属性名jquery。因为这个匿名方法内部的jquery构造方法被全局对象window.jquery引用，所以外围的匿名方法在运行时产生的call object会继续生存在内存中。此时，这个call object可以利用firebug或者chrome的debug工具可以看到，在firebug中的scopechain中称之为"object"，在chrome的console中称之为"closure"，该对象中记录了当前这个最外围的匿名方法被调用后生成的call object上变量的值，这些变量是未绑定的，是自由的，其值可以被修改并保存在作用域链上。运行此匿名方法时，会将其call object置于global object之上，形成作用域链。这里注意一点，这匿名方法是一个闭包，但运行方法生成的call object对象只是作用域链顶端的一个对象，记录了方法中的变量名和值。闭包不但包括这个运行的作用域，还包括其运行所需的代码。
3. 页面不关闭，这个匿名方法调用生成的call object就会一直驻在内存中，接下来当页面发生了一个jquery.fn.animate()方法的调用，这个时候javascript又会为.animate()方法生成一个call object，这个对象拥有传进来的参数名和值，以及在.animate()方法内部定义的一个局部变量opt和它的值。
同时，javascript会将生成的这个call object置于其作用域链(scope chain)的最前端，即此时的作用域链为：global object->anonymous function call object->animate call object。
4. 接下来会调用jquery.fn.queue()->jquery.fn.each()->jquery.fn.dequeue()，在这些方法调用过程也都会接触到第2步中所提到的那个匿名方法调用后生成的闭包，这中间过程略过，当运行到最后传参给.queue(function)的function时，因为这个匿名方法是定义在jquery.fn.animate()方法内部的，所以其作用域链(scope chain)也就已经确定了，即global object->anonymous function call object->animate call object，当此匿名方法调用生成一个call object，会将此call object再置于animate call object之上。
5. 对于最后的匿名function运行完成之后，如果这个匿名function对象还被其他element的queue数组引用，则第3步中运行.animate()方法生成的闭包将继续生存在内存之中，直到所有的效果方法运行完成，此匿名function没有其他引用时，.animate()调用生成的call object就会被回收。

References
-----

1. [javascript函数调用时的作用域链和调用对象是如何形成的及与闭包的关系](http://yuweijun.blogspot.com/2008/10/javascript.html)
