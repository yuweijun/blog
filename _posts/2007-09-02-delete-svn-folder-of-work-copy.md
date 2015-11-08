---
layout: post
title: "delete .svn folder of work copy"
date: "Sun Sep 02 2007 23:44:00 GMT+0800 (CST)"
categories: linux
---

Credit: Zed Shaw, at the Mongrel mailing list.

{% highlight bash %}
$> find . -name ".svn" -exec rm -rf {} \;
$> find . -name ".svn" | xargs rm -Rf
{% endhighlight %}
