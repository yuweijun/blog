---
layout: post
title: "document referer and referrer"
date: "Thu Dec 20 2007 09:35:00 GMT+0800 (CST)"
categories: javascript
---

在http中`referer`这个词错误拼写是个历史原因，在不同的环境下，写法并不相同，如php和javascript中就不一样

http request referer:
-----

{% highlight http %}
Referer http://www.google.cn/search?hl=zh-CN&q=%E6%B5%B7%E8%AF%8D&btnG=Google+%E6%90%9C%E7%B4%A2&meta=
{% endhighlight %}

php
-----

{% highlight php %}
$_SERVER['http_referer']
{% endhighlight %}

javascript
-----

{% highlight javascript %}
document.referrer
{% endhighlight %}
