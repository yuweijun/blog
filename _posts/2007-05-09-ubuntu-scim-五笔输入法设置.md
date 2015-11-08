---
layout: post
title: "Ubuntu scim 五笔输入法设置"
date: "Wed May 09 2007 10:53:00 GMT+0800 (CST)"
categories: ubuntu
---

默认安装后SCIM无法调出五笔，需要安装中文支持，在新立得和Administrator->Language support里安装或者是直接在命令行里输入:

{% highlight bash %}
$> apt-get install language-support-zh
{% endhighlight %}

如果报错说找不到包language-support-zh时，可以到网上到一些中文的sourses.lists更新源文件后运行:

{% highlight bash %}
$> sudo apt-get update
{% endhighlight %}

然后再到Administrator->Language support里安装中文字体。

安装字体库:

{% highlight bash %}
$> apt-get install ttf-bitstream-vera ttf-arphic-*
{% endhighlight %}

之后修改/etc/environment这个配置文件如下：

{% highlight bash %}
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games"
LANGUAGE="en_US:en"
LANG="zh_CN.UTF-8"
LC_ALL="zh_CN.UTF-8"
#GST_ID3_TAG_ENCODING="GBK"
{% endhighlight %}

这样注销重新登录即可看到输入法，并且系统托盘看得到一个键盘标志。
不过这样子原来会转为中文系统，如果不想改变系统语言属性，则可以在Language support里点选上Input method下面的checkbox，注销后再进就能看到这个键盘标志，scim已经可以使用。
