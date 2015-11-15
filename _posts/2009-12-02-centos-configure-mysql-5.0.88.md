---
layout: post
title: "在64位的centos5中编译安装mysql-5.0.88"
date: "Wed Dec 02 2009 14:42:00 GMT+0800 (CST)"
categories: mysql
---

mysql-5.0.88编译安装
-----

{% highlight bash %}
$> wget http://dev.mysql.com/get/Downloads/MySQL-5.0/mysql-5.0.88.zip/from/http://mysql.byungsoo.net/
$> yum -y install gcc
$> yum -y install gcc-c++
$> yum -y install ncurses-devel

$> CC=gcc \
CFLAGS="-O3 -fno-omit-frame-pointer" \
CXX=gcc \
CXXFLAGS="-O3 -fno-omit-frame-pointer -felide-constructors -fno-exceptions -fno-rtti" \
./configure --prefix=/usr/local/mysql --without-debug --with-big-tables --with-unix-socket-path=/tmp/mysql.sock --with-client-ldflags=-all-static --with-mysqld-ldflags=-all-static --enable-assembler --with-extra-charsets=gbk,gb2312,utf8 --with-pthread --enable-thread-safe-client

$> make && make install
{% endhighlight %}

References
-----

1. [http://dev.mysql.com/doc/refman/5.0/en/linux-ia-64.html](http://dev.mysql.com/doc/refman/5.0/en/linux-ia-64.html)

