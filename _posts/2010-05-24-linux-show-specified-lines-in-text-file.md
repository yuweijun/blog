---
layout: post
title: "linux命令行显示指定行号的内容"
date: "Mon May 24 2010 10:45:00 GMT+0800 (CST)"
categories: linux
---

以第四行为例,要查询的文件名为list.txt。

方法1
-----

{% highlight bash %}
$> grep -n '^' list.txt |grep  '^4:'|grep -o '[^4:].*'
{% endhighlight %}

方法2
-----

{% highlight bash %}
$> sed -n '4p' list.txt
$> sed -n '4,4p' list.txt
{% endhighlight %}

方法3
-----

{% highlight bash %}
$> awk '{if ( NR==4 ) print $0}' list.txt
{% endhighlight %}

方法4
-----

{% highlight bash %}
$> tac list.txt |tail -4|tac|tail -1
$> tac list.txt |tail -n 4|tac|tail -n 1
{% endhighlight %}

References
-----

1. [http://zhidao.baidu.com/question/91856742](http://zhidao.baidu.com/question/91856742)
