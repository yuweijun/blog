---
layout: post
title: "git status中文文件名显示问题"
date: "Tue Apr 28 2015 22:25:00 GMT+0800 (CST)"
categories: linux
---

在linux或者maxos上用`git status`查看项目状态时，发现中文文件名显示有问题，如下：

{% highlight text %}
modified:   "public/\346\230\216\344\273\243\347\216\213\345\256\210/\347\216\213\345\256\210\350\241\214\344\271\246\345\274\240\346\241\202\345\262\251\345\242\223\345\277\227\351\223\255/info.json"
{% endhighlight %}

上面中文文字被显示成`\xxx\xxx\xxx`，是因为git对`0x80`以上的字符进行`quote`造成的。

设置中文显示
-----

{% highlight bash %}
$> git config core.quotepath false
{% endhighlight %}
