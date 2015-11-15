---
layout: post
title: "configure php xdebug in ubuntu-11.04"
date: "Wed May 18 2011 17:25:00 GMT+0800 (CST)"
categories: php
---

先安装php和xdebug，并配置php.ini文件。

{% highlight bash %}
$> sudo apt-get install apache2 php5 xdebug
$> sudo vi /etc/php5/apache2/php.ini
{% endhighlight %}

在php.ini文件最后面添加下面3行内容，其他如`zend_extension`的值ubuntu在xdebug安装完会自动在`/etc/php5/conf.d/xdebug.ini`中设置:

{% highlight text %}
[Xdebug]
xdebug.remote_autostart=On
xdebug.remote_enable=On
{% endhighlight %}

然后从eclipse.org官网下载eclipse for php版本：

我的电脑是`ubuntu-11.04 64位`的机器，所以下载64位的版本：

{% highlight bash %}
$> wget http://www.eclipse.org/downloads/download.php?file=/technology/epp/downloads/release/helios/SR2/eclipse-cpp-helios-SR2-linux-gtk-x86_64.tar.gz&url=http://mirrors.ustc.edu.cn/eclipse/technology/epp/downloads/release/helios/SR2/eclipse-cpp-helios-SR2-linux-gtk-x86_64.tar.gz&mirror_id=1093
{% endhighlight %}

打开eclipse之后，进入`window-Performances-PHP-Debug`设置面板，在右边的`PHP Debuger`中选择Xdebug，然后点击`Xdebug`的Configure，再选择其中的`Xdebug`进行编辑，将`Accept remote session(JIT)`的值设置为`localhost`，(默认值为off时，是不会开启xdebug调试功能的)，这个设置和`php.ini`设置必须要都做到才会开启debug功能。

这样配置就完成了，可以打开浏览器访问PHP页面，在eclipse中的PHP源码，如果有设置断点就会进入Debug模式。

在Debug模式中的Server就使用默认的http://localhost，"PHP Executeable"原来是"None Defined"，这个没有关系，也可以手动设置一个值，如`/usr/bin/php5`，这个不影响debug功能。

抄录部分xdebug远程调试相关的参数说明，官网可参考http://xdebug.org/docs/remote中的说明。

{% highlight text %}
xdebug.remote_autostart
类型：布尔型 默认值：0
{% endhighlight %}

一般来说，你需要使用明确的HTTP GET/POST变量来开启远程debug。而当这个参数设置为On，xdebug将经常试图去开启一个远程debug session并试图去连接客户端，即使GET/POST/COOKIE变量不是当前的。

{% highlight text %}
xdebug.remote_enable
类型：布尔型 默认值：0
{% endhighlight %}

这个开关控制xdebug是否应该试着去连接一个按照xdebug.remote_host和xdebug.remote_port来设置监听主机和端口的debug客户端。

{% highlight text %}
xdebug.remote_host
类型：字符串 默认值：localhost
{% endhighlight %}

选择debug客户端正在运行的主机，你不仅可以使用主机名还可以使用IP地址

{% highlight text %}
xdebug.remote_port
类型：整型 默认值：9000
{% endhighlight %}

这个端口是xdebug试着去连接远程主机的。9000是一般客户端和被绑定的debug客户端默认的端口。许多客户端都使用这个端口数字，最好不要去修改这个设置。

Windows的设置可以参考下面这个链接中说明：[http://be-evil.org/post-70.html](http://be-evil.org/post-70.html)

Mac OSX中可以下载一个`MacGDBp`，这个用来调试php，非常好用，与ide就完全脱离关系了。
