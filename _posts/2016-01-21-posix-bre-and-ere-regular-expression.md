---
layout: post
title: "POSIX正则表达式规范BRE和ERE简介"
date: "Thu, 21 Jan 2016 15:25:55 +0800"
categories: linux
---

## POSIX规范

常见的正则表达式记法，其实都源于Perl，实际上，正则表达式从Perl衍生出一个显赫的流派，叫做PCRE（Perl Compatible Regular Expression），`\d`、`\w`、`\s` 之类的记法，就是这个流派的特征。但是在PCRE之外，正则表达式还有其它流派，比如下面要介绍的`POSIX规范`的正则表达式。

POSIX的全称是`Portable Operating System Interface for uniX`，它由一系列规范构成，定义了UNIX操作系统应当支持的功能，所以“POSIX规范的正则表达式”其实只是“关于正则表达式的POSIX规范”，它定义了BRE（Basic Regular Expression，基本型正则表达式）和ERE（Extended Regular Express，扩展型正则表达式）两大流派。在兼容POSIX的UNIX系统上，grep和egrep之类的工具都遵循POSIX规范，一些数据库系统中的正则表达式也符合POSIX规范。

BRE和ERE二者的区别，简单的说就在于`(`、`)`、`{`、`}`、`+`、`?`、`|`这7个特殊字符的使用方法上：

1. 在BRE中如果想要这些字符表示特殊的含义，就需要把它们转义。
2. 反之，在ERE中如果要这些字符不表示特殊的含义，就需要把它们转义。
3. BRE中的特殊字符：`.`、`\`、`[`、`^`、`$`、`*`。
4. ERE中的特殊字符多了7个，即：`.`、`\`、`[`、`^`、`$`、`*`、`(`、`)`、`{`、`}`、`+`、`?`、`|`。

## BRE

在Linux/Unix常用工具中，grep、vi、sed都属于BRE这一派，它的语法看起来比较奇怪，元字符 `(`、`)`、`{`、`}`、`+`、`?`、`|` 必须转义之后才具有特殊含义，如`\+`、`\?`、`\|`，而且也支持`\1`、`\2`之类反向引用。

## ERE

在Linux/Unix常用工具中，egrep、awk则属于ERE这一派。虽然BRE名为“基本”而ERE名为“扩展”，但ERE并不要求兼容BRE的语法，而是自成一体。

注：PCRE中常用`\b`来表示“单词的起始或结束位置”，但Linux/Unix的工具中，通常用`\<`来匹配“单词的起始位置”，用`\>`来匹配“单词的结束位置”，sed中的`\y`可以同时匹配这两个位置。

## POSIX字符组

在某些文档中，你还会发现类似`[:digit:]`、`[:lower:]`之类的表示法，它们看起来不难理解（digit就是“数字”，lower就是“小写”），但又很奇怪，这就是POSIX字符组。

在POSIX规范中，`[a-z]`、`[aeiou]`之类的记法仍然是合法的，其意义与PCRE中的字符组也没有区别，只是这类记法的准确名称是POSIX方括号表达式（bracket expression），它主要用在Unix/Linux系统中。

## POSIX与PCRE方括号表示法的区别

POSIX与PCRE方括号表示法的最主要差别在于：POSIX字符组中，反斜线`\`不是用来转义的。所以POSIX方括号表示法`[\d]`只能匹配`\`和`d`两个字符，而不是`[0-9]`对应的数字字符。

References
-----

1. [Linux/Unix工具与正则表达式的POSIX规范](http://www.infoq.com/cn/news/2011/07/regular-expressions-6-POSIX)
2. [9. Regular Expressions](http://pubs.opengroup.org/onlinepubs/009695399/basedefs/xbd_chap09.html)
3. [BRE与ERE的差异](http://blog.chinaunix.net/uid-23045379-id-2562051.html)
4. [sed正则表达式和12个实例](http://www.caiyiting.com/blog/2013/expressions.html)
