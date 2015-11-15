---
layout: post
title: "installing php xdebug on mac os x leopard"
date: "Thu Mar 05 2009 00:02:00 GMT+0800 (CST)"
categories: php
---

install php xdebug
-----

{% highlight bash %}
$> curl -O http://xdebug.org/files/xdebug-2.0.4.tgz
$> tar zxvf ./xdebug-2.0.4.tgz
$> cd ./xdebug-2.0.4/xdebug-2.0.4
$> phpize
$> MACOSX_DEPLOYMENT_TARGET=10.5 CFLAGS="-arch ppc -arch ppc64 -arch i386 -arch x86_64 -g -Os -pipe -no-cpp-precomp" CCFLAGS="-arch ppc -arch ppc64 -arch i386 -arch x86_64 -g -Os -pipe" CXXFLAGS="-arch ppc -arch ppc64 -arch i386 -arch x86_64 -g -Os -pipe" LDFLAGS="-arch ppc -arch ppc64 -arch i386 -arch x86_64 -bind_at_load" ./configure --enable-xdebug
$> make
$> sudo make install
$> sudo cp modules/xdebug.so /usr/lib/php/extensions/no-debug-non-zts-20060613/
$> sudo cp /etc/php.ini.default /etc/php.ini
$> sudo vi /etc/php.ini
; append below lines to file bottom of /etc/php.ini

zend_extension=/usr/lib/php/extensions/no-debug-non-zts-20060613/xdebug.so
[xdebug]
xdebug.file_link_format="txmt://open?url=file://%f&line=%l"
xdebug.remote_enable=1
xdebug.remote_host=localhost
xdebug.remote_port=9000
xdebug.remote_autostart=1

$> php -m
You should see xdebug module in list.
$> sudo apachectl restart
{% endhighlight %}

`phpinfo` will output `xdebug` module.

After `xdebug` installed, then install `PDT` for eclipse, or download `MacGDBp`, php can debug using bleakpoint.

References
-----

1. [http://jamesangus.ucantblamem.com/programming/installing-php-xdebug-on-mac-os-x-leopard/214/](http://jamesangus.ucantblamem.com/programming/installing-php-xdebug-on-mac-os-x-leopard/214/)
2. [http://developers.sugarcrm.com/wordpress/2008/11/25/enabling-xdebug-under-os-x-leopard/](http://developers.sugarcrm.com/wordpress/2008/11/25/enabling-xdebug-under-os-x-leopard/)
