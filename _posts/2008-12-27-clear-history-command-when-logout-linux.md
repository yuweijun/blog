---
layout: post
title: "clear history command when logout linux"
date: "Sat Dec 27 2008 15:23:00 GMT+0800 (CST)"
categories: linux
---

history -c
-----

{% highlight bash %}
$> vi .bash_logout
# ~/.bash_logout

history -c
clear
{% endhighlight %}

References
-----

1. [http://rcsg-gsir.imsb-dsgi.nrc-cnrc.gc.ca/documents/advanced/node125.html](http://rcsg-gsir.imsb-dsgi.nrc-cnrc.gc.ca/documents/advanced/node125.html)
