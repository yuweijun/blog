---
layout: post
title: "hadoop-2.7.1 single cluster settings on Mac OS"
date: "Wed, 27 Jan 2016 17:58:58 +0800"
categories: java
---

以下操作命令是在Mac OS上进行的，linux上略有不同，比如${JAVA_HOME}设置。

### ssh rsa公钥设置

设置本地ssh公钥私钥，使ssh可以无密码登录本地，以下命令中出现提示，则按提示回答。

{% highlight bash %}
$> ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
# ssh-copy-id localhost
$> cat ~/.ssh/id_rsa.pub >> ~/.ssh/authorized_keys
$> ssh localhost
{% endhighlight %}

### 下载安装预编译版本hadoop-2.7.1

{% highlight bash %}
$> wget https://archive.apache.org/dist/hadoop/core/hadoop-2.7.1/hadoop-2.7.1.tar.gz
$> tar zxvf hadoop-2.7.1.tar.gz
$> mkdir -p ~/Applications
$> mv hadoop-2.7.1 ~/Applications/hadoop
{% endhighlight %}

### 配置hadoop-2.7.1运行环境

添加以下内容到`~/.bash_profile`文件如下(注意linux中JAVA_HOME配置与此不同)，Mac OS还可以[参考此文](/blog/java/2016/01/26/unable-to-load-native-hadoop-library.html)：

{% highlight bash %}
export JAVA_HOME=$(/usr/libexec/java_home)
export HADOOP_HOME=~/Applications/hadoop
export HADOOP_PREFIX=$HADOOP_HOME
export PATH=$PATH:$HADOOP_HOME/bin
export PATH=$PATH:$HADOOP_HOME/sbin
export HADOOP_MAPRED_HOME=$HADOOP_HOME
export HADOOP_COMMON_HOME=$HADOOP_HOME
export HADOOP_HDFS_HOME=$HADOOP_HOME
export YARN_HOME=$HADOOP_HOME
export HADOOP_COMMON_LIB_NATIVE_DIR=$HADOOP_HOME/lib/native
export HADOOP_OPTS="-Djava.library.path=$HADOOP_COMMON_LIB_NATIVE_DIR"
{% endhighlight %}

加载`~/.bash_profile`：

{% highlight bash %}
$> source ~/.bash_profile
{% endhighlight %}

### hadoop-2.7.1伪分布式配置

Hadoop可以在单节点上以伪分布式的方式运行，Hadoop进程以分离的Java进程来运行，节点既作为`NameNode`也作为`DataNode`，同时，读取的是HDFS中的文件。

Hadoop的配置文件位于`${HADOOP_HOME}/etc/hadoop/`中，这与Hadoop 1.x中配置文件位置不同，伪分布式需要修改2个配置文件`core-site.xml`和`hdfs-site.xml`。Hadoop的配置文件是`xml`格式，每个配置通过声明`property`的`name`和`value`来实现。

修改配置文件`etc/hadoop/core-site.xml`，将当中的

{% highlight xml %}
<configuration>
</configuration>
{% endhighlight %}

修改为以下配置，将其中`hadoop-username`替换为实际用户名，这里是Mac OS下的配置，linux下用户路径略有不同：

{% highlight xml %}
<configuration>
    <property>
        <name>hadoop.tmp.dir</name>
        <value>file:///Users/hadoop-username/Applications/hadoop/tmp</value>
        <description>Abase for other temporary directories.</description>
    </property>
    <property>
        <name>fs.defaultFS</name>
        <value>hdfs://localhost:9000</value>
    </property>
</configuration>
{% endhighlight %}

修改配置文件`etc/hadoop/hdfs-site.xml`：

{% highlight xml %}
<configuration>
    <property>
        <name>dfs.replication</name>
        <value>1</value>
    </property>
    <property>
        <name>dfs.namenode.name.dir</name>
        <value>file:///Users/hadoop-username/Applications/hadoop/tmp/dfs/name</value>
    </property>
    <property>
        <name>dfs.datanode.data.dir</name>
        <value>file:///Users/hadoop-username/Applications/hadoop/tmp/dfs/data</value>
    </property>
</configuration>
{% endhighlight %}

> 关于Hadoop配置项的一点说明：
>
> 虽然只需要配置`fs.defaultFS`和`dfs.replication`就可以运行(官方教程如此)，不过若没有配置`hadoop.tmp.dir`参数，则默认使用的临时目录为`/tmp/hadoop-username/`，而这个目录在重启时有可能被系统清理掉，导致必须重新执行`format`才行。所以我们进行了设置，同时也指定`dfs.namenode.name.dir`和`dfs.datanode.data.dir`，否则在接下来的步骤中可能会出错。

### 初始化并启动hadoop hdfs相关服务

