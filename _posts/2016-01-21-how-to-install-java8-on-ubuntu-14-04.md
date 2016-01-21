---
layout: post
title: "howto install java8 on ubuntu-14.04"
date: "Thu, 21 Jan 2016 16:25:52 +0800"
categories: linux
---

下面通过手工和ppa二种方式安装java-8-oracle。

## wget方式下载安裝java-8-oracle

{% highlight bash %}
$> wget --header "Cookie: oraclelicense=accept-securebackup-cookie" http://download.oracle.com/otn-pub/java/jdk/8u25-b17/jdk-8u25-linux-x64.tar.gz
$> mkdir -p /usr/lib/jvm
$> tar zxvf jdk-8u25-linux-x64.tar.gz
$> mv jdk1.8.0_25 /usr/lib/jvm/java-8-oracle

$> sudo update-alternatives --install /usr/bin/java java /usr/lib/jvm/java-8-oracle/bin/java 10
$> sudo update-alternatives --install /usr/bin/javac javac /usr/lib/jvm/java-8-oracle/bin/javac 10
$> sudo update-alternatives --install /usr/bin/jps jps /usr/lib/jvm/java-8-oracle/bin/jps 10

$> javac -version
{% endhighlight %}

## 使用ppa方式安装java8

{% highlight bash %}
$> sudo add-apt-repository ppa:webupd8team/java
$> sudo apt-get update
$> sudo apt-get install oracle-java8-installer

$> javac -version
{% endhighlight %}

## 选择java版本

{% highlight bash %}
$> sudo update-alternatives --config java
There are 2 choices for the alternative java (providing /usr/bin/java).

  Selection    Path                                     Priority   Status
------------------------------------------------------------
* 0            /usr/lib/jvm/java-8-oracle/jre/bin/java   2         auto mode
  1            /usr/lib/jvm/java-6-oracle/jre/bin/java   1         manual mode
  2            /usr/lib/jvm/java-8-oracle/jre/bin/java   2         manual mode

Press enter to keep the current choice[*], or type selection number:
{% endhighlight %}

References
-----

1. [how to install java 8 on mac](http://www.4e00.com/blog/java/2015/12/28/howto-install-java8-using-brew.html)
2. [install sun java on ubuntu 10.04](http://www.4e00.com/blog/java/2010/08/08/install-sun-java-on-ubuntu-10-04.html)
