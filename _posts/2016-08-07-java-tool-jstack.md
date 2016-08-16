---
layout: post
title: "java tool - jstack"
date: Sun, 07 Aug 2016 21:51:18 +0800
categories: java
---

能过java自带一些工具分析java线程的死锁情况和定位特别占用系统资源的线程及相关代码。

java thread dump
-----

以`tomcat`进程为例，当`tomcat`运行出现一些异常情况，比如长时间高CPU消耗，此时可以使用`java thread dump`，分析java的各线程状态，进行问题定位。

{% highlight bash %}
# step 1: find tomcat $pid
$> ps -ef | grep tomcat
# or
$> jps
# step 2: send quit signal to tomcat and tomcat will dump thread into catalina.out
$> kill -3 $pid
# or
$> kill -QUIT $pid
# or
$> jstack -m $pid
# step 3: analyse catalina.out
$> tail -n 300 $CATALINA_HOME/logs/catalina.out
{% endhighlight %}

通过上述操作导出的线程栈信息，和java安装后其自带的一个工具`jstack`导出的线程栈基本上一样，接下来主要分析`jstask`这个工具。


`jstack`可以在应用程序运行时，`Thread.getAllStackTraces()`或`Thread.dumpStack()`完整转储当前所有线程栈，用于分析线程和定位问题。

`stack`的`-l`参数提供了一个较长的转储，包括关于每个Java线程持有锁的更多详细信息，因此发现死锁或程序bug是极其重要的。

利用stack查找占用CPU资源最多的那个线程
-----

一个应用占用CPU很高，除了确实是计算密集型应用之外，通常原因都是出现了死循环。

linux环境下，当发现java进程占用CPU资源很高，且又要想更进一步查出哪一个java线程占用了CPU资源时，按照以下步骤进行查找：

1. 先用top命令找出占用资源厉害的java进程id，如：
2. 如上图所示，java的进程id为'12377'，接下来用top命令单独对这个进程中的所有线程作监视：
top -p 12377 -H
3. 如上图所示，linux下，所有的java内部线程，其实都对应了一个进程id，也就是说，linux上的sun jvm将java程序中的线程映射为了操作系统进程；我们看到，占用CPU资源最高的那个进程id是'15417'，这个进程id对应java线程信息中的'nid'('n' stands for 'native');
4. 要想找到到底是哪段具体的代码占用了如此多的资源，先使用jstack打出当前栈信息到一个文件里, 比如stack.log： jstack 12377 > stack.log
5. 其次将需要的线程ID转换为16进制格式： printf "%x\n" tid
6. grep -i "16进制ID" stack.log

1. 通过ps aux | grep PID命令，可以进一步确定是tomcat进程出现了问题。但是，怎么定位到具体线程或者代码呢？
2. 首先显示线程列表: ps -mp pid -o THREAD,tid,time
3. 找到了耗时最高的线程28802，占用CPU时间快两个小时了！ 其次将需要的线程ID转换为16进制格式： printf "%x\n" tid
4. jstack pid |grep tid -A 30

{% highlight bash %}
# step 1: find tomcat $pid
$> ps -ef|grep tomcat
# step 2: find java thread $tid which cause high CPU usage
$> top -Hp $pid
# step 3: dump thread stack trace
$> jstack $pid > jstack.log
# step 4: change $tid to nid
$> printf "%x\n" $tid
# step 5: find $nid in jstack.log
$> grep -A 30 -i $tid jstack.log
{% endhighlight %}

对于线程是否挂住的处理一般是这样:
1. 先进行一次thread dump (jstack -m <pid> 或者 kill -3 <pid> ， 或者使用jconsole， jvisualvm等) (jstack 命令有一些选项不是每个平台都支持的, jconsole jvisualvm都是有界面的， 如果你要运行一般需要配置agent或者重定向display到某台机器).

2. 然后过了一段时间再做一次， 如果发现同一个thread NID 还是停在同一个地方， 基本上可以怀疑是否挂住了(一般只需要查看你业务相关的stack信息就行了).

3. 还有一种就是你的日志很详细， 也可以看到一些的情况(打印到某个地方就卡住了, 呵呵).

JVM的内存问题:
1. 这个首先建议所有的应用都加上GC log ， 他能告诉你每次gc是快还是满，有多频繁， 如果配合其他参数还能输出很多有用的参考信息.

2. 请应用都配置上-XX:+HeapDumpOnOutOfMemoryError ， 如果OOM 会有memory dump， 可以使用MAT进行分析， 可以知道很多你想要的知道的请情况.

3. 另外就是可以手工进行一些memory dump（jmap -dump:file=xxx.bin <pid> 如果是1.5, jmap -heap:format=b <pid>）, 不过对于堆比较大的java应用，会暂停很长时间。对线上系统使用请谨慎.

