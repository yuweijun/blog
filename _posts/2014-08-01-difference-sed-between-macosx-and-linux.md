---
layout: post
title: "difference sed on macosx and linux"
date: "Fri Aug 01 2014 23:10:36 GMT+0800 (CST)"
categories: linux
---

sed正则替换内容时，碰到在Linux上正常执行的命令在MacOSX执行失败的问题，如：

{% highlight bash %}
$> sed -i 's/text/replaced/g' filename
sed: 1: "filename": invalid command code -
{% endhighlight %}

google之后发现有别人碰到类似的[问题](http://www.markhneedham.com/blog/2011/01/14/sed-sed-1-invalid-command-code-r-on-mac-os-x/)，在MacOSX上需要加个空字符串在`-i`参数后面：

{% highlight bash %}
$> sed -i "" 's/text/relpaced/g' filename
{% endhighlight %}

> What I hadn’t realised is that on the Mac version of sed the `-i` flag has a mandatory suffix, as described in this post.
>
> The appropriate section of the man page for sed on the Mac looks like this:
>
> {% highlight bash %}
-i extension
    Edit files in-place, saving backups with the specified extension. If a zero-length extension is given, no backup will be saved.
{% endhighlight %}>
> It is not recommended togive a zero-length extension when in-place editing files, as you risk corruption or partial content in situations where disk space is exhausted, etc.


> Whereas on Ubuntu the suffix is optional so we see this:
>
>{% highlight javascript %}
-i[SUFFIX], –in-place[=SUFFIX]
    edit files in place (makes backup if extension supplied)
{% endhighlight %}>
> In order to get around this we need to provide a blank suffix when using the `-i` flag on the Mac:
