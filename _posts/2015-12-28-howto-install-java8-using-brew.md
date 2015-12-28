---
layout: post
title: "how to install java 8 on mac"
date: "Mon, 28 Dec 2015 19:58:24 +0800"
categories: java
---

许多应用已经是基于java1.7或者是java1.8环境的，如eclipse-4.5.1，因此需要在Mac OS上安装java8，以下是通过`homebrew`在命令行上安装java8，而不用手动去Oracle官网下载安装这么麻烦。

### 安装java8

{% highlight bash %}
$> brew tap caskroom/cask
$> brew install brew-cask
{% endhighlight %}

如果命令行提示“already installed”，则按照如下命令操作：

{% highlight bash %}
$> brew unlink brew-cask
$> brew install brew-cask
{% endhighlight %}

最后安装java8：
{% highlight bash %}
$> brew cask install java
{% endhighlight %}

如果系统中需要支持多个java版本，则可以利用`jenv`进行切换。

### 安装jenv

{% highlight bash %}
$> brew install jenv
{% endhighlight %}

为jenv添加系统中已经存在的java版本

{% highlight bash %}
$> jenv add /System/Library/Java/JavaVirtualMachines/1.6.0.jdk/Contents/Home
$> jenv add /Library/Java/JavaVirtualMachines/jdk17011.jdk/Contents/Home
{% endhighlight %}

### 查看已经安装的jdk版本号

{% highlight bash %}
$> jenv versions
{% endhighlight %}

### 为系统全局设置java版本

{% highlight bash %}
$> jenv global oracle64-1.6.0.39
{% endhighlight %}

### 当前工作目录

{% highlight bash %}
$> jenv local oracle64-1.6.0.39
{% endhighlight %}

### 当前shell指定使用的java版本

{% highlight bash %}
$> jenv shell oracle64-1.6.0.39
{% endhighlight %}

References
-----

1. [How to install java 8 on Mac](http://stackoverflow.com/questions/24342886/how-to-install-java-8-on-mac)
2. [http://www.jenv.be/](http://www.jenv.be/)