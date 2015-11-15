---
layout: post
title: "linux命令nc使用实例说明"
date: "Mon Apr 18 2011 18:26:00 GMT+0800 (CST)"
categories: linux
---

使用netcat检查服务器的指定的端口是否开通
-----

{% highlight bash %}
root@localhost:~$ nc -z -v 192.168.1.242 80
linux.local [192.168.1.242] 80 (www) open
{% endhighlight %}

使用netcat扫描端口
-----

{% highlight bash %}
root@localhost:~$ nc -z -v -w 1 192.168.1.242 1-100
linux.local [192.168.1.242] 80 (www) open
linux.local [192.168.1.242] 22 (ssh) open
{% endhighlight %}

使用netcat模拟HTTP请求
-----

{% highlight bash %}
root@localhost:~$ nc www.google.com.hk 80
GET / HTTP/1.1
Host: www.google.com.hk
User-Agent: google-chrome9

HTTP/1.1 200 OK
Date: Mon, 18 Apr 2011 08:56:03 GMT
Expires: -1
Cache-Control: private, max-age=0
Content-Type: text/html; charset=Big5
Set-Cookie: PREF=ID=5006f5c292697224:FF=0:NW=1:TM=1303116963:LM=1303116963:S=Lr6ijyXSbmjA6bmZ; expires=Wed, 17-Apr-2013 08:56:03 GMT; path=/; domain=.google.com.hk
Set-Cookie: NID=46=ky6egq_YajJNEzdnM39_2u3CFq2hJLvSVuQm6BokYXSBKhAefFIuL-ZsZOvnDpMISnI2glY25IZxS8_5G0V-EHj-oEc7KRGW4rSZk9yIRnCgsPnm43qhLUMb9hJuKeVW; expires=Tue, 18-Oct-2011 08:56:03 GMT; path=/; domain=.google.com.hk; HttpOnly
Server: gws
X-XSS-Protection: 1; mode=block
Transfer-Encoding: chunked
[......]
{% endhighlight %}
