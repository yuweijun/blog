---
layout: post
title: "grep_options and grep_color on mac os x"
date: "Fri, 26 Oct 2012 17:49:21 +0800"
categories: macos
---

在Mac OS X下将grep匹配到的结果高亮显示。

{% highlight bash %}
$> vi ~/.bash_profile
export GREP_OPTIONS='--color=auto'
export GREP_COLOR='1;30;40'
{% endhighlight %}
