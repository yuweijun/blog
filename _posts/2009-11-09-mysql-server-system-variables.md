---
layout: post
title: "mysql server system variables"
date: "Mon Nov 09 2009 15:55:00 GMT+0800 (CST)"
categories: mysql
---

mysql服务器端配置中几个重要系统变量的简单说明。

connect_timeout
-----

{% highlight text%}
Command Line Format  --connect_timeout=#
Config File Format  connect_timeout
Option Sets Variable  Yes, connect_timeout
Variable Name  connect_timeout
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values (<= 5.1.22)
Type  numeric
Default  5
   Permitted Values (>= 5.1.23)
Type  numeric
Default  10

The number of seconds that the mysqld server waits for a connect packet before responding with Bad handshake. The default value is 10 seconds as of MySQL 5.1.23 and 5 seconds before that.

Increasing the connect_timeout value might help if clients frequently encounter errors of the form Lost connection to MySQL server at 'XXX', system error: errno.

增加此值可以解决此问题：Lost connection to MySQL server at 'XXX', system error: errno.
{% endhighlight %}

delay_key_write
-----

{% highlight text%}
Command Line Format  --delay-key-write[=name]
Config File Format  delay-key-write
Option Sets Variable  Yes, delay_key_write
Variable Name  delay-key-write
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  enumeration
Default  ON
Valid Values  ON, OFF, ALL

This option applies only to MyISAM tables.

此参数只针对MyISAM表。

It can have one of the following values to affect handling of the DELAY_KEY_WRITE table option that can be used in CREATE TABLE statements.
Option  Description
OFF  DELAY_KEY_WRITE is ignored.
ON  MySQL honors any DELAY_KEY_WRITE option specified in CREATE TABLE statements. This is the default value.
ALL  All new opened tables are treated as if they were created with the DELAY_KEY_WRITE option enabled.

If DELAY_KEY_WRITE is enabled for a table, the key buffer is not flushed for the table on every index update, but only when the table is closed. This speeds up writes on keys a lot, but if you use this feature, you should add automatic checking of all MyISAM tables by starting the server with the --myisam-recover option (for example, --myisam-recover=BACKUP,FORCE). See Section 5.1.2, “Server Command Options”, and Section 13.5.1, “MyISAM Startup Options”.

Warning

如果启用了DELAY_KEY_WRITE，说明使用该项的表的键缓冲区在每次更新索引时不被清空，只有关闭表时才清空。遮掩盖可以大大加快键的写操作，但如果你使用该特性，你应用--myisam-recover选项启动服务器，为所有MyISAM表添加自动检查(例如，--myisam-recover=BACKUP,FORCE)。

If you enable external locking with --external-locking, there is no protection against index corruption for tables that use delayed key writes.
{% endhighlight %}

expire_logs_days
-----

{% highlight text%}
Command Line Format  --expire_logs_days=#
Config File Format  expire_logs_days
Option Sets Variable  Yes, expire_logs_days
Variable Name  expire_logs_days
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  0
Range  0-99

The number of days for automatic binary log removal. The default is 0, which means “no automatic removal.” Possible removals happen at startup and when the binary log is flushed.

二进制日志自动删除的天数，可设置值的范围为0至99。默认值为0,表示“没有自动删除”。启动时和二进制日志循环时可能删除。
{% endhighlight %}

init_connect
-----

{% highlight text%}
Command Line Format  --init-connect=name
Config File Format  init_connect
Option Sets Variable  Yes, init_connect
Variable Name  init_connect
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  string

A string to be executed by the server for each client that connects. The string consists of one or more SQL statements. To specify multiple statements, separate them by semicolon characters. For example, each client begins by default with autocommit mode enabled. There is no global system variable to specify that autocommit should be disabled by default, but init_connect can be used to achieve the same effect:

SET GLOBAL init_connect='SET autocommit=0';

This variable can also be set on the command line or in an option file. To set the variable as just shown using an option file, include these lines:

[mysqld]
init_connect='SET autocommit=0'

