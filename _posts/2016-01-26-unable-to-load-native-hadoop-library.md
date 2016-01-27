---
layout: post
title: "unable to load native-hadoop library on Mac OS"
date: "Tue, 26 Jan 2016 15:08:20 +0800"
categories: java
---

Mac OS 10.9下运行官网直接下载的hadoop-2.7.1包中的`start-dfs.sh`命令后，出现如下提示信息：

> 16/01/26 15:00:41 WARN util.NativeCodeLoader: Unable to load native-hadoop library for your platform... using builtin-java classes where applicable

官方文档说明`native hadoop library`不支持Mac OS X平台，不过可以通过homebrew安装环境后支持，如下：

> The native hadoop library is supported on *nix platforms only. The library does not to work with Cygwin or the Mac OS X platform.

### 安装hadoop-2.7.1编译环境

{% highlight bash %}
$> brew update
$> brew install homebrew/dupes/zlib
$> brew install cmake
$> brew install bzip2
$> brew install lz4
$> brew install snappy
$> brew install openssl
{% endhighlight %}

安装hadoop-2.7.1对应的protobuf-2.5.0版本。

{% highlight bash %}
$> brew install https://raw.githubusercontent.com/Homebrew/homebrew-versions/master/protobuf250.rb
{% endhighlight %}

### 下载git源码并切出hadoop-2.7.1版本

{% highlight bash %}
$> git clone git@github.com:apache/hadoop.git
$> git checkout -b v-2.7.1 release-2.7.1
{% endhighlight %}

### maven编译源码

{% highlight bash %}
$> mvn package -Pdist,native -DskipTests -Dtar
{% endhighlight %}

编译了30分钟左右才完成。

> [INFO] ------------------------------------------------------------------------
>
> [INFO] Reactor Summary:
>
> [INFO]
>
> [INFO] Apache Hadoop Main ................................. SUCCESS [ 18.768 s]
>
> [INFO] Apache Hadoop Project POM .......................... SUCCESS [ 18.085 s]
>
> [INFO] Apache Hadoop Annotations .......................... SUCCESS [ 24.687 s]
>
> [INFO] Apache Hadoop Assemblies ........................... SUCCESS [  0.768 s]
>
> [INFO] Apache Hadoop Project Dist POM ..................... SUCCESS [ 10.420 s]
>
> [INFO] Apache Hadoop Maven Plugins ........................ SUCCESS [01:01 min]
>
> [INFO] Apache Hadoop MiniKDC .............................. SUCCESS [ 58.732 s]
>
> [INFO] Apache Hadoop Auth ................................. SUCCESS [ 18.533 s]
>
> [INFO] Apache Hadoop Auth Examples ........................ SUCCESS [ 12.126 s]
>
> [INFO] Apache Hadoop Common ............................... SUCCESS [05:53 min]
>
> [INFO] Apache Hadoop NFS .................................. SUCCESS [ 14.946 s]
>
> [INFO] Apache Hadoop KMS .................................. SUCCESS [ 45.090 s]
>
> [INFO] Apache Hadoop Common Project ....................... SUCCESS [  0.224 s]
>
> [INFO] Apache Hadoop HDFS ................................. SUCCESS [07:21 min]
>
> [INFO] Apache Hadoop HttpFS ............................... SUCCESS [01:14 min]
>
> [INFO] Apache Hadoop HDFS BookKeeper Journal .............. SUCCESS [ 29.207 s]
>
> [INFO] Apache Hadoop HDFS-NFS ............................. SUCCESS [ 11.920 s]
>
> [INFO] Apache Hadoop HDFS Project ......................... SUCCESS [  0.068 s]
>
> [INFO] hadoop-yarn ........................................ SUCCESS [  0.099 s]
>
> [INFO] hadoop-yarn-api .................................... SUCCESS [ 51.454 s]
>
> [INFO] hadoop-yarn-common ................................. SUCCESS [01:28 min]
>
> [INFO] hadoop-yarn-server ................................. SUCCESS [  0.194 s]
>
> [INFO] hadoop-yarn-server-common .......................... SUCCESS [ 16.224 s]
>
> [INFO] hadoop-yarn-server-nodemanager ..................... SUCCESS [ 28.020 s]
>
> [INFO] hadoop-yarn-server-web-proxy ....................... SUCCESS [  4.838 s]
>
> [INFO] hadoop-yarn-server-applicationhistoryservice ....... SUCCESS [ 11.069 s]
>
> [INFO] hadoop-yarn-server-resourcemanager ................. SUCCESS [ 31.600 s]
>
> [INFO] hadoop-yarn-server-tests ........................... SUCCESS [  8.712 s]
>
> [INFO] hadoop-yarn-client ................................. SUCCESS [  9.745 s]
>
> [INFO] hadoop-yarn-server-sharedcachemanager .............. SUCCESS [  3.970 s]
>
> [INFO] hadoop-yarn-applications ........................... SUCCESS [  0.066 s]
>
> [INFO] hadoop-yarn-applications-distributedshell .......... SUCCESS [  3.557 s]
>
> [INFO] hadoop-yarn-applications-unmanaged-am-launcher ..... SUCCESS [  2.378 s]
>
> [INFO] hadoop-yarn-site ................................... SUCCESS [  0.075 s]
>
> [INFO] hadoop-yarn-registry ............................... SUCCESS [  6.924 s]
>
> [INFO] hadoop-yarn-project ................................ SUCCESS [ 10.061 s]
>
> [INFO] hadoop-mapreduce-client ............................ SUCCESS [  0.229 s]
>
> [INFO] hadoop-mapreduce-client-core ....................... SUCCESS [ 29.888 s]
>
> [INFO] hadoop-mapreduce-client-common ..................... SUCCESS [ 37.864 s]
>
> [INFO] hadoop-mapreduce-client-shuffle .................... SUCCESS [  7.038 s]
>
> [INFO] hadoop-mapreduce-client-app ........................ SUCCESS [ 13.625 s]
>
> [INFO] hadoop-mapreduce-client-hs ......................... SUCCESS [  7.790 s]
>
> [INFO] hadoop-mapreduce-client-jobclient .................. SUCCESS [ 19.295 s]
>
> [INFO] hadoop-mapreduce-client-hs-plugins ................. SUCCESS [  2.261 s]
>
> [INFO] Apache Hadoop MapReduce Examples ................... SUCCESS [  6.444 s]
>
> [INFO] hadoop-mapreduce ................................... SUCCESS [ 10.324 s]
>
> [INFO] Apache Hadoop MapReduce Streaming .................. SUCCESS [  6.257 s]
>
> [INFO] Apache Hadoop Distributed Copy ..................... SUCCESS [02:37 min]
>
> [INFO] Apache Hadoop Archives ............................. SUCCESS [  3.625 s]
>
> [INFO] Apache Hadoop Rumen ................................ SUCCESS [  6.757 s]
>
> [INFO] Apache Hadoop Gridmix .............................. SUCCESS [  6.773 s]
>
> [INFO] Apache Hadoop Data Join ............................ SUCCESS [  4.355 s]
>
> [INFO] Apache Hadoop Ant Tasks ............................ SUCCESS [  3.538 s]
>
> [INFO] Apache Hadoop Extras ............................... SUCCESS [  4.413 s]
>
> [INFO] Apache Hadoop Pipes ................................ SUCCESS [01:20 min]
>
> [INFO] Apache Hadoop OpenStack support .................... SUCCESS [  6.422 s]
>
> [INFO] Apache Hadoop Amazon Web Services support .......... SUCCESS [ 30.433 s]
>
> [INFO] Apache Hadoop Azure support ........................ SUCCESS [ 15.044 s]
>
> [INFO] Apache Hadoop Client ............................... SUCCESS [ 19.818 s]
>
> [INFO] Apache Hadoop Mini-Cluster ......................... SUCCESS [  0.176 s]
>
> [INFO] Apache Hadoop Scheduler Load Simulator ............. SUCCESS [  8.057 s]
>
> [INFO] Apache Hadoop Tools Dist ........................... SUCCESS [ 17.948 s]
>
> [INFO] Apache Hadoop Tools ................................ SUCCESS [  0.159 s]
>
> [INFO] Apache Hadoop Distribution ......................... SUCCESS [01:11 min]
>
> [INFO] ------------------------------------------------------------------------
>
> [INFO] BUILD SUCCESS
>
> [INFO] ------------------------------------------------------------------------
>
> [INFO] Total time: 34:18 min
>
> [INFO] Finished at: 2016-01-26T16:19:07+08:00
>
> [INFO] Final Memory: 174M/522M
>
> [INFO] ------------------------------------------------------------------------

