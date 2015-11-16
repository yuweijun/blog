---
layout: post
title: "apache2全站重定向配置"
date: "Mon, 23 Jul 2012 14:09:00 +0800"
categories: linux
---

网站域名修改后，需要整个网站永久301重定向。

{% highlight text %}
RedirectMatch Permanent ^/(.*) http://www.another-domain.com/$1
{% endhighlight %}
