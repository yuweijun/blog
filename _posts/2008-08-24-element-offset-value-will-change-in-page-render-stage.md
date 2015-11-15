---
layout: post
title: "element offset value will change in page render stage"
date: "Sun Aug 24 2008 14:40:00 GMT+0800 (CST)"
categories: javascript
---

页面元素的`element.offsetLeft`和`element.offsetTop`会因为元素前面的外部图片元素载入而发生变化的，如下所示：

html
-----

{% highlight html %}
<img id="image" src="http://www.google.com/logos/closing_ceremonies.gif"/>
<p>
test
<span id="span" style="color: blue; border: 2px solid orange; padding: 5px; background-color: yellow;">offset will change</span> in page render stage
</p>
{% endhighlight %}

javascript
-----

{% highlight javascript %}
var span = document.getElementById("span");
console.log([span.offsetLeft, span.offsetTop]);
setTimeout(function(){
   console.log([span.offsetLeft, span.offsetTop]);
}, 3000);
{% endhighlight %}

比如上面的例子中，在span这个元素前还有一张图片是从服务器外部载入的，在页面渲染过程中，这个span对象会被先渲染到页面上，并有了对应的offset值，但当图片被载入页面之后，span元素又往下移了，此时offset值重新计算了，注意在测试中要每次清一下缓存才能看到效果，否则图片本地有缓存的话就看不出来的。
