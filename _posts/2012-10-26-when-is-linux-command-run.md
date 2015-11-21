---
layout: post
title: "linux查看某个进程的启动时间"
date: "Fri, 26 Oct 2012 17:09:27 +0800"
categories: linux
---

linux
-----

{% highlight bash %}
$> ps -eo pid,lstart,comm|grep nginx
  939 Mon Oct 22 09:57:15 2012 nginx
  940 Mon Oct 22 09:57:15 2012 nginx
  941 Mon Oct 22 09:57:15 2012 nginx
  943 Mon Oct 22 09:57:15 2012 nginx
  944 Mon Oct 22 09:57:15 2012 nginx
{% endhighlight %}

Mac OS X
-----

{% highlight bash %}
$> ps -eo pid,lstart,comm|grep nginx
   64 Mon Oct  22 11:54:11 2012     nginx: master process /usr/local/bin/nginx -g daemon off;
  928 Mon Oct  22 12:36:56 2012     nginx: worker process
{% endhighlight %}
