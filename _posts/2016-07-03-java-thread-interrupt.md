---
layout: post
title: "java thread interrupt mechanism"
date: Sun, 03 Jul 2016 19:08:55 +0800
categories: java
---

如何中断线程
-----

通过`Thread#interrupt()`方法中断线程，这里的中断并不是stop线程，而只是设置线程的中断状态位，是否中断是取决于线程本身。

实际上Java的线程中断是一种线程之间的协作机制，也就是说调用线程对象的`Thread#interrupt()`方法并不一定就中断了正在运行的线程，它只是要求线程自己在合适的时机中断自己，每个线程都有一个boolean的中断状态（这个状态不在Thread的属性上），`Thread#interrupt()`方法仅仅只是将该状态置为`true`。

判断线程是否被中断
-----

使用`Thread#isInterrupted()`方法，一般是这样使用：`while(!Thread.currentThread().isInterrupted()) {}`。

线程中断相关方法说明
-----

| Method | Description |
|:--------------------------:|:--------------------------|
| `public static boolean interrupted()` | 测试当前线程是否已经中断，线程的中断状态由该方法清除。换句话说，如果连续两次调用该方法，则第二次调用将返回`false`。|
| `public boolean isInterrupted()`      | 测试线程是否已经中断。线程的中断状态不受该方法的影响。|
| `public void interrupt()`             | 中断线程。|

线程中断和线程阻塞
-----

java中有很多方法会导致线程阻塞：

* Object.wait()
* Thread.sleep()
* Process.waitFor()
* AsynchronousChannelGroup.awaitTermination()
* ExecutorService.awaitTermination()
* Future.get()
* BlockingQueue.take()
* Semaphore.acquire()
* Condition.await() and many others in java.util.concurrent.*
* SwingUtilities.invokeAndWait()

> 需要特别注意传统阻塞IO(`blocking I/O`)并不会抛出`InterruptedException`。

以上提及的这些阻塞方法，如`Object.wait()`，`Thread.sleep()`等方法被调用后，线程处理阻塞状态，这些方法会不断的轮询监听线程的`interrupted`标志位，发现其设置为`true`后，会停止阻塞并抛出`InterruptedException`异常，并且在抛出异常之后**立即清除中断状态**，也就是说在这时候在异常`catch`处理块中调用`Thread.currentThread().isInterrupted()`方法返回的仍然是`false`。

抛出异常是为了唤醒线程，并且通知线程有中断信号需要处理，如何处理是由线程本身决定，下面也会提到2种常见的处理方式。

中断异常处理方式
-----

1\. 在`catch`子句中，调用`Thread.currentThread.interrupt()`来重新设置中断状态，因为在抛出`InterruptedException`异常后会清除中断状态，重置中断状态后可以让后面的代码通过判断`Thread.currentThread().isInterrupted()`结果而知道之前有中断发生过：

{% highlight java %}
void run() {
    ...
    try {
        sleep(delay);
    } catch (InterruptedException e) {
        Thread.currentThread().isInterrupted();
    }
    ...
}
{% endhighlight %}

2\. 更好的做法就是，不使用`try`来捕获这样的异常，让方法直接抛出，交由方法调用者来处理`InterruptedException`异常：

{% highlight java %}
void run() throws InterruptedException {
    ...
    sleep(delay);
    ...
}
{% endhighlight %}

使用共享变量中断非阻塞状态的线程示例
-----

注意共享变量的修饰符`volatile`，这是`volatile`最常见的用法之一，为了保证共享变量在不同线程中的内存可见性。

{% highlight java %}
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

class ThreadInterruptExample2 extends Thread {
    private static final Logger logger = LoggerFactory.getLogger(ThreadInterruptExample2.class);
    volatile boolean stop = false;

    public static void main(String args[]) throws Exception {
        ThreadInterruptExample2 thread = new ThreadInterruptExample2();
        logger.info("Starting thread...");
        thread.start();
        Thread.sleep(3000);
        logger.info("Asking thread to stop...");

        thread.stop = true; // reset share variable
        Thread.sleep(3000);
        logger.info("Stopping application...");
    }

    @Override
    public void run() {
        // Every second check interrupt signal
        while (!stop) {
            logger.info("Thread is running...");
            long time = System.currentTimeMillis();
            /*
             * Use a while loop simulation sleep method, here don't use sleep, otherwise they will be thrown on the obstruction
             * Abnormal InterruptedException and exit the loop, so that the while detection of stop conditions will not be executed,
             * Lost meaning.
             */
            while ((System.currentTimeMillis() - time < 1000) && (!stop)) {
            }
        }

        logger.info("Thread exiting under request...");
    }

}
{% endhighlight %}

通过线程中断机制取消线程运行示例
-----

{% highlight java %}
import org.joda.time.DateTime;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import static java.util.concurrent.TimeUnit.SECONDS;

public class ThreadInterruptClock extends Thread {
    private static final Logger LOGGER = LoggerFactory.getLogger(ThreadInterruptClock.class);

    public ThreadInterruptClock() { // The constructor
        // Daemon thread: if set daemon true, java interpreter will exit and not trigger thread InterrutpedException
        setDaemon(false);
    }

