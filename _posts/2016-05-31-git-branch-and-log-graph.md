---
layout: post
title: "通过git log查看版本合并历史"
date: "Tue, 31 May 2016 22:56:54 +0800"
categories: linux
---

以下脚本用于生成多分支的`git log`，通过`git log --oneline --decorate --graph --all`查看分支提交及合并示意图。

{% highlight bash %}
git config --global alias.co checkout
git config --global alias.br branch
git config --global alias.ci commit
git config --global alias.st status

[ -a git-branch-test.git ] && rm -rf git-branch-test.git
[ -a git-branch-logs ] && rm -rf git-branch-logs

git init --bare git-branch-test.git
git clone git-branch-test.git git-branch-logs

cd git-branch-logs

echo v1 > v1.txt
git add -A
git commit -m v1-master
sleep 2s

git checkout -b br1
git branch
echo v2 > v2.txt
git add -A
git commit -m v2-br1
sleep 2s

git branch
git checkout -b br2
echo v3 > v3.txt
git commit -m v3-br2
sleep 2s

git add -A
git commit -m v3-br2
sleep 2s

git checkout -b br3
echo v4 > v4.txt
git add v4.txt
git commit -m v4-br3
sleep 2s

git checkout master
git merge br3 -m "merge br3 into master"
git checkout br3
echo v5 > v5.txt
git add -A
git commit -m v5-br3
sleep 2s

git checkout master
echo v6 > v6.txt
git add -A
git commit -m v6-master
sleep 2s

git checkout br1
echo v7 > v7.txt
git add -A
git commit -m v7-br1
sleep 2s

git checkout master
echo v8 > v8.txt
git add -A
git commit -m v8-master
sleep 2s

echo v9 > v9.txt
git add -A
git commit -m v9-master
sleep 2s

git checkout br2
echo v10 > v10.txt
git add -A
git commit -m v10-br2
sleep 2s

echo v11 > v11.txt
git add -A
git commit -m v11-br2
sleep 2s

echo v12 > v12.txt
git add -A
git commit -m v12-br2
sleep 2s

git checkout br3
echo v13 > v13.txt
git add -A
git commit -m v13-br3
sleep 2s

git checkout br1
echo v14 > v14.txt
git add -A
git commit -m v14-br1
sleep 2s

echo v15 > v15.txt
git add -A
git commit -m v15-br1
sleep 2s

git checkout master
git merge br3 -m "merge br3 into master"
git merge br1 -m "merge br1 into master"
git merge br2 -m "merge br2 into master"

git log --oneline --decorate --graph --all
{% endhighlight %}

以上脚本运行完成之后生成命令行的结果如图所示，可以通过结果来查看分支版本的合并衍进历史。

![git log --graph]({{ site.baseurl }}/img/linux/git/git-log.jpg)

gitk中的显示结果：

![git log gitk graph]({{ site.baseurl }}/img/linux/git/git-log-gitk.png)

sourceTree中的显示结果：

![git log source tree graph]({{ site.baseurl }}/img/linux/git/git-log-source-tree.png)

如果想按照提交时间顺序查看分支历史，可以使用以下命令：

{% highlight bash %}
$> git log --oneline --decorate --graph --all --date-order
{% endhighlight %}

![git log date order graph]({{ site.baseurl }}/img/linux/git/git-log-date-order.png)
