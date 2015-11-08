---
layout: post
title: "get iframe body contents"
date: "Thu Aug 30 2007 17:08:00 GMT+0800 (CST)"
categories: javascript
---

iframe中的HTML正文内容获取，在FF/IE中测试通过。

{% highlight javascript %}
document.getElementById('iframe_id').contentWindow.document.body.innerHTML
{% endhighlight %}
