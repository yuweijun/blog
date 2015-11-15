---
layout: post
title: "linux基础"
date: "Sun Jul 12 2009 01:54:00 GMT+0800 (CST)"
categories: linux
---

linux/unix历史
-----

{% highlight textt %}
1973 UNIX
1977 BSD (Bill Joy) -> FreeBSD
1979 AT&T 版权声明
1984 x86 Minix
1984 Richard Mathew Stallman -> GNU & FSF GPL / LGPL / Apache / BSD / MIT
1991 Linus Torvalds -> Linux
{% endhighlight %}

Linux 执行命令
-----

{% highlight bash %}
# Command [-options] parameters

$> date -s '2009-09-09 09:09:09'
#cal 2009

$> netstat -a
$> ps -aux

# 查看错误信息
# google错误信息和example用法
# 用man/info查说明及用法
{% endhighlight %}


keyboard shortcut
-----

1. tab
1. ctrl + c
1. ctrl + d

Linux 目录和文件相关命令
-----

{% highlight textt %}
文件权限：所属用户(user)、所属组(group)、其他人(other)
drwxr-xr-x   19        user            staff     646     Mar 11 18:39  Books
[属性]       [文件数]  [所属用户]      [所属组]  [大小]  [修改日期]    [文件名]

mtime 修改时间(modify)
ctime 状态变更时间(change)
atime 访问时间(access)
touch 修改文件的3个时间值
chgrp/chown/chmod
{% endhighlight %}

目录相关操作
-----

{% highlight bash %}
./../-/~/~user
pwd/mkdir/rm/ls/cp/mv
{% endhighlight %}

文件相关操作
-----

{% highlight bash %}
cat/tac/nl/more/less/head/tail
man/more/less 过程中可以按h/H帮助键
{% endhighlight %}
