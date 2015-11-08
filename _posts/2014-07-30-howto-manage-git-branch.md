---
layout: post
title: "git分支管理"
date: "Wed Jul 30 2014 17:54:54 GMT+0800 (CST)"
categories: git
---

一般而言，我们要检出一个git项目会使用git clone命令，git clone默认会把远程仓库包括全部分支，完整clone下来：

{% highlight bash %}
$ git clone git@github.com:twbs/bootstrap.git
{% endhighlight %}

检出分支
--------

git常用命令之间的关系:

![git](http://image.beekka.com/blog/2014/bg2014061202.jpg)

直接检出某个分支：

{% highlight bash %}
$ git clone -b gh-pages git@github.com:twbs/bootsrap.git
{% endhighlight %}

也可以在clone之后，从origin检出分支：

{% highlight bash %}
$ git checkout -t origin/gh-pages
{% endhighlight %}

或者是从本地切换分支，本地检出的分支与远端仓库同名分支，默认就会有tracking：

{% highlight bash %}
$ git checkout gh-pages
{% endhighlight %}

参数说明：

> -t, --track
>
>    When creating a new branch, set up configuration to mark the start-point branch as "upstream" from the new branch. This configuration will tell git to show the relationship between the two branches in git status and git branch -v. Furthermore, it directs git pull without arguments to pull from the upstream when the new branch is checked out.
>
>    This behavior is the default when the start point is a remote-tracking branch. Set the branch.autosetupmerge configuration variable to false if you want git checkout and git branch to always behave as if --no-track were given. Set it to always if you want this behavior when the start-point is either a local or remote-tracking branch.

这里的参数说明不太好理解，用命令来操作一遍就会更了解一些，以git pull为例，下面这段内容从[阮一峰的博客](http://www.ruanyifeng.com/blog/2014/06/git_remote.html)复制过来的：

{% highlight bash %}
$ git pull <远程主机名> <远程分支名>:<本地分支名>
{% endhighlight %}

比如，取回origin主机的next分支，与本地的master分支合并，需要写成下面这样。

{% highlight bash %}
$ git pull origin next:master
{% endhighlight %}

如果远程分支是与当前分支合并，则冒号后面的部分可以省略。

{% highlight bash %}
$ git pull origin next
{% endhighlight %}

上面命令表示，取回origin/next分支，再与当前分支合并。实质上，这等同于先做git fetch，再做git merge。

{% highlight bash %}
$ git fetch origin
$ git merge origin/next
{% endhighlight %}

在某些场合，Git会自动在本地分支与远程分支之间，建立一种追踪关系（tracking）。比如，在git clone的时候，所有本地分支默认与远程主机的同名分支，建立追踪关系，也就是说，本地的master分支自动"追踪"origin/master分支。

Git也允许手动建立追踪关系。

{% highlight bash %}
$ git branch --set-upstream master origin/next
{% endhighlight %}

上面命令指定master分支追踪origin/next分支。

如果当前分支与远程分支存在追踪关系，git pull就可以省略远程分支名。

{% highlight bash %}
$ git pull origin
{% endhighlight %}

上面命令表示，本地的当前分支自动与对应的origin主机"追踪分支"（remote-tracking branch）进行合并。

如果当前分支只有一个追踪分支，连远程主机名都可以省略。

{% highlight bash %}
$ git pull
{% endhighlight %}

上面命令表示，当前分支自动与唯一一个追踪分支进行合并。

查看分支
--------
参数说明:

> -r, --remotes
>
>    list or delete (if used with -d) the remote-tracking branches.
>
> -a, --all
>
>    list both remote-tracking branches and local branches.

{% highlight bash %}
$ git branch -r
$ git branch -av
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

删除分支
--------

删除远程分支则加-r参数
{% highlight bash %}
$ git branch -d branch-name
{% endhighlight %}