配置完成后，执行`NameNode`的格式化:

{% highlight bash %}
$> hdfs namenode -format
{% endhighlight %}

接着调用命令`start-dfs.sh`开启`NameNode`和`DataNode`守护进程。若出现ssh的提示“Are you sure you want to continue connecting”，输入yes即可。

{% highlight bash %}
$> start-dfs.sh

Starting namenodes on [localhost]
localhost: starting datanode, logging to ...
Starting secondary namenodes [0.0.0.0]
0.0.0.0: starting secondarynamenode, logging to ...
{% endhighlight %}

> 启动时可能会有提示警告信息“WARN util.NativeCodeLoader ...”，这个提示不会影响正常使用。

启动完成后，通过`jps`命令查看当前用户在系统中的java进程情况，如果看不到`NameNode`进程就发生错误了，可以到logs目录下查看对应日志：

{% highlight bash %}
$> jps
3285 NameNode
3542 Jps
3367 DataNode
3467 SecondaryNameNode
{% endhighlight %}

成功启动后，可以访问web界面`http://localhost:50070`查看`NameNode`和`Datanode`信息，还可以在线查看HDFS中的文件。

初始化招待MapReduce任务的HDFS目录，`hadoop-username`使用当前系统登录的用户名：

{% highlight bash %}
$> cd ${HADOOP_HOME}
$> hdfs dfs -mkdir /user
$> hdfs dfs -mkdir /user/hadoop-username
{% endhighlight %}

### NameNode启动错误

> 执行命令`hdfs dfs -mkdir -p /user`可能会如下报错：
>
> mkdir: Call From MacBookPro.local/192.168.31.151 to localhost:9000 failed on connection exception: java.net.ConnectException: Connection refused; For more details see:  http://wiki.apache.org/hadoop/ConnectionRefused

查看日志，可以看到其实是因为namenode根本就没有成功启动，所以前面要用`jps`命令查看java进程情况。

> ERROR org.apache.hadoop.hdfs.server.namenode.NameNode: Failed to start namenode.
org.apache.hadoop.hdfs.server.common.InconsistentFSStateException: Directory `/Users/hadoop-username/Applications/hadoop/tmp/dfs/name` is in an inconsistent state: storage directory does not exist or is not accessible.

如果发生这种情况，则执行以下命令，重新格式化namenode节点。

{% highlight bash %}
$> stop-dfs.sh
$> rm -rf tmp
$> hdfs namenode -format
$> start-dfs.sh
{% endhighlight %}

再次检查一下`jps`情况，看看`NameNode`进程是否存在，然后再继续执行前面的mkdir的命令。

### 测试hadoop jar example

复制输入文件到HDFS分布式文件系统中：

{% highlight bash %}
$> hdfs dfs -put etc/hadoop input
{% endhighlight %}

执行测试例子：

{% highlight bash %}
$> hadoop jar share/hadoop/mapreduce/hadoop-mapreduce-examples-2.7.1.jar grep input output 'dfs[a-z.]+'
{% endhighlight %}

从HDFS分布式文件系统中复制出结果，检验命令运行后生成的结果：

