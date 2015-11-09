---
layout: post
title: "mysql configure 安装参数及其查看方法"
date: "Thu May 24 2007 22:23:00 GMT+0800 (CST)"
categories: mysql
---

在bash里运行：

{% highlight bash %}
$> export VISUAL=vi
{% endhighlight %}

然后直接运行：

{% highlight bash %}
$> bin/mysqlbug
{% endhighlight %}

可以在vim里看到最后一行里的configure line，就是configure安装时的参数。

MySQL AB提供的二进制文件configure参数：

Linux 2.4.xx x86中的gcc 2.95.3：

{% highlight bash %}
CFLAGS="-O2 -mcpu=pentiumpro" CXX=gcc CXXFLAGS="-O2 -mcpu=pentiumpro -felide-constructors" ./configure --prefix=/usr/local/mysql --with-extra-charsets=complex --enable-thread-safe-client --enable-local-infile --enable-assembler --disable-shared --with-client-ldflags=-all-static --with-mysqld-ldflags=-all-static
{% endhighlight %}

Mac OS X 10.x：

{% highlight bash %}
CC=gcc CFLAGS="-O3 -fno-omit-frame-pointer" CXX=gcc \
CXXFLAGS="-O3 -fno-omit-frame-pointer -felide-constructors \
-fno-exceptions -fno-rtti" \
./configure --prefix=/usr/local/mysql \
--with-extra-charsets=complex --enable-thread-safe-client \
--enable-local-infile --disable-share
{% endhighlight %}

如果编译静态链接程序(例如，制作二进制分发版、获得更快的速度或与解决与RedHat分发版的一些问题)，configure 里加上：

{% highlight bash %}
$> ./configure --with-client-ldflags=-all-static --with-mysqld-ldflags=-all-static
{% endhighlight %}

如果正在使用gcc并且没有安装libg++或libstdc++，可以告诉configure使用gcc作为C++编译器：

{% highlight bash %}
$> CC=gcc CXX=gcc ./configure
{% endhighlight %}

当使用gcc作为C++编译器用时，它将不试图链接libg++或libstdc++。即使安装了这些库，这样也很好，因为过去使用MySQL时，它们的部分版本会出现一些奇怪的问题。

如果在x86机器上运行Linux，在大多数情况下最好使用Mysql提供的二进制文件。Mysql已经将二进制连接到了Mysql能找到的打了最好补丁的glibc版本，并使用了最优的编译器选项，尽力使它适合高负荷服务器。对于典型用户，即使对于超过2GB限制的大量并行连接或表设置，在大多数情况下，Mysql的二进制仍然是最佳选择。如果不清楚怎样做，先试用Mysql的二进制看它是否满足需求。如果发现它不够完善，那么可以尝试自己构建。

当开始自己make二进制文件时,如果使用的gcc版本足够新，并可以识别-fno-exceptions选项，则configure时要使用该选项。否则，编译二进制时可能会出现问题。Mysql建议同时使用-felide-constructors和-fno-rtti选项。执行下面操作：

{% highlight bash %}
$> CFLAGS="-O3 -mpentiumpro" CXX=gcc CXXFLAGS="-O3 -mpentiumpro \
-felide-constructors -fno-exceptions -fno-rtti" ./configure \
--prefix=/usr/local/mysql \
--without-debug \
--with-charset=gbk \
--with-extra-charsets=complex \
--enable-thread-safe-client \
--enable-local-infile \
--enable-assembler \
--disable-shared \
--with-bdb \
--with-innodb \
--with-client-ldflags=-all-static \
--with-mysqld-ldflags=-all-static \
--with-big-tables \
--with-ndbcluster \
--with-ndb-port \
--with-ndb-port-base \
--without-ndb-debug
{% endhighlight %}

配置并编译完源码分发后，便开始安装。默认情况下，可以将文件安装到/usr/local/mysql，生成：

* bin
* include/mysql
* info
* lib/mysql
* libexec
* share/mysql
* sql-bench
* var

在一个安装目录内，源码安装的布局在下列方面不同于二进制安装：

1. mysqld服务器被安装在“libexec”目录而不是“bin”目录内。
2. 数据目录是“var”而非“data”。
3. mysql_install_db被安装在“bin”目录而非“scripts”内。
4. 头文件和库目录是“include/mysql”和“lib/mysql”而非“include”和“lib”。
