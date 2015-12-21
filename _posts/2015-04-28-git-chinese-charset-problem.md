---
layout: post
title: "git status中文文件名显示问题"
date: "Tue Apr 28 2015 22:25:00 GMT+0800 (CST)"
categories: linux
---

在linux或者maxos上用`git status`查看项目状态时，发现中文文件名显示有问题，如下：

{% highlight text %}
modified:   "public/\346\230\216\344\273\243\347\216\213\345\256\210/\347\216\213\345\256\210\350\241\214\344\271\246\345\274\240\346\241\202\345\262\251\345\242\223\345\277\227\351\223\255/info.json"
{% endhighlight %}

上面中文文字被显示成`\xxx\xxx\xxx`，是因为git对`0x80`以上的字符进行`quote`造成的，`man git-config`官方文档说明如下。

{% highlight text %}
core.quotePath

The commands that output paths (e.g. ls-files, diff), when not given the -z option, will quote "unusual" characters in the pathname by enclosing the pathname in a double-quote pair and with backslashes the same way strings in C source code are quoted. If this variable is set to false, the bytes higher than 0x80 are not quoted but output as verbatim. Note that double quote, backslash and control characters are always quoted without -z regardless of the setting of this variable.
{% endhighlight %}

设置中文显示
-----

{% highlight bash %}
$> git config core.quotepath false
{% endhighlight %}

### 设置git编辑器

默认会启用`shell`的环境变量`$EDITOR`所指定的软件，一般都是`vim`或`emacs`，也有些是`nano`。当然也可以按照起步介绍的方式，使用`git config --global core.editor`命令设定你喜欢的编辑软件。

{% highlight bash %}
$> git config --global core.editor "vim"
{% endhighlight %}

{% highlight bash %}
$> export GIT_EDITOR=vim
{% endhighlight %}

使用`Sublime Text`则用以下命令：

{% highlight bash %}
$> git config --global core.editor "subl -n -w"
{% endhighlight %}

使用`TextMate`则用以下命令：

{% highlight bash %}
$> git config --global core.editor "mate -w"
{% endhighlight %}

References
-----

1. [Associating text editors with Git](https://help.github.com/articles/associating-text-editors-with-git/)
2. [git-config Manual Page](https://www.kernel.org/pub/software/scm/git/docs/git-config.html)

