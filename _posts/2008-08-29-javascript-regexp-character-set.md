---
layout: post
title: "javascript 正则表达式的字符类使用说明"
date: "Mon Sep 29 2008 17:59:00 GMT+0800 (CST)"
categories: javascript
---

{% highlight text %}
[^...] 不在方括号之中的任意字符
{% endhighlight %}

需要注意的是， 在方括号之内也可以使用特殊的字符类转序列，对于`\()[]`这5个字符如果要出现在方括号中则需要转义，其他的如`.*?+`等字符就不需要转义了，如以下二个效果是一样的：

{% highlight javascript %}
/[^.*?+]/
/[^\.\*\?\+]/
{% endhighlight %}

另补充一点是`[\b]`是回退键的直接量：

{% highlight javascript %}
/[\b]/.test("[\b]") // true
{% endhighlight %}
