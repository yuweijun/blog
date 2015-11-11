---
layout: post
title: "create macos tomcat startup script"
date: "Thu Mar 13 2008 23:44:00 GMT+0800 (CST)"
categories: macos
---

在苹果系统自己创建启动脚本
-----

{% highlight bash %}
$> mkdir Tomcat
$> cd Tomcat
$> cp /System/Library/StartupItems/Apache/Apache ./Tomcat
$> cp /System/Library/StartupItems/Apache/StartupParameters.plist .
$> vi StartupParameters.plist
{% endhighlight %}

{% highlight bash %}
{
  Description     = "Tomcat servlet engine";
  Provides        = ("Servlet Engine");
  Requires        = ("DirectoryServices");
  Uses            = ("NFS");
  OrderPreference = "None";
}
{% endhighlight %}

{% highlight bash %}
$> vi Tomcat
{% endhighlight %}

{% highlight bash %}
#!/bin/sh

##
# Tomcat Servlet Engine
##

. /etc/rc.common

StartService ()
{
    ConsoleMessage "Starting Tomcat"
    /usr/local/jakarta-tomcat-4.1.18/bin/startup.sh
}

StopService ()
{
    ConsoleMessage "Stopping Tomcat"
    /usr/local/jakarta-tomcat-4.1.18/bin/shutdown.sh
}

RestartService ()
{
    ConsoleMessage "Restarting Tomcat"
    /usr/local/jakarta-tomcat-4.1.18/bin/shutdown.sh
    /usr/local/jakarta-tomcat-4.1.18/bin/startup.sh
}

JAVA_HOME=/Library/Java/Home; export JAVA_HOME
RunService "$1"
{% endhighlight %}


{% highlight bash %}
$> mv Tomcat /Library/StartupItems/
{% endhighlight %}

Reboot and accept the chmod of Tomcat, reboot again.

References
-----

1. [http://www.oreilly.com/pub/a/mac/2003/10/21/startup.html](http://www.oreilly.com/pub/a/mac/2003/10/21/startup.html)
