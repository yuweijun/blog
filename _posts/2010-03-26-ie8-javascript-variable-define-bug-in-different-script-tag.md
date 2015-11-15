---
layout: post
title: "ie8 javascript variable define bug in different script tag"
date: "Fri Mar 26 2010 15:08:00 GMT+0800 (CST)"
categories: javascript
---

当前测试只是针对IE8，没有测试之前IE版本，以下代码在firefox/chrome中会按预期一样正常运行，代码测试注意打开firebug或者是console查看输出:

注意以下代码必须是放在不同的script标签执行，不能是在同一段script代码中，否则结果都是一样的。

{% highlight html %}
<script type="text/javascript">
// window.a 设置一个属性a到window上
window.a = 1;
// 正常打印出a:1
window.console.log(a);
</script>
{% endhighlight %}

{% highlight html %}
<script type="text/javascript">
// 必须在此分二块script来写，如果写在一块script标签内，都会正常执行
// 在同一个script代码块内，javascript会解析当前代码块中所有的变量(之前没有出现过的变量名)，并且都为 undefined
// 在这里还没有执行到var a=2，a为 undefined
// 针对IE8: window.a 也被置为 undefined
// 其他浏览器: window.a前面代码中已经定义，所以这里还是原来的值：1，而非像IE8中一样为 undefined
window.console.log(a);
var a = 2; // 注意前面有个变量声明: var
window.console.log(a);
</script>
{% endhighlight %}

{% highlight html %}
<script type="text/javascript">
window.console.log(a);
// 这里因为没有用var新定义变量，所以a即为window.a，只是对变量重新赋值，代码执行没有问题
a = 3; // 注意前面没有变量声明: var
window.console.log(a);
</script>
{% endhighlight %}

运行结果
-----

以上代码在firefox/chrome中执行结果为：`1 1 2 2 3`

以上代码在ie8中执行结果为：`1 undefined 2 2 3`