4. 上面都是对一些堆的情况分析。 如果是堆外的，某一些情况是可以通过JVM的特定参数可以拿到(可以使用jinfo 命令拿到很多东西， 还可以使用btrace 等)， 其他的就是就是和分析c/c++程序一样了。

性能:
1. 如果是查看性能可以使用oprofile， 或者淘宝的tprofiler， 各种profiler等， 可以查看到某个api的运行时间(profiler 对应用性能一定有一些影响, 所以如果是对线上的系统使用profiler请谨慎).

2. 如果是线上系统(之前没有配置过什么profiler的东西， 又不想停机), 可以简单的使用btrace 进行某个api的拦截， 打印出每次运行的时间(前提是你已经怀疑了某个方法).


以下是针对tomcat上的应用的. 其他的java程序, 只要你能触发他的thread dump并且拿到结果, 也是一样.
1. ps -ef | grep java
找到你的java程序的进程id, 定位 pid
2. top -Hp $pid
shift+t
查看耗cpu时间最多的几个线程, 记录下线程的id
3. 把上诉线程ID转换成16进制小写 比如 : 0x12ef
4. kill -3 $pid 触发tomcat的thread dump
5. 找到tomcat的catalin.out 日志, 把 上面几个线程对应的代码段拿出来.
DONE.


前言
Java Thread Dump是一个非常有用的应用诊断工具, 通过thread dump出来的信息, 可以定位到你需要了解的线程, 以及这个线程的调用栈. 如果配合linux的top命令, 可以找到你的系统中的最耗CPU的线程代码段, 这样才能有针对性地进行优化.

场景和实践
    2.1. 后台系统一直是在黑盒运行, 除了能暂停一部分任务的执行, 根本无法知道哪些任务耗CPU过多。所以一直以为是业务代码的问题, 经过各种优化(删减没必要的逻辑, 合并写操作)等等优化, 系统负载还是很高. 没什么访问量, 后台任务处理也就是每天几百万的级别, load还是达到了15以上. CPU只有4核，天天收到load告警却无从下手, 于是乎就被迫来分析一把线程.

大致内容如下:

2012-04-13 16:30:41
Full thread dump OpenJDK 64-Bit Server VM (1.6.0-b09 mixed mode):
"TP-Processor12" daemon prio=10 tid=0x00000000045acc00 nid=0x7f19 in Object.wait() [0x00000000483d0000..0x00000000483d0a90]
java.lang.Thread.State: WAITING (on object monitor)
at java.lang.Object.wait(Native Method)
- waiting on <0x00002aaab5bfce70> (a org.apache.tomcat.util.threads.ThreadPool$ControlRunnable)
at java.lang.Object.wait(Object.java:502)
at org.apache.tomcat.util.threads.ThreadPool$ControlRunnable.run(ThreadPool.java:662)
- locked <0x00002aaab5bfce70> (a org.apache.tomcat.util.threads.ThreadPool$ControlRunnable)
at java.lang.Thread.run(Thread.java:636)

"TP-Processor11" daemon prio=10 tid=0x00000000048e3c00 nid=0x7f18 in Object.wait() [0x00000000482cf000..0x00000000482cfd10]
java.lang.Thread.State: WAITING (on object monitor)
....
"VM Thread" prio=10 tid=0x00000000042ff400 nid=0x77de runnable"GC task thread#0 (ParallelGC)" prio=10 tid=0x000000000429c400 nid=0x77d9 runnable

"GC task thread#1 (ParallelGC)" prio=10 tid=0x000000000429d800 nid=0x77da runnable

"GC task thread#2 (ParallelGC)" prio=10 tid=0x000000000429ec00 nid=0x77db runnable

"GC task thread#3 (ParallelGC)" prio=10 tid=0x00000000042a0000 nid=0x77dc runnable

"VM Periodic Task Thread" prio=10 tid=0x0000000004348400 nid=0x77e5 waiting on condition

JNI global references: 815

Heap
PSYoungGen      total 320192K, used 178216K [0x00002aaadce00000, 0x00002aaaf1800000, 0x00002aaaf1800000)
eden space 303744K, 55% used [0x00002aaadce00000,0x00002aaae718e048,0x00002aaaef6a0000)
from space 16448K, 65% used [0x00002aaaf0690000,0x00002aaaf110c1b0,0x00002aaaf16a0000)
to   space 16320K, 0% used [0x00002aaaef6a0000,0x00002aaaef6a0000,0x00002aaaf0690000)
PSOldGen        total 460992K, used 425946K [0x00002aaab3a00000, 0x00002aaacfc30000, 0x00002aaadce00000)
object space 460992K, 92% used [0x00002aaab3a00000,0x00002aaacd9f6a30,0x00002aaacfc30000)
PSPermGen       total 56192K, used 55353K [0x00002aaaae600000, 0x00002aaab1ce0000, 0x00002aaab3a00000)
object space 56192K, 98% used [0x00002aaaae600000,0x00002aaab1c0e520,0x00002aaab1ce0000)
最后一段是系统的对内存的使用情况.
2.3. 要知道thread dump是不会告诉你每个线程的负载情况的, 需要知道每个线程的负载情况, 还得靠top命令来查看.
    【linux 命令】：top -H -p $PID
