---
layout: post
title: "java.lang.thread线程相关知识"
date: "Tue, 13 Nov 2012 10:23:15 +0800"
categories: java
---

java.lang.Thread是Java API中的基础线程类。定义线程的方法有两种：

1. 一种是制作Thead的子类，覆盖run()方法，然后实例化Thread子类
2. 另一种是定义实现了Runnable接口的类，然后传递此Runnable对象的instance给Thread()构造函数。

无论哪个方法，结果都是得到Thread对象，其中run()方法是线程主体。

{% highlight java %}
import static java.util.concurrent.TimeUnit.SECONDS;  // utility class

public class Clock extends Thread {
    // This field is volatile because two different threads may access it
    // volatile 修饰符很重要，防止不同线程同时访问修改这个属性，从而使得这个类是线程安全的
    volatile boolean keepRunning = true;

    public Clock() {     // The constructor
        setDaemon(true); // Daemon thread: interpreter can exit while it runs
    }

    public void run() {        // The body of the thread
        while(keepRunning) {   // This thread runs until asked to stop
            long now = System.currentTimeMillis();    // Get current time
            System.out.printf("%tr%n", now);          // Print it out
            try { Thread.sleep(1000); }               // Wait 1000 milliseconds
            catch (InterruptedException e) { return; }// Quit on interrupt
        }
    }

    // Ask the thread to stop running.  An alternative to interrupt().
    // 这个方法被设计来以可控制的方式来停止时钟线程。此范例被编写为可以通过调用从Thread继承来的interrupt()方法来打断
	// 另外Thread还有个stop()方法，但不建议使用
    public void pleaseStop() { keepRunning = false; }

    // This method demonstrates how to use the Clock class
    public static void main(String[] args) {
        Clock c = new Clock();                 // Create a Clock thread
        c.start();                             // Start it
        try {  SECONDS.sleep(10); }            // Wait 10 seconds
        catch(InterruptedException ignore) {}  // Ignore interrupts
        // Now stop the clock thread.  We could also use c.interrupt()
        c.pleaseStop();
    }
}
{% endhighlight %}

在Java 5.0中，java.util.concurrent包包含了Executor interface，这是一个新加入的线程池框架(Doug Lea)，java.util.concurrent提供了有灵活性且强大的ThreadPoolExecutor类，因此不需要自已再去实现自已的Executor。通常只要使用Executors类中的静态factory方法来使用：

{% highlight java %}
Executor oneThread = Executors.newSingleThreadExecutor(); // pool size of 1
Executor fixedPool = Executors.newFixedThreadPool(10); // 10 threads in pool
Executor unboundedPool = Executors.newCachedThreadPool(); // as many as needed
{% endhighlight %}

查看上面的方法签名可以知道：

{% highlight java %}
ExecutorService java.util.concurrent.Executors.newCachedThreadPool()
{% endhighlight %}

实际上返回的对象，它是个ExecutorService对象，此接口扩展了Executor，并加上了执行Callalble对象的能力。Callable和Runnable很像，但是Callable不是把程序代码封装在run()方法中，而是把程序代码放在call()方法中，而call()和run()有两个地方非常不同：call()方法会返回一个结果，并允许抛出异常。另外Runnable接口不是泛型接口，而Callable是个泛型接口。

互斥与锁
-----

使用多线程时，如果允许多个线程访问同一个数据结构，就必须非常小心。预防这类有害的并行操作是多线程计算的主要问题之一。

避免两个线程同时访问一个对象的基本技巧，就是要求线程必须先取得对象的锁，才能加以修改。当任一个线程占有锁时，另一个请求得到锁的线程就必须等待，直到第一个线程完成任务并释放锁。每个Java对象都有基本功能来提供这样的锁定功能。

要让对象具有线程安全性的最简单的方式就是把所有具有敏感性的method声明为synchronized。

java.util.concurrent.locks包
-----

需要注意，当使用synchronized修饰符时，你所要求的锁只是作用在块内，离线程离开此method或代码块时，它就会被自动释放。而使用java.util.concurrent.locks，可以使用Lock对象显式的锁定和解锁。

必须小心使用try/finally结构来确保锁一定会被释放。

避免死锁
-----

