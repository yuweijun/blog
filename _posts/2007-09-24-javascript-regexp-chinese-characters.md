---
layout: post
title: "javascript检测中文字符集的正则表达式"
date: "Mon Sep 24 2007 14:36:00 GMT+0800 (CST)"
categories: javascript
---

校验中文字符的正则表达式
-----

{% highlight javascript %}
/[\u4E00-\u9FA5]+/.test('中文')

// or

/[\u4E00-\u9FCC]+/.test('中文')
{% endhighlight %}
