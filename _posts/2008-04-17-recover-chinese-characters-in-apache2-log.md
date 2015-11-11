---
layout: post
title: "apache2 log中的url查询字符串中文乱码"
date: "Thu Apr 17 2008 16:56:00 GMT+0800 (CST)"
categories: linux
---

当IE在访问如下url
-----

{% highlight tex %}
http://localhost/a.html?q=中文
{% endhighlight %}

在apache2 log里能看到以下内容
-----

{% highlight tex %}
GET /a.html?q=\xd6\xd0\xce\xc4 HTTP/1.1
{% endhighlight %}

而在firefox中访问相同url
-----

日志中看到的内容是对字符串进行urlencode过后的格式：

{% highlight tex %}
GET /a.html?q=%D6%D0%CE%C4 HTTP/1.1
{% endhighlight %}

现在IE下，`\xd6`在\x后面的`d6`是个16进制值，如果想将`\xd6\xd0\xce\xc4`直接还原出`中文`2个字比较困难。

不过在与firefox中的日志对比之下，能看出只要直接将\x替换成%，然后用php的`urldecode`反解析就能得到对应的字符串。

另外说明一点
-----

这个例子中看到的值`%D6%D0%CE%C4`，是按`gbk`字符集进行`urlencode`计算得到的对应值，如果`urldecode`反解析得到是乱码，则日志记录的应该是`utf-8`格式的`urlencode`值，用php的`iconv`函数处理一下。