不要在同步代码块里调用外来方法。

协调线程
-----

有时线程必须停止运行并等待，直到某个事件发生，在那之后它会被告知继续运行。这可以用wait()和notify()方法来实现。但是，这些不是Thread类的method，而是Object的method。就和每个Java对象都有和其相关联的锁一样，每个对象都可以维持其等待线程的列表。

当线程调用对象的wait()方法时，线程所占有的所有锁都会被暂释放，而线程会被加到那个对象的等待线程列表并停止运行。当另一个线程调用同一个对象的notifyAll()方法时，对象就会唤醒等待的线程并允许它们继续运行：

{% highlight java %}
import java.util.*;

/**
 * A queue. One thread calls push() to put an object on the queue.
 * Another calls pop() to get an object off the queue. If there is no
 * data, pop() waits until there is some, using wait()/notify().
 * wait() and notify() must be used within a synchronized method or
 * block. In Java 5.0, use a java.util.concurrent.BlockingQueue instead.
 */
public class WaitingQueue {
    LinkedList q = new LinkedList();  // Where objects are stored
    public synchronized void push(E o) {
        q.add(o);         // Append the object to the end of the list
        this.notifyAll(); // Tell waiting threads that data is ready
    }
    public synchronized E pop() {
        while(q.size() == 0) {
            try { this.wait(); }
            catch (InterruptedException ignore) {}
        }
        return q.remove(0);
    }
}
{% endhighlight %}

等待指定条件
-----

Java5.0为对象的wait()和notifyAll()方法提供了替代方案。java.util.concurrent.locks定义了具有await()和signalAll()方法的Condition对象。Condition对象一定会与Lock对象相结合，而且在用法上与置于每个Java对象内的锁定和等待能力都相同。它的主要用途就是让每个Lock具有多个Condition成为可能，这在使用基于对象的锁定和等待时是不可能的。

等待线程完成
-----

有时一个线程必须停止并等待另一个线程完成。可以使用Thread的join()方法来完成。

同步化实用程序
-----

java.util.concurrent包含了四个“同步化程序synchronizer”类，能通过让线程等待直到出现指定条件为止来同步化并行程序的状态：

{% highlight text %}
Semaphore
    Semaphore类模拟了信号量(semaphore)，是传统的并行程序设计结构。概念上，semaphore代表了一个或多个“许可证”(permit)，需要许可证的线程会调用acqure()，接着在使用完时调用release()。如果没有可用的许可证，acquire()就会停止以使线程暂停，直到另一个线程释放许可证为止。

CountDownLatch
    倒计数锁存器(latch)在概念上是任一个具有两种可能状态而且从初始状态到最终状态只会变化一次的变量或并行结构，一旦发生变换，就会永久保持在最终状态。

Exchanger
    Exchanger是个实用程序，能让两个线程会合并交换一些值。

CyclicBarrier
    CyclicBarrier是个实用程序，能让N个线程的组互相等待以达到同步化的时刻。
{% endhighlight %}

线程中断
-----

所有线程方式的调用都可能抛出InterruptedException，因为线程的interrupt()方法能让一个线程中断另一个线程的运行。

阻塞式队列
-----

java.util.concurrent扩展了Queue接口，BlockingQueue定义了put()和take()方法，能让你增加或移除队列的元素，它在有必要时会被冻结，直到队列有空间或有元素可被移除为止。阻塞式队列在多线程程序设计中常被使用：一个线程产生了一些对象并把它们放在队列中以供另一个队列消耗，并将那些对象从队列移除。

java.util.concurrent提供了五个BlockingQueue的实现：

1. ArrayBlockingQueue
2. LinkedBlockingQueue
3. PriorityBlockingQueue
4. DelayQueue
5. SynchronousQueue


Effective java
-----

使用关键字synchronized同步访问共享的可变数据。

关键字valotile修饰符虽然不执行互斥访问，但它可以保证任何一个线程在读取该域的时候都将看到最近刚刚被写入的值。另外在使用volatile的时候务必小心与增量操作符++的使用，因为这个操作符不是原子的，它是会先读值，然后再写回一个新值。

最佳的做法是不共享可变的数据，要么共享不可变的数据，要么压根不共享。也就是说，将可变数据限制在单个线程中。

