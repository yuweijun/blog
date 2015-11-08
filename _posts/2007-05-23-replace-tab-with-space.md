---
layout: post
title:  "replace Tab with Space"
date: "Mon May 21 2007 12:01:00 GMT+0800 (CST)"
categories: linux
---

在bash下试了sed , expand , awk等去替换一个文件里的Tab(shell里这样按出Tab: CTRL+V->CTRL+I->TAB), 但是都比较麻烦，最后google到一个人用perl做的命令：

{% highlight bash %}
perl -pi.bak -e 's/\t/ /g' myfile.txt
{% endhighlight %}

在learnig Perl书中有这个例子：

{% highlight bash %}
perl –p –i.bak –w –e 's/test/text/g' [a-z]*.txt
{% endhighlight %}

原理同上，这个命令行相当于构建了以下脚本的功能：

{% highlight bash %}
#! /usr/bin/perl –w
$^I = ".txt";
while(<>) {
    s/test/text/g;
    print;
}
{% endhighlight %}

