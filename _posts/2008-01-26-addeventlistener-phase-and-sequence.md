---
layout: post
title: "addeventlistener's phase and sequence"
date: "Sat Jan 26 2008 14:59:00 GMT+0800 (CST)"
categories: javascript
---

html源码
-----

{% highlight html %}
 <div id="a1">
   <div id="a2"><input type="button" name="input1" id="i1" value="click here"></div>
 </div>
{% endhighlight %}

javascript源码
-----

{% highlight javascript %}
function log(event) {
    console.log("currentTarget=" + event.currentTarget.id + "; target=" + event.target + "; eventPhase=" + event.eventPhase);
}
var a1 = document.getElementById("a1");
var a2 = document.getElementById("a2");
var i1 = document.getElementById("i1");
a1.addEventListener('click', log, true);
a2.addEventListener('click', log, false);
i1.addEventListener('click', log, false);
{% endhighlight %}

`addEventListener`的第3个参数是指在capturing_phase是否捕获此动作，因此上面会按"a1->i1->a2"的顺序弹出提示，这个顺序与dom文档节点顺序和`addEventListener`第3个参数有关，与javascript代码中的`addEventListener`的顺序无关。

要在事件流的所有阶段侦听某一事件，您必须调用`addEventListener`两次，第一次调用时将`useCapture`设置为true，第二次调用时将`useCapture`设置为false。如：
{% highlight javascript %}
a1.addEventListener('click', log, false);
{% endhighlight %}

此时在capturing phase和bubbling phase都会有a1的事件提示。
