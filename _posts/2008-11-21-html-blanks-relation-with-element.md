---
layout: post
title: "html中的空白对页面元素布局的影响"
date: "Fri Nov 21 2008 14:42:00 GMT+0800 (CST)"
categories: web
---

css
-----

{% highlight css %}
span {
    border: 1px #333 solid;
}
{% endhighlight %}

html
-----

{% highlight html %}
<p>
<span>测试</span>      <span>     开始标签前的空白会覆盖开始标签后的空白。</span>     <span>      测试</span>
</p>
<p>
<span>结束标签前的空白会覆盖结束标签后的空白。     </span>    <span>此文字之前的空白被前面的结束标签前的空白覆盖。</span>
<span>测试   </span>   <span>测试</span>
</p>
<p>
<span>结束标签前的空白会覆盖结束标签后的空白，而开始标签前的空白又覆盖了后面的空白。     </span>    <span>     此文字之前的空白被前面开始标签前的空白覆盖，而开始标签前的空白也被前面的空白覆盖。</span>
<span>测试   </span>   <span>测试</span>
</p>
{% endhighlight %}

以上结果基于firefox/safari/opera测试，IE未测试，神奇的IE表现肯定与众不同。
