---
layout: post
title: "vim substitute usage tips"
date: "Mon Dec 17 2007 17:53:00 GMT+0800 (CST)"
categories: vim
---

统计文章中出现的单词的数目，可以使用下面的命令：

{% highlight vim %}
:%s/\w//gn
{% endhighlight %}

如何将一串十进制数字转换为16进制数字，使用vim完成转换的最简单方法如下：

{% highlight vim %}
:%s/\d\+/\=printf("%X", submatch(0))/g
{% endhighlight %}

这条命令的原理是，把一串数字，用printf()函数的输出替换掉，printf()函数输出的正是这串数字的16进制形式。

分析如下：

{% highlight vim %}
\= 使用表达式的结果进行替换 (:help /\w )
printf 按指定格式输出 (:help printf() )
submatch() 返回:s命令中的指定匹配字符串 (:help submatch() )
g 替换行内所有出现的匹配 (:help :s_flags)
{% endhighlight %}
