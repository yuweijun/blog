---
layout: post
title: "mysql-bin.index not found"
date: "Sun, 03 Feb 2013 10:46:08 +0800"
categories: mysql
---

mysql抛出如下错误信息：


{% highlight text %}
'mysql-bin.index' not found (Errcode: 13)
{% endhighlight %}

这个问题原因是因为`mysql-bin.index`文件属主不正确，或者是权限不对造成的，而不是这个文件真的找不到。

解决方法
-----

mysql目录和文件属主都是`mysql`，目录权限`700`，文件权限`660`。
