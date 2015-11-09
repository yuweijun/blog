---
layout: post
title: "javascript/ruby/perl/php正则中点号的区别"
date: "Fri Sep 21 2007 22:46:00 GMT+0800 (CST)"
categories: ruby
---

在Ruby/Perl/PHP中
-----

. (period) [^\n] 匹配除换行符(\n)之外所有字符

在javascript中
-----

. (period) [^\n\r] Any character except new line and carriage return

在Ruby/Javascript中
-----

可以用m这个选项使得.period匹配换行符\n，不过不匹配Javascript的\r。

在PHP/Perl中
-----

可以用s这个选项使得.period匹配换行符\n。在PHP/Perl中的m选项是使得正则表达式里的^和$能匹配字符串的多行。