    @Override
    public void run() {
        // The body of the thread
        // While using the executor framework you do not know which thread instance is executing your code currently and hence the convention is to use Thread.currentThread.isInterrupted()
        while (!Thread.currentThread().isInterrupted()) {
            LOGGER.info(new DateTime().toString("yyyy-MM-dd HH:mm:ss Z")); // Print it out
            try {
                // Wait 1000 milliseconds
                Thread.sleep(1000);
            } catch (InterruptedException e) {
                LOGGER.error("sleep interrutped!", e);
                // Thread.sleep() and Object.wait() Throws:
                // IllegalMonitorStateException - if the current thread is not the owner of the object's monitor.
                // InterruptedException - if any thread interrupted the current thread before or while the current thread was waiting for a notification. The interrupted status of the current thread is cleared when this exception is thrown.

                // IMPORTANT: reset interrupt mark
                Thread.currentThread().interrupt(); // reset on interrupt status again for thread exit.
                LOGGER.info("Quit on interrupt and clean interrupt status");
            }
        }
    }

    // Ask the thread to stop running.
    public void pleaseStop() {
        interrupt();
    }

    // This method demonstrates how to use the Clock class
    public static void main(String[] args) throws InterruptedException {
        ThreadInterruptClock c = new ThreadInterruptClock(); // Create a Clock thread
        c.start(); // Start it

        // Wait 10 seconds
        SECONDS.sleep(5);

        // Now stop the clock thread. We could also use c.interrupt()
        c.pleaseStop(); // or c.interrupt();
        LOGGER.info("=================finished===============");
    }
}
{% endhighlight %}

通过ExecutorService控制线程中断示例
-----

`ExecutorService`中会加入2个任务，其中一个线程会配合取消任务执行，另一个线程会无视中断信号，忽略异常并继续在后台执行。所以在主线程结束后，`UncooperativeTask`会继续在控制台中输出日志消息。

{% highlight java %}

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.ArrayList;
import java.util.Collection;
import java.util.concurrent.Callable;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.TimeUnit;

/**
 * http://wiki.deegree.org/deegreeWiki/deegree3/ProgrammingCooperativeThreads
 *
 * {@link CooperativeTask}会被正常取消任务, 调用过程:
 * ExcecutorService.invokeAll()->FutureTask.cancel()->Thread.interrupt()
 *
 * {@link UncooperativeTask}在收到中断消息之后, 忽略了异常, 所以不会被取消执行, 任务会继续在后台执行。
 */
public class ThreadInterruptWithExecutorService {

    private static final Logger LOGGER = LoggerFactory.getLogger(ThreadInterruptWithExecutorService.class);

    public static void main(String[] args) throws InterruptedException {
        ExecutorService execService = Executors.newCachedThreadPool();

        LOGGER.info("Main thread: Using Executor to invoke an Uncooperative Task with a timeout of 1000 milliseconds.");

        Collection<Callable<Object>> tasks = new ArrayList<Callable<Object>>();
        tasks.add(new CooperativeTask());
        tasks.add(new UncooperativeTask());
        execService.invokeAll(tasks, 1000, TimeUnit.MILLISECONDS);
        LOGGER.info("Main thread: exiting.");
        execService.shutdown();
    }
}

/**
 * As one can see, the invokeAll-call in the main thread returns just after the timeout has occured (and the main thread exits).
 * However, the thread that runs the uncooperative task keeps on running forever.
 * In this case it is really simple to fix this behaviour: don't catch the InterruptedException (but use it to leave the loop).
 */
class CooperativeTask implements Callable<Object> {

    private static final Logger LOGGER = LoggerFactory.getLogger(CooperativeTask.class);

    @Override
    public Object call() throws Exception {

        long start = System.currentTimeMillis();

        while (true) {
            LOGGER.info("CooperativeTask@" + (System.currentTimeMillis() - start) + ": Running.");
            Thread.sleep(250);
        }
    }
}

/**
 * As one can see, the methods of ExecutorService cannot cancel uncooperative code as well -- these methods just use Thread.interrupt() internally.
 * If the code does not cope correctly with InterruptedExceptions (i.e. it just catches and ignores them), threads keep on going forever.
 */
class UncooperativeTask implements Callable<Object> {

    private static final Logger LOGGER = LoggerFactory.getLogger(UncooperativeTask.class);

    @Override
    public Object call() throws Exception {

        long start = System.currentTimeMillis();

        while (true) {
            LOGGER.info("UncooperativeTask@" + (System.currentTimeMillis() - start) + ": Running.");
            try {
                Thread.sleep(250);
            } catch (InterruptedException e) {
                LOGGER.info("UncooperativeTask@" + (System.currentTimeMillis() - start) + ": Caught InterruptedException, but ignoring it.");
            }
        }
    }
}
{% endhighlight %}

References
-----

1. [The interrupt mechanism of Thread](http://www.programering.com/a/MDO2MTNwATc.html)
2. [Thread的中断机制](http://www.cnblogs.com/onlywujun/p/3565082.html)
3. [中断JAVA线程](http://www.blogjava.net/jinfeng_wang/archive/2008/04/27/196477.html)
4. [Programming cooperative threads](http://wiki.deegree.org/deegreeWiki/deegree3/ProgrammingCooperativeThreads)
