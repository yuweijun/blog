---
layout: post
title: "cleaning up rails out of date session files"
date: "Fri Feb 22 2008 11:31:00 GMT+0800 (CST)"
categories: ruby
---

参数说明：
-----

find -ctime n: 文件修改时间n*24小时，+2即文件最后修改时间大于2天，此session已经过期。

xargs command: 执行命令。

{% highlight bash %}
$> cd rails_app
$> find tmp/sessions/ruby_sess* -ctime +2 | xargs rm -f
{% endhighlight %}
