---
layout: post
title: "tomcat get请求的url支持中文文件名"
date: "Fri Jan 28 2011 15:53:00 GMT+0800 (CST)"
categories: java
---

在tomcat的默认配置情况下，如果访问一个"中文.html"页面，服务器会抛出文件找不到的错误提示，这需要对tomcat的配置文件略作改动，为Connector设置`URIEncoding`的参数。

在server.xml文件中找到8080服务器配置的一段设置：

{% highlight html %}
<Connector port="8080" maxThreads="150" minSpareThreads="25" maxSpareThreads="75" enableLookups="false" redirectPort="8443" acceptCount="100" debug="0" connectionTimeout="20000" disableUploadTimeout="true" URIEncoding="UTF-8"/>
{% endhighlight %}

加上`URIEncoding="UTF-8"`这句就可以识别中文文件名了，同时如果url中的参数值有中文，服务器端也可以正常解析对应的参数，否则服务器端Java程序中取到的参数是以`ISO-8859-1`编码的，有中文字符的参数值是乱码的，加了`URIEncoding="UTF-8"`之后，就可以正确获取到参数值了。

对于这个参数的说明，可以查看tomcat目录中的doc帮助文件，也可以从官网中找到此参数的解释。

References
-----

1. [官方文档](http://tomcat.apache.org/tomcat-7.0-doc/config/http.html)
2. [Configure tomcat's uri encoding](http://confluence.atlassian.com/display/DOC/Configuring+Tomcat's+URI+encoding)

