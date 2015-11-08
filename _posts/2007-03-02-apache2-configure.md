---
layout: post
title: "apache2 configure"
date: "Fri Mar 02 2007 22:41:00 GMT+0800 (CST)"
categories: apache2
---

apache-2.2.4 configure parameters:
-----

{% highlight bash %}
./configure \
--prefix=/usr/local/apache2 \
--enable-so \
--enable-rewrite \
--enable-module=most \
--enable-shared=max \
--enable-cgi \
--enable-mime-magic \
--enable-dav \
--enable-dav-fs \
--enable-maintainer-mode \
--enable-ssl \
--enable-proxy \
--enable-proxy-connect \
--enable-proxy-ftp \
--enable-proxy-http \
--enable-proxy-balancer \
--with-included-apr
{% endhighlight %}
