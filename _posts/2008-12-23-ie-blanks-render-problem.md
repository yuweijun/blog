---
layout: post
title: "ie中空白渲染的问题"
date: "Tue Dec 23 2008 20:21:00 GMT+0800 (CST)"
categories: web
---

ie空白渲染之后，连续的空白被合并为一个了。

html
-----

{% highlight javascript %}
<form action="test_submit" method="get" accept-charset="utf-8">
    <input type="text" name="some_name" value="" id="name" />
    <div id="x" style="white-space:pre;">x xx x</div>
    <div id="y">y yy y</div>
    <p>
        <input type="button" value="click here" onclick="document.getElementById('name').value=document.getElementById('x').innerHTML" />
    </p>
    <p>
        <input type="button" value="click here" onclick="document.getElementById('name').value=document.getElementById('y').innerHTML" />
    </p>
</form>
{% endhighlight %}

上面的例子在ie6/ie7中测试时可发现y组成的字符串，其中的空格被ie渲染过后，取到的`innerHTML`已经变为一个空格了，在`firefox/safari`上在渲染后看上去是只有一个空格，但`innerHTML`取到的还是与原码是保持一致的。
