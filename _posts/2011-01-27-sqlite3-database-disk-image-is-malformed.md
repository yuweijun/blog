---
layout: post
title: "sqlite3 database disk image is malformed"
date: "Thu Jan 27 2011 17:46:00 GMT+0800 (CST)"
categories: linux
---

sqlite3 报错提示：

{% highlight text %}
database disk image is malformed
{% endhighlight %}

看提示意思是指数据库的数据文件格式发生异常，所以数据查询和写入不正常，在网上google了一些文章，找到了一个解决方法。

一般来说，sqlite3的数据文件发生这个问题，想直接修复数据是行不通了，

在进入sqlite3后的命令行中，运行以下命令：

{% highlight text %}
PRAGMA integrity_check
*** in database main ***
On tree page 120611 cell 0: 3 of 4 pages missing from overflow list starting at 120617
On tree page 120616 cell 0: 3 of 4 pages missing from overflow list starting at 120621
On tree page 3309 cell 0: 3 of 4 pages missing from over
{% endhighlight %}

假设原数据库名: abc.db

运行命令:

{% highlight bash %}
$> sqlite3 abc.db

.output "data.sql"
.dump
.quit
{% endhighlight %}

再建个新数据库 abcd.db

{% highlight bash %}
$> sqlite3 abcd.db
{% endhighlight %}

然后

{% highlight bash %}
.read "data.sql"
.quit
{% endhighlight %}

最后修复原来的数据库名和文件权限。

References
-----

1. [sqlite3 database disk image malformed](http://vi-i.blogspot.com/2009/02/sqlite3-database-disk-image-is.html)
