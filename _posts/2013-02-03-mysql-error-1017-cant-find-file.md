---
layout: post
title: "mysql error 1017"
date: "Sun, 03 Feb 2013 10:35:38 +0800"
categories: mysql
---

mysql日志抛出如下错误：

{% highlight text %}
ERROR 1017 (HY000): Can’t find file:   (errno: 13)
{% endhighlight %}

原因是对mysql的数据库教程文件没有读写权限。

以ubuntu系统为例子，一般数据文件放在`/var/lib/mysql`下面，数据库目录的权限是`700(rwx——)`，所有者是`mysql`，数据库目录下面的文件权限是`660(rw-rw—)`, 所有者也是`mysql`。

如果重启服务器前没有关闭mysql，mysql的myisam表很有可能会出现`ERROR #1017 :Can't find file: '/xxx.frm'`的错误。

出现这个问题的原因不是`/xxx.frm`这个文件不见了，而是这些文件的所有者权限(应该要是mysql)不知道为什么变成了root。

解决方法
-----

切到`xxx.frm`文件所在目录下，执行一下命令：

{% highlight bash %}
$> chown -R mysql.mysql *
{% endhighlight %}

将所有文件的权限都改过来就可以了！

附mysql-5.0.37编译参数
-----

{% highlight bash %}
$> ./configure --prefix=/usr/local/mysql/5.0.37 --without-debug --with-big-tables --with-unix-socket-path=/tmp/mysql.sock --with-client-ldflags=-all-static --with-mysqld-ldflags=-all-static --enable-assembler --with-extra-charsets=gbk,gb2312,utf8 --with-pthread --enable-thread-safe-client
{% endhighlight %}
