---
layout: post
title: "隐藏Gvim菜单和工具栏"
date: "Fri Dec 21 2007 17:11:00 GMT+0800 (CST)"
categories: vim
---

在~/.vimrc(_vimrc)中添加下面几行：

{% highlight vim %}
" 完全隐藏菜单：
" 可以随时用 :set guioptions+=m 再呼出菜单,下面工具条也类似
:set guioptions-=m

" 完全隐藏工具栏：
:set guioptions-=T
{% endhighlight %}
