---
layout: post
title: "apache2 mod_headers using to modify http response headers"
date: "Tue Apr 08 2008 23:45:00 GMT+0800 (CST)"
categories: linux
---

将mod_headers.so复制到modules目录下，修改httpd.conf文件：

{% highlight bash %}
LoadModule headers_module modules/mod_headers.so
Header add P3P "CP=\"CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR\""
Header set TEST "%D %t"
{% endhighlight %}

其中Header可添加在Context: server config, virtual host, directory, .htaccess

References
-----

1. [http://httpd.apache.org/docs/1.3/mod/mod_headers.html](http://httpd.apache.org/docs/1.3/mod/mod_headers.html)
2. [http://httpd.apache.org/docs/2.2/mod/mod_headers.html](http://httpd.apache.org/docs/2.2/mod/mod_headers.html)
