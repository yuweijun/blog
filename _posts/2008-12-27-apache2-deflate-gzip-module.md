---
layout: post
title: "添加deflate gzip模块到apache2服务器"
date: "Sat Dec 27 2008 14:45:00 GMT+0800 (CST)"
categories: linux
---

compile deflate gzip module for apache2
-----

{% highlight bash %}
$> ./configure --prefix=/usr/local/apache2 --enable-so --enable-mods-shared=all --enable-modules=all
Configuring Apache Portable Runtime Utility library...

checking for APR-util... yes
configure: error: Cannot use an external APR-util with the bundled APR
{% endhighlight %}

如果编译过程中发现APR-util的错误，可以加上--with-included-apr这个参数，如下：

{% highlight bash %}
$> ./configure --prefix=/usr/local/apache2 --enable-so --enable-mods-shared=all --enable-modules=all --with-included-apr
$> make
$> cp ./modules/filters/.libs/mod_deflate.so /usr/local/apache2/modules
$> vi /usr/local/apache2/conf/httpd.conf
# append 2 line to httpd.conf
LoadModule deflate_module modules/mod_deflate.so
AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/x-json application/x-javascript
$> /usr/local/apache2/bin/apachectl graceful
{% endhighlight %}

更多关于`deflate module`的设置可参考[官方说明](http://httpd.apache.org/docs/2.0/mod/mod_deflate.html)

References
-----

1. [http://yuweijun.blogspot.com/2007/09/apache22module.html](http://yuweijun.blogspot.com/2007/09/apache22module.html)
