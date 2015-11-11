---
layout: post
title: "java bin目录下的native2ascii使用"
date: "Sat Jun 28 2008 12:58:00 GMT+0800 (CST)"
categories: java
---

直接在命令行中输入`native2ascii`后可以进入控制台，输入字符串之后`native2ascii`会将字符串转为unicode编码。

{% highlight bash %}
$> native2ascii
test中文
test\u2030\u220f\u2260\u00ca\u00f1\u00e1
{% endhighlight %}

当然命令可以接收参数，通过源文件生成目标properties文件。