这时候, 可以看到java进程下各个线程的负载和内存等使用情况. 也不用全部搞下来, 只要top几个负载过高的记录即可（最好按下SHIFT+T 按CPU耗时总时间倒序排序，这样找到的top几个是最耗CPU时间的，而且系统启动时间应该持续15分钟以上，这样容易看出哪个线程耗时多。）
     大致内容如下:
?
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
Tasks: 118 total,   2 running, 116 sleeping,   0 stopped,   0 zombie
Cpu(s): 92.6%us,  2.3%sy,  0.0%ni,  3.8%id,  0.7%wa,  0.1%hi,  0.7%si,  0.0%st
Mem:   4054168k total,  3892212k used,   161956k free,   115816k buffers
Swap:  4192956k total,   294448k used,  3898508k free,  2156024k cachedPID USER      PR  NI  VIRT  RES  SHR S %CPU %MEM    TIME+  COMMAND
8091 admin     16   0 1522m 814m 9660 R 22.3 20.6   4:05.61 java
8038 admin     16   0 1522m 814m 9660 R 10.3 20.6   2:46.31 java
8043 admin     15   0 1522m 814m 9660 S  3.7 20.6   1:52.04 java
8039 admin     15   0 1522m 814m 9660 S  0.7 20.6   2:10.98 java
8041 admin     15   0 1522m 814m 9660 S  0.7 20.6   1:39.66 java
8009 admin     15   0 1522m 814m 9660 S  0.3 20.6   0:27.05 java
8040 admin     15   0 1522m 814m 9660 S  0.3 20.6   0:51.46 java
7978 admin     25   0 1522m 814m 9660 S  0.0 20.6   0:00.00 java
7980 admin     19   0 1522m 814m 9660 S  0.0 20.6   0:05.05 java
7981 admin     16   0 1522m 814m 9660 S  0.0 20.6   0:06.31 java
7982 admin     15   0 1522m 814m 9660 S  0.0 20.6   0:06.50 java
7983 admin     15   0 1522m 814m 9660 S  0.0 20.6   0:06.66 java
7984 admin     15   0 1522m 814m 9660 S  0.0 20.6   0:06.87 java
7985 admin     15   0 1522m 814m 9660 S  0.0 20.6   0:33.82 java
几个字段跟top的字段意思是一致的, 就是这里的 PID是 线程在系统里面的ID, 也就是进程每创建一个线程, 不仅进程自己会分配ID, 系统也会的. 接下来的问题排查就是主要根据这个PID来走的.
看到上面的部分数据, 当前正在跑的任务中, CPU占用最高的几个线程ID
2.4. 如果不借助工具, 自己分析的话, 可以把PID字段从10进制数改为 16进制, 然后到threaddump日志中去查找一把, 找对对应的线程上下文信息, 就可以知道哪段代码耗CPU最多了.
比如 8091  的16进制是 1F9B, 查找 thread dump 日志中, nid=0x1F9B 的线程( 这里的nid意思是nativeid, 也就是上面讲的系统为线程分配的ID), 然后找到相关的代码段, 进行优化即可.
比如
?
1
2
3
4
5
"链路检测" prio=10 tid=0x00002aaafa498000 nid=0x1F9B runnable [0x0000000045fac000..0x0000000045facd10]</div>

java.lang.Thread.State: RUNNABLE
at cn.emay.sdk.communication.socket.AsynSocket$CheckConnection.run(AsynSocket.java:112)
at java.lang.Thread.run(Thread.java:636)
可以看出, 这是一个 发短信的客户端的链路检测引擎的系统负载飙升. (实际上这个线程引起的负载绝不止这么一点.)


使用工具的话, 可以看到更多一点的信息, java的tda工具就是专门分析thread dump的.
https://java.net/projects/tda/downloads/directory/webstart


top中shift+h查找出哪个线程消耗的cpu高

jstack查找这个线程的信息
jstack [进程]|grep -A 10 [线程的16进制]
即：
Java代码  收藏代码
jstack 21125|grep -A 10 52f1

-A 10表示查找到所在行的后10行。21233用计算器转换为16进制52f1，注意字母是小写。
结果：
Java代码  收藏代码
"http-8081-11" daemon prio=10 tid=0x00002aab049a1800 nid=0x52f1 in Object.wait() [0x0000000042c75000]
   java.lang.Thread.State: WAITING (on object monitor)
     at java.lang.Object.wait(Native Method)
     at java.lang.Object.wait(Object.java:485)
     at org.apache.tomcat.util.net.JIoEndpoint$Worker.await(JIoEndpoint.java:416)

