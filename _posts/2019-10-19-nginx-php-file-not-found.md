---
layout: post
title: "nginx中php的文件无法访问问题"
date: "Fri, 19 Oct 2012 18:25:31 +0800"
categories: linux
---

nginx配置文件`fastcgi_params`中缺少以下部分，会造成访问php文件时报`Primary script unknown`错误。

{% highlight text %}
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
{% endhighlight %}

可以用命令看`fastcgi_params`和`fastcgi.conf`这2个文件的区别，注意：`$document_root`和`$fastcgi_script_name`之间没有`/`。

{% highlight bash %}
$> diff fastcgi.conf fastcgi_params
2d1
< fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
{% endhighlight %}

原本nginx只有`fastcgi_params`，后来发现很多人在定义`SCRIPT_FILENAME`时使用了硬编码的方式，于是为了规范用法便引入了`fastcgi.conf`。

不过这样的话就产生一个疑问：为什么一定要引入一个新的配置文件，而不是修改旧的配置文件？

这是因为`fastcgi_param`指令是数组型的，和普通指令相同的是：内层替换外层；和普通指令不同的是：当在同级多次使用的时候，是新增而不是替换。换句话说，如果在同级定义两次`SCRIPT_FILENAME`，那么它们都会被发送到后端，这可能会导致一些潜在的问题，为了避免此类情况，便引入了一个新的配置文件。

References
-----

1. [http://www.tupan.net/changjiandenginxpeizhiwuqu/](http://www.tupan.net/changjiandenginxpeizhiwuqu/)
2. [FASTCGI_PARAMS VERSUS FASTCGI.CONF – NGINX CONFIG HISTORY](https://blog.martinfjordvald.com/2013/04/nginx-config-history-fastcgi_params-versus-fastcgi-conf/)
