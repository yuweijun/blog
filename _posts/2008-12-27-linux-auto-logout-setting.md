---
layout: post
title: "linux auto logout setting"
date: "Sat Dec 27 2008 14:53:00 GMT+0800 (CST)"
categories: linux
---

如果是针对所有用户，则修改/etc/profile文件。

{% highlight bash %}
$> cd
$> vi .profile
# user will be auto logout after 300 seconds.
export TMOUT=300
{% endhighlight %}
