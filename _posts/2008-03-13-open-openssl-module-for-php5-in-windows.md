---
layout: post
title: "open openssl module for php5 in windows"
date: "Thu Mar 13 2008 19:08:00 GMT+0800 (CST)"
categories: php
---

控制台看到如下警告
=====

{% highlight text %}
Warning: SoapClient::SoapClient() [function.SoapClient-SoapClient]: I/O warning : failed to load external entity "http://localhost:9090/test?wsdl" in test.php on line 13
{% endhighlight %}

google了一下，是因为PHP的openssl模块没有打开。

修改php.ini文件，去掉extension=php_openssl.dll前面的注释。

将PHP安装目录下的libeay32.dll 拷贝一份到system32目录下，重启Apache即可。

如果不行可以把ssleay32.dll也拷过去重启。

完成后可以在phpinfo()中看到类似消息如下：

{% highlight bash %}
openssl
OpenSSL support enabled
OpenSSL Version OpenSSL 0.9.8e 23 Feb 2007
{% endhighlight %}
