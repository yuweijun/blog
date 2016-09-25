---
layout: post
title: "git format-patch and git diff"
date: Wed, 21 Sep 2016 22:28:22 +0800
categories: linux
---

linux下有个补丁工具：`patch`，它会根据`diff`命令生成的文件更新代码。

git中则提供了两种`patch`方案：

1. 用`git diff`生成的标准patch，可以提供上述的`patch`命令使用。
2. 用`git format-patch`生成的git专用patch。

两种patch的比较：

1. 兼容性：很明显，`git diff`生成的patch兼容性强。如果你在修改的代码的官方版本库不是git管理的版本库，那么你必须使用`git diff`生成的patch才能让你的代码被项目的维护人接受。
2. 除错功能：对于`git diff`生成的patch，你可以用`git apply --check`查看补丁是否能够干净顺利地应用到当前分支中；如果`git format-patch`生成的补丁不能打到当前分支，`git am`会给出提示，并协助你完成打补丁工作，你也可以使用`git am -3`进行三方合并，详细的做法可以参考git手册或者`《Progit》`。从这一点上看，两者除错功能都很强。
3. 提交信息：由于`git format-patch`生成的补丁中含有这个补丁开发者的名字，因此在应用补丁时，这个名字会被记录进版本库，显然，这样做是恰当的。因此，目前使用git的开源社区往往建议大家使用`git format-patch`生成补丁。

git diff/apply 与 git format-patch/am 测试脚本
-----

以下脚本命名并保存为`git-patch-commands.sh`：

{% highlight bash %}
#! /bin/bash

[ -a git-patch-test.git ] && rm -rf git-patch-test.git
[ -a git-patch-logs ] && rm -rf git-patch-logs

git --bare init git-patch-test.git
git clone git-patch-test.git git-patch-logs

cd git-patch-logs/
echo "v1@master" > v01-master.txt
git add -A && git commit -m "v01-master"
echo "v2@master" > v02-master.txt
git add -A && git commit -m "v02-master"
echo "v3@master" > v03-master.txt
git add -A && git commit -m "v03-master"
git push origin master

echo "change to branch feature1"
git checkout -b feature1

echo "v4有中文内容" >> v01-master.txt
echo "v4@feature1" > v04-feature1.txt
git add -A && git commit -m "v04-feature1"
echo "v5追加内容到v02-master.txt" >> v02-master.txt
echo "v5@feature1" > v05-feature1.txt
git add -A && git commit -m "v05-feature1"
echo "v6 append to v03-master.txt" >> v03-master.txt
echo "v6@feature1" > v06-feature1.txt
git add -A && git commit -m "v06-feature1"

echo "change to branch feature2"
git checkout master
git checkout -b feature2

echo "v7 append to v01-master.txt" >> v01-master.txt
echo "v7@feature2" > v07-feature2.txt
git add -A && git commit -m "v07-feature2"
echo "v8追加内容到v02-master.txt" >> v02-master.txt
echo "v8@feature2" > v08-feature2.txt
git add -A && git commit -m "v08-feature2"
echo "v9追加内容到v03-master.txt" >> v03-master.txt
echo "v9@feature2" > v09-feature2.txt
git add -A && git commit -m "v09-feature2"

git checkout master
{% endhighlight %}

git diff and git apply example
-----

{% highlight bash %}
cd ..
./git-patch-commands.sh
cd git-patch-logs
echo "git diff HEAD~1 HEAD"
git diff HEAD~1 HEAD > diff1.patch
git reset --hard HEAD~1
echo "git apply diff1"
git apply diff1.patch
git add v03-master.txt
git commit -am "apply diff1.patch"
git status | more
git log --stat | more
{% endhighlight %}

从上述的命令操作结果可以看到，`git apply`打补丁后，文件在工作区内被修改了，与直接`patch -p1 < diff1.patch`的效果一样，所以还需要再次提交修改，这样之后补丁作者，时间，补丁提交包含的版本和提交注释都丢失了。

#### patch 命令简单说明

一般`patch`命令使用方法为：`patch -pnum < patchfile`，`-pnum`参数说明如下，常用一般是`-p1`和`-p0`，用于指定patchfile中的路径名前面的`/`要移除几个。

{% highlight text %}
-pnum  or  --strip=num
      Strip  the  smallest  prefix  containing num leading slashes from each file name found in the patch file.  A sequence of one or more adjacent slashes is
      counted as a single slash.  This controls how file names found in the patch file are treated, in case you keep your files in a different directory  than
      the person who sent out the patch.  For example, supposing the file name in the patch file was

         /u/howard/src/blurfl/blurfl.c

      setting -p0 gives the entire file name unmodified, -p1 gives

         u/howard/src/blurfl/blurfl.c

      without the leading slash, -p4 gives

         blurfl/blurfl.c
{% endhighlight %}

