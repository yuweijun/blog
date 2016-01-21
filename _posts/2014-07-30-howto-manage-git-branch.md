---
layout: post
title: "git分支管理"
date: "Wed Jul 30 2014 17:54:54 GMT+0800 (CST)"
categories: linux
---

一般而言，我们要检出一个git项目会使用`git clone`命令，`git clone`默认会把远程仓库包括全部分支，完整`clone`下来：

{% highlight bash %}
$> git clone git@github.com:twbs/bootstrap.git
{% endhighlight %}

git常用命令之间的关系:

![git]({{ site.baseurl }}/img/linux/git/git-commands.jpg)

### 检出分支

直接检出某个分支：

{% highlight bash %}
$> git clone -b gh-pages git@github.com:twbs/bootsrap.git
{% endhighlight %}

也可以在`clone`之后，从`origin`检出分支：

{% highlight bash %}
$> git checkout -t origin/gh-pages
{% endhighlight %}

或者是从本地切换分支，如果本地检出的分支与远端仓库同名分支，默认就会有tracking：

{% highlight bash %}
$> git checkout gh-pages
{% endhighlight %}

### 分支创建

{% highlight bash %}
$> git branch testing
{% endhighlight %}

### 分支切换

此命令只是创建了新分支，还是在原来的分支下工作，`HEAD`指针并没有变化：

{% highlight bash %}
$> git checkout testing
{% endhighlight %}

新建一个分支并直接切换到新分支下：

{% highlight bash %}
$> git checkout -b iss53
Switched to a new branch "iss53"
{% endhighlight %}

它是下面两条命令的简写：

{% highlight bash %}
$> git branch iss53
$> git checkout iss53
{% endhighlight %}

### 分支合并

比如要将上面`iss53`这个分支修改后的内容合并到主分支`master`上，需要先切到`master`分支上，再使用`merge`命令：

{% highlight bash %}
$> git checkout master
$> git merge iss53
{% endhighlight %}

另外还有一个命令`git rebase`可以和`git merge`命令配合，使得合并历史变得更简洁。

### 查看分支

> 参数说明:
>
> -r, --remotes
>
>    list or delete (if used with -d) the remote-tracking branches.
>
> -a, --all
>
>    list both remote-tracking branches and local branches.

{% highlight bash %}
$> git branch -r

$> git branch -av
    origin/HEAD -> origin/master
    origin/bundle
    origin/derp
    origin/fix-13818
    origin/fix-13897
    origin/gh-pages
    origin/grunt-concurrent
    origin/grunt-no-touch
    origin/ie8-label-wrap-bug
    origin/master
    origin/media-query-mixins
    origin/sauce-screenshots
{% endhighlight %}

### 删除分支

{% highlight bash %}
$> git branch -d branch-name
{% endhighlight %}

### 删除远程分支

可以运行带有`--delete`选项的`git push`命令来删除一个远程分支。如果想要从服务器上删除`serverfix`分支，运行下面的命令：

{% highlight bash %}
$> git push origin --delete serverfix

...
 - [deleted]         serverfix
{% endhighlight %}

References
-----

1. [git分支](http://yuweijun.github.io/git-zh/1-git-branching.html)

