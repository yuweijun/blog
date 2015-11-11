---
layout: post
title: "redirect stdout in ruby"
date: "Thu Mar 13 2008 13:45:00 GMT+0800 (CST)"
categories: css
---

若想对标准输入、输出、错误输出等进行重定向(redirect)时，可以使用`IO#reopen`:

{% highlight ruby %}
STDOUT.reopen("/tmp/foo", "a+")
{% endhighlight %}

不建议使用:

{% highlight ruby %}
$stdout = File.open("/tmp/foo", "a+")
{% endhighlight %}

这还有个输出缓存的过程，需要用`IO#sync（IO#fsync`或者是`IO#flush`才会将输入写入log中。
