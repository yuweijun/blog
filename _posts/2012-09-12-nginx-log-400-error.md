---
layout: post
title: "chrome造成nginx访问日志中大量400错误"
date: "Wed, 12 Sep 2012 16:45:23 +0800"
categories: linux
---

服务器中的错误记录类似于这种：

{% highlight text %}
127.0.0.1 - - [01/Oct/2011:11:51:04 +0800] "-" 400 0 "-" "-" "-"
{% endhighlight %}

这个问题都是发生在google chrome浏览器访问之后产生的，也就是说400错误是由chrome浏览器产生的。

通过smartsniff抓取了一下tcp的包，如图所示，注意选中的那三条记录的字节数，原来chrome发送了一条无http请求头的空连接给服务器，所以服务器自然返回400了，
其原因与chrome的预先连接`pre-connection`这项技术有关系。

![chrome-smartsniff-tcp-jpg]({{ site.baseurl }}/img/web/chrome/chrome-smartsniff-tcp.jpg)

[stackexchange.com](http://webmasters.stackexchange.com/questions/23584/why-there-suddenly-were-so-many-400-request-in-my-access-log)上也有人做了如下回复：

{% highlight text %}
Check and see if the ip address causing the 400 is using Google Chrome. Chrome uses pre-connection to establish several connection with server, and close them if not used.

Since no request is made in the connection, nginx will record this error.
{% endhighlight %}

测试
-----

要验证上面的分析结果很简单，打开命令行cmd.exe，在里面输入`telnet server_ip 80`，等待连接成功之后直接关掉cmd，这时去查看nginx的log文件中就多了一条400错误记录。

关闭默认主机的日志记录就可以解决问题:

{% highlight text %}
server {
    listen *:80 default;
    server_name _;
    return 444;
    access_log   off;
}
{% endhighlight %}

References
-----

1. [Nginx 日志中神秘的 HTTP 400 错误](http://chaoskeh.com/blog/nginx-400-bad-request-error-reason.html)
1. [从 nginx 访问日志中的400错误说起](http://www.oschina.net/question/12_34650)
1. [Why there suddenly were so many 400 request in my access log](http://webmasters.stackexchange.com/questions/23584/why-there-suddenly-were-so-many-400-request-in-my-access-log)
