---
layout: post
title: "acme网站上几个http小工具"
date: "Sat Jul 17 2010 18:10:00 GMT+0800 (CST)"
categories: web
---

[http_load](http://www.acme.com/software/http_load/)
-----

同`apache ab`和`jmeter`一样可以对数据库和web服务器作压力测试。

{% highlight bash %}
$> vi urls
$> ./http_load -rate 5 -seconds 10 urls
  49 fetches
  2 max parallel
  289884 bytes
  in 10.0148 seconds 5916 mean bytes/connection 4.89274 fetches/sec
  28945.5 bytes/sec msecs/connect: 28.8932 mean
  44.243 max
  24.488 min msecs/first-response: 63.5362 mean
  81.624 max
  57.803 min
  HTTP response codes:   code 200 -- 49
{% endhighlight %}

[http_ping](http://www.acme.com/software/http_ping/)
-----

与`ping`命令类似，但是是通过`http`请求来检查服务器，而非`icmp`请求服务器返回响应。

{% highlight bash %}
$> http_ping http://www.example.com/
  7816 bytes from http://www.example.com/:
  246.602 ms (9.923c/23.074r/213.605d)
  7816 bytes from http://www.example.com/:
  189.997 ms (11.619c/22.971r/155.407d)
  7816 bytes from http://www.example.com/:
  190.463 ms (8.994c/25.091r/156.378d)
  7816 bytes from http://www.example.com/:
  190.07 ms (9.234c/23.9r/156.936d)
  7816 bytes from http://www.example.com/:
  190.706 ms (10.142c/46.579r/133.985d)
  --- http://www.example.com/
  http_ping statistics
  --- 5 fetches started, 5 completed (100%), 0 failures (0%), 0 timeouts (0%)
  total    min/avg/max = 189.997/201.568/246.602 ms
  connect  min/avg/max = 8.994/9.9824/11.619 ms
  response min/avg/max = 22.971/28.323/46.579 ms
  data     min/avg/max = 133.985/163.262/213.605 ms
{% endhighlight %}
