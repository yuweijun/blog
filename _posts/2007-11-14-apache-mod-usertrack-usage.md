---
layout: post
title: "apache mod_usertrack 使用及其问题"
date: "Wed Nov 14 2007 15:59:00 GMT+0800 (CST)"
categories: linux
---

为apache增加mod_usertrack模块，只要动态生成一个so文件在配置文件里包括进来即可，之前有写了个文章关于这个操作过程。

在虚拟主机的配置里再加上以下配置，其中具体的参数含义可参考此链接：[mod_usertrack](http://httpd.apache.org/docs/1.3/mod/mod_usertrack.html)

{% highlight bash %}
CookieTracking on
CookieStyle Cookie
CookieExpires "2 weeks"
CookieDomain .foo.bar
CustomLog logs/cookie-track.log "%{cookie}n %h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\""
CookieName FOO_BAR
{% endhighlight %}

重启apache即可。

这个模块在Mozilla/Firefox中工作一切正常，而且可以做第3方的Cookie跟踪。

但是在IE（仅测试了IE6和IE7）中这个Cookie工作机制却类似于phpsessionid的工作机制，关闭IE重开之后却重新生了一个Cookie，如果不关则能正常记录用户的clickstream。但是从客户端查看HttpResponse则看得出来服务器端返回来的是正常的请求，是让IE写个2周的Cookie，不过事实IE并不这样工作。至于用作第3方Cookie跟踪就更是不行，受P3P策略限制。