避免过度同步
-----

过度同步可能会导致性能降低、死锁、甚至不确定的行为。

通常，应该在同步区域内做尽可能少的工作。过度同步的实际成本并不是指获取锁所花费的CPU时间，而是指失去了并行的机会，以及因为需要确保每个核都有一个一致的内存视图而导致的延迟。过度同步的另一项潜在开销在于，它会限制VM优化代码执行的能力。

简而言之，为了避免死锁和数据破坏，千万不要从同步区域内调用外来方法，尽量限制同步区域内部的工作量。

使用Executor和Task优先于线程
-----

应该尽量不要编写自己的工作队列，而且还应该尽量不直接使用线程。现在关键的抽象不再是Thread了，它以前可是即充当工作单元，又是执行机制，现在工作单元和执行机制是分开的。现在关键的抽象是工作单元，称作任务task，任务有两种：Runnable及其近亲Callable(它有返回值)。执行任务的通用机制是executor service。

1. Executors.newCachedThreadPool
2. Executors.newFixedThreadPool

不要在loop循环外调用wait()方法
-----

Object.wait方法是使一个线程暂停等待某些条件，必须将代码块放在synchronized块中，标准写法如下：

{% highlight java %}
synchronized (obj) {
    while ()
        obj.wait();
    ... // Perform action appropriate to condition
}
{% endhighlight %}

绐络应该使用wait循环模式来调用wait方法，记住永远不要在循环外面调用wait方法。

一般情况下，应该优先使用notifyAll，而不是使用notify。

并发工具优于使用wait和notify
-----

自从Java 1.5之后，Java平台已经提供了更高级的并发工具，几乎没有理由再使用wait和notify了，而且要正确地使用wait和notify比较困难。

java.util.concurrent中更高级的工具分成三类：Executor Framework, Concurrent Collection, Synchronizer.

1. ConcurrentHashMap除了提供卓越的并发性之外，速度也非常快。除非不得已，否则应该优化使用ConcurrentHashMap，而不是使用Collections.synchronizedMap或者Hashtable。应该优先使用并发集合，而不是使用外部同步的集合。
2. Synchronizer同步器是一些使用线程能够等待另一个线程的对象，允许它们协调动作。最常用的同步器是CountDownLatch和Semaphore。较不常用的是CyclicBarrier和Exchanger。
3. 倒计数锁存器CountDown Latch是一次性的障碍，允许一个或者多个线程等待一个或者多个其他线程来做某些事情。CountDownLatch的唯一构造器带有一个int类型的参数，这个int参数是指允许所有在等待的线程被处理之前，必须在锁存器上调用countDonw方法的次数。

线程安全性的几种级别：
-----

1. 不可变的 immutable：这些类的实例是不变的，所以不需要外部的同步，如String, Long, BigInteger。
2. 无条件的线程安全 unconditionally thread-safe：这个类的实例是可变的，但是这个类有着足够的内部同步，所以，它的实例可以被并发使用，无需任何外部同步。如Random, ConcurrentHashMap。
3. 有条件的线程安全 conditionally thread-safe：除了有些方法为进行安全的并发使用而需要外部同步之外，这种线程安全级另与无条件的线程安全相同。
4. 非线程安全 not thread-safe：这个类的实例是可变的，为了并发地使用它们，客户必须利用自己选择的外部同步包围每个方法调用。如ArrayList, HashMap。
5. 线程对方的 thread-hostile: 这个类不能安全地被多个线程并发使用，即使所有的方法调用都被外部同步包围。不过幸运的是，在Java平台类库中，线程对立的类或者方法已经非常少。

避免使用ThreadGroup，线程组并没有提供太多有用的功能，并且提供的功能还都是有缺陷的，因此可以忽略这项功能，当它们根本不存在一样。

谷歌Guava建议用户应该优先使用ListenableFuture，来替代原来JDK中的Future。

ListenableFuture的代码更加清晰，在后台进程完成之后，会调用之前绑定的回调方法，或者线程运行异常时调用失败的回调方法。

References
-----

1. [Guava ListenableFuture](http://code.google.com/p/guava-libraries/wiki/ListenableFutureExplained)