Note that the content of init_connect is not executed for users that have the SUPER privilege. This is done so that an erroneous value for init_connect does not prevent all clients from connecting. For example, the value might contain a statement that has a syntax error, thus causing client connections to fail. Not executing init_connect for users that have the SUPER privilege enables them to open a connection and fix the init_connect value.

在这里比较常用的初始化语句是设置一个字符集，当然也可以直接修改my.cnf中的配置参数(character_set_connection/character_set_client等)：

[mysqld]
init_connect='SET names utf8'
{% endhighlight %}

interactive_timeout
-----

{% highlight text%}
Command Line Format  --interactive_timeout=#
Config File Format  interactive_timeout
Option Sets Variable  Yes, interactive_timeout
Variable Name  interactive_timeout
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  28800
Min Value  1

The number of seconds the server waits for activity on an interactive connection before closing it. An interactive client is defined as a client that uses the CLIENT_INTERACTIVE option to mysql_real_connect(). See also wait_timeout.

一个连接如果长时间空闲会被mysql服务端强行关闭掉，其默认的时间是8小时，在用JDBC连接到mysql中，如果池里的连接超过8小时没有使用，就会产生连接异常。
{% endhighlight %}

wait_timeout
-----

{% highlight text%}
Command Line Format  --wait_timeout=#
Config File Format  wait_timeout
Option Sets Variable  Yes, wait_timeout
Variable Name  wait_timeout
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  28800
Range  1-31536000
   Permitted Values
Type (windows)  numeric
Default  28800
Range  1-2147483

The number of seconds the server waits for activity on a noninteractive connection before closing it. This timeout applies only to TCP/IP and Unix socket file connections, not to connections made via named pipes, or shared memory.

On thread startup, the session wait_timeout value is initialized from the global wait_timeout value or from the global interactive_timeout value, depending on the type of client (as defined by the CLIENT_INTERACTIVE connect option to mysql_real_connect()).

这个值一般不需要去设置，只要设置interactive_timeout。
{% endhighlight %}

join_buffer_size
-----

{% highlight text%}
Command Line Format  --join_buffer_size=#
Config File Format  join_buffer_size
Option Sets Variable  Yes, join_buffer_size
Variable Name  join_buffer_size
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Platform Bit Size  64
Type  numeric
Default  131072
Range  8200-18446744073709547520

The size of the buffer that is used for plain index scans, range index scans, and joins that do not use indexes and thus perform full table scans. Normally, the best way to get fast joins is to add indexes. Increase the value of join_buffer_size to get a faster full join when adding indexes is not possible. One join buffer is allocated for each full join between two tables. For a complex join between several tables for which indexes are not used, multiple join buffers might be necessary.

The maximum allowable setting for join_buffer_size is 4GB. As of MySQL 5.1.23, values larger than 4GB are allowed for 64-bit platforms (except 64-bit Windows, for which large values are truncated to 4GB with a warning).
{% endhighlight %}

key_buffer_size
-----

{% highlight text%}
Command Line Format  --key_buffer_size=#
Config File Format  key_buffer_size
Option Sets Variable  Yes, key_buffer_size
Variable Name  key_buffer_size
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  8388608
Range  8-4294967295

此参数仅用于MyISAM表中，一般此值可设置为物理内存的25-50%，尽可能的使用 Key_reads/Key_read_requests 的比率小于0.01，最重要的mysql性能调优参数。

Index blocks for MyISAM tables are buffered and are shared by all threads. key_buffer_size is the size of the buffer used for index blocks. The key buffer is also known as the key cache.

MyISAM表的索引块分配了缓冲区，由所有线程共享。key_buffer_size是索引块缓冲区的大小。键值缓冲区即为键值缓存。

The maximum allowable setting for key_buffer_size is 4GB on 32-bit platforms. As of MySQL 5.1.23, values larger than 4GB are allowed for 64-bit platforms, except 64-bit Windows prior to MySQL 5.1.31, for which large values are truncated to 4GB with a warning. As of MySQL 5.1.31, values larger than 4GB are also allowed for 64-bit Windows. The effective maximum size might be less, depending on your available physical RAM and per-process RAM limits imposed by your operating system or hardware platform. The value of this variable indicates the amount of memory requested. Internally, the server allocates as much memory as possible up to this amount, but the actual allocation might be less.

