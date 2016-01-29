---
layout: post
title: "hadoop-2.7.1 single cluster settings"
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

修改为以下配置，将其中`<username>`替换为实际用户名，这里是Mac OS下的配置，linux下用户路径略有不同：

{% highlight xml %}
<configuration>
    <property>
        <name>hadoop.tmp.dir</name>
        <value>file:///Users/<username>/Applications/hadoop/tmp</value>
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
        <value>file:///Users/<username>/Applications/hadoop/tmp/dfs/name</value>
    </property>
    <property>
        <name>dfs.datanode.data.dir</name>
        <value>file:///Users/<username>/Applications/hadoop/tmp/dfs/data</value>
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

通过`jps`命令查看当前用户在系统中的java进程情况：

{% highlight bash %}
$> jps
3285 NameNode
3542 Jps
3367 DataNode
3467 SecondaryNameNode
{% endhighlight %}

成功启动后，可以访问web界面`http://localhost:50070`查看`NameNode`和`Datanode`信息，还可以在线查看HDFS中的文件。

初始化招待MapReduce任务的HDFS目录，`<username>`使用当前系统登录的用户名：

{% highlight bash %}
$> cd ${HADOOP_HOME}
$> hdfs dfs -mkdir /user
$> hdfs dfs -mkdir /user/<username>
{% endhighlight %}

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

运行完任务后，可以用以下命令关闭dfs后台服务：

{% highlight bash %}
$> stop-dfs.sh
{% endhighlight %}

### YARN配置

在前面的Hadoop伪分布式测试环境中，没有启动YARN也可正常运行的。

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

编辑`${HADOOP_HOME}/etc/hadoop/yarn-site.xml`:

{% highlight xml %}
<configuration>
    <property>
        <name>yarn.nodemanager.aux-services</name>
        <value>mapreduce_shuffle</value>
    </property>
</configuration>
{% endhighlight %}

启动`ResourceManager`和`NodeManager`后台进程:

{% highlight bash %}
$> start-yarn.sh
{% endhighlight %}

通过web界面查看`ResourceManager`，默认地址为：`http://localhost:8088/`。

关闭YARN：

{% highlight bash %}
$> stop-yarn.sh
{% endhighlight %}

References
-----

1. [Ubuntu 14.10 安裝Hadoop 2.7.1](http://jyc-blog.blogspot.tw/2015/09/ubuntu-1410-hadoop-271.html)
2. [Hadoop: Setting up a Single Node Cluster.](https://hadoop.apache.org/docs/current/hadoop-project-dist/hadoop-common/SingleCluster.html)
3. [Hadoop安装教程单机/伪分布式配置](http://www.powerxing.com/install-hadoop-in-centos/)
4. [unable to load native-hadoop library on Mac OS](/blog/java/2016/01/26/unable-to-load-native-hadoop-library.html)

