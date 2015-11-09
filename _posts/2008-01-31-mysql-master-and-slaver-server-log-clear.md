---
layout: post
title: "mysql master and slaver server日志文件清理方法"
date: "Thu Jan 31 2008 15:52:00 GMT+0800 (CST)"
categories: mysql
---

一、要在master要清理日志，需按照以下步骤：
-----

1. 在每个从属服务器上，使用show slave status来检查它正在读取哪个日志。
2. 使用show master logs获得主服务器上的一系列日志。
3. 在所有的从属服务器中判定最早的日志。这个是目标日志。如果所有的从属服务器是更新的，这是清单上的最后一个日志。
4. 制作您将要删除的所有日志的备份。（这个步骤是自选的，但是建议采用。）
5. 清理所有的日志，但是不包括目标日志。

二、要在slaver上清理日志,需要按以下步骤:
-----

1. 在本机上使用show slave status来检查它正在使用哪个relay_log_file日志。
2. 保留此目标日志，可以删除之前的中继日志文件。(中继日志也可以将本机mysql server shutdown后，将hostname-relay-bin.index, hostname-relay-bin.*, relay-log.info删除之后重启mysql server，其中master.info文件不能删除)
3. 使用show master logs获得本机上的一系列日志。
4. 保留最新一个，可以删除之前的bin-log文件。
5. 建议删除之前都先备份，删除后重启server看是否正常同步数据，把当前正式使用的中继日志和bin-log日志删除可能会导致同步不可用。

三、如果主机更新了replication slave user的密码，在slave上执行：
-----

{% highlight bash %}
mysql> stop slave; -- if replication was running
mysql> change master to master_password='new_password';
mysql> start slave; -- if you want to restart replication
{% endhighlight %}

四、change master 使用注意
-----

change master会删除所有的中继日志文件并启动一个新的日志，除非指定了relay_log_file或relay_log_pos，在此情况下，中继日志被保持；relay_log_purge全局变量被静默地设置为0。

change master to可以更新master.info和relay-log.info文件的内容。
