---
layout: post
title: "apache2.2日志中的一些注意事项"
date: "Tue Dec 20 2011 11:37:00 GMT+0800 (CST)"
categories: linux
---

出于安全考虑，从2.0.46版本开始，`%r`，`%i`，`%o`中的特殊字符，除了双引号(`"`)和反斜线(`\`)分别用`\"`和`\\`进行转义、空白字符用C风格(`\n`，`\t`等)进行转义以外，非打印字符和其它特殊字符使用`\xhh`格式进行转义(hh是该字符的16进制编码)。

在2.0.46以前的版本中，这些内容会被完整的按原样记录。这种做法将导致客户端可以在日志中插入控制字符，所以你在处理这些日志文件的时候要特别小心。

在2.0版本中(不同于1.3)，`%b`和`%B`格式字符串并不表示发送到客户端的字节数，而只是简单的表示HTTP应答字节数(在连接中断或使用SSL时与前者有所不同)。`mod_logio`提供的`%O`格式字符串将会记录发送的实际字节数。

{% highlight java %}
String url = "http://www.baidu.com/s?cl=3&wd=\\xe6\\x90\\x9e\\xe7\\xac\\x91\\xe6\\xbc\\xab\\xe7\\x94\\xbb\\xe9\\x9b\\x86";
String replaced = url.replaceAll("\\\\x", "%");
String decode = URLDecoder.decode(replaced, "utf-8");
{% endhighlight %}
