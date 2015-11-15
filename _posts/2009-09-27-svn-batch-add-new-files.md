---
layout: post
title: "svn批量添加新文件"
date: "Sun Sep 27 2009 09:34:00 GMT+0800 (CST)"
categories: ruby
---

svn batch add new files
-----

{% highlight bash %}
$> svn status |grep '^\?'|awk -F " " '{print $2}'|xargs svn add
{% endhighlight %}

或者是

{% highlight bash %}
$> svn st|grep '^\?'|sed 's/^\?\s*//g'|xargs svn add
{% endhighlight %}

