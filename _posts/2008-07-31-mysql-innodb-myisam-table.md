---
layout: post
title: "myisam和innodb引擎选择"
date: "Thu Jul 31 2008 20:35:00 GMT+0800 (CST)"
categories: mysql
---

看到一篇讨论MyISAM和INNODB的文章，分析这二种引擎的适用环境。

1. 如果要采用事务，那只能是innodb，没有别的选择。
1. 一般非事务性的数据存储用MyISAM引擎。
1. 如果经常要做大数据量查询，使用MyISAM引擎。
1. 读写比例为9比1的话，使用MyISAM引擎。
1. 如果用INNODB也要注意修改my.cnf，按表存储。

References
------

1. [What should I use MYISAM or INNODB](http://mysqldba.blogspot.com/2008/07/what-should-i-use-myisam-or-innodb.html)
