---
layout: post
title: "java日志框架简介"
date: Sun, 18 Sep 2016 20:17:28 +0800
categories: java
---

Java Log Frameworks
-----

常见的java日志框架和实现主要有：

1. slf4j
2. log4j
3. logback
4. apache jakarta commons-logging
5. java.util.logging
6. log4j 2

上述几个日志工具的简单说明：

1. 前面3个日志工具是同一个作者 Ceki G&uuml;lc&uuml; 开发的。
2. `slf4j`是`Simple Logging Facade for Java`的缩写，主要是在`slf4j-api`中定义了日志接口和工厂方法，它并不具体实现日志操作，平时项目开发中引用的日志相关的代码都应该来自这个包。
3. `logback-classic`是`slf4j`的完整实现，目前有取代`log4j`的趋势，`spring-boot`项目就在使用这个日志框架。
4. `slf4j-log4j12`是连接`slf4j-api`和`log4j`中间的适配器，它实现了`slf4j-api`中`StaticLoggerBinder`接口，从而使得在编译时绑定的是`slf4j-log4j12`的`getSingleton()`方法。
5. apache的`JCL`在较早的一些类库中用得较多，后来更多第三方类库采用`log4j`，比如`struts`、`spring`和`hibernate`。
6. `java.util.logging`是JDK自带的，日志等级较多，较少使用。
7. `log4j 2`在多线程模型中性能远高于其他日志框架。

Logger LEVEL
-----

`slf4j`中定义的常用的日志级别：

1. ERROR
2. WARN
3. INFO
4. DEBUG

另外还有一个比`DEBUG`还详细的`TRACE`，主要是用在一些非常复杂的算法中，每一步计算结果中输出详细的信息，用于跟踪分析计算步骤，并且代码完成后应该也会被移除，不会上传的版本库，因此下面就不做说明了。

日志级别使用场景
-----

#### DEBUG

* 开发调试使用
* 信息面向开发工程师

#### INFO

* 对象状态变化前后
* API方法调用前显示传入的参数列表
* API方法返回的关键信息
* 定时任务的开始结束信息
* 关键方法的进入退出点及相应参数
* 代码块运行时间监控，性能分析
* 信息面向开发工程师及运维人员

#### WARN

* 不影响程序运行的配置问题
* 可恢复的异常
* 信息面向开发工程师及运维人员

#### ERROR

* 运行时异常
* 无法处理的异常
* 记录异常信息和堆栈，不要吞没异常
* Logger不是异常处理的工具
* 信息面向开发工程师及运维人员


不同的运行环境使用不同的日志等级
-----

* production - INFO
* development/stage - DEBUG
* test - DEBUG

Should logger object static or not
-----

1. 建议使用`private static final`形式，每个类所有对象共用一个日志对象即可，KISS。
2. `non-static`被子类继承后，有一个`getClass()`方法`运行时绑定`的优势，只要在父类里声明一个`protected`的日志对象，子类就可以直接使用，但是每个子类对象创建都会生成一个日志对象。
3. `non-static`的在面向`IOC`的应用中使用也是很不错的，因为`spring`的`bean`一般都是单例的。

#### STATIC

{% highlight java %}
private static final Logger LOGGER = LoggerFactory.getLogger(LoggerExample.class);
{% endhighlight %}

#### INSTANCE

{% highlight java %}
private final Logger logger = LoggerFactory.getLogger(this.getClass());
{% endhighlight %}

日志查询RequstID
-----

如果应用的分布式的，或者是用户会访问多个子系统的，当访问者第一次进入系统时，可以为其生成一个统一的`RequestID`，并使用`MDC`(Mapped Diagnostic Context，映射调试上下文)写入日志文件中，可以跟踪用户完整的操作过程，在问题分析和数据分析会很有用。使用如下这些信息可以配合其他关键信息，使用可逆的算法生成这个`RequestID`。

1. JSESSIONID
2. USERID
3. TIMESTAMP
4. ORDERID

使用结构化或者半结构化的日志消息
-----

日志格式设计的比较好的日志消息，对于查找问题会很有利，也便于使用`logstash`导入到`elasticsearch`中进行分析。

日志命名格式以及日志文件名
-----

日志文件一般按日期，应用标识和日志级别三者来命名，也可以再带上主机名。如：`web-info-2016-09-18.log`。

Logger使用注意事项
-----

#### 不要使用字符串拼接方法

{% highlight java %}
log.debug("orderId is " + order.getId() + " and amounts is " + amounts);
{% endhighlight %}

#### 使用对象占位符

{% highlight java %}
log.debug("orderId is {} and amounts is {}", order.getId(), amounts);
{% endhighlight %}

#### 记录异常信息

{% highlight java %}
e.printStackTrace(); // BAD
log.error("IO exception", e.getMessage()); // BAD
log.error("IO exception", e); // OK
{% endhighlight %}

#### 不要记录异常并抛出异常

{% highlight java %}
log.error("IO exception"); // OK
log.error("IO exception", e); // BAD
throw new Exception(e);
{% endhighlight %}

#### 不要记录集合和复杂对象

{% highlight java %}
log.info("fetch users: {} from db.", users);
{% endhighlight %}

#### 程序中不要使用System.out

junit中可以少量使用`System.out`，更多还是使用断言。

{% highlight java %}
System.out.println("current login user name is : " + user.getName());
{% endhighlight %}

References
-----

1. [Should logger be private static or not](http://stackoverflow.com/questions/3842823/should-logger-be-private-static-or-not)
2. [Should Logger members of a class be declared as static](http://www.slf4j.org/faq.html#declared_static)
3. [Get Rid of Java Static Loggers](http://www.yegor256.com/2014/05/23/avoid-java-static-logger.html)
4. [Java日志性能那些事](http://www.infoq.com/cn/articles/things-of-java-log-performance)
5. [Java 日志管理最佳实践](http://www.ibm.com/developerworks/cn/java/j-lo-practicelog/index.html)
6. [java日志规范](https://www.linkedin.com/pulse/java%E6%97%A5%E5%BF%97%E8%A7%84%E8%8C%83-ding-lau)
7. [最佳日志实践](http://blog.jobbole.com/56574/)

