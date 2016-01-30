---
layout: post
title: "homebrew service简介"
date: "Sat, 30 Jan 2016 12:37:38 +0800"
categories: macos
---

利用`brew service`来管理Mac OS下的后台守护进程。这些守护进程从以下位置启动：

1. `~/Library/LaunchAgents/`随用户登陆启动。
2. `/Library/LaunchDaemons/`随系统一起启动。

## Installation

{% highlight bash %}
$> brew tap homebrew/services
{% endhighlight %}

## Examples

### Install and start service mysql at login

{% highlight bash %}
$> brew install mysql
$> brew services start mysql
{% endhighlight %}

### Stop service mysql

{% highlight bash %}
$> brew services stop mysql
{% endhighlight %}

### Restart service mysql

{% highlight bash %}
$> brew services restart mysql
{% endhighlight %}

### Install and start dnsmasq service at boot

{% highlight bash %}
$> brew install dnsmasq
$> sudo brew services start dnsmasq
{% endhighlight %}

### Start/stop/restart all available services

{% highlight bash %}
$> brew start|stop|restart --all
{% endhighlight %}

References
-----

1. [Starts Homebrew formulae's plists with launchctl.](https://github.com/Homebrew/homebrew-services)
2. [Apache, PHP, and MySQL with Homebrew](https://echo.co/blog/os-x-1010-yosemite-local-development-environment-apache-php-and-mysql-homebrew)

