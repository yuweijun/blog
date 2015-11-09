---
layout: post
title: "ruby debug method object.tap"
date: "Fri Feb 15 2008 15:13:00 GMT+0800 (CST)"
categories: ruby
---

作用有点类似linux中的tee命令：

{% highlight ruby %}
class Object
  def tap
    yield self
    self
  end
end
{% endhighlight %}

usage of linux command: tee
-----

{% highlight bash %}
$> ps -ef |grep httpd|tee > grep_result |awk -f " " '{print $1}'|wc -l
{% endhighlight %}

其中`tee > file`是将前一个命令输出的结果打到文件里以方便查看结果。

use tee in vim
-----

{% highlight vim %}
:w !sudo tee %
{% endhighlight %}

这个是vim中以普通用户编辑文件时，无法保存，可以用vi的`!{cmd}`指令调用tee进行文件同名`%`保存`w`操作。
