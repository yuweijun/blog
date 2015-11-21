---
layout: post
title: "compile objective-c programs on ubuntu-12.04"
date: "Tue, 13 Nov 2012 10:24:45 +0800"
categories: linux
---

ubuntu 12.04上编译Objective-C代码。

{% highlight bash %}
$> sudo apt-get install gnustep gobjc
$> sudo apt-get install gnustep-devel libgnustep-base-dev gnustep-games
$> sudo apt-get install build-essential
$> sudo chmod +x /usr/share/GNUstep/Makefiles/GNUstep.sh
$> /usr/share/GNUstep/Makefiles/GNUstep.sh
$> gnustep-config --objc-flags

$> vi /home/david/.bashrc
{% endhighlight %}

添加以下内容到`~/.bashrc`：

{% highlight bash %}
alias gcc='gcc `gnustep-config --objc-flags`'
{% endhighlight %}

{% highlight bash %}
$> . /home/david/.bashrc
{% endhighlight %}

{% highlight bash %}
$> vi hello.m
{% endhighlight %}

{% highlight objectivec %}
#import
int main(int argc,const char *argv[]){
    NSLog(@"Hello World");
    return (0);
}//main
{% endhighlight %}

{% highlight bash %}
$> gcc hello.m -o hello -lgnustep-base
# or
$> gcc hello.m -lgnustep-base -o hello
# for multi files
$> gcc Fraction.m FractionTest.m -lgnustep-base -o bin/FractionTest
{% endhighlight %}

可以创建一个build脚本来帮助完成编译任务。

{% highlight bash %}
$> vi ~/bin/build
{% endhighlight %}

{% highlight bash %}
#! /bin/sh
gcc `gnustep-config --objc-flags` ${1}.m -o $1 -lgnustep-base
{% endhighlight %}

{% highlight bash %}
$> vi ~/bin/build-project
{% endhighlight %}

{% highlight bash %}
#! /bin/sh
gcc `gnustep-config --objc-flags` $* -lgnustep-base
{% endhighlight %}

{% highlight bash %}
$> chmod +x ~/bin/build*
$> build prog
{% endhighlight %}
