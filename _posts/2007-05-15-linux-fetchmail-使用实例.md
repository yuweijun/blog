---
layout: post
title:  "Linux fetchmail 使用实例"
date: "Tue May 15 2007 22:41:00 GMT+0800 (CST)"
categories: linux
---

fetchmail工具需要一个名为`.fetchmailrc`的配置文件才能正常工作。 这个文件中包含了服务器信息， 以及登录使用的凭据。 由于这个文件包含敏感内容， 建议将其设置为只有属主所有， 使用下面的命令：

{% highlight bash %}
$> chmod 600 .fetchmailrc
{% endhighlight %}

下面的 .fetchmailrc 提供了一个将某一用户的信箱通过 POP 下载到本地的例子。

{% highlight bash %}
poll pop3.163.com proto pop3:
username "test", with password "password", is "yu" here;
{% endhighlight %}

更多的资料可参考官方手册，软件作者Eric Steven Raymond是个大师，出过一篇极有影响力的文章《大教堂与集市》。