前面用`git diff`命令生成的补丁文件`diff1.patch`内容如下，其文件名形式为`a/v03-master.txt`和`b/v030-master.txt`，在当前目录中打补丁，需要把前缀`a/`删除，所以补丁提交给非git项目后，可以使用`patch -p1 < diff1.patch`来应用补丁。

{% highlight bash %}
$> cat diff1.patch
{% endhighlight %}

> diff --git a/v03-master.txt b/v03-master.txt
>
> new file mode 100644
>
> index 0000000..09c74b0
>
> --- /dev/null
>
> +++ b/v03-master.txt
>
> @@ -0,0 +1 @@
>
> +v3@master

git format-patch and git am example
-----

如果是为非git项目提供补丁，可以使用上述的`git diff`命令产生补丁文件，如果是为git项目提供补丁，则推荐使用git方式生成补丁文件和应用补丁文件。

#### git format-patch 简单示例一

{% highlight bash %}
cd ..
./git-patch-commands.sh
cd git-patch-logs
git log --stat | more

echo "git format-patch -1"
git format-patch -1
git reset --hard HEAD~1

echo "git am patchfile"
git am 0001-v03-master.patch
git log --stat | more
{% endhighlight %}

补丁应用完成后，可以看到二次`git log --stat`生成的信息是一致的。

#### git format-patch 简单示例二

在上面`git-patch-commands.sh`脚本中，从`master`分支切出去2个特性分支，并且文件更新有重合冲突之处，现在开始使用补丁方式合并2个特性分支到主分支上。

{% highlight bash %}
cd ..
./git-patch-commands.sh
cd git-patch-logs
git log feature1
git format-patch -3 feature1
git am *.patch
rm -f *.patch
{% endhighlight %}

通过`git log`可以知道`feature1`分支上有3次提交，导出feature1上的3次提交，或者是切到特性分支上导出：

{% highlight bash %}
cd ..
./git-patch-commands.sh
cd git-patch-logs
git checkout feature1
git format-patch master
git checkout master
git am *.patch
rm -f *.patch
{% endhighlight %}

导出的结果如下，每次提交都会生成一个patch文件，并且根据提交时间，由先到后按序号生成，应用补丁时就会按序号从小到大应用。

> 0001-v04-feature1.patch
>
> 0002-v05-feature1.patch
>
> 0003-v06-feature1.patch

#### 输出patch文件的diffstat

{% highlight bash %}
$> git apply --stat *.patch
{% endhighlight %}

#### 测试patch文件应用是否可以成功

{% highlight bash %}
$> git apply --check *.patch
{% endhighlight %}

如果失败，会输出如下提示：

> error: patch failed:

#### 应用patch文件

{% highlight bash %}
$> git am *.patch
{% endhighlight %}

输出如下表示补丁应用成功：

> Applying: v04-feature1
>
> Applying: v05-feature1
>
> Applying: v06-feature1

git am patch 失败处理
-----

前面的例子都是很顺利的执行正确了，实际项目中却经常会发生合并冲突，或者补丁应用失败。

在前面`feature1`分支顺利合并的主分支之后，再应用`feature2`分支的补丁时，就会冲突，`git merge feature2`也会提示合并冲突。

{% highlight bash %}
$> git format-patch -3 feature2
{% endhighlight %}

> 0001-v07-feature2.patch
>
> 0002-v08-feature2.patch
>
> 0003-v09-feature2.patch

{% highlight bash %}
$> git am *.patch
{% endhighlight %}

> Applying: v07-feature2
>
> error: patch failed: v01-master.txt:1
>
> error: v01-master.txt: patch does not apply
>
> Patch failed at 0001 v07-feature2
>
> The copy of the patch that failed is found in:
>
>    /data/git-patch-logs/.git/rebase-apply/patch
>
> When you have resolved this problem, run "git am --continue".
>
> If you prefer to skip this patch, run "git am --skip" instead.
>
> To restore the original branch and stop patching, run "git am --abort".

#### 解决方法一：git apply --reject

默认情况下`git am`失败之后，是不会应用补丁文件的，可以使用`git apply --reject patchfile`强制执行补丁文件，并留下`.rej`后缀的冲突文件。

{% highlight text %}
--reject
    For atomicity, git apply by default fails the whole patch and does not touch the working tree when some of the hunks do not apply. This option makes it apply the parts of the patch that are applicable, and leave the rejected hunks in corresponding *.rej files.
{% endhighlight %}

在`git am`失败之后，不要`git am --abort`放弃应用补丁，而是手工修复冲突的部分代码。

按顺序应用`0001-v07-feature2.patch`补丁：

