---
layout: post
title: "mysql5 table name case sensitive problem on linux"
date: "Wed Jul 15 2009 11:08:00 GMT+0800 (CST)"
categories: mysql
---

add below line under `[mysqld]` block of my.cnf:

{% highlight text %}
lower_case_table_names=1
{% endhighlight %}

References
-----

1. [http://dev.mysql.com/doc/refman/5.1/en/identifier-case-sensitivity.html](http://dev.mysql.com/doc/refman/5.1/en/identifier-case-sensitivity.html)
