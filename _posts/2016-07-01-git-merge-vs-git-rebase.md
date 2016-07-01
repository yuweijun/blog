---
layout: post
title: "git merge vs git rebase"
date: "Fri, 01 Jul 2016 17:36:21 +0800"
categories: linux
---

`git-merge-test.sh`测试脚本

{% highlight bash %}
[ -a git-merge-test.git ] && rm -rf git-merge-test.git
[ -a git-merge-logs ] && rm -rf git-merge-logs

git --bare init git-merge-test.git
git clone git-merge-test.git git-merge-logs

cd git-merge-logs/
echo "14:59:+0800" > v01-master.txt
git add -A && git commit -m "v01-master"
echo "15:02:+0800" > v02-master.txt
git add -A && git commit -m "v02-master"
echo "15:03:+0800" > v03-master.txt
git add -A && git commit -m "v03-master"
git push origin master

git checkout -b feature
echo "15:04:+0800" > v04-feature.txt
git add -A && git commit -m "v04-feature"
echo "15:06:+0800" > v05-feature.txt
git add -A && git commit -m "v05-feature"
echo "15:09:+0800" > v06-feature.txt
git add -A && git commit -m "v06-feature"

git checkout master
echo "15:10:+0800" > v07-master.txt
git add -A && git commit -m "v07-master"
echo "15:12:+0800" > v08-master.txt
git add -A && git commit -m "v08-master"

git checkout feature
echo "15:15:+0800" > v09_feature.txt
git add -A && git commit -m "v09_feature"

git checkout master
# This creates a new "merge commit" in the feature branch that ties together the histories of both branches
git merge feature -m "merge feature into master"

git push

git log --decorate --graph --oneline --stat --all
{% endhighlight %}

执行结果
=====

![git-merge-commands]({{ site.baseurl }}/img/linux/git/git-merge-history.jpg)

git merge简要说明
-----

1. `git merge`会产生一次新的`commit`版本，并且此版本会有2个或者更多个父提交，可以通过`HEAD^1`，`HEAD^2`，`HEAD^n`方法来引用此提交的父提交。
2. `git merge`是非破坏性的(non-destructive)操作，没有负作用，并且被合并的这些分支都可以`git push`发布到`remote`公共仓库中。
3.  由于每次合并会产生一次新的提交历史，如果主分支非常活跃，就会产生很多这种合并分支的提交历史，会对项目本身的提交历史产生污染。

`git-rebase-test.sh`测试脚本

{% highlight bash %}
[ -a git-rebase-test.git ] && rm -rf git-rebase-test.git
[ -a git-rebase-logs ] && rm -rf git-rebase-logs

git --bare init git-rebase-test.git
git clone git-rebase-test.git git-rebase-logs

cd git-rebase-logs/
echo "14:59:+0800" > v01-master.txt
git add -A && git commit -m "v01-master"
echo "15:02:+0800" > v02-master.txt
git add -A && git commit -m "v02-master"
echo "15:03:+0800" > v03-master.txt
git add -A && git commit -m "v03-master"
git push origin master

git checkout -b feature
echo "15:04:+0800" > v04-feature.txt
git add -A && git commit -m "v04-feature"
echo "15:06:+0800" > v05-feature.txt
git add -A && git commit -m "v05-feature"
echo "15:09:+0800" > v06-feature.txt
git add -A && git commit -m "v06-feature"

git checkout master
echo "15:10:+0800" > v07-master.txt
git add -A && git commit -m "v07-master"
echo "15:12:+0800" > v08-master.txt
git add -A && git commit -m "v08-master"

git checkout feature
# Instead of using a merge commit, rebasing re-writes the project history by creating brand new commits for each commit in the original branch.
# The golden rule of git rebase is to never use it on public branches.
git rebase master
echo "15:15:+0800" > v09_feature.txt
git add -A && git commit -m "v09_feature"

git checkout master
git merge feature # -m "rebasing the feature branch onto master, and merge the feature branch does not create new commit."

git push

git log --decorate --graph --oneline --stat --all

# Don't push temporary feature branch to remote, and can delete this temporary branch.
git branch -d feature

# Interactive Rebasing to change history of commits
# git rebase -i master
{% endhighlight %}

执行结果
=====

![git-rebase-commands]({{ site.baseurl }}/img/linux/git/git-rebase-history.jpg)

git rebase简要说明
-----

1. The major benefit of rebasing is that you get a much cleaner project history, rebasing creates a linear history by moving your `feature` branch onto the tip of `master`.
2. There are two `trade-offs` for this pristine commit history: `safety` and `traceability`.
3. Instead of using a merge commit, rebasing `re-writes` the project history by creating `brand new commits` for each commit in the original branch.
4. `git rebase -i master` can edit history of commits and comments.
5. The Golden Rule of Rebasing: `NEVER USE IT ON PUBLIC BRANCHES`.
6. Any changes from other developers need to be incorporated with `git merge` instead of `git rebase`.
7. If you’re not entirely comfortable with git rebase, you can always perform the rebase in a temporary branch.

{% highlight bash %}
$> git checkout feature
$> git checkout -b temporary-branch
$> git rebase -i master
# [Clean up the history]
$> git checkout master
$> git merge temporary-branch
$> git branch -d temporary-branch
{% endhighlight %}

References
-----

1. [Merging vs. Rebasing](https://www.atlassian.com/git/tutorials/merging-vs-rebasing/workflow-walkthrough)

