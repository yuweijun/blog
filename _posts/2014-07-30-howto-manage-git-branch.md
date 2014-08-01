---
layout: post
date: "Wed Jul 30 2014 17:54:54 GMT+0800 (CST)"
title: git分支管理
categories: howto
---

一般而言，我们要检出一个git项目会使用git clone命令，git clone默认会把远程仓库包括全部分支，完整clone下来：

{% highlight bash %}
$ git clone git@github.com:twbs/bootstrap.git
{% endhighlight %}

检出分支
--------

直接检出某个分支：

{% highlight bash %}
$ git clone -b gh-pages git@github.com:twbs/bootsrap.git
{% endhighlight %}

也可以在clone之后，直接从origin检出分支：

{% highlight bash %}
$ git checkout -t origin/gh-pages
{% endhighlight %}

或者是直接从本地切换分支：

{% highlight bash %}
$ git checkout gh-pages
{% endhighlight %}

参数说明：

    -t, --track
       When creating a new branch, set up configuration to mark the start-point branch as "upstream" from the new branch. This configuration will tell git to show the relationship between the two branches in git status and git branch -v. Furthermore, it directs git pull without arguments to pull from the upstream when the new branch is checked out.

       This behavior is the default when the start point is a remote-tracking branch. Set the branch.autosetupmerge configuration variable to false if you want git checkout and git branch to always behave as if --no-track were given. Set it to always if you want this behavior when the start-point is either a local or remote-tracking branch.

查看分支
--------
参数说明:

    -r, --remotes
       list or delete (if used with -d) the remote-tracking branches.

    -a, --all
       list both remote-tracking branches and local branches.

{% highlight bash %}
$ git branch -r
$ git branch -av
{% endhighlight %}

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

删除分支
--------

删除远程分支则加-r参数
{% highlight bash %}
$ git branch -d branch-name
{% endhighlight %}

