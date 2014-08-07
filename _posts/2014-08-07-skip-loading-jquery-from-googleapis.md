---
layout: post
title: "从ajax.googleapis.com加载jquery失败"
date: "Thu Aug 07 2014 21:03:43 GMT+0800 (CST)"
categories: howto
---

一些网站jquery.js是从ajax.googleapis.com引用的，但是经常加载jquery.js失败，而导致网页长时间被卡住，如访问[http://api.jquery.com](http://api.jquery.com)时就是这样的情况。需要注意的是，这个解决方法只能处理加载不了jquery.js的问题。

添加这行到/etc/hosts文件中: `127.0.0.1 ajax.googleapis.com`

{% highlight bash %}
$ sudo vi /etc/hosts
{% endhighlight %}

然后在本地的nginx服务中添加一个虚拟主机:

{% highlight nginx %}
server {
    server_name ajax.googleapis.com;
    root /var/www/html/googleapis;

    # http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js
    rewrite ^/.*jquery.*.js$ /jquery-1.9.1.js last;
    break;

    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        expires max;
        log_not_found off;
    }
}
{% endhighlight %}