说不定可以一下子定位到出问题的代码。


这个脚本用于定位出当前java进程里最耗cpu的那个线程，给出cpu的占用率和当前堆栈信息。这个脚本仅限于linux上，我没有找到在mac下定位线程使用cpu情况的工具，如果你知道请告诉我一下。

先模拟一个耗cpu的java进程，启动一个scala的repl并在上面跑一段死循环：

scala> while(true) {}
脚本执行效果：

$ ./busythread.sh `pidof java`
tid: 431  cpu: %98.8
"main" prio=10 tid=0x00007f777800a000 nid=0x1af runnable [0x00007f7781c2e000]
    java.lang.Thread.State: RUNNABLE
    at $line3.$read$$iw$$iw$.<init>(<console>:8)
    at $line3.$read$$iw$$iw$.<clinit>(<console>)
    at $line3.$eval$.$print$lzycompute(<console>:7)
    - locked <0x00000000fc201758> (a $line3.$eval$)
    at $line3.$eval$.$print(<console>:6)
    at $line3.$eval.$print(<console>)
    at sun.reflect.NativeMethodAccessorImpl.invoke0(Native Method)
    at sun.reflect.NativeMethodAccessorImpl.invoke(NativeMethodAccessorImpl.java:57)
    at sun.reflect.DelegatingMethodAccessorImpl.invoke(DelegatingMethodAccessorImpl.java:43)
    at java.lang.reflect.Method.invoke(Method.java:606)
    at scala.tools.nsc.interpreter.IMain$ReadEvalPrint.call(IMain.scala:739)
    at scala.tools.nsc.interpreter.IMain$Request.loadAndRun(IMain.scala:986)
    at scala.tools.nsc.interpreter.IMain$WrappedRequest$$anonfun$loadAndRunReq$1.apply(IMain.scala:593)
    at scala.tools.nsc.interpreter.IMain$WrappedRequest$$anonfun$loadAndRunReq$1.apply(IMain.scala:592)
    at scala.reflect.internal.util.ScalaClassLoader$class.asContext(ScalaClassLoader.scala:31)
    at scala.reflect.internal.util.AbstractFileClassLoader.asContext(AbstractFileClassLoader.scala:19)
    at scala.tools.nsc.interpreter.IMain$WrappedRequest.loadAndRunReq(IMain.scala:592)
    at scala.tools.nsc.interpreter.IMain.interpret(IMain.scala:524)
    at scala.tools.nsc.interpreter.IMain.interpret(IMain.scala:520)
脚本内容：

#!/bin/bash

