---
layout: post
title: "java tool - jstack"
date: Sun, 07 Aug 2016 21:51:18 +0800
categories: java
---

本文介绍一些java原生提供的工具，分析java线程的死锁情况，定位特别占用系统资源的java线程。

java thread dump
-----

以`tomcat`进程为例，当`tomcat`运行出现一些异常情况，比如长时间高CPU消耗，此时可以使用`java thread dump`，分析java的各线程状态，进行问题定位。

#### step 1: find tomcat $pid

{% highlight bash %}
$> ps -ef | grep tomcat
# or
$> jps -v
{% endhighlight %}

#### step 2: send quit signal to tomcat and tomcat will dump thread into catalina.out

{% highlight bash %}
$> kill -3 $pid
# or
$> kill -QUIT $pid
{% endhighlight %}

#### step 3: analyse catalina.out

{% highlight bash %}
$> tail -n 300 $CATALINA_HOME/logs/catalina.out
{% endhighlight %}

通过上述操作导出的线程栈信息，和java原生提供的工具`jstack`导出的线程栈基本上一样，接下来主要分析`jstask`这个工具。

java tool - jstack
-----

`jstack`可以在java应用程序运行时，通过`Thread.getAllStackTraces()`或`Thread.dumpStack()`完整转储当前所有线程栈，用于分析线程和定位问题，`jstack`命令的实现其实是一个叫做`sun.tools.jstack.JStack.java`的类。

命令行参数选项说明如下：

1. `-l`: long listings，会打印出额外的锁信息，在发生死锁时可以用`jstack -l $pid`来观察锁持有情况。
2. `-m`: mixed mode，不仅会输出Java堆栈信息，还会输出C/C++堆栈信息（比如Native方法）。

利用jstack查找占用CPU资源最多的那个线程
-----

一个应用占用CPU很高，极有可能是出现了死循环，linux环境下，当发现java进程占用CPU资源很高，按照以下步骤查找最占CPU资源的线程：

#### step 1: find tomcat $pid

{% highlight bash %}
$> ps -ef|grep tomcat
{% endhighlight %}

#### step 2: find java thread $tid which cause high CPU usage

{% highlight bash %}
$> top -Hp $pid
# or
$> ps -mp $pid -o THREAD,tid,time
{% endhighlight %}

#### step 3: dump thread stack trace

{% highlight bash %}
$> jstack $pid > jstack.log
# or
$> jstack -m $pid > jstack.log
{% endhighlight %}

#### step 4: change $tid to $nid

{% highlight bash %}
$> printf "%x\n" $tid
{% endhighlight %}

#### step 5: find $nid in jstack.log

{% highlight bash %}
$> grep -A 30 -i $tid jstack.log
{% endhighlight %}

bash脚本检测最占CPU的java线程
-----

把java进程的id直接会给下面这个bash脚本，可以直接找出java进程中哪个线程占用CPU资源最多：

{% highlight bash %}
#! /bin/bash

