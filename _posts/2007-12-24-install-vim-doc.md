---
layout: post
title: "install vim doc"
date: "Mon Dec 24 2007 12:56:00 GMT+0800 (CST)"
categories: vim
---

在Vim中运行:

{% highlight vim %}
:help =
提示错误类似:
E433：No tags file
E149：Sorry, no help for =
Press ENTER or type command to continue
{% endhighlight %}

在Vim下运行:

{% highlight vim %}
:helptags ~/.vim/doc

(或者是 :helptags 安装目录下的/vim7/doc)
{% endhighlight %}

安装了新的插件也是一样要运行一下此命令,可单独指定help文件名
