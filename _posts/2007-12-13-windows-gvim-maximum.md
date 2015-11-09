---
layout: post
title: "windows下让gvim启动时窗口最大化"
date: "Thu Dec 13 2007 14:50:00 GMT+0800 (CST)"
categories: vim
---

在用户目录下的~/_vimrc文件里加上此行(只是Windows生效)

{% highlight vim %}
" maximum the initial window on windows plateform
au GUIEnter * simalt ~x
{% endhighlight %}