if [ $# -eq 0 ];then
    echo "please enter java pid"
    exit -1
fi

pid=$1
jstack_cmd=""

if [[ $JAVA_HOME != "" ]]; then
    jstack_cmd="$JAVA_HOME/bin/jstack"
else
    r=`which jstack 2>/dev/null`
    if [[ $r != "" ]]; then
        jstack_cmd=$r
    else
        echo "can not find jstack"
        exit -2
    fi
fi

#line=`top -H  -o %CPU -b -n 1  -p $pid | sed '1,/^$/d' | grep -v $pid | awk 'NR==2'`

line=`top -H -b -n 1 -p $pid | sed '1,/^$/d' | sed '1d;/^$/d' | grep -v $pid | sort -nrk9 | head -1`
echo "$line" | awk '{print "tid: "$1," cpu: %"$9}'
tid_0x=`printf "%0x" $(echo "$line" | awk '{print $1}')`
$jstack_cmd $pid | grep $tid_0x -A20 | sed -n '1,/^$/p'
脚本已放到服务器上，可以通过下面的方式执行：

$ bash <(curl -s http://hongjiang.info/busythread.sh)  java_pid

Linux下如何查看高CPU占用率线程

在 Linux 下 top 工具可以显示 cpu 的平均利用率(user,nice,system,idle,iowait,irq,softirq,etc.)，可以显示每个 cpu 的利用率。但是无法显示每个线程的 cpu 利用率情况，
这时就可能出现这种情况，总的 cpu 利用率中 user 或 system 很高，但是用进程的 cpu 占用率进行排序时，没有进程的 user 或 system 与之对应。

可以用下面的命令将 cpu 占用率高的线程找出来:
$ ps H -eo user,pid,ppid,tid,time,%cpu,cmd --sort=%cpu

这个命令首先指定参数'H'，显示线程相关的信息，格式输出中包含:user,pid,ppid,tid,time,%cpu,cmd，然后再用%cpu字段进行排序。这样就可以找到占用处理器的线程了。


  jstack主要用来查看某个Java进程内的线程堆栈信息。语法格式如下：

jstack [option] pid
jstack [option] executable core
jstack [option] [server-id@]remote-hostname-or-ip
    命令行参数选项说明如下：

-l long listings，会打印出额外的锁信息，在发生死锁时可以用jstack -l pid来观察锁持有情况
-m mixed mode，不仅会输出Java堆栈信息，还会输出C/C++堆栈信息（比如Native方法）
    jstack可以定位到线程堆栈，根据堆栈信息我们可以定位到具体代码，所以它在JVM性能调优中使用得非常多。

第一步先找出Java进程ID，我部署在服务器上的Java应用名称为mrf-center：

root@ubuntu:/# ps -ef | grep mrf-center | grep -v grep
root     21711     1  1 14:47 pts/3    00:02:10 java -jar mrf-center.jar
    得到进程ID为21711，第二步找出该进程内最耗费CPU的线程，可以使用ps -Lfp pid或者ps -mp pid -o THREAD, tid, time或者top -Hp pid，我这里用第三个，输出如下：

IME列就是各个Java线程耗费的CPU时间，CPU时间最长的是线程ID为21742的线程，用

printf "%x\n" 21742
    得到21742的十六进制值为54ee，下面会用到。

    OK，下一步终于轮到jstack上场了，它用来输出进程21711的堆栈信息，然后根据线程ID的十六进制值grep，如下：

root@ubuntu:/# jstack 21711 | grep 54ee


一、Thread Dump介绍
1.1什么是Thread Dump？
Thread Dump是非常有用的诊断Java应用问题的工具。每一个Java虚拟机都有及时生成所有线程在某一点状态的thread-dump的能力，虽然各个 Java虚拟机打印的thread dump略有不同，但是大多都提供了当前活动线程的快照，及JVM中所有Java线程的堆栈跟踪信息，堆栈信息一般包含完整的类名及所执行的方法，如果可能的话还有源代码的行数。

1.2 Thread Dump特点
1. 能在各种操作系统下使用
2. 能在各种Java应用服务器下使用
3. 可以在生产环境下使用而不影响系统的性能
4. 可以将问题直接定位到应用程序的代码行上

1.3 Thread Dump 能诊断的问题
1. 查找内存泄露，常见的是程序里load大量的数据到缓存；
2. 发现死锁线程；

1.4如何抓取Thread Dump
一般当服务器挂起,崩溃或者性能底下时,就需要抓取服务器的线程堆栈(Thread Dump)用于后续的分析. 在实际运行中，往往一次 dump的信息，还不足以确认问题。为了反映线程状态的动态变化，需要接连多次做threaddump，每次间隔10-20s，建议至少产生三次 dump信息，如果每次 dump都指向同一个问题，我们才确定问题的典型性。

有很多方式可用于获取ThreadDump, 下面列出一部分获取方式：
操作系统命令获取ThreadDump:
Windows:
1.转向服务器的标准输出窗口并按下Control + Break组合键, 之后需要将线程堆栈复制到文件中；
UNIX/ Linux：
首先查找到服务器的进程号(process id), 然后获取线程堆栈.
1. ps –ef  | grep java
2. kill -3 <pid>
注意：一定要谨慎, 一步不慎就可能让服务器进程被杀死。kill -9 命令会杀死进程。

JVM 自带的工具获取线程堆栈:
JDK自带命令行工具获取PID，再获取ThreadDump:
1. jps 或 ps –ef|grepjava (获取PID)
2. jstack [-l ]<pid> | tee -a jstack.log  (获取ThreadDump)

二、java线程的状态转换介绍(为后续分析做准备)

2.1 新建状态（New）
用new语句创建的线程处于新建状态，此时它和其他Java对象一样，仅仅在堆区中被分配了内存。
2.2 就绪状态（Runnable）
当一个线程对象创建后，其他线程调用它的start()方法，该线程就进入就绪状态，Java虚拟机会为它创建方法调用栈和程序计数器。处于这个状态的线程位于可运行池中，等待获得CPU的使用权。
2.3 运行状态（Running）
处于这个状态的线程占用CPU，执行程序代码。只有处于就绪状态的线程才有机会转到运行状态。
2.4 阻塞状态（Blocked）
阻塞状态是指线程因为某些原因放弃CPU，暂时停止运行。当线程处于阻塞状态时，Java虚拟机不会给线程分配CPU。直到线程重新进入就绪状态，它才有机会转到运行状态。
阻塞状态可分为以下3种：
 1）位于对象等待池中的阻塞状态（Blocked in object’s wait pool）：当线程处于运行状态时，如果执行了某个对象的wait()方法，Java虚拟机就会把线程放到这个对象的等待池中，这涉及到“线程通信”的内容。
 2）位于对象锁池中的阻塞状态（Blocked in object’s lock pool）：当线程处于运行状态时，试图获得某个对象的同步锁时，如果该对象的同步锁已经被其他线程占用，Java虚拟机就会把这个线程放到这个对象的锁池中，这涉及到“线程同步”的内容。
 3）其他阻塞状态（Otherwise Blocked）：当前线程执行了sleep()方法，或者调用了其他线程的join()方法，或者发出了I/O请求时，就会进入这个状态。
2.5 死亡状态（Dead）
当线程退出run()方法时，就进入死亡状态，该线程结束生命周期。

三、Thread Dump分析
通过前面1.4部分的方法，获取Thread Dump信息后，对其进行分析；
3.1 首先介绍一下Thread Dump信息的各个部分
头部信息：
时间，jvm信息
2011-11-02 19:05:06
Full thread dump Java HotSpot(TM) Server VM (16.3-b01 mixed mode):

线程info信息块：
1. "Timer-0" daemon prio=10tid=0xac190c00 nid=0xaef in Object.wait() [0xae77d000]
2.  java.lang.Thread.State: TIMED_WAITING (on object monitor)
3.  atjava.lang.Object.wait(Native Method)
4.  -waiting on <0xb3885f60> (a java.util.TaskQueue)     ###继续wait
5.  atjava.util.TimerThread.mainLoop(Timer.java:509)
6.  -locked <0xb3885f60> (a java.util.TaskQueue)         ###已经locked
7.  atjava.util.TimerThread.run(Timer.java:462)
* 线程名称：Timer-0
* 线程类型：daemon
* 优先级: 10，默认是5
* jvm线程id：tid=0xac190c00，jvm内部线程的唯一标识（通过java.lang.Thread.getId()获取，通常用自增方式实现。）
* 对应系统线程id（NativeThread ID）：nid=0xaef，和top命令查看的线程pid对应，不过一个是10进制，一个是16进制。（通过命令：top -H -p pid，可以查看该进程的所有线程信息）
* 线程状态：in Object.wait().
* 起始栈地址：[0xae77d000]
* Java thread statck trace：是上面2-7行的信息。到目前为止这是最重要的数据，Java stack trace提供了大部分信息来精确定位问题根源。


六、给运维人员的简单步骤

如果事发突然且不能留着现场太久，要求运维人员：
1. top: 记录cpu idle%。如果发现cpu占用过高，则c, shift+h, shift + p查看线程占用CPU情况，并记录
2. free: 查看内存情况，如果剩余量较小，则top中shift+m查看内存占用情况，并记录
3. 如果top中发现占用资源较多的进程名称（例如java这样的通用名称）不太能说明进程身份，则要用ps xuf | grep java等方式记录下具体进程的身份
4. 取jstack结果。假如取不到，尝试加/F
  jstack命令：jstack PID > jstack.log
5. jstat查看OLD区占用率。如果占用率到达或接近100%，则jmap取结果。假如取不到，尝试加/F
  jstat命令： jstat -gcutil PID
    S0  S1    E      O     P     YGC    YGCT    FGC  FGCT   GCT
   0.00 21.35 88.01 97.35 59.89 111461 1904.894 1458 291.369 2196.263
  jmap命令：  jmap -dump:file=dump.map PID
6. 重启服务

public class MyThread implements Runnable{

     public void run() {
         synchronized(this) {
              for (int i = 0; i < 1; i--) {
                   System.out.println(Thread.currentThread().getName() + " synchronized loop " + i);
              }
         }
    }
    public static void main(String[] args) {
        MyThread t1 = new MyThread();
         Thread ta = new Thread(t1, "A");
         Thread tb = new Thread(t1, "B");
         ta.start();
         tb.start();
    }

}


每个 Monitor在某个时刻，只能被一个线程拥有，该线程就是 “Active Thread”，而其它线程都是 “Waiting Thread”，分别在两个队列 “ Entry Set”和 “Wait Set”里面等候。
在 “ Entry Set”里面的线程都等待拿到Monitor，拿到了线程就成为了Runnable线程，否则就会一直处于处于 “waiting for monitor entry”。一段代码作为例子

在 “Wait Set”里面的线程都如饥似渴地等待拿到Monitor。他们是怎么进入到“Wait Set”的呢？当一个线程拿到了Monitor，但是在其他资源没有到位的情况下，调用同步锁对象（一般是synchronized()内的对象）的 wait() 方法，放弃了 Monitor，它就进入到了 “Wait Set”队列。只有当其他线程通过notify() 或者 notifyAll()，释放了同步锁后，这个线程才会有机会重新去竞争Monitor

public class WaitThread implements Runnable{
Java代码  收藏代码
public void run() {
      synchronized(this) {
        try {
        this.wait();
    } catch (InterruptedException e) {
        // TODO Auto-generated catch block
        e.printStackTrace();
    }
      }
 }
 public static void main(String[] args) {
   WaitThread t1 = new WaitThread();
      Thread ta = new Thread(t1, "A");
      Thread tb = new Thread(t1, "B");
      ta.start();
      tb.start();
 }
 对应的stack:

Java代码  收藏代码
"B" prio=10 tid=0x08173000 nid=0x1304 in Object.wait() [0x8baf2000]
   java.lang.Thread.State: WAITING (on object monitor)
    at java.lang.Object.wait(Native Method)
    - waiting on <0xa9cb50e0> (a org.marshal.WaitThread)
    at java.lang.Object.wait(Object.java:502)
    at org.marshal.WaitThread.run(WaitThread.java:8)
    - locked <0xa9cb50e0> (a org.marshal.WaitThread)
    at java.lang.Thread.run(Thread.java:636)

"A" prio=10 tid=0x08171c00 nid=0x1303 in Object.wait() [0x8bb43000]
   java.lang.Thread.State: WAITING (on object monitor)
    at java.lang.Object.wait(Native Method)
    - waiting on <0xa9cb50e0> (a org.marshal.WaitThread)
    at java.lang.Object.wait(Object.java:502)
    at org.marshal.WaitThread.run(WaitThread.java:8)
    - locked <0xa9cb50e0> (a org.marshal.WaitThread)
    at java.lang.Thread.run(Thread.java:636)
 A和B线程都进入了”wait set“。B线程也拿到过这个Monitor，因为A线程释放过了，这也验证上面的话，他们都在等待得而复失的<0xa9cb50e0>。

基于我们经常讨论到的死锁问题，构造一段代码如下

Java代码  收藏代码
public class DeadThread implements Runnable{

    private Object monitor_A = new Object();

    private Object monitor_B = new Object();

    public void  method_A(){
         synchronized(monitor_A) {
               synchronized(monitor_B) {
                   System.out.println(Thread.currentThread().getName()+" invoke method A");
               }
           }
    }

    public void  method_B(){
         synchronized(monitor_B) {
               synchronized(monitor_A) {
                   System.out.println(Thread.currentThread().getName()+" invoke method B");
               }
           }
    }

    public void run() {
        for(int i=0;i<1;i--){
            method_A();
            method_B();
        }
    }

  public static void main(String[] args) {
      DeadThread t1 = new DeadThread();
       Thread ta = new Thread(t1, "A");
       Thread tb = new Thread(t1, "B");

       ta.start();
       tb.start();
  }
}
  对应的stack:

Java代码  收藏代码
"B" prio=10 tid=0x0898d000 nid=0x269a waiting for monitor entry [0x8baa2000]
   java.lang.Thread.State: BLOCKED (on object monitor)
    at org.marshal.DeadThread.method_A(DeadThread.java:11)
    - waiting to lock <0xaa4d6f88> (a java.lang.Object)
    - locked <0xaa4d6f80> (a java.lang.Object)
    at org.marshal.DeadThread.run(DeadThread.java:28)
    at java.lang.Thread.run(Thread.java:636)

"A" prio=10 tid=0x0898b800 nid=0x2699 waiting for monitor entry [0x8baf3000]
   java.lang.Thread.State: BLOCKED (on object monitor)
    at org.marshal.DeadThread.method_B(DeadThread.java:19)
    - waiting to lock <0xaa4d6f80> (a java.lang.Object)
    - locked <0xaa4d6f88> (a java.lang.Object)
    at org.marshal.DeadThread.run(DeadThread.java:29)
    at java.lang.Thread.run(Thread.java:636)
 同时注意到，在stack trace尾部信息

Java代码  收藏代码
Found one Java-level deadlock:
=============================
"B":
  waiting to lock monitor 0x089615d8 (object 0xaa4d6f88, a java.lang.Object),
  which is held by "A"
"A":
  waiting to lock monitor 0x08962258 (object 0xaa4d6f80, a java.lang.Object),
  which is held by "B"

Java stack information for the threads listed above:
===================================================
"B":
    at org.marshal.DeadThread.method_A(DeadThread.java:11)
    - waiting to lock <0xaa4d6f88> (a java.lang.Object)
    - locked <0xaa4d6f80> (a java.lang.Object)
    at org.marshal.DeadThread.run(DeadThread.java:28)
    at java.lang.Thread.run(Thread.java:636)
"A":
    at org.marshal.DeadThread.method_B(DeadThread.java:19)
    - waiting to lock <0xaa4d6f80> (a java.lang.Object)
    - locked <0xaa4d6f88> (a java.lang.Object)
    at org.marshal.DeadThread.run(DeadThread.java:29)
    at java.lang.Thread.run(Thread.java:636)

Found 1 deadlock.
  stack中直接报告了Java级别的死锁，够智能吧。



 jps主要用来输出JVM中运行的进程状态信息。语法格式如下：

?

1

jps [options] [hostid]
    如果不指定hostid就默认为当前主机或服务器。

    命令行参数选项说明如下：

-q 不输出类名、Jar名和传入main方法的参数

-m 输出传入main方法的参数

-l 输出main类或Jar的全限名

-v 输出传入JVM的参数


C、 jmap（Memory Map）和jhat（Java Heap Analysis Tool）

    jmap用来查看堆内存使用状况，一般结合jhat使用。

    jmap语法格式如下：

jmap [option] executable core

jmap [option] [server-id@]remote-hostname-or-ip
    如果运行在64位JVM上，可能需要指定-J-d64命令选项参数。
jmap -permstat pid
    打印进程的类加载器和类加载器加载的持久代对象信息，输出：类加载器名称、对象是否存活（不可靠）、对象地址、父类加载器、已加载的类大小等信息，如下图：

使用jmap -histo[:live] pid查看堆内存中的对象数目、大小统计直方图，如果带上live则只统计活对象，如下


三：在Java Visualvm工具里面安装JTA插件，分析线程dump文件，注意，正常阶段的dump文件与非正常时期的Dump文件进行比较更容易分析出问题：
（1）下载：https://java.net/projects/tda/downloads/directory/visualvm



jstat 实用程序可以用于收集各种各样不同的统计数据。jstat 统计数据被分类到 “选项” 中，这些选项在命令行中被指定作为第一参数。对于 JDK 1.6 来说，您可以通过采用命令 -options 运行 jstat 查看可用的选项清单。清单 1 中显示了部分选项：
清单 1. jstat 选项
-class
-compiler
-gc
-gccapacity
-gccause
-gcnew
-gcnewcapacity
-gcold
-gcoldcapacity
-gcpermcapacity
-gcutil
-printcompilation
实用程序的 JDK 记录（参见 参考资料）将告诉您清单 1 中每个选项返回的内容，但是其中大多数用于收集垃圾的收集器或者其部件的性能信息。-class 选项显示了加载及未加载的类（使其成为检测应用程序服务器或代码中 ClassLoader 泄露的重要实用程序，且 -compiler 和 -printcompilation 都显示了有关 Hotspot JIT 编译程序的信息。
默认情况下，jstat 在您核对信息时显示信息。如果您希望每隔一定时间拍摄快照，请在 -options 指令后以毫秒为单位指定间隔时间。jstat 将持续显示监控进程信息的快照。如果您希望 jstat 在终止前进行特定数量的快照，在间隔时间/时间值后指定该数字。
如果 5756 是几分钟前开始的运行 SwingSet2 程序的 VMID，那么下列命令将告诉 jstat 每 250 毫秒为 10 个佚代执行一次 gc 快照转储，然后停止：

jstat -gc 31594 250 10
 S0C    S1C    S0U    S1U      EC       EU        OC         OU       MC     MU    CCSC   CCSU   YGC     YGCT    FGC    FGCT     GCT
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463
23552.0 24576.0  0.0    0.0   366080.0 322855.8  144896.0   50032.9   59136.0 57791.7 7424.0 7081.1     12    0.147   3      0.316    0.463

jhat (com.sun.tools.hat.Main)
将堆转储至一个二进制文件后，您就可以使用 jhat 分析二进制堆转储文件。jhat 创建一个 HTTP/HTML 服务器，该服务器可以在浏览器中被浏览，提供一个关于堆的 object-by-object 视图，及时冻结。根据对象引用草率处理堆可能会非常可笑，您可以通过对总体混乱进行某种自动分析而获得更好的服务。幸运的是，jhat 支持 OQL 语法进行这样的分析。
例如，对所有含有超过 100 个字符的 String 运行 OQL 查询看起来如下：
select s from java.lang.String s where s.count >= 100
结果作为对象链接显示，然后展示该对象的完整内容，字段引用作为可以解除引用的其他链接的其他对象。OQL 查询同样可以调用对象的方法，将正则表达式作为查询的一部分，并使用内置查询工具。一种查询工具，referrers() 函数，显示了引用指定类型对象的所有引用。下面是寻找所有参考 File 对象的查询：
select referrers(f) from java.io.File f
您可以查找 OQL 的完整语法及其在 jhat 浏览器环境内 “OQL Help” 页面上的特性。将 jhat 与 OQL 相结合是对行为不当的堆进行对象调查的有效方法。

jconsole

jvisualvm


tda / visualvm

https://java.net/projects/tda/downloads/directory/visualvm


search download tags
  search

showing 1 - 3 of 3
Title + Description + Tags  Tablescrollupicon	Updated Date  Tablescrolldownicon  Tablescrollupicon	Size	Downloads	Actions
File      Logfile Plugin Version 2.2

VisualVM Logfile Plugin (needed by TDA Plugin)
Tags: --	over 4 years ago	16.5 KB	8,420
File      TDA Library Plugin Version 2.2

TDA Library Plugin, needed by TDA VisualVM Plugin for VisualVM
Tags: --	over 4 years ago	2.1 MB	8,446
File      TDA VisualVM Plugin Version 2.2

TDA Plugin for VisualVM


References
-----

1. [Java性能监控](http://www.ibm.com/developerworks/cn/java/j-5things8.html)
1. [检测最耗cpu的java线程的脚本](http://hongjiang.info/find-busiest-thread-of-java/)
