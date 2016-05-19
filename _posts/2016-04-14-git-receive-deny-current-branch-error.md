---
layout: post
title: "git remote error receive.denyCurrentBranch"
date: "Thu, 14 Apr 2016 16:50:42 +0800"
categories: linux
---

在线上服务器已经有文件的目录里通过以下操作：

{% highlight bash %}
$> git init
$> git add -A
$> git commit -m "create git repository by existing files"
{% endhighlight %}

初始化了一个git仓库，并将目录下面的文件全部加入仓库版本管理库中，然后远程切出此仓库，并修改内容后，通过`git push`命令上传到线上服务器时，报了以下错误：

{% highlight bash %}
$> git push

Counting objects: 6, done.
Delta compression using up to 2 threads.
Compressing objects: 100% (5/5), done.
Writing objects: 100% (5/5), 964.60 KiB | 0 bytes/s, done.
Total 5 (delta 1), reused 0 (delta 0)
remote: error: refusing to update checked out branch: refs/heads/master
remote: error: By default, updating the current branch in a non-bare repository
remote: error: is denied, because it will make the index and work tree inconsistent
remote: error: with what you pushed, and will require 'git reset --hard' to match
remote: error: the work tree to HEAD.
remote: error:
remote: error: You can set 'receive.denyCurrentBranch' configuration variable to
remote: error: 'ignore' or 'warn' in the remote repository to allow pushing into
remote: error: its current branch; however, this is not recommended unless you
remote: error: arranged to update its work tree to match what you pushed in some
remote: error: other way.
remote: error:
remote: error: To squelch this message and still keep the default behaviour, set
remote: error: 'receive.denyCurrentBranch' configuration variable to 'refuse'.
To git@server:repo
 ! [remote rejected] master -> master (branch is currently checked out)
{% endhighlight %}

看错误提示可以知道，这是因为默认情况下，对于non-bare的git仓库拒绝了push的更新操作，要避免此问题，有以下二种方法。


方法一
-----

可根据提示进行设置，修改线上git仓库目录下的`.git/config`文件，添加如下代码：

{% highlight bash %}
    [receive]
    denyCurrentBranch = ignore
{% endhighlight %}

方法二
-----

另外一个方法是将线上`non-bare repository`仓库转换成`bare repository`，在线上git仓库目录下如下操作：

{% highlight bash %}
$> cd ..
$> mv repo/.git repo.git
$> git --git-dir=repo.git config core.bare true
$> rm -rf repo
{% endhighlight %}

从此错误也可以得到一点经验，在初始化远程仓库时最好使用`git --bare init`，而不要使用：`git init`。

References
-----

1. [How to convert a normal Git repository to a bare one?](http://stackoverflow.com/questions/2199897/how-to-convert-a-normal-git-repository-to-a-bare-one)