key_buffer_size的最大允许设定值为4GB。有效最大值可以更小，取决于可用物理RAM和操作系统或硬件平台强加的每个进程的RAM限制。

You can increase the value to get better index handling for all reads and multiple writes; on a system whose primary function is to run MySQL using the MyISAM storage engine, 25% of the machine's total memory is an acceptable value for this variable. However, you should be aware that, if you make the value too large (for example, more than 50% of the machine's total memory), your system might start to page and become extremely slow. This is because MySQL relies on the operating system to perform file system caching for data reads, so you must leave some room for the file system cache. You should also consider the memory requirements of any other storage engines that you may be using in addition to MyISAM.

增加该值，达到你可以提供的更好的索引处理(所有读和多个写操作)。通常为主要运行MySQL的机器内存的25%。但是，如果你将该值设得过大(例如，大于总内存的50%)，系统将转换为页并变得极慢。MySQL依赖操作系统来执行数据读取时的文件系统缓存，因此你必须为文件系统缓存留一些空间。

For even more speed when writing many rows at the same time, use LOCK TABLES. See Section 7.2.21, “Speed of INSERT Statements”.

同时写多行时要想速度更快，应使用LOCK TABLES。

You can check the performance of the key buffer by issuing a SHOW STATUS statement and examining the Key_read_requests, Key_reads, Key_write_requests, and Key_writes status variables. (See Section 12.5.5, “SHOW Syntax”.) The Key_reads/Key_read_requests ratio should normally be less than 0.01. The Key_writes/Key_write_requests ratio is usually near 1 if you are using mostly updates and deletes, but might be much smaller if you tend to do updates that affect many rows at the same time or if you are using the DELAY_KEY_WRITE table option.

The fraction of the key buffer in use can be determined using key_buffer_size in conjunction with the Key_blocks_unused status variable and the buffer block size, which is available from the key_cache_block_size system variable:

   ((Key_blocks_unused × key_cache_block_size) / key_buffer_size)

用key_buffer_size结合Key_blocks_unused状态变量和缓冲区块大小，可以确定使用的键值缓冲区的比例。从key_cache_block_size服务器变量可以获得缓冲区块大小。使用的缓冲区的比例为：
   ((Key_blocks_unused * key_cache_block_size) / key_buffer_size)
该值为约数，因为键值缓冲区的部分空间被分配用作内部管理结构。

可以创建多个MyISAM键值缓存。4GB限制可以适合每个缓存，而不是一个组。

This value is an approximation because some space in the key buffer may be allocated internally for administrative structures.

It is possible to create multiple MyISAM key caches. The size limit of 4GB applies to each cache individually, not as a group. See Section 7.4.5, “The MyISAM Key Cache”.
{% endhighlight %}

log_slave_updates
-----

{% highlight text%}
Whether updates received by a slave server from a master server should be logged to the slave's own binary log. Binary logging must be enabled on the slave for this variable to have any effect.

假如mysql slave还用作为master server时，需要设置此参数，记录所有SQL操作，提供其他slave复制。
{% endhighlight %}

log_slow_queries
-----

{% highlight text%}
Command Line Format  --log-slow-queries[=name]
Config File Format  log-slow-queries
Option Sets Variable  Yes, log_slow_queries
Variable Name  log_slow_queries
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  boolean

Whether slow queries should be logged. “Slow” is determined by the value of the long_query_time variable.

记录查询非常慢的SQL到一个独立的日志文件中，可用于SQL分析和优化。
{% endhighlight %}

lower_case_file_system
-----

