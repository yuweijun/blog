---
layout: post
title: "javascript函数调用时的作用域链和调用对象是如何形成的及与闭包的关系"
date: "Fri Oct 03 2008 23:01:00 GMT+0800 (CST)"
categories: javascript
---

1. javascript解析器启动时就会初始化建立一个全局对象`global object`，这个`全局对象`就拥有了一些预定义的全局变量和全局方法，如Infinity，parseInt，Math，所有程序中定义的全局变量都是这个`全局对象`的属性。在浏览器客户端javascript中，`window`就是这个javascript的`全局对象`。
2. 当javascript调用一个function时，会生成一个对象，称之为`call object`（调用对象），function中的局部变量和function的参数都成为这个`call object`的属性，以免覆写同名的全局变量。
调用对象: ECMAScript规范术语称之为`activation object`(活动对象)。
3. javascript解析器每次执行function时，都会为此function创建一个`execution context`执行环境，在此function执行环境中最重要的一点就是function的作用域链`scope chain`，这是一个`对象链`，由`全局对象`和`调用对象`构成，`对象链`具体构成过程见下面说明。
4. 当javascript查询变量x的值时，就会检查此`作用域链中`第一个对象，可能是`调用对象`或者是`全局对象`，如果对象中有定义此x属性，则返回值，不然检查`作用域链`中的下一个对象是否定义x属性，在`作用域链`中没有找到，最后返回undefined。
5. 当javascript调用一个function时，它会先将此function定义时的`作用域`作为其`作用域链`，然后创建一个`调用对象`，置于`作用域链`的顶部，function的参数及内部`var`声明的所有局部变量都会成为此`调用对象`的属性。
6. `this`关键词指向方法的`调用者`，而不是以`调用对象`的属性存在，同一个方法中的`this`在不同的function调用中，可能指向不同的对象。
7. The call object as a namespace.
8. javascript中所有的function都是一个`闭包`，但只有当一个嵌套函数被导出到它所定义的`作用域`外时，这种`闭包`才强大。如果理解了`闭包`，就会理解function调用时的`作用域链`和`调用对象`，才能真正掌握javascript。
9. 当一个嵌套函数的引用被保存到一个`全局变量`或者另外一个对象的属性时，在这种情况下，此嵌套函数有一个外部引用，并且在其外围调用函数的`调用对象`中有一个属性指向此嵌套函数。因为有其他对象引用此嵌套函数，所以在外围函数被调用一次后，其创建的`调用对象`会继续存在，并不会被垃圾回收器回收，其函数参数和局部变量都会在这个`调用对象`中得以维持，javascript代码任何形式都不能直接访问此对象，但是此`调用对象`是嵌套函数被调用时创建的`作用域链`中的一部分，可以被嵌套函数访问并修改。

call object as a namespace example
-----

{% highlight javascript %}
(function() {
    // 在方法体内用var声明的所有局部变量，都是以方法调用时创建的调用对象的属性形式存在。
    // 这样就避免与全局变量发生命名冲突。
})();
{% endhighlight %}
