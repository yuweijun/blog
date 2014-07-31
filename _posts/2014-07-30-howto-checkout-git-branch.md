---
layout: post
date: "Wed Jul 30 2014 17:54:54 GMT+0800 (CST)"
title: git clone之后的分支管理
categories: howto
---

一般而言，我们要检出一个git项目会使用git clone命令，git clone默认会把远程仓库包括全部分支，完整clone下来：

`$ git clone git@github.com:twbs/bootstrap.git`

但是如果想直接切某个分支，那命令则为：

`$ git clone -b gh-pages git@github.com:twbs/bootsrap.git`

也可以在检出主分支之后，直接从本地检出：

`$ git checkout -t origin/gh-pages`

参数说明：

    -t, --track
       When creating a new branch, set up configuration to mark the start-point branch as "upstream" from the new branch. This configuration will tell git to show the relationship between the two branches in git status and git branch -v. Furthermore, it directs git pull without arguments to pull from the upstream when the new branch is checked out.

       This behavior is the default when the start point is a remote-tracking branch. Set the branch.autosetupmerge configuration variable to false if you want git checkout and git branch to always behave as if --no-track were given. Set it to always if you want this behavior when the start-point is either a local or remote-tracking branch.

它默认会在本地建立一个和远程分支名字一样的分支，这样就可以在分支上修改，并且commit了。如果只是checkout，没有-t的话，只是切出代码，并没有tracking分支的。

查看branch列表
--------------
参数说明:

    -r, --remotes
       list or delete (if used with -d) the remote-tracking branches.

    -a, --all
       list both remote-tracking branches and local branches.


`$ git branch -r`

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

