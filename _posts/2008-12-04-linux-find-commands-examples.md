---
layout: post
title: "find command examples related with file status"
date: "Thu Dec 04 2008 11:50:00 GMT+0800 (CST)"
categories: linux
---

find空目录

{% highlight bash %}
$> find . -type d -empty
{% endhighlight %}

`n*24`小时前访问过的文件

{% highlight bash %}
$> find . -atime +2
{% endhighlight %}

`n`分钟前状态发生改变的文件

{% highlight bash %}
$> find . -cmin +10
{% endhighlight %}

`n*24`小时前内容有被修改过的文件

{% highlight bash %}
$> find . -mtime +1
{% endhighlight %}

`n*24`小时与`(n+1)*24`小时这一天中，内容有被修改过的文件(数字n前没有`+`号)

{% highlight bash %}
$> find . -mtime 1
{% endhighlight %}

删除find命令找到的文件

{% highlight bash %}
$> find . -cmin 1 -delete
{% endhighlight %}

这上面的命令中需要注意一点是其中数字前面需要带上`+`号，才表示这个时间点之前的时间段，如果没有提供`+`号，则指这的就限定在这个时间点内，如这一天内或者这一分钟内。

References
-----

1. [http://linux.about.com/od/commands/l/blcmdl1_find.htm](http://linux.about.com/od/commands/l/blcmdl1_find.htm)
