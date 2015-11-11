---
layout: post
title: "http request and response headers examples using telnet"
date: "Tue Apr 08 2008 23:48:00 GMT+0800 (CST)"
categories: http
---

使用telnet进行http模拟请求。

{% highlight bash %}
$> telnet www.google.com 80
Trying 64.233.189.99...
Connected to www.l.google.com.
Escape character is '^]'.
GET /intl/en_ALL/images/logo.gif HTTP/1.1
Host: www.google.com
User-Agent: Mozilla/5.0(Robots) Gecko/20061206 Firefox/1.5.0.9
Accept-Encoding: gzip,deflate

HTTP/1.1 200 OK
Content-Type: image/gif
Last-Modified: Wed, 07 Jun 2006 19:38:24 GMT
Expires: Sun, 17 Jan 2038 19:14:07 GMT
Server: gws
Content-Length: 8558
Age: 13
Date: Sun, 06 Apr 2008 06:51:56 GMT

GIF89an.....
{% endhighlight %}

{% highlight bash %}
$> telnet www.google.com 80
Trying 64.233.189.104...
Connected to www.l.google.com.
Escape character is '^]'.
GET /intl/en_ALL/images/logo.gif HTTP/1.1
Host: www.google.com
User-Agent: Mozilla/5.0(Robots) Gecko/20061206 Firefox/1.5.0.9
Accept-Encoding: gzip,deflate
If-Modified-Since: Wed, 07 Jun 2006 19:38:24 GMT

HTTP/1.1 304 Not Modified
Date: Sun, 06 Apr 2008 06:53:10 GMT
Server: GFE/1.3
{% endhighlight %}

{% highlight bash %}
$> telnet l.yimg.com 80
Trying 203.209.246.249...
Connected to geoycs-l.yahoo8.akadns.net.
Escape character is '^]'.
GET /a/i/ww/news/2008/04/04/crocodile-sm.jpg HTTP/1.1
Host: l.yimg.com
User-Agent: Mozilla/5.0(Robots) Gecko/20061206 Firefox/1.5.0.9
Accept-Encoding: gzip,deflate
Connection: keep-alive

HTTP/1.1 200 OK
Date: Sat, 05 Apr 2008 00:14:27 GMT
Cache-Control: max-age=315360000
Expires: Tue, 03 Apr 2018 00:14:27 GMT
Last-Modified: Fri, 04 Apr 2008 17:49:26 GMT
Accept-Ranges: bytes
Content-Length: 860
Content-Type: image/jpeg
Age: 133250
Connection: keep-alive
Server: YTS/1.16.0



Connection closed by foreign host.
{% endhighlight %}


In HTTP/1.1 the ETag and If-None-Match headers are another way to make conditional GET requests.

{% highlight html %}
<img alt="red star" src="data:image/gif;base64,R0lGODlhDAAMALMLAPN8ffBiYvWW
lvrKy/FvcPewsO9VVfajo+w6O/zl5estLv/8/AAAAAAAAAAAAAAAACH5BAEA
AAsALAAAAAAMAAwAAAQzcElZyryTEHyTUgknHd9xGV+qKsYirKkwDYiKDBia
tt2H1KBLQRFIJAIKywRgmhwAIlEEADs=">
{% endhighlight %}
