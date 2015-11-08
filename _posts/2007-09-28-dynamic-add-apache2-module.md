---
layout: post
title: "动态增加apache2的模块"
date: "Fri Sep 28 2007 15:11:00 GMT+0800 (CST)"
categories: linux
---

为已经在运行的apache2新加一个模块。

{% highlight bash %}
$> cd httpd－2.2.4
$> ./configure --prefix=/usr/local/apache2 --enable-so --enable-mods-shared=all --enable-modules=all
{% endhighlight %}

这里需要自己将需要enable的module一起列在configure中，如--enable-proxy --enable-proxy-http --enable-proxy-ftp等，才会生成对应的so文件

{% highlight bash %}
$> make
$> find . -name "*.so"
{% endhighlight %}

将找到的需要的so文件，复制到apache的modules目录下，然后修改http.conf文件，Load这些需要的Modules
