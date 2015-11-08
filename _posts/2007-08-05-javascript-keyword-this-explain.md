---
layout: post
title: "javascript keyword this explain"
date: "Sun Aug 05 2007 19:51:00 GMT+0800 (CST)"
categories: javascript
---

In JavaScript `this` always refers to the "owner" of the function we're executing, or rather, to the object that a function is a method of.  When we define our faithful function `doSomething()` in a page, its owner is the page, or rather, the `window` object (or global object) of JavaScript.

An `onclick` property, though, is owned by the HTML element it belongs to.

Examples - copying
-----

`this` is written into the `onclick` method in the following cases:

{% highlight javascript %}
// <element onclick="this.style.color = '#cc0000';">
element.onclick = doSomething
element.addEventListener('click',doSomething,false)
element.onclick = function () {this.style.color = '#cc0000';}
{% endhighlight %}

Examples - referring
-----

In the following cases `this` refers to the `window`:

{% highlight javascript %}
element.onclick = function () {doSomething()}
element.attachEvent('onclick',doSomething)
// <element onclick="doSomething()">
// <a href="#" id="a1" style="color: #ddd"> test </a>
function doSomething() {
   this.style.color = '#cc0000';
}

var a1 = $('a1');
// a1.addEventListener('click', doSomething, false);
a1.onclick = doSomething; // copy function.
{% endhighlight %}