{% highlight bash %}
$> git apply --reject 0001-v07-feature2.patch
{% endhighlight %}

> Checking patch v01-master.txt...
>
> error: while searching for:
>
> v1@master
>
> &nbsp;
>
> error: patch failed: v01-master.txt:1
>
> Checking patch v07-feature2.txt...
>
> Applying patch v01-master.txt with 1 reject...
>
> Rejected hunk #1.
>
> Applied patch v07-feature2.txt cleanly.

第一个补丁文件`0001-v07-feature2.patch`内容如下：

{% highlight text %}
From 81edb3924de8459d3c55a27c90ee748c537a7ed0 Mon Sep 17 00:00:00 2001
From: yuweijun <yuweijun@live.com>
Date: Sat, 24 Sep 2016 15:06:12 +0800
Subject: [PATCH 1/3] v07-feature2

---
 v01-master.txt   | 1 +
 v07-feature2.txt | 1 +
 2 files changed, 2 insertions(+)
 create mode 100644 v07-feature2.txt

diff --git a/v01-master.txt b/v01-master.txt
index c1112b6..807121d 100644
--- a/v01-master.txt
+++ b/v01-master.txt
@@ -1 +1,2 @@
 v1@master
+v7 append to v01-master.txt
diff --git a/v07-feature2.txt b/v07-feature2.txt
new file mode 100644
index 0000000..7efd0e3
--- /dev/null
+++ b/v07-feature2.txt
@@ -0,0 +1 @@
+v7@feature2
--
2.3.5
{% endhighlight %}

而补丁失败部分生成的`v01-master.txt.rej`文件内容如下：

{% highlight text %}
diff a/v01-master.txt b/v01-master.txt  (rejected hunks)
@@ -1 +1,2 @@
 v1@master
+v7 append to v01-master.txt
{% endhighlight %}

对比上面完整的补丁内容，可以发现，补丁中不冲突的部分代码，已经被应用成功，也就是`v07-feature2.txt`这个文件被添加了，失败的就需要手工修改了，根据`.rej`文件提示编辑原文件`v01-master.txt`，然后加入暂存区，删除`.rej`文件。

{% highlight bash %}
$> git add v01-master.txt
$> git add v07-feature2.txt
$> rm -f v01-master.txt.rej
{% endhighlight %}

继续应用下一个补丁：

{% highlight bash %}
$> git apply --reject 0002-v08-feature2.patch
{% endhighlight %}

同样将生成的`.rej`内容，手工加入到`v02-master.txt`文件中。

{% highlight bash %}
$> git add v02-master.txt
$> git add v08-feature2.txt
$> rm -f v02-master.txt.rej
{% endhighlight %}

同样处理下一个补丁：

{% highlight bash %}
$> git apply --reject 0003-v09-feature2.patch
{% endhighlight %}

修改完成后，添加相应新建文件和修改的文件，然后提交版本。

{% highlight bash %}
$> git add v03-master.txt
$> git add v09-feature2.txt
$> rm -f v02-master.txt.rej
{% endhighlight %}

按`.rej`文件修改原文件之后，使用如下命令解决`git am`冲突：

{% highlight bash %}
$> git am --resolved
{% endhighlight %}

如上述操作成功，会提示相应的`Applying`信息，如果有补丁文件手动修复的内容和`.rej`文件中的不一样，则在执行`git am --resolved`命令之后，会提示如下错误信息：

> Applying: v08-feature2
>
> Applying: v09-feature2
>
> error: patch failed: v03-master.txt:1
>
> error: v03-master.txt: patch does not apply
>
> error: v09-feature2.txt: already exists in index
>
> Patch failed at 0003 v09-feature2
>
> The copy of the patch that failed is found in:
>
>    /data/git-patch-logs/.git/rebase-apply/patch
>
> When you have resolved this problem, run "git am --continue".
>
> If you prefer to skip this patch, run "git am --skip" instead.
>
> To restore the original branch and stop patching, run "git am --abort".

在`git am --resolved`执行成功后，`feature2`的3次提交的所有内容都正确应用到主分支上，可以使用`git log --stat`查看原来在`feature2`分支上的`git commit`的信息。

解决方法二：git am -3

{% highlight bash %}
cd ..
./git-patch-commands.sh
cd git-patch-logs
git merge feature1
git format-patch -3 feature2
git am --3way *.patch
{% endhighlight %}

三方合并冲突，提示错误信息如下，与前面`git apply --reject`的很相似，只是在原文件中加入了与`git merge`相同的版本冲突的内容：

