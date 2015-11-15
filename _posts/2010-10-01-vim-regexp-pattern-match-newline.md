---
layout: post
title: "vim中匹配换行符的正则表达式"
date: "Fri Oct 01 2010 19:29:00 GMT+0800 (CST)"
categories: vim
---

`\s*`匹配0或多个空白(比如空格、tab等，不匹配换行)

vim里面，如果要连换行一起匹配，则加个下划线，比如`\_s`匹配包括换行在内的空白，而`\_.`匹配包括换行在内的任意字符(注意，后面有个小数点)。

References
-----

1. [vim正则表达式查找替换](http://jianzi0307.blog.163.com/blog/static/2081200200951613843867/)
