---
layout: post
title:  "mysqld_safe 启动选项文件使用说明"
date: "Thu May 26 2007 22:55:00 GMT+0800 (CST)"
categories: mysql
---

--defaults-file=FILE Use the specified defaults file, 如果给出，必须首选该选项。

--defaults-extra-file=FILE Also use defaults from the specified file, 如果给出，必须首选该选项。

执行mysqld_safe时，后面参数必须先给出--defaults-file或--defaults-extra-file，否则选项文件不生效。例如，该命令将不使用选项文件，这个指定的配置文件就没有生效：

{% highlight bash %}
mysqld_safe --port=port_num --defaults-file=file_name
{% endhighlight %}

相反，使用下面的命令，则选项文件生效：

{% highlight bash %}
mysqld_safe --defaults-file=file_name --port=port_num
{% endhighlight %}
