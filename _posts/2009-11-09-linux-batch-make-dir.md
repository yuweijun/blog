---
layout: post
title: "在linux命令行中批量新建文件夹"
date: "Mon Nov 09 2009 17:20:00 GMT+0800 (CST)"
categories: linux
---

用`printf`输出结果后，调用`xargs`命令创建目录。

{% highlight bash %}
$> printf 'dir%01d\n' {1..100} | xargs mkdir
{% endhighlight %}
