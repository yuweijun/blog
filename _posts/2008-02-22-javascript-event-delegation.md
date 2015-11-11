---
layout: post
title: "javascript event delegation"
date: "Fri Feb 22 2008 15:18:00 GMT+0800 (CST)"
categories: javascript
---

html
-----

{% highlight html %}
<div id='d1'>test</div>
<ul id="example">
    <li id="li0">foo</li>
    <li id="li1">bar</li>
    <li id="li2">baz</li>
    <li id="li3">thunk</li>
</ul>
{% endhighlight %}

javascript
-----

{% highlight javascript %}
var addListener = function() {
    if (window.addEventListener) {
        return function(el, type, fn) {
            el.addEventListener(type, fn, false);
        };
    } else if (window.attachEvent) {
        return function(el, type, fn) {
            var f = function() {
                fn.call(el, window.event);
            };
            el.attachEvent('on' + type, f);
        };
    } else {
        return function(el, type, fn) {
            element['on' + type] = fn;
        }
    }
}();

addListener(document.getElementById('d1'), 'click', getUserNameById);

function getUserNameById(e) {
    alert('api function getUserNameById');
    getUserNameById(this.id);
}

var element = document.getElementById('example');
addListener(element, 'click', handleClick);

function handleClick(e) {
    var element = e.target || e.srcElement;
    alert('target: ' + e.target + " currentTarget: " + e.currentTarget + " id: " + element.id + " this: " + this + " eventPhase: " + e.eventPhase);
}
{% endhighlight %}

`event.target`是指事件发生时的对象，可以是document中的任何一个node,包括text node。


如果在捕捉阶段或者起泡阶段处理事件，事件`event.target`仍然都是指向发生事件的node上，但`event.currentTarget`却不是指向此node。


如上例子中点击的`event.target`是li元素时，ul上的事件处理是发生在起泡阶段(第3阶段)，而不是在AT_TARGET(2)阶段。此时的`event.target`指向li元素，`event.currentTarget`则是指向ul元素。


callback中的`this`与`event.currentTarget`在目前实现的`addEventListener`方法中是指向相同的元素，但最好不要用`this`.
