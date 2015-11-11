---
layout: post
title: "ie中img的onload和onerror问题"
date: "Thu Apr 17 2008 17:21:00 GMT+0800 (CST)"
categories: javascript
---

如果img标签`src`指向的图片有问题，在ie6中会触发`img.onerror`事件，ff2中触发`img.onload`事件。

javascript
-----

{% highlight javascript %}
function test() {
    alert('test');
}
{% endhighlight %}

html
----

{% highlight html %}
<img src="a.png" onload="test()" />
{% endhighlight %}

当img的`src`指向的a.png不是一个实际的png文件，比如是个空文本文件名为a.png(文件大小为0字节)，此时就不会触发`onload`事件，实际发生的此`img.onerror`事件。

如果往此文件中写入一个字符，使文件大小大于0字节，此时在firefox中能触发`onload`事件，在IE中则因为图片加载不正常，会在页面上显示一个加载图片失败的X图形，并触发的是`onerror`事件。
