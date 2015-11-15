---
layout: post
title: "ctags for rails and gvim"
date: "Mon Mar 01 2010 17:37:00 GMT+0800 (CST)"
categories: vim
---

ctags支持41种语言，常见的java/c/c++/javascript/php/perl/python/ruby等都包括其中，更多信息可查看[官网](http://ctags.sourceforge.net/index.html)和[帮助手册](http://ctags.sourceforge.net/ctags.html)。

在ubuntu中安装ctags:

{% highlight bash %}
$> sudo apt-get install exuberant-ctags
{% endhighlight %}

在rails项目下生成ctags文件，生成的TAGS文件可以用于vim/emacs等编辑器：

{% highlight bash %}
$> ctags-exuberant -a -e -f TAGS --tag-relative -R app lib vendor /opt/ruby-enterprise/lib/ruby/gems/1.8/gems
{% endhighlight %}

在gvim中常的操作有以下4个：

{% highlight bash %}
$> gvim −t tag
{% endhighlight %}

打开定义 tag 的文件。

在vi中使用:ta tag打开定义tag的文件

{% highlight vim %}
:ta tag
{% endhighlight %}

在vi的指针位置打开定义所在tag的文件

{% highlight vim %}
Ctrl-]
{% endhighlight %}

回退到之前的位置

{% highlight vim %}
Ctrl-T
{% endhighlight %}
