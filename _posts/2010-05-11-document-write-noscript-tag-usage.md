---
layout: post
title: "利用document.write()和noscript标签做内文替换的小技巧"
date: "Tue May 11 2010 11:14:00 GMT+0800 (CST)"
categories: javascript
---

巧用`document.write`和`noscript`标签，可以替换网页同一位置上的内容，可用于同一网页`A/B测试`。

{% highlight html %}
<script>document.write("新页面内容" + "<nosc"+"ript>");</script>
      原来的页面内容: 利用document.write()打印新的页面内容，并且打出一个noscript起始标签，在新页面内容想要替换的页面内容结尾处，添加一个noscript结束标签，从而利用document.write()和noscript标签对，用新页面内容替换原来的页面内容，这个小技巧是在看google做页面内容不同时的效果跟踪代码时发现的。
</noscript>
{% endhighlight %}