{% highlight bash %}
$> mkdir -p ~/Applications
$> cp -r hadoop-dist/target/hadoop-2.7.1 ~/Applications/hadoop
{% endhighlight %}

### 设置hadoop本机运行环境

> The bin/hadoop script ensures that the native hadoop library is on the library path via the system property: -Djava.library.path=<path>

编译完成之后，添加以下内容到`~/.bash_profile`文件如下，尤其注意最后一句，设置`-Djava.library.path=<path>`：

{% highlight bash %}
export JAVA_HOME=`readlink -f /usr/bin/java | sed 's:/bin/java::g'`
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

加载`~/.bash_profile`，通过`NativeLibraryChecker`检查本地类库加载情况：

{% highlight bash %}
$> source ~/.bash_profile
$> hadoop checknative -a
{% endhighlight %}

运行结果：

> 16/01/26 18:00:41 WARN bzip2.Bzip2Factory: Failed to load/initialize native-bzip2 library system-native, will use pure-Java version
>
> 16/01/26 18:00:41 INFO zlib.ZlibFactory: Successfully loaded & initialized native-zlib library
>
> Native library checking:
>
> hadoop:  true /Users/david/Applications/hadoop/lib/native/libhadoop.dylib
>
> zlib:    true /usr/lib/libz.1.dylib
>
> snappy:  true /usr/lib/libsnappy.so.1
>
> lz4:     true revision:99
>
> bzip2:   false
>
> openssl: false build does not support openssl.
>
> 16/01/26 18:00:41 INFO util.ExitUtil: Exiting with status 1

Refereneces
-----

1. [Building Native Hadoop (v 2.4.1) libraries for OS X](http://gauravkohli.com/2014/09/28/building-native-hadoop-v-2-4-1-libraries-for-os-x/)
2. [Native Libraries Guide](https://hadoop.apache.org/docs/current/hadoop-project-dist/hadoop-common/NativeLibraries.html)
3. [CentOS7中编译Hadoop2.x](http://my.oschina.net/allman90/blog/486768)
4. [Mac OSX 下 Hadoop 使用本地库提高效率](http://rockyfeng.me/hadoop_native_library_mac.html)
