---
layout: post
title: "java pattern.quote 使用说明"
date: "Fri Feb 13 2009 13:55:00 GMT+0800 (CST)"
categories: java
---

Pattern.quote的java doc说明
-----

{% highlight text %}
\   Nothing，但是引用以下字符
\Q  Nothing，但是引用所有字符，直到 \E
\E  Nothing，但是结束从 \Q 开始的引用
{% endhighlight %}

例子
-----

{% highlight java %}
Pattern.quote("[test]")
// 返回 => \Q[test]\E
{% endhighlight %}

这样就不需要对正则的特殊字符`[`和`]`做转义了

