---
layout: post
title: "install sun java on ubuntu 10.04"
date: "Sun Aug 08 2010 18:22:00 GMT+0800 (CST)"
categories: java
---

Sun Java moved to the Partner repository

For ubuntu-10.04-lts, the sun-java6 packages have been dropped from the multiverse section of the ubuntu archive. it is recommended that you use openjdk-6 instead.

If you can not switch from the proprietary Sun JDK/JRE to OpenJDK, you can install sun-java6 packages from the Canonical Partner Repository. You can configure your system to use this repository via command-line:

{% highlight bash %}
$> add-apt-repository "deb http://archive.canonical.com/ lucid partner"
{% endhighlight %}

then

{% highlight bash %}
$> sudo add-apt-repository "deb http://archive.canonical.com/ lucid partner"
$> sudo apt-get update
$> sudo apt-get install sun-java6-jdk
{% endhighlight %}
