---
layout: post
title: "javascript中函数function说明"
date: "Mon Oct 15 2007 13:57:00 GMT+0800 (CST)"
categories: javascript
---

从技术上说，function并非是一个语句。在JavaScript程序中，语名会引发动态的行为，但是函数定义描述的却是静态的程序结构。语句是在运行时执行的，而函数是在实际运行之前，浏览器载入JavaScript的时候被解析的，或者说是在被编译时定义了这个函数。当Javascript解析程序遇到一个函数定义时，它就解析并存储（而不执行）构成函数主体的语句，然后定义一个和该函数同名的属性（如果函数定义嵌套在其他函数中，那么就在调用对象中定义这个属性，否则在全局对象中定义这个属性）以保存它。

The fact that function definitions occur at parse time rather than at runtime causes some surprising effects. Consider the following code:

{% highlight javascript %}
console.log(f(4)); // Displays 16. f( ) can be called before it is defined.
var f = 0; // This statement overwrites the property f.

function f(x) { // This "statement" defines the function f before either
    return x*x; // of the lines above are executed.
}
console.log(f); // Displays 0. f( ) has been overwritten by the variable f.
{% endhighlight %}

另外如果Ajax调用返回的内容包含JS的话，需要对JS进行eval()操作，才能获取到JS中的变量和方法，其中方法必须以Function Literals直接量的方式赋个一个变量才能获得此方法。另外Ajax载入的JS中变量都要以全局变量方式载入才能得到，即变量前不能加var声明。

Function内部语句发变量定义如果不加var声明的话，只要function被执行过，此变量也会成为一个全局变量。
