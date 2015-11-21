---
layout: post
title: "vim converting tabs to spaces"
date: "Tue, 09 Oct 2012 16:36:00 +0800"
categories: vim
---

将文件中的`tab`转化为空格。

{% highlight vim %}
:set tabstop=4
:set shiftwidth=4
:set expandtab
:retab
{% endhighlight %}

References
-----

1. [http://vim.wikia.com/wiki/Converting_tabs_to_spaces](http://vim.wikia.com/wiki/Converting_tabs_to_spaces)