if [ $# -eq 0 ];then
    echo "please enter java $pid"
    exit -1
fi

pid=$1
jstack_cmd="jstack"

if [[ $JAVA_HOME != "" ]]; then
    jstack_cmd="$JAVA_HOME/bin/jstack"
else
    r=`which jstack 2> /dev/null`
    if [[ $r != "" ]]; then
        jstack_cmd=$r
    else
        echo "can not find jstack"
        exit -2
    fi
fi

# line=`top -H  -o %CPU -b -n 1  -p $pid | sed '1,/^$/d' | grep -v $pid | awk 'NR==2'`

line=`top -H -b -n 1 -p $pid | sed '1,/^$/d' | sed '1d;/^$/d' | grep -v $pid | sort -nrk9 | head -1`
echo "$line" | awk '{print "tid: "$1," cpu: %"$9}'
tid_0x=`printf "%0x" $(echo "$line" | awk '{print $1}')`
$jstack_cmd $pid | grep $tid_0x -A20 | sed -n '1,/^$/p'
{% endhighlight %}

jstack测试代码一
-----

{% highlight java %}
public class WhileTrueDump {

    public static void main(String[] args) {

        while (true) {
        }
    }

}
{% endhighlight %}

`jstack`导出线程栈如下：

{% highlight text %}
2016-08-17 23:06:05
Full thread dump Java HotSpot(TM) 64-Bit Server VM (25.77-b03 mixed mode):

"Attach Listener" #9 daemon prio=9 os_prio=31 tid=0x00007ff4b3060800 nid=0x310b waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"Service Thread" #8 daemon prio=9 os_prio=31 tid=0x00007ff4b3857800 nid=0x4303 runnable [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"C1 CompilerThread2" #7 daemon prio=9 os_prio=31 tid=0x00007ff4b482b800 nid=0x4103 waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"C2 CompilerThread1" #6 daemon prio=9 os_prio=31 tid=0x00007ff4b383c800 nid=0x3f03 waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"C2 CompilerThread0" #5 daemon prio=9 os_prio=31 tid=0x00007ff4b381a800 nid=0x3d03 waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"Signal Dispatcher" #4 daemon prio=9 os_prio=31 tid=0x00007ff4b381a000 nid=0x330f runnable [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"Finalizer" #3 daemon prio=8 os_prio=31 tid=0x00007ff4b3811000 nid=0x2d03 in Object.wait() [0x0000000121917000]
   java.lang.Thread.State: WAITING (on object monitor)
        at java.lang.Object.wait(Native Method)
        - waiting on <0x0000000795588ee0> (a java.lang.ref.ReferenceQueue$Lock)
        at java.lang.ref.ReferenceQueue.remove(ReferenceQueue.java:143)
        - locked <0x0000000795588ee0> (a java.lang.ref.ReferenceQueue$Lock)
        at java.lang.ref.ReferenceQueue.remove(ReferenceQueue.java:164)
        at java.lang.ref.Finalizer$FinalizerThread.run(Finalizer.java:209)

"Reference Handler" #2 daemon prio=10 os_prio=31 tid=0x00007ff4b380e000 nid=0x2b03 in Object.wait() [0x0000000121814000]
   java.lang.Thread.State: WAITING (on object monitor)
        at java.lang.Object.wait(Native Method)
        - waiting on <0x0000000795586b50> (a java.lang.ref.Reference$Lock)
        at java.lang.Object.wait(Object.java:502)
        at java.lang.ref.Reference.tryHandlePending(Reference.java:191)
        - locked <0x0000000795586b50> (a java.lang.ref.Reference$Lock)
        at java.lang.ref.Reference$ReferenceHandler.run(Reference.java:153)

"main" #1 prio=5 os_prio=31 tid=0x00007ff4b4002800 nid=0xb07 runnable [0x000000010b88f000]
   java.lang.Thread.State: RUNNABLE
        at com.example.test.threads.jstack.WhileTrueDump.main(WhileTrueDump.java:11)

"VM Thread" os_prio=31 tid=0x00007ff4b3809800 nid=0x2903 runnable

"GC task thread#0 (ParallelGC)" os_prio=31 tid=0x00007ff4b300d000 nid=0x2103 runnable

"GC task thread#1 (ParallelGC)" os_prio=31 tid=0x00007ff4b3010800 nid=0x2303 runnable

"GC task thread#2 (ParallelGC)" os_prio=31 tid=0x00007ff4b3011000 nid=0x2503 runnable

"GC task thread#3 (ParallelGC)" os_prio=31 tid=0x00007ff4b3011800 nid=0x2703 runnable

"VM Periodic Task Thread" os_prio=31 tid=0x00007ff4b3060000 nid=0x4503 waiting on condition

JNI global references: 6
{% endhighlight %}

dump出来的线程栈中的线程介绍
-----

对以上导出的内容简单介绍一下。

#### dump时间和虚拟机的信息

{% highlight text %}
2016-08-17 23:06:05
Full thread dump Java HotSpot(TM) 64-Bit Server VM (25.77-b03 mixed mode):
{% endhighlight %}

#### 主线程及字段说明

{% highlight text %}
"main" #1 prio=5 os_prio=31 tid=0x00007ff4b4002800 nid=0xb07 runnable [0x000000010b88f000]
   java.lang.Thread.State: RUNNABLE
        at com.example.test.threads.jstack.WhileTrueDump.main(WhileTrueDump.java:11)
{% endhighlight %}

以上示例线程栈中各字段具体含义是：

1. Thread name: `main`。
2. 线程优先级：`prio=5`，默认是5。
3. thread id：`tid=0x00007ff4b4002800`。
4. native id： `nid=0xb07`，和`top`命令查看的线程`$pid`对应，不过一个是`10进制`，一个是`16进制`，通过命令：`top -Hp $pid`，可以查看该进程的所有线程信息。
5. 线程的状态：`java.lang.Thread.State: RUNNABLE`。
6. 线程栈地址：`[0x000000010b88f000]`。
7. Java thread statck trace：这是最重要的数据，Java stack trace提供了大部分信息来精确定位问题根源。

#### HotSpot VM Thread and Compiler Threads

{% highlight text %}
"C1 CompilerThread2" #7 daemon prio=9 os_prio=31 tid=0x00007ff4b482b800 nid=0x4103 waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"C2 CompilerThread1" #6 daemon prio=9 os_prio=31 tid=0x00007ff4b383c800 nid=0x3f03 waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"C2 CompilerThread0" #5 daemon prio=9 os_prio=31 tid=0x00007ff4b381a800 nid=0x3d03 waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE

"CompilerThread0" daemon prio=10 tid=0x097e9000 nid=0xa9e waiting on condition [0x00000000]
   java.lang.Thread.State: RUNNABLE

"VM Thread" os_prio=31 tid=0x00007ff4b3809800 nid=0x2903 runnable

"VM Periodic Task Thread" os_prio=31 tid=0x00007ff4b3060000 nid=0x4503 waiting on condition
{% endhighlight %}

#### HotSpot GC Threads

{% highlight text %}
"GC task thread#0 (ParallelGC)" os_prio=31 tid=0x00007ff4b300d000 nid=0x2103 runnable
"GC task thread#1 (ParallelGC)" os_prio=31 tid=0x00007ff4b3010800 nid=0x2303 runnable
"GC task thread#2 (ParallelGC)" os_prio=31 tid=0x00007ff4b3011000 nid=0x2503 runnable
"GC task thread#3 (ParallelGC)" os_prio=31 tid=0x00007ff4b3011800 nid=0x2703 runnable
{% endhighlight %}

#### Finalizer daemon

{% highlight text %}
"Finalizer" #3 daemon prio=8 os_prio=31 tid=0x00007ff4b3811000 nid=0x2d03 in Object.wait() [0x0000000121917000]
   java.lang.Thread.State: WAITING (on object monitor)
        at java.lang.Object.wait(Native Method)
        - waiting on <0x0000000795588ee0> (a java.lang.ref.ReferenceQueue$Lock)
        at java.lang.ref.ReferenceQueue.remove(ReferenceQueue.java:143)
        - locked <0x0000000795588ee0> (a java.lang.ref.ReferenceQueue$Lock)
        at java.lang.ref.ReferenceQueue.remove(ReferenceQueue.java:164)
        at java.lang.ref.Finalizer$FinalizerThread.run(Finalizer.java:209)

"Reference Handler" #2 daemon prio=10 os_prio=31 tid=0x00007ff4b380e000 nid=0x2b03 in Object.wait() [0x0000000121814000]
   java.lang.Thread.State: WAITING (on object monitor)
        at java.lang.Object.wait(Native Method)
        - waiting on <0x0000000795586b50> (a java.lang.ref.Reference$Lock)
        at java.lang.Object.wait(Object.java:502)
        at java.lang.ref.Reference.tryHandlePending(Reference.java:191)
        - locked <0x0000000795586b50> (a java.lang.ref.Reference$Lock)
        at java.lang.ref.Reference$ReferenceHandler.run(Reference.java:153)
{% endhighlight %}

从上面可以看到有一个`Finalizer`守护线程正在运行。`Finalizer`线程是个单一职责的线程。这个线程会不停的循环等待`java.lang.ref.Finalizer.ReferenceQueue`中的新增对象。一旦`Finalizer`线程发现队列中出现了新的对象，它会弹出该对象，调用它的`finalize()`方法，将该引用从`Finalizer`类中移除，因此下次`GC`再执行的时候，这个`Finalizer`实例以及它引用的那个对象就可以回垃圾回收掉了。

需要注意这个线程的优先级低于主线程的优先级，如果主线程生成对象的速度远远大于对象回收的速度，则会造成`java.lang.OutOfMemoryError`错误。

#### Signal Dispatcher and Attach Listener daemons

{% highlight text %}
"Attach Listener" #9 daemon prio=9 os_prio=31 tid=0x00007ff4b3060800 nid=0x310b waiting on condition [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE
"Service Thread" #8 daemon prio=9 os_prio=31 tid=0x00007ff4b3857800 nid=0x4303 runnable [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE
"Signal Dispatcher" #4 daemon prio=9 os_prio=31 tid=0x00007ff4b381a000 nid=0x330f runnable [0x0000000000000000]
   java.lang.Thread.State: RUNNABLE
{% endhighlight %}

`Signal Dispatcher`是随jvm一起启动的，是jvm处理操作系统信号的线程。而在jvm启动时并不启动`Attach Listener`这个线程，这是`jstack`命令运行时启动的线程。

#### JNI global references count

{% highlight text %}
JNI global references: 6
{% endhighlight %}

java线程的状态说明
-----

简单说一下线程栈转储中出现的线程状态说明，更加详细的定义可以参考`Thread.State`中的javadoc：

#### 新建状态（NEW）

用new语句创建的线程处于新建状态，此时它和其他Java对象一样，仅仅在堆区中被分配了内存。

> Thread state for a thread which has not yet started.

#### 就绪状态（RUNNABLE）

当一个线程对象创建后，其他线程调用它的start()方法，该线程就进入就绪状态，Java虚拟机会为它创建方法调用栈和程序计数器，处于这个状态的线程位于可运行池中，等待获得CPU的使用权。

> Thread state for a runnable thread.  A thread in the runnable
>
> state is executing in the Java virtual machine but it may
>
> be waiting for other resources from the operating system
>
> such as processor.

#### 阻塞状态（BLOCKED）

它不是一般意义上的阻塞，而是特指被`synchronized`块阻塞，即是跟线程同步有关的一个状态。

> Thread state for a thread blocked waiting for a monitor lock.
>
> A thread in the blocked state is waiting for a monitor lock
>
> to enter a synchronized block/method or
>
> reenter a synchronized block/method after calling
>
> {@link Object#wait() Object.wait}.


#### 运行状态（RUNNING）

处于这个状态的线程占用CPU，执行程序代码，只有处于就绪状态的线程才有机会转到运行状态，`RUNNING`状态中的线程最为复杂，可能会进入`RUNNABLE`、`WAITING`、`TIMED_WAITING`、`BLOCKED`、`DEAD`状态：

1. 如果CPU调度给了别的线程，或者执行了`Thread.yield()`方法，则进入`RUNNABLE`状态，但是也有可能立刻又进入`RUNNING`状态。
2. 如果执行了`Thread.sleep(long)`，或者`thread.join(long)`，或者在锁对象上调用`object.wait(long)`方法，则会进入`TIMED_WAITING`状态。
3. 如果执行了`thread.join()`，或者在锁对象上调用了`object.wait()`方法，则会进入`WAITING`状态。
4. 如果进入了同步方法或者同步代码块，没有获取锁对象的话，则会进入`BLOCKED`状态。

#### 等待状态（WAITING）

处于`WAITING`状态中的线程，如果是因为`thread.join()`方法进入等待的话，在目标thread执行完毕之后，会回到`RUNNABLE`状态；如果是因为`object.wait()`方法进入等待的话，在锁对象执行`object.notify()`或者`object.notifyAll()`之后会回到`RUNNABLE`状态，这时线程没有拥有锁，等别的线程释放之后，才有机会获得锁，结束等待状态。

> Thread state for a waiting thread.
>
> A thread is in the waiting state due to calling one of the
>
> following methods:
>
> 1. {@link Object#wait() Object.wait} with no timeout
>
> 2. {@link #join() Thread.join} with no timeout
>
> 3. {@link LockSupport#park() LockSupport.park}

#### 等待状态（TIMED_WAITING）

线程`sleeping`或者`parking`时，会进入此状态，线程此时仍然拥有锁，等待时间结束，自动结果等待状态。

> Thread state for a waiting thread with a specified waiting time.
>
> A thread is in the timed waiting state due to calling one of
>
> the following methods with a specified positive waiting time:
>
> 1. {@link #sleep Thread.sleep}
>
> 2. {@link Object#wait(long) Object.wait} with timeout
>
> 3. {@link #join(long) Thread.join} with timeout
>
> 4. {@link LockSupport#parkNanos LockSupport.parkNanos}
>
> 5. {@link LockSupport#parkUntil LockSupport.parkUntil}

#### 死亡状态（TERMINATED/DEAD）

当线程执行完毕，或者抛出了未捕获的异常之后，会进入DEAD状态，该线程结束。

线程状态举例说明
-----

Java中每个对象都有一个`内置锁`，也有一个内置的`线程表`，当程序运行到非静态的`synchronized`方法上时，会获得与正在执行代码类的当前实例`this`有关的锁；当运行到同步代码块时，获得与声明的对象有关的锁。

释放锁是指持锁线程退出了`synchronized`方法或代码块，当程序运行到`synchronized`同步方法或代码块时对象锁才起作用。

每个对象的监视器Monitor，即对象内置锁，在某个时刻，只能被一个线程拥有，该线程就是`Active Thread`，而其它线程都是`Waiting Thread`，分别在两个队列`Entry Set`和`Wait Set`里面等候。在`Entry Set`里面的线程都等待拿到对象的监视器Monitor，拿到了线程就成为了`RUNNABLE`线程，否则就会一直处于处于`waiting for monitor entry`，如下示例代码中的`B`线程。

在`Wait Set`里面的线程都如饥似渴地等待拿到Monitor。他们是怎么进入到`Wait Set`的呢？当一个线程拿到了Monitor，但是在其他资源没有到位的情况下，调用同步锁对象（一般是synchronized()内的对象）的 wait() 方法，放弃了 Monitor，它就进入到了`Wait Set`队列。只有当其他线程通过notify() 或者 notifyAll()，释放了同步锁后，这个线程才会有机会重新去竞争Monitor。

![threads-using-object-monitor]({{ site.baseurl }}/img/java/threads-using-object-monitor.png)

{% highlight java %}
public class SynchronizedMonitorDump implements Runnable {

    public void run() {
        synchronized (this) {
            for (int i = 0; i < 1; i--) {
                System.out.println(Thread.currentThread().getName() + " synchronized loop " + i);
                try {
                    TimeUnit.SECONDS.sleep(2);
                } catch (InterruptedException e) {
                    Thread.currentThread().interrupt();
                }
            }
        }
    }

    public static void main(String[] args) {
        SynchronizedMonitorDump t1 = new SynchronizedMonitorDump();
        Thread ta = new Thread(t1, "A");
        Thread tb = new Thread(t1, "B");
        ta.start();
        tb.start();
    }
}
{% endhighlight %}

上述代码对应导出的相关线程栈内容如下：

{% highlight text %}
"B" #10 prio=5 os_prio=31 tid=0x00007f93ca02b800 nid=0x4903 waiting for monitor entry [0x0000000127831000]
   java.lang.Thread.State: BLOCKED (on object monitor)
        at com.example.test.threads.jstack.SynchronizedMonitorDump.run(SynchronizedMonitorDump.java:12)
        - waiting to lock <0x00000007955f3968> (a com.example.test.threads.jstack.SynchronizedMonitorDump)
        at java.lang.Thread.run(Thread.java:745)

"A" #9 prio=5 os_prio=31 tid=0x00007f93ca02b000 nid=0x4703 waiting on condition [0x000000012772e000]
   java.lang.Thread.State: TIMED_WAITING (sleeping)
        at java.lang.Thread.sleep(Native Method)
        at java.lang.Thread.sleep(Thread.java:340)
        at java.util.concurrent.TimeUnit.sleep(TimeUnit.java:386)
        at com.example.test.threads.jstack.SynchronizedMonitorDump.run(SynchronizedMonitorDump.java:15)
        - locked <0x00000007955f3968> (a com.example.test.threads.jstack.SynchronizedMonitorDump)
        at java.lang.Thread.run(Thread.java:745)
{% endhighlight %}

其他用于查看java线程和内存问题的工具
-----

1. jinfo
2. jconsole
3. jvisualvm
4. [tda](https://java.net/projects/tda/downloads/directory/visualvm)
5. jstat -gcutil $pid
6. jmap -dump:file=dump.map $pid
{% highlight java %}

public class WaitThread implements Runnable {
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
}
{% endhighlight %}

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
 A和B线程都进入了`wait set`。B线程也拿到过这个Monitor，因为A线程释放过了，这也验证上面的话，他们都在等待得而复失的<0xa9cb50e0>。

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

Found one Java-level deadlock:
"B":
  waiting to lock monitor 0x089615d8 (object 0xaa4d6f88, a java.lang.Object),
  which is held by "A"
"A":
  waiting to lock monitor 0x08962258 (object 0xaa4d6f80, a java.lang.Object),
  which is held by "B"

Java stack information for the threads listed above:
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

jps [options] [hostid]
    如果不指定hostid就默认为当前主机或服务器。

    命令行参数选项说明如下：

-q 不输出类名、Jar名和传入main方法的参数

-m 输出传入main方法的参数

-l 输出main类或Jar的全限名

-v 输出传入JVM的参数


jmap（Memory Map）和jhat（Java Heap Analysis Tool）

    jmap用来查看堆内存使用状况，一般结合jhat使用。

    jmap语法格式如下：

jmap [option] executable core

jmap [option] [server-id@]remote-hostname-or-ip
    如果运行在64位JVM上，可能需要指定-J-d64命令选项参数。
jmap -permstat pid
    打印进程的类加载器和类加载器加载的持久代对象信息，输出：类加载器名称、对象是否存活（不可靠）、对象地址、父类加载器、已加载的类大小等信息，如下图：

使用jmap -histo[:live] pid查看堆内存中的对象数目、大小统计直方图，如果带上live则只统计活对象，如下

jstat 实用程序可以用于收集各种各样不同的统计数据。jstat 统计数据被分类到`选项`中，这些选项在命令行中被指定作为第一参数。对于 JDK 1.6 来说，您可以通过采用命令 -options 运行 jstat 查看可用的选项清单。清单 1 中显示了部分选项：
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
您可以查找 OQL 的完整语法及其在 jhat 浏览器环境内`OQL Help`页面上的特性。将 jhat 与 OQL 相结合是对行为不当的堆进行对象调查的有效方法。

References
-----

1. [Java性能监控](http://www.ibm.com/developerworks/cn/java/j-5things8.html)
2. [虚拟机stack全分析](http://go-on.iteye.com/blog/1673894)
3. [检测最耗cpu的java线程的脚本](http://hongjiang.info/find-busiest-thread-of-java/)
4. [Java的Finalizer引发的内存溢出](http://it.deepinmind.com/gc/2014/05/13/debugging-to-understand-finalizer.html)
5. [JVM Attach机制实现](http://lovestblog.cn/blog/2014/06/18/jvm-attach/)
