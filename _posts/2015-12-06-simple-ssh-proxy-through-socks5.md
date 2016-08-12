---
layout: post
title: "一个命令建立socks5代理服务器"
date: "Sun, 06 Dec 2015 22:53:40 +0800"
categories: linux
---

### 为什么需要运行一个socks5代理服务器

比如在免费wifi的地方上网，通过这个代理服务器连接出去的所有数据都是经过加密的，像用户名密码就不会在网络上明文传输，数据安全性会更有保障。也可用此方法访问google搜索引擎或者是facebook网站。

### 原理简单说明

利用`ssh`命令在本地`1080`端口运行一个`socks5`服务，本地网络所有数据经过`socks5`代理加密之后，先通过ssh隧道，将数据传递到远程运行`sshd`服务的服务器上，然后再由此服务器发送请求到真正的目标网站，目标网站返回数据给此运行`sshd`服务的服务器，由ssh隧道返回给`socks5`代理，最后回到客户端。

### 建立socks5代理服务器的命令

当然这里需要有一台你信任的并运行`sshd`的远程服务器，假设ssh用户名为`ssh-user`，服务器ip地址为`ssh-server-ip`，运行如下命令即可。

{% highlight bash %}
$> ssh -D 1080 -f -C -q -N -p 22 ssh-user@ssh-server-ip
{% endhighlight %}

如果要提供给局域网使用，则使用参数`-D HOST:PORT`格式：

{% highlight bash %}
$> ssh -D 192.168.31.101:1080 -f -C -q -N -p 22 ssh-user@ssh-server-ip
{% endhighlight %}

### socks5代理服务器使用

本地`socks5`代理服务建立之后，还需要进行相应设置才能起作用。

在Mac OS X上依次打开`Network` => `Advanced...` => `Proxies` => `SOCKS Proxy`，配置地址为`localhost:1080`即可。

如果只是想为chrome浏览器单独配置`socks5`代理的话，可使用chrome浏览器的一个插件：`SwitchyOmega`，安装之后新建一个配置，使用`socks5`代理，服务器和端口配置为`localhost:1080`即可。

最后可以访问这个[url](https://www.baidu.com/s?wd=ip)确认`socks5`代理是否生效，如果配置正确的话，这里显示的就是运行`sshd`的服务器ip地址。

References
-----

1. [Really simple SSH proxy - SOCKS5](https://thomashunter.name/blog/really-simple-ssh-proxy-socks5/)

