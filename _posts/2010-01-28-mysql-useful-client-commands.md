---
layout: post
title: "mysql客户端中比较有用的几个命令"
date: "Thu Jan 28 2010 15:35:00 GMT+0800 (CST)"
categories: mysql
---

help
-----

{% highlight text %}
mysql> help

For information about MySQL products and services, visit:
   http://www.mysql.com/
For developer information, including the MySQL Reference Manual, visit:
   http://dev.mysql.com/
To buy MySQL Network Support, training, or other products, visit:
   https://shop.mysql.com/

List of all MySQL commands:
Note that all text commands must be first on line and end with ';'
?         (\?) Synonym for `help'.
clear     (\c) Clear the current input statement.
connect   (\r) Reconnect to the server. Optional arguments are db and host.
delimiter (\d) Set statement delimiter.
edit      (\e) Edit command with $EDITOR.
ego       (\G) Send command to mysql server, display result vertically.
exit      (\q) Exit mysql. Same as quit.
go        (\g) Send command to mysql server.
help      (\h) Display this help.
nopager   (\n) Disable pager, print to stdout.
notee     (\t) Don't write into outfile.
pager     (\P) Set PAGER [to_pager]. Print the query results via PAGER.
print     (\p) Print current command.
prompt    (\R) Change your mysql prompt.
quit      (\q) Quit mysql.
rehash    (\#) Rebuild completion hash.
source    (\.) Execute an SQL script file. Takes a file name as an argument.
status    (\s) Get status information from the server.
system    (\!) Execute a system shell command.
tee       (\T) Set outfile [to_outfile]. Append everything into given outfile.
use       (\u) Use another database. Takes database name as argument.
charset   (\C) Switch to another charset. Might be needed for processing binlog with multi-byte charsets.
warnings  (\W) Show warnings after every statement.
nowarning (\w) Don't show warnings after every statement.

For server side help, type 'help contents'
{% endhighlight %}

上面几个命令简单说明。

edit
-----

{% highlight text %}
mysql>\e
{% endhighlight %}

输入`\e`后调用系统的默认文本编辑器，如vi出来，用于编辑sql语句，避免直接在命令行中，太长的sql输入错误，修改起来麻烦，输入完成保存退出即运行。

pager
-----

利用此命令最方便就是可以结合less查看输出结果，有时比用`\G`输出要方便整齐。在mysql命令行中输入：

{% highlight text %}
mysql>pager less -S
{% endhighlight %}

之后的查询就会以不折行的形式输出到控制台。

另一个用法是将SQL输出重定向到另外一个文本日志文件中：

{% highlight text %}
mysql>pager cat > /tmp/sql_log.txt
{% endhighlight %}

以后的查询生成结果会被写入此文件中。

tee
-----

这个命令的功能与`pager`重定向日志文件类似，可以将生成的日志写入外部文件中。

{% highlight text %}
mysql>tee /tmp/sql_log.txt
{% endhighlight %}

system
-----

在mysql console中运行外部操作系统的命令：

{% highlight text %}
mysql>\! ls -l
{% endhighlight %}

source
-----

执行外部的sql文件，有时在命令行中直接导入外部的一个sql文件：

{% highlight text %}
mysql>source /tmp/test.sql;
{% endhighlight %}

这个与在操作系统命令行下执行：

{% highlight bash %}
$> mysql -u test -p < /tmp/test.sql
{% endhighlight %}

效果一样。

References
-----

1. [http://dev.mysql.com/doc/refman/5.0/en/mysql-commands.html](http://dev.mysql.com/doc/refman/5.0/en/mysql-commands.html)
