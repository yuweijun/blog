---
layout: post
title: "mysql-5.1 启动关闭和重启命令并添加系统启动项"
date: "Thu May 25 2007 12:25:00 GMT+0800 (CST)"
categories: mysql
---

在安装目录下运行:

{% highlight bash %}
$> support-files/mysql.server start
$> support-files/mysql.server stop
$> support-files/mysql.server restart
$> support-files/mysql.server reload
{% endhighlight %}

执行：

{% highlight bash %}
$> cp support-files/mysql.server /etc/init.d/mysql
$> chmod +x /etc/init.d/mysql
{% endhighlight %}

旧的Red Hat系统使用`/etc/rc.d/init.d`目录，不使用`/etc/init.d`。相应地调节前面的命令。
后即安装了mysql server以便自己重启和关闭。
安装脚本后，用来激活它以便在系统启动时运行所需要的命令取决于你的操作系统。在Linux中，你可以使用`chkconfig`：

{% highlight bash %}
$> chkconfig --list mysql
{% endhighlight %}

mysql 服务支持`chkconfig`，但它在任何级别中都没有被引用(运行`chkconfig --add mysql`)

{% highlight bash %}
$> chkconfig --add mysql
{% endhighlight %}

再用list查看：
mysql 0:关闭 1:关闭 2:启用 3:启用 4:启用 5:启用 6:关闭

{% highlight bash %}
$> ntsysv
{% endhighlight %}

可以看到 [ * ] mysql 这一行，表示开机后会自动启动mysql server。
在一些Linux系统中，还需要下面的命令来完全激活MySQL脚本：

{% highlight bash %}
$> chkconfig --level 345 mysql on
{% endhighlight %}


便可以用：

{% highlight bash %}
$> service mysql start
$> service mysql stop
$> service mysql restart
{% endhighlight %}

控制mysql server(不同Linux分发版本这个命令不一样)。


另外上面的内容是基于mysql-5.1二进制分发版操作，源码包里support-files文件夹下面有的是mysql.server.sh，这个文件就算按上面一样操作也是不能启动mysql server的，需要make之后用mysql安装目录下生成的share/mysql/mysql.server按照上面的操作执行后即可以，直接用二进制分发包里提供的mysql.server也可以。
