---
layout: post
title: "linux命令nc使用说明"
date: "Mon Apr 18 2011 18:26:00 GMT+0800 (CST)"
categories: linux
---

使用netcat检查服务器的指定的端口是否开通
-----

{% highlight bash %}
$> nc -z -v 192.168.1.242 80
linux.local [192.168.1.242] 80 (www) open
{% endhighlight %}

使用netcat扫描端口
-----

{% highlight bash %}
$> nc -z -v -w 1 192.168.1.242 1-100
linux.local [192.168.1.242] 80 (www) open
linux.local [192.168.1.242] 22 (ssh) open
{% endhighlight %}

使用netcat模拟HTTP请求
-----

{% highlight bash %}
$> nc www.google.com.hk 80
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

netcat参数说明
-----

{% highlight text %}
语　　法：nc [-hlnruz][-g<网关...>][-G<指向器数目>][-i<延迟秒数>][-o<输出文件>][-p<通信端口>][-s<来源位址>][-v...][-w<超时秒数>][主机名称][通信端口...]
参　　数：
  -g<网关>  设置路由器跃程通信网关，最多可设置8个。
  -G<指向器数目>  设置来源路由指向器，其数值为4的倍数。
  -h   在线帮助。
  -i<延迟秒数>  设置时间间隔，以便传送信息及扫描通信端口。
  -l   使用监听模式，管控传入的资料。
  -n   直接使用IP地址，而不通过域名服务器。
  -o<输出文件>  指定文件名称，把往来传输的数据以16进制字码倾倒成该文件保存。
  -p<通信端口>  设置本地主机使用的通信端口。
  -r   乱数指定本地与远端主机的通信端口。
  -s<来源位址>  设置本地主机送出数据包的IP地址。
  -u   使用UDP传输协议。
  -v   显示指令执行过程。
  -w<超时秒数>  设置等待连线的时间。
  -z   使用0输入/输出模式，只在扫描通信端口时使用。
{% endhighlight %}