{% highlight text%}
Command Line Format  --lower_case_file_system[=#]
Config File Format  lower_case_file_system
Option Sets Variable  Yes, lower_case_file_system
Variable Name  lower_case_file_system
Variable Scope  Global
Dynamic Variable  No
   Permitted Values
Type  boolean

This variable describes the case sensitivity of file names on the file system where the data directory is located. OFF means file names are case sensitive, ON means they are not case sensitive.
{% endhighlight %}

lower_case_table_names
-----

{% highlight text%}
Command Line Format  --lower_case_table_names[=#]
Config File Format  lower_case_table_names
Option Sets Variable  Yes, lower_case_table_names
Variable Name  lower_case_table_names
Variable Scope  Global
Dynamic Variable  No
   Permitted Values
Type  numeric
Default  0
Range  0-2

If set to 1, table names are stored in lowercase on disk and table name comparisons are not case sensitive. If set to 2 table names are stored as given but compared in lowercase. This option also applies to database names and table aliases. See Section 8.2.2, “Identifier Case Sensitivity”.

这个参数在linux服务器上需要注意，一般是将此值设置为1，使表名全部以小写方式写入硬盘，避免表名的大小写问题。

If you are using InnoDB tables, you should set this variable to 1 on all platforms to force names to be converted to lowercase.

You should not set this variable to 0 if you are running MySQL on a system that does not have case-sensitive file names (such as Windows or Mac OS X). If this variable is not set at startup and the file system on which the data directory is located does not have case-sensitive file names, MySQL automatically sets lower_case_table_names to 2.
{% endhighlight %}

max_allowed_packet
-----

{% highlight text%}
Command Line Format  --max_allowed_packet=#
Config File Format  max_allowed_packet
Option Sets Variable  Yes, max_allowed_packet
Variable Name  max_allowed_packet
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  1048576
Range  1024-1073741824

The maximum size of one packet or any generated/intermediate string.

The packet message buffer is initialized to net_buffer_length bytes, but can grow up to max_allowed_packet bytes when needed. This value by default is small, to catch large (possibly incorrect) packets.

此值需要是1024的一个倍数值，如果操作的数据有使用很长的字符串和大的BLOB字段，如图片，需要增加此值。

You must increase this value if you are using large BLOB columns or long strings. It should be as big as the largest BLOB you want to use. The protocol limit for max_allowed_packet is 1GB. The value should be a multiple of 1024; nonmultiples are rounded down to the nearest multiple.

When you change the message buffer size by changing the value of the max_allowed_packet variable, you should also change the buffer size on the client side if your client program allows it. On the client side, max_allowed_packet has a default of 1GB. Some programs such as mysql and mysqldump enable you to change the client-side value by setting max_allowed_packet on the command line or in an option file.
{% endhighlight %}

max_connect_errors
-----

{% highlight text%}
Command Line Format  --max_connect_errors=#
Config File Format  max_connect_errors
Option Sets Variable  Yes, max_connect_errors
Variable Name  max_connect_errors
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Platform Bit Size  32
Type  numeric
Default  10
Range  1-4294967295
   Permitted Values
Platform Bit Size  64
Type  numeric
Default  10
Range  1-18446744073709547520

If there are more than this number of interrupted connections from a host, that host is blocked from further connections. You can unblock blocked hosts with the FLUSH HOSTS statement.

如果从某台服务器的连接错误过多，会被mysql服务器阻挡连接。
{% endhighlight %}

max_connections
-----

{% highlight text%}
Command Line Format  --max_connections=#
Config File Format  max_connections
Option Sets Variable  Yes, max_connections
Variable Name  max_connections
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values (<= 5.1.14)
Type  numeric
Default  100
   Permitted Values (>= 5.1.15)
Type  numeric
Default  151
Range  1-16384
   Permitted Values (>= 5.1.17)
Type  numeric
Default  151
Range  1-100000

The number of simultaneous client connections allowed. By default, this is 151, beginning with MySQL 5.1.15. (Previously, the default was 100.)

这个值一般都会调整得大一些，如200或者是500，用于处理并发连接数。如果看到“Too many connections”这样的错误提示就是表示mysql服务器的连接数已经被用完。
{% endhighlight %}

max_relay_log_size
-----

{% highlight text%}
Command Line Format  --max_relay_log_size=#
Config File Format  max_relay_log_size
Option Sets Variable  Yes, max_relay_log_size
Variable Name  max_relay_log_size
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  0
Range  0-1073741824

If a write by a replication slave to its relay log causes the current log file size to exceed the value of this variable, the slave rotates the relay logs (closes the current file and opens the next one). If max_relay_log_size is 0, the server uses max_binlog_size for both the binary log and the relay log. If max_relay_log_size is greater than 0, it constrains the size of the relay log, which enables you to have different sizes for the two logs. You must set max_relay_log_size to between 4096 bytes and 1GB (inclusive), or to 0. The default value is 0.
{% endhighlight %}

net_read_timeout
-----

{% highlight text%}
Command Line Format  --net_read_timeout=#
Config File Format  net_read_timeout
Option Sets Variable  Yes, net_read_timeout
Variable Name  net_read_timeout
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  30
Min Value  1

The number of seconds to wait for more data from a connection before aborting the read. This timeout applies only to TCP/IP connections, not to connections made via Unix socket files, named pipes, or shared memory. When the server is reading from the client, net_read_timeout is the timeout value controlling when to abort. When the server is writing to the client, net_write_timeout is the timeout value controlling when to abort.

此参数只针对TCP/IP的mysql连接，当超过此值的秒数后，服务器端会放弃从客户端读取数据。
{% endhighlight %}

old_passwords
-----

{% highlight text%}
Command Line Format  --old_passwords
Config File Format  old-passwords
Option Sets Variable  Yes, old_passwords
Variable Name  old_passwords
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Type  boolean
Default  FALSE

Whether the server should use pre-4.1-style passwords for MySQL user accounts.

如果客户端收到的错误消息为：“Client does not support authentication protocol”，说明服务器使用的是旧的密码格式，需要为用户按旧的格式重设密码。

mysql>SET PASSWORD 'some_user'@'some_host' = OLD_PASSWORD('newpwd');
{% endhighlight %}

read_only
-----

{% highlight text%}
Command Line Format  --read_only
Config File Format  read_only
Option Sets Variable  Yes, read_only
Variable Name  read_only
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  0

This variable is off by default. When it is enabled, the server allows no updates except from users that have the SUPER privilege or (on a slave server) from updates performed by slave threads. On a slave server, this can be useful to ensure that the slave accepts updates only from its master server and not from clients. This variable does not apply to TEMPORARY tables, nor does it prevent the server from inserting rows into the log tables (see Section 5.2.1, “Selecting General Query and Slow Query Log Output Destinations”).

read_only exists only as a GLOBAL variable, so changes to its value require the SUPER privilege. Changes to read_only on a master server are not replicated to slave servers. The value can be set on a slave server independent of the setting on the master.

As of MySQL 5.1.15, the following conditions apply:

    * If you attempt to enable read_only while you have any explicit locks (acquired with LOCK TABLES) or have a pending transaction, an error occurs.
    * If you attempt to enable read_only while other clients hold explicit table locks or have pending transactions, the attempt blocks until the locks are released and the transactions end. While the attempt to enable read_only is pending, requests by other clients for table locks or to begin transactions also block until read_only has been set.
    * read_only can be enabled while you hold a global read lock (acquired with FLUSH TABLES WITH READ LOCK) because that does not involve table locks.

这个参数用于slave服务器上，可以控制避免同步复制发生问题。在master上设置此值与slave是无关的，二都互相独立。
{% endhighlight %}

server_id
-----

{% highlight text%}
Command Line Format  --server-id=#
Config File Format  server-id
Option Sets Variable  Yes, server_id
Variable Name  server_id
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  0
Range  0-4294967295

The server ID, used in replication to give each master and slave a unique identity. This variable is set by the --server-id option. For each server participating in replication, you should pick a positive integer in the range from 1 to 232 – 1 to act as that server's ID.

这个参数用在同步复制时，分配给每个mysql server一个独立唯一的ID标识。
{% endhighlight %}

skip_networking
-----

{% highlight text%}
This is ON if the server allows only local (non-TCP/IP) connections. On Unix, local connections use a Unix socket file. On Windows, local connections use a named pipe or shared memory. On NetWare, only TCP/IP connections are supported, so do not set this variable to ON. This variable can be set to ON with the --skip-networking option.

这个参数在许多linux发行版中是被打开的，这样如果是通过TCP/IP进行连接的话，是无法连接成功的，需要注释掉这一行设置才可以，或者使用socket进行连接。
{% endhighlight %}

slow_query_log
-----

{% highlight text%}
Whether the slow query log is enabled. The value can be 0 (or OFF) to disable the log or 1 (or ON) to enable the log. The default value depends on whether the --slow_query_log option is given (--log-slow-queries before MySQL 5.1.29). The destination for log output is controlled by the log_output system variable; if that value is NONE, no log entries are written even if the log is enabled. The slow_query_log variable was added in MySQL 5.1.12.

用于分析查询效率低下的SQL。
{% endhighlight %}

slow_query_log_file
-----

{% highlight text%}
Version Introduced  5.1.12
Command Line Format  --slow-query-log-file=file_name
Config File Format  slow_query_log_file
Option Sets Variable  Yes, slow_query_log_file
Variable Name  slow_query_log_file
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  filename

The name of the slow query log file. The default value is host_name-slow.log, but the initial value can be changed with the --slow_query_log_file option (--log-slow-queries before MySQL 5.1.29). This variable was added in MySQL 5.1.12.
{% endhighlight %}

sort_buffer_size
-----

{% highlight text%}
Command Line Format  --sort_buffer_size=#
Config File Format  sort_buffer_size
Option Sets Variable  Yes, sort_buffer_size
Variable Name  sort_buffer_size
Variable Scope  Both
Dynamic Variable  Yes
   Permitted Values
Platform Bit Size  32
Type  numeric
Default  2097144
Max Value  4294967295
   Permitted Values
Platform Bit Size  64
Type  numeric
Default  2097144
Max Value  18446744073709547520

Each thread that needs to do a sort allocates a buffer of this size. Increase this value for faster ORDER BY or GROUP BY operations. See Section B.5.4.4, “Where MySQL Stores Temporary Files”.

The maximum allowable setting for sort_buffer_size is 4GB. As of MySQL 5.1.23, values larger than 4GB are allowed for 64-bit platforms (except 64-bit Windows, for which large values are truncated to 4GB with a warning).

对于SQL中用到order by和group by子句的，提高此值可以增加查询的速度。
{% endhighlight %}

table_cache
-----

{% highlight text%}
Version Removed  5.1.3
Version Deprecated  5.1.3
Command Line Format  --table_cache=#
Config File Format  table_cache
Option Sets Variable  Yes, table_cache
Variable Name  table_cache
Variable Scope  Global
Dynamic Variable  Yes
Deprecated  5.1.3, by table_open_cache
   Permitted Values
Type  numeric
Default  64
Range  1-524288

This is the old name of table_open_cache before MySQL 5.1.3. From 5.1.3 on, use table_open_cache instead.
{% endhighlight %}

table_open_cache
-----

{% highlight text%}
Version Introduced  5.1.3
Command Line Format  --table-open-cache=#
Config File Format  table_open_cache
Variable Name  table_open_cache
Variable Scope  Global
Dynamic Variable  Yes
   Permitted Values
Type  numeric
Default  64
Range  64-524288

The number of open tables for all threads. Increasing this value increases the number of file descriptors that mysqld requires. You can check whether you need to increase the table cache by checking the Opened_tables status variable. See Section 5.1.7, “Server Status Variables”. If the value of Opened_tables is large and you don't do FLUSH TABLES often (which just forces all tables to be closed and reopened), then you should increase the value of the table_open_cache variable. For more information about the table cache, see Section 7.4.7, “How MySQL Opens and Closes Tables”. Before MySQL 5.1.3, this variable is called table_cache.
{% endhighlight %}

thread_concurrency
-----

{% highlight text%}
Command Line Format  --thread_concurrency=#
Config File Format  thread_concurrency
Option Sets Variable  Yes, thread_concurrency
Variable Name  thread_concurrency
Variable Scope  Global
Dynamic Variable  No
   Permitted Values
Type  numeric
Default  10
Range  1-512

This variable is specific to Solaris systems, for which mysqld invokes the thr_setconcurrency() with the variable value. This function enables applications to give the threads system a hint about the desired number of threads that should be run at the same time.
{% endhighlight %}
