---
layout: post
title: "在vim中如何使用tidy为html验证并格式化内容"
date: "Sat Oct 23 2010 19:26:00 GMT+0800 (CST)"
categories: vim
---

在ubuntu中安装tidy只要输入命令:

{% highlight bash %}
$> sudo apt-get install tidy

$> man tidy
{% endhighlight %}

查看手册，可以看到比较有用的参数设置，一般我的文件都是utf8编码的，所以tidy命令会跟上`-utf8`，并且一般情况就是将tidy输出的文件直接替换掉原来的文件，所以`-m`选项也很有用，输出的HTML也希望是格式化，有缩进的代码，这时就要加上`-i`参数，这样在命令行上我的tidy命令会如下所示:

{% highlight bash %}
$> tidy -m -i -utf8 target-files1 target-files2
{% endhighlight %}

可以将常用的参数设置写在一个配置文件中，在调用tidy命令时指定`-config config-file-name`，下面这份配置就是官网上的一个配置例子，稍做了点调整:

{% highlight text %}
// sample config file for HTML tidy
indent: auto
indent-spaces: 4
wrap: 120
markup: yes
output-xml: no
input-xml: no
show-warnings: yes
numeric-entities: yes
quote-marks: yes
quote-nbsp: yes
quote-ampersand: no
break-before-br: no
uppercase-tags: no
uppercase-attributes: no
char-encoding: utf8
new-inline-tags: cfif, cfelse, math, mroot,
mrow, mi, mn, mo, msqrt, mfrac, msubsup, munderover,
munder, mover, mmultiscripts, msup, msub, mtext,
mprescripts, mtable, mtr, mtd, mth
new-blocklevel-tags: cfoutput, cfquery
new-empty-tags: cfelse
{% endhighlight %}

在vim中，如果需要对当前文件进行html格式化操作，那么可以在vim命令模式下输入以下指令:

{% highlight vim %}
:!tidy -m -i -utf8 %
{% endhighlight %}

References
-----

1. [Clean up your Web pages with HTML TIDY](http://www.w3.org/People/Raggett/tidy/)
