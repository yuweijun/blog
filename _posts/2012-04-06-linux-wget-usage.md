---
layout: post
title: "wget常用命令"
date: "Fri, 06 Apr 2012 12:32:42 +0800"
categories: linux
---

wget指定下载文件名
-----

{% highlight bash %}
$> wget -O wordpress.zip https://wordpress.org/latest.zip
{% endhighlight %}

wget断点续传
-----

{% highlight text %}
-c --continue
    Continue getting a partially-downloaded file.  This is useful when you want to finish up a download started by a previous instance of Wget, or by another program.
{% endhighlight %}

{% highlight bash %}
$> wget -c ftp://sunsite.doc.ic.ac.uk/ls-lR.Z
{% endhighlight %}

wget整个目录
-----

{% highlight bash %}
$> wget \
     --recursive \
     --no-clobber \
     --page-requisites \
     --html-extension \
     --convert-links \
     --restrict-file-names=windows \
     --domains www.4e00.com \
     --no-parent \
     http://www.4e00.com/blog/
{% endhighlight %}

wget整个目录参数说明
-----

{% highlight text %}
--recursive: download the entire Web site.
--domains www.4e00.com: don't follow links outside www.4e00.com.
--no-parent: don't follow links outside the directory /blog/.
--page-requisites: get all the elements that compose the page (images, CSS and so on).
--html-extension: save files with the .html extension.
--convert-links: convert links so that they work locally, off-line.
--restrict-file-names=windows: modify filenames so that they will work in Windows as well.
--no-clobber: don't overwrite any existing files (used in case the download is interrupted and
resumed).
{% endhighlight %}

wget选项详细说明
-----

{% highlight text %}
wget [参数列表] [目标软件、网页的网址]

-V, -–version 显示软件版本号然后退出；
-h, -–help 显示软件帮助信息；
-e, -–execute=COMMAND 执行一个".wgetrc"命令
-o, -–output-file=FILE 将软件输出信息保存到文件；
-a, -–append-output=FILE 将软件输出信息追加到文件；
-d, -–debug 显示输出信息；
-q, -–quiet 不显示输出信息；
-i, -–input-file=FILE 从文件中取得URL；
-t, -–tries=NUMBER 是否下载次数（0表示无穷次）
-O, -–output-document=FILE 下载文件保存为别的文件名
-nc, -–no-clobber 不要覆盖已经存在的文件
-N, -–timestamping 只下载比本地新的文件
-T, -–timeout=SECONDS 设置超时时间
-Y, -–proxy=on/off 关闭代理
-nd, -–no-directories 不建立目录
-x, -–force-directories 强制建立目录
-–http-user=USER 设置HTTP用户
-–http-passwd=PASS 设置HTTP密码
-–proxy-user=USER 设置代理用户
-–proxy-passwd=PASS 设置代理密码
-r, -–recursive 下载整个网站、目录
-l, -–level=NUMBER 下载层次
-A, -–accept=LIST 可以接受的文件类型
-R, -–reject=LIST 拒绝接受的文件类型
-D, -–domains=LIST 可以接受的域名
-–exclude-domains=LIST 拒绝的域名
-L, -–relative 下载关联链接
-–follow-ftp 只下载FTP链接
-H, -–span-hosts 可以下载外面的主机
-I, -–include-directories=LIST 允许的目录
-X, -–exclude-directories=LIST 拒绝的目录
{% endhighlight %}

References
-----

1. [Linux wget: Your Ultimate Command Line Downloader](http://www.cyberciti.biz/tips/linux-wget-your-ultimate-command-line-downloader.html)
2. [wget vs curl: How to Download Files Using wget and curl](http://www.thegeekstuff.com/2012/07/wget-curl/)
3. [wget 命令用法详解](http://www.cnblogs.com/analyzer/archive/2010/05/04/1727438.html)
