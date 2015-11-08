---
layout: post
title: "disruptor demo"
date: "Tue Jul 29 2014 22:20:34 GMT+0800 (CST)"
categories: java
---

基于disruptor 3.3.0版本的一个示例，这是[原文地址](http://blog.csdn.net/jiang_bing/article/details/9292163)，略有调整，其中用到的几个类可从[前文](http://yuweijun.github.io/blog/java/disruptor/2014/07/28/learning-disruptor-part-one/)获得。

{% highlight java %}

import java.util.concurrent.Executors;

import org.apache.commons.lang.builder.ReflectionToStringBuilder;
import org.junit.Test;

import com.lmax.disruptor.BlockingWaitStrategy;
import com.lmax.disruptor.IgnoreExceptionHandler;
import com.lmax.disruptor.RingBuffer;
import com.lmax.disruptor.Sequence;
import com.lmax.disruptor.WorkHandler;
import com.lmax.disruptor.WorkerPool;
import com.lmax.disruptor.dsl.ProducerType;


public class DisruptorTest {

    @Test
    @SuppressWarnings("unchecked")
    public void test() {
        // 定义一个ringBuffer,也就是相当于一个队列
        RingBuffer<LongEvent> buffer = RingBuffer.create(ProducerType.SINGLE, new LongEventFactory(), 1 << 2, new BlockingWaitStrategy());

        // 定义消费者,只要有生产出来的东西，该事件就会被触发,参数event为被生产出来的东西　
        // 几个workerHandler 表示有几个消费者
        WorkHandler<LongEvent> workHandler = new WorkHandler<LongEvent>() {
            @Override
            public void onEvent(LongEvent event) throws Exception {
                System.out.println(Thread.currentThread().getName() + " " + ReflectionToStringBuilder.toString(event));
            }
        };

        // 定义一个消费者池，每一个handler都是一个消费者，也就是一个线程，会不停地就队列中请求位置，如果这们位置中被生产者放入了东西，
        // 而这个东西则是上面定义RingBuffer中的 factory
        // 创建出来的一个event,消费者会取出这个event,调用handler中的onEvent方法，
        // 如果这个位置还没有被生产者放入东西，则阻塞，等待生产者生产后唤醒.
        // 而生产者在生产时要先申请slot，而这个slot位置不能高于最后一个消费者的位置，否则会覆盖没有消费的slot，如果大于消费者的最后一个slot，则进行自旋等待.
        WorkerPool<LongEvent> workerPool = new WorkerPool<LongEvent>(buffer, buffer.newBarrier(), new IgnoreExceptionHandler(), workHandler, workHandler);
        // 每个消费者，也就是 workProcessor都有一个sequence，表示上一个消费的位置,这个在初始化时都是-1
        Sequence[] sequences = workerPool.getWorkerSequences();
        // 将其保存在ringBuffer中的 sequencer
        // 中，在为生产申请slot时要用到,也就是在为生产者申请slot时不能大于此数组中的最小值,否则产生覆盖
        buffer.addGatingSequences(sequences);
        // 用executor 来启动workProcessor 线程
        workerPool.start(Executors.newFixedThreadPool(10));
        System.out.println("disruptor started");

        /**
         * 对于生产者生产都是以下面方式，disruptor3.0之后还有一个translator形式可以发布事件
         *
         * long next = ringBuffer.next();
         *
         * <pre>
         * try{
         *     Event event = ringBuffer.get(next);
         *     //相当于生产
         *     event.set(...)
         * } finally {
         *     // 将事件发布
         *     ringBuffer.publis(next);
         * }
         * </pre>
         */
        System.out.println("开始生产");
        for (int i = 0; i < 10; i++) {
            long next = buffer.next();
            try {
                LongEvent event = buffer.get(next);
                event.set(i);
            } finally {
                System.out.println("生产:" + i);
                buffer.publish(next);
            }

        }
    }

}
{% endhighlight %}
