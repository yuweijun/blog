---
layout: post
title: "cronolog 设置定期轮循日志"
date: "Mon Apr 23 2007 16:21:00 GMT+0800 (CST)"
categories: linux
---

点击[下载](http://cronolog.org/download/index.html)安装`cronolog`。

假设安装目录为：`/usr/local/cronolog`

{% highlight bash %}
$ cat access_log |/usr/local/cronolog/sbin/cronolog -p 12hours /home/test/%Y-%m-%d.log
{% endhighlight %}

无提示，正确在/home/test/目录下生成Log文件。

{% highlight bash %}
$ cat access_log |/usr/local/cronolog/sbin/cronolog -p 13hours /home/test/%Y-%m-%d.log
{% endhighlight %}

提示：`/usr/local/cronolog/sbin/cronolog: invalid explicit period specification ((null))`

这个需要看一下Cronolog文档说明，如下：

{% highlight text %}
-p PERIOD
--period=PERIOD

specifies the period explicitly as an optional digit string followed by one of units: seconds, minutes,
hours, days, weeks or months. The count cannot be greater than the number of units in the
next larger unit, i.e. you cannot specify "120 minutes", and for seconds, minutes and hours the
count must be a factor of the next higher unit, i.e you can specify 1, 2, 3, 4, 5, 6, 10, 15, 20 or 30
minutes but not say 7 minutes.
{% endhighlight %}

假设需要每4小时导出一次Log，则在http.conf里设置：

{% highlight bash %}
CustomLog "|/usr/local/cronolog/sbin/cronolog -p 4hours /home/test/%Y-%m-%d-%H.log" common
{% endhighlight %}

注释原来apache配置文件中的CustomLog那一行。
