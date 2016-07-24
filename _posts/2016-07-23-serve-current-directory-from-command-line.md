---
layout: post
title: "serve current directory from command line"
date: Sat, 23 Jul 2016 14:40:36 +0800
categories: linux
---

在当前目录下，用简单的命令行启动一个web服务器。

python版本最常用
-----

{% highlight bash %}
$> python -m SimpleHTTPServer 9090
{% endhighlight %}

> Serving HTTP on 0.0.0.0 port 9090 ...
>
> 127.0.0.1 - - [23/Jul/2016 14:48:09] "GET / HTTP/1.1" 200 -
>
> 127.0.0.1 - - [23/Jul/2016 14:48:09] "GET /css/main.css HTTP/1.1" 200 -
>
> 127.0.0.1 - - [23/Jul/2016 14:48:09] "GET /favicon.ico HTTP/1.1" 200 -

ruby版本
-----

{% highlight bash %}
$> ruby -run -e httpd . -p 9090
{% endhighlight %}

> [2016-07-23 14:40:23] INFO  WEBrick 1.3.1
>
> [2016-07-23 14:40:23] INFO  ruby 2.0.0 (2014-05-08) [universal.x86_64-darwin14]
>
> [2016-07-23 14:40:23] INFO  WEBrick::HTTPServer#start: pid=39145 port=9090
>
> localhost - - [23/Jul/2016:14:40:32 CST] "GET / HTTP/1.1" 200 5002
>
> --> /
>
> localhost - - [23/Jul/2016:14:40:32 CST] "GET /css/main.css HTTP/1.1" 200 4123
>
> http://localhost:9090/ -> /css/main.css
>
> localhost - - [23/Jul/2016:14:40:33 CST] "GET /favicon.ico HTTP/1.1" 200 6518
>
> http://localhost:9090/ -> /favicon.ico

nodejs版本
-----

{% highlight bash %}
$> sudo npm install http-server -g
$> http-server -p 9090
{% endhighlight %}

> Starting up http-server, serving ./
>
> Available on:
>
>   http://127.0.0.1:9090
>
> Hit CTRL-C to stop the server

References
-----

1. [SimpleHTTPServer: a quick way to serve a directory](http://www.2ality.com/2014/06/simple-http-server.html)
2. [serve current directory from command line](http://stackoverflow.com/questions/3108395/serve-current-directory-from-command-line)

