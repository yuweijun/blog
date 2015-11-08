---
layout: post
title: "rpm查询文件所属的rpm包"
date: "Mon Oct 22 2007 11:33:00 GMT+0800 (CST)"
categories: linux
---

rpm根据命令查询所在包名

{% highlight bash %}
$> rpm -qf /usr/bin/crontab
vixie-cron-4.1-66.1.el5
{% endhighlight %}

没找到crontab命令，可以用yum直接在线安装

{% highlight bash %}
$> yum install vixie-cron
{% endhighlight %}
