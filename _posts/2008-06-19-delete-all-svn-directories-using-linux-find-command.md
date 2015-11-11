---
layout: post
title: "delete all .svn directories using linux find command"
date: "Thu Jun 19 2008 14:26:00 GMT+0800 (CST)"
categories: linux
---

linux命令批量删除`.svn`的目录：

{% highlight bash %}
$> find . -name '.svn' -exec /bin/rm -f {} \;
$> find . -name '.svn' |xargs /bin/rm -f
{% endhighlight %}

对于delete操作可以简写如下：

{% highlight bash %}
$> find . -name '.svn' -delete
{% endhighlight %}

确认即可删除所有.svn目录。