> Applying: v07-feature2
>
> Using index info to reconstruct a base tree...
>
> M       v01-master.txt
>
> Falling back to patching base and 3-way merge...
>
> Auto-merging v01-master.txt
>
> CONFLICT (content): Merge conflict in v01-master.txt
>
> Failed to merge in the changes.
>
> Patch failed at 0001 v07-feature2
>
> The copy of the patch that failed is found in:
>
>    /data/git-patch-logs/.git/rebase-apply/patch
>
> When you have resolved this problem, run "git am --continue".
>
> If you prefer to skip this patch, run "git am --skip" instead.
>
> To restore the original branch and stop patching, run "git am --abort".

查看`v01-master.txt`中的内容：

{% highlight text %}
v1@master
<<<<<<< HEAD
v4有中文内容
=======
v7 append to v01-master.txt
>>>>>>> v07-feature2
{% endhighlight %}

与`git merge`一样，手动解决冲突后，加入暂存区后，按照git提示，执行命令：

{% highlight bash %}
$> git add v01-master.txt
{% endhighlight %}

必须先将修改完成的用`git add`加入暂存区，然后才能`--continue`：

{% highlight bash %}
$> git am --continue
{% endhighlight %}

同样，按上述相同方式解决冲突并加入暂存区，并继续`git am --continue`，直到第3个补丁应用成功，并提示：

> Applying: v09-feature2

表示`git am`应用补丁成功，并且根据补丁进行了相应的提交，然后删除3个patch文件：

{% highlight bash %}
$> rm -f *.patch
{% endhighlight %}

另外如果是使用三方合并的方式打补丁，当补丁被重复应用时，它会更友好的提示补丁已经应用过：

{% highlight bash %}
$> rm -f *.patch
$> git format-patch -1
{% endhighlight %}

> 0001-v09-feature2.patch

{% highlight bash %}
$> git am -3 0001-v09-feature2.patch
{% endhighlight %}

> Applying: v09-feature2
>
> Using index info to reconstruct a base tree...
>
> M       v03-master.txt
>
> Falling back to patching base and 3-way merge...
>
> No changes -- Patch already applied.

如果去掉三方合并的参数`-3`，则认为补丁应用失败，需要`git am --abort`放弃应用补丁，相比第一种方法，第二种解决方法会更简单一些。

git format-patch 手册中的例子
-----

Extract commits between revisions R1 and R2, and apply them on top of the current branch using git am to cherry-pick them:

{% highlight bash %}
$> git format-patch -k --stdout R1..R2 | git am -3 -k
{% endhighlight %}

Extract all commits which are in the current branch but not in the origin branch:

{% highlight bash %}
$> git format-patch origin
{% endhighlight %}

For each commit a separate file is created in the current directory.

Extract all commits that lead to origin since the inception of the project:

{% highlight bash %}
$> git format-patch --root origin
{% endhighlight %}

The same as the previous one:

{% highlight bash %}
$> git format-patch -M -B origin
{% endhighlight %}

Additionally, it detects and handles renames and complete rewrites intelligently to produce a renaming patch. A renaming patch reduces the amount of text output, and generally makes it easier to review. Note that non-Git "patch" programs won’t understand renaming patches, so use it only when you know the recipient uses Git to apply your patch.

Extract three topmost commits from the current branch and format them as e-mailable patches:

{% highlight bash %}
$> git format-patch -3
{% endhighlight %}

If you want to format only `commit` itself, you can do this with:

{% highlight bash %}
$> git format-patch -1 <commit>
{% endhighlight %}

git format-patch 命令示例
-----

通过`-1`提定提交的版本号，只导出该提交的补丁，也可以用`HEAD~n`指定前n个版本：

{% highlight bash %}
$> git format-patch -1 HEAD~3
{% endhighlight %}

> 0001-v06-feature1.patch

没有`-1`参数，则导出从`HEAD~3`开始的所有补丁：

{% highlight bash %}
$> git format-patch HEAD~3
{% endhighlight %}

> 0001-v07-feature2.patch
>
> 0002-v08-feature2.patch
>
> 0003-v09-feature2.patch

导出最近2个提交版本：

{% highlight bash %}
$> git log --oneline | more
$> git format-patch 8da4587...eb27d80
$> git format-patch HEAD~2...HEAD
$> git format-patch HEAD~2..HEAD
{% endhighlight %}

> 0001-v08-feature2.patch
>
> 0002-v09-feature2.patch

References
-----

1. [git-format-patch](https://git-scm.com/docs/git-format-patch)
2. [Git的Patch功能](http://www.cnblogs.com/y041039/articles/2411600.html)
3. [使用Git生成patch和应用patch](http://www.jianshu.com/p/814fb6606734)
4. [Deal with git am failures](http://blog.sina.com.cn/s/blog_5372b1a301015y0n.html)
5. [git format-patch 用法](http://m.blog.csdn.net/article/details?id=9425739)

