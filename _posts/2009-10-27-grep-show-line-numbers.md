---
layout: post
title: "grep命令参数-显示查询内容的所在行号"
date: "Tue Oct 27 2009 15:35:00 GMT+0800 (CST)"
categories: linux
---

使用`grep`命令时，添加行号(`-n`)可以显示查询内容的所在行的行号，如：

{% highlight bash %}
$> grep -n ls ~/.bashrc
1:# ~/.bashrc: executed by bash(1) for non-login shells.
47: else
54:else
77:# enable color support of ls and also add handy aliases
80: alias ls='ls --color=auto'
81: alias ll='ls -la'
90:# some more ls aliases
91:#alias ll='ls -l'
92:#alias la='ls -A'
93:#alias l='ls -CF'
{% endhighlight %}

