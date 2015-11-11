---
layout: post
title: "lighttpd php fastcgi and 500 internal error"
date: "Wed Apr 23 2008 23:59:00 GMT+0800 (CST)"
categories: php
---

当访问php页面时，得到一个500服务器内部错误（500 internal error ），服务器端错误日志如下：

{% highlight tex %}
2008-04-23 23:20:50: (mod_fastcgi.c.1743) connect failed: Connection refused on unix:/tmp/php-fastcgi.socket-3
2008-04-23 23:20:50: (mod_fastcgi.c.2912) backend died; we'll disable it for 5 seconds and send the request to another backend instead: reconnects: 0 load: 1
2008-04-23 23:20:50: (mod_fastcgi.c.2471) unexpected end-of-file (perhaps the fastcgi process died): pid: 350 socket: unix:/tmp/php-fastcgi.socket-3
2008-04-23 23:20:50: (mod_fastcgi.c.3281) response not received, request sent: 872 on socket: unix:/tmp/php-fastcgi.socket-3 for /php/phpinfo.php , closing connection
{% endhighlight %}

用lighttpd fastcgi模式调用php时报以上错误，在lighttpd官方网站上查到，是因为php在编译时默认是未指定`--enable-fastcgi`的, 重新编译了最新版的php5.2.5：

{% highlight bash %}
$> ./configure --prefix=/usr/local/php5 --with-zlib --enable-fastcgi --with-mysql=/usr/local/mysql --enable-mbstring --enable-sockets --enable-gd-native-ttf --with-snmp --enable-soap
$> make
$> sudo make install
{% endhighlight %}

重启lighttpd之后即可正确访问php文件。