{% highlight bash %}
$> hdfs dfs -get output output
$> cat output/*
{% endhighlight %}

或者直接在HDFS分布式文件系统中直接查看运行结果：

{% highlight bash %}
$> hdfs dfs -cat output/*
{% endhighlight %}

### hadoop dfs服务关闭

运行完任务后，可以用以下命令关闭dfs后台服务：

{% highlight bash %}
$> stop-dfs.sh
{% endhighlight %}

### YARN配置

在前面的Hadoop伪分布式测试环境中，没有启动YARN也可正常运行`hadoop jar ...`命令的。

> 有的读者可能会疑惑，怎么启动Hadoop后，见不到书上所说的`JobTracker`和`TaskTracker`，这是因为新版的Hadoop使用了新的`MapReduce`框架(MapReduce V2，也称为YARN，Yet Another Resource Negotiator)。
>
> YARN是从`MapReduce`中分离出来的，负责资源管理与任务调度。YARN运行于`MapReduce`之上，提供了高可用性、高扩展性，前面通过`start-dfs.sh`启动Hadoop，仅仅是启动了`MapReduce`环境，我们可以启动YARN，让YARN来负责资源管理与任务调度。

创建并编辑`${HADOOP_HOME}/etc/hadoop/mapred-site.xml`:

{% highlight bash %}
$> cp etc/hadoop/mapred-site.xml.template etc/hadoop/mapred-site.xml
{% endhighlight %}

{% highlight xml %}
<configuration>
    <property>
        <name>mapreduce.framework.name</name>
        <value>yarn</value>
    </property>
</configuration>
{% endhighlight %}

> 注意： 不启动YARN需重命名`mapred-site.xml`。
>
> 如果不想启动YARN，务必把配置文件`mapred-site.xml`重命名，改成`mapred-site.xml.template`，需要用时改回来就行。否则在该配置文件存在，而未开启YARN的情况下，运行程序会提示“Retrying connect to server: 0.0.0.0/0.0.0.0:8032”的错误，这也是为何该配置文件初始文件名为`mapred-site.xml.template`。

编辑`${HADOOP_HOME}/etc/hadoop/yarn-site.xml`:

{% highlight xml %}
<configuration>
    <property>
        <name>yarn.nodemanager.aux-services</name>
        <value>mapreduce_shuffle</value>
    </property>
</configuration>
{% endhighlight %}

启动`ResourceManager`和`NodeManager`后台进程，并开启历史服务器，才能在web中查看任务运行情况，下面碰到的一个问题可以通过历史服务器上日志查到解决方法。

{% highlight bash %}
$> start-yarn.sh
$> mr-jobhistory-daemon.sh start historyserver
{% endhighlight %}

通过web界面查看`ResourceManager`，默认地址为：`http://localhost:8088/`。

### Mac OS下/bin/java问题

YARN启动完成之后，在命令行中运行以下命令：

{% highlight bash %}
$> hadoop jar share/hadoop/mapreduce/hadoop-mapreduce-examples-2.7.1.jar grep input output-yarn 'dfs[a-z.]+'
{% endhighlight %}

运行命令报错：

{% highlight bash %}
For more detailed output, check application tracking page: http://localhost:8088/cluster/app/application_1454064430945_0001 Then, click on links to logs of each attempt.
Diagnostics: Exception from container-launch.
Container id: container_1454064430945_0001_02_000001
Exit code: 127
Stack trace: ExitCodeException exitCode=127:
    at org.apache.hadoop.util.Shell.runCommand(Shell.java:545)
... more

org.apache.hadoop.mapreduce.lib.input.InvalidInputException: Input path does not exist: hdfs://localhost:9000/user/hadoop-username/grep-temp-1235671912
    at org.apache.hadoop.mapreduce.Job$10.run(Job.java:1290)
... more
{% endhighlight %}

访问错误提示的链接，此处为`http://localhost:8088/cluster/app/application_1454064430945_0001`，依次点击链接查看`Log`，`stderr : Total file length is 48 bytes.`，看到错误日志提示内容：`/bin/bash: /bin/java: No such file or directory`。

> 如果没有启动刚才的历史服务器，根据前面的错误提示，可以到${HADOOP_HOME}目录下的 `logs/userlogs/application_1454064430945_0001/container_1454064430945_0001_02_000001/stderr` 文件中查看错误信息，注意替换`application_id`和`container_id`。

这个错误是因为在Mac OS上的java命令不在`/bin/java`这个位置上，运行以下命令，添加java的软链接：

{% highlight bash %}
$> sudo ln -s /usr/bin/java /bin/java
{% endhighlight %}

再次运行刚才失败的命令：

{% highlight bash %}
$> hadoop jar share/hadoop/mapreduce/hadoop-mapreduce-examples-2.7.1.jar grep input output-yarn 'dfs[a-z.]+'
{% endhighlight %}

### hostname问题

运行后如看到以下错误：

{% highlight bash %}
INFO org.apache.hadoop.yarn.server.resourcemanager.scheduler.capacity.ParentQueue: Application removed - appId: application_1454123074116_0001 user: hadoopuser leaf-queue of parent: root #applications: 0
2016-01-30 11:05:04,791 WARN org.apache.hadoop.yarn.server.resourcemanager.RMAuditLogger: USER=hadoopuser OPERATION=Application Finished - Failed TARGET=RMAppManager RESULT=FAILURE  DESCRIPTION=App failed with state: FAILED   PERMISSIONS=Application application_1454123074116_0001 failed 2 times due to Error launching appattempt_1454123074116_0001_000002. Got exception: java.io.IOException: Failed on local exception: java.io.IOException: java.io.EOFException; Host Details : local host is: "MacBookPro.local/192.168.31.151"; destination host is: "192.168.31.151":50706;
    at org.apache.hadoop.net.NetUtils.wrapException(NetUtils.java:776)
    at org.apache.hadoop.ipc.Client.call(Client.java:1480)
    at org.apache.hadoop.ipc.Client.call(Client.java:1407)
    at org.apache.hadoop.ipc.ProtobufRpcEngine$Invoker.invoke(ProtobufRpcEngine.java:229)
    at com.sun.proxy.$Proxy32.startContainers(Unknown Source)
    at org.apache.hadoop.yarn.api.impl.pb.client.ContainerManagementProtocolPBClientImpl.startContainers(ContainerManagementProtocolPBClientImpl.java:96)
    at org.apache.hadoop.yarn.server.resourcemanager.amlauncher.AMLauncher.launch(AMLauncher.java:120)
    at org.apache.hadoop.yarn.server.resourcemanager.amlauncher.AMLauncher.run(AMLauncher.java:254)
    at java.util.concurrent.ThreadPoolExecutor.runWorker(ThreadPoolExecutor.java:1142)
    at java.util.concurrent.ThreadPoolExecutor$Worker.run(ThreadPoolExecutor.java:617)
    at java.lang.Thread.run(Thread.java:745)
Caused by: java.io.IOException: java.io.EOFException
    at org.apache.hadoop.ipc.Client$Connection$1.run(Client.java:682)
    at java.security.AccessController.doPrivileged(Native Method)
    at javax.security.auth.Subject.doAs(Subject.java:422)
    at org.apache.hadoop.security.UserGroupInformation.doAs(UserGroupInformation.java:1657)
    at org.apache.hadoop.ipc.Client$Connection.handleSaslConnectionFailure(Client.java:645)
    at org.apache.hadoop.ipc.Client$Connection.setupIOstreams(Client.java:732)
    at org.apache.hadoop.ipc.Client$Connection.access$2800(Client.java:370)
    at org.apache.hadoop.ipc.Client.getConnection(Client.java:1529)
    at org.apache.hadoop.ipc.Client.call(Client.java:1446)
    ... 9 more
Caused by: java.io.EOFException
    at java.io.DataInputStream.readInt(DataInputStream.java:392)
    at org.apache.hadoop.security.SaslRpcClient.saslConnect(SaslRpcClient.java:367)
    at org.apache.hadoop.ipc.Client$Connection.setupSaslConnection(Client.java:555)
    at org.apache.hadoop.ipc.Client$Connection.access$1800(Client.java:370)
    at org.apache.hadoop.ipc.Client$Connection$2.run(Client.java:724)
    at org.apache.hadoop.ipc.Client$Connection$2.run(Client.java:720)
    at java.security.AccessController.doPrivileged(Native Method)
    at javax.security.auth.Subject.doAs(Subject.java:422)
    at org.apache.hadoop.security.UserGroupInformation.doAs(UserGroupInformation.java:1657)
    at org.apache.hadoop.ipc.Client$Connection.setupIOstreams(Client.java:720)
    ... 12 more
. Failing the application.
{% endhighlight %}

修改`etc/hadoop/core-site.xml`：
{% highlight xml %}
    <property>
        <name>fs.defaultFS</name>
        <value>hdfs://localhost:9000</value>
    </property>
{% endhighlight %}

根据电脑的hostname

{% highlight bash %}
$> hostname
MacBookPro.local
{% endhighlight %}

修改为：

{% highlight xml %}
    <property>
        <name>fs.defaultFS</name>
        <value>hdfs://MacBookPro.local:9000</value>
    </property>
{% endhighlight %}

或者是`fs.defaultFS`的value用`hdfs://0.0.0.0:9000`，重启HDFS和YARN服务后再运行前面失败的命令：

{% highlight bash %}
$> hadoop jar share/hadoop/mapreduce/hadoop-mapreduce-examples-2.7.1.jar grep input output-hostname 'dfs[a-z.]+'
{% endhighlight %}

### 关闭YARN

{% highlight bash %}
$> stop-yarn.sh
$> mr-jobhistory-daemon.sh stop historyserver

# show java processes
$> jps
{% endhighlight %}

References
-----

1. [Ubuntu 14.10 安裝Hadoop 2.7.1](http://jyc-blog.blogspot.tw/2015/09/ubuntu-1410-hadoop-271.html)
2. [Hadoop: Setting up a Single Node Cluster.](https://hadoop.apache.org/docs/current/hadoop-project-dist/hadoop-common/SingleCluster.html)
3. [Hadoop安装教程单机/伪分布式配置](http://www.powerxing.com/install-hadoop-in-centos/)
4. [unable to load native-hadoop library on Mac OS](/blog/java/2016/01/26/unable-to-load-native-hadoop-library.html)
5. [/bin/bash: /bin/java: No such file or directory](http://bbs.csdn.net/topics/390969463)
6. [JAVA_HOME detected in hadoop-config.sh under OS X does not work](https://issues.apache.org/jira/browse/HADOOP-8717)
7. [YARN Job Problem: exited with exitCode: 127](https://cloudcelebrity.wordpress.com/2014/01/31/yarn-job-problem-application-application_-failed-1-times-due-to-am-container-for-xx-exited-with-exitcode-127/)
8. [yarn问题记录](http://blog.csdn.net/codepeak/article/details/13170147)
