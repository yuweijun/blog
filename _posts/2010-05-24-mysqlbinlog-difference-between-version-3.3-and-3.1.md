---
layout: post
title: "mysqlbinlog version 3.3和3.1版本之间的输出差异"
date: "Mon May 24 2010 14:59:00 GMT+0800 (CST)"
categories: mysql
---

mysqlbinlog version 3.3中多了一个`--base64-output`参数，默认值为`auto`:

{% highlight text %}
--base64-output[=name]
    Determine when the output statements should be
    base64-encoded BINLOG statements: 'never' disables it and
    works only for binlogs without row-based events; 'auto'
    is the default and prints base64 only when necessary
    (i.e., for row-based events and format description
    events); 'always' prints base64 whenever possible.
    'always' is for debugging only and should not be used in
    a production system. The default is 'auto'.
    --base64-output is a short form for
    --base64-output=always.
{% endhighlight %}

在原来的v3.1版本中没有此参数，对比`mysql-5.0`生成的日志文件的输出结果：

v3.1版本中使用mysqlbinlog输出结果
-----

{% highlight bash %}
$> mysqlbinlog mysql-bin.000182|less
/*!40019 SET @@session.max_insert_delayed_threads=0*/;
/*!50003 SET @OLD_COMPLETION_TYPE=@@COMPLETION_TYPE,COMPLETION_TYPE=0*/;
DELIMITER /*!*/;
# at 4
#100515  1:38:58 server id 25  end_log_pos 98   Start: binlog v 4, server v 5.0.37-log created 100515  1:38:58
# at 98
{% endhighlight %}

v3.3版本中使用mysqlbinlog输出结果
-----

{% highlight bash %}
$> mysqlbinlog --base64-output=never mysql-bin.000182|less
/*!40019 SET @@session.max_insert_delayed_threads=0*/;
/*!50003 SET @OLD_COMPLETION_TYPE=@@COMPLETION_TYPE,COMPLETION_TYPE=0*/;
DELIMITER /*!*/;
# at 4
#100515  1:38:58 server id 25  end_log_pos 98   Start: binlog v 4, server v 5.0.37-log created 100515  1:38:58
# at 98

$> mysqlbinlog --base64-output=auto mysql-bin.000182|less
/*!40019 SET @@session.max_insert_delayed_threads=0*/;
/*!50003 SET @OLD_COMPLETION_TYPE=@@COMPLETION_TYPE,COMPLETION_TYPE=0*/;
DELIMITER /*!*/;
# at 4
#100515  1:38:58 server id 25  end_log_pos 98   Start: binlog v 4, server v 5.0.37-log created 100515  1:38:58
BINLOG '
sortSw8ZAAAAXgAAAGIAAAAAAAQANS4wLjM3LWxvZwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
AAAAAAAAAAAAAAAAAAAAAAAAEzgNAAgAEgAEBAQEEgAASwAEGg==
'/*!*/;
# at 98
{% endhighlight %}

从上面结果比较而言，相当于v3.1版本的`mysqlbinlog`的`base64-output`参数默认为`never`。

在`mysql-5.0`中带的`mysqlbinlog`一般为v3.1版本的，而`mysql-5.1`版本中带的`mysqlbinlog`则为v3.3版本，如果需要将`mysql-5.0`生成的日志文件导入`mysql-5.1`时，需要将`mysqlbinlog`的`base64-output`值设置为`never`。

如果不设置此参数，导入日志时，会在

{% highlight text %}
BINLOG 'sortSw8ZAAAAXgAAAGIAAAAAAAQANS4wLjM3LWxvZwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
AAAAAAAAAAAAAAAAAAAAAAAAEzgNAAgAEgAEBAQEEgAASwAEGg=='
{% endhighlight %}

处报sql语法错误。
