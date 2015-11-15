---
layout: post
title: "ie6/ie7上ajax请求的一个bug"
date: "Tue Dec 23 2008 20:39:00 GMT+0800 (CST)"
categories: web
---

utf-8编码的页面，在ie6/ie7中用ajax方式和普通直接访问方式请求此url，结果是不一样的：

1. ajax请求：http://localhost/test.php?q=测试中文
2. 地址栏里普通的get请求：http://localhost/test.php?q=测试中文

ajax
-----

服务器端收到的ajax请求，其中的参数`q`的值`测试中文`是以`ISO-8859-1`编码的（浏览器的默认编码方式），所以服务器端收到的参数其实是乱码的，所以ajax请求url中的参数一定要经过encode，除了ie之外，其他浏览器测试下来都会自动url encode其中的参数。

非ajax
-----

{% highlight text %}
http://localhost/test.php?q=%E6%B5%8B%E8%AF%95%E4%B8%AD%E6%96%87
{% endhighlight %}

服务器端收到正常的utf-8编码后的参数。
