---
layout: post
title: "java c3p0 configuration properties"
date: "Fri, 26 Oct 2012 16:19:26 +0800"
categories: java
---

c3p0配置参数说明
-----

{% highlight html %}
<c3p0-config>
    <default-config>
        <!--当连接池中的连接耗尽的时候c3p0一次同时获取的连接数。Default: 3 -->
        <property name="acquireIncrement">3</property>

        <!--定义在从数据库获取新连接失败后重复尝试的次数。Default: 30 -->
        <property name="acquireRetryAttempts">30</property>

        <!--两次连接中间隔时间，单位毫秒。Default: 1000 -->
        <property name="acquireRetryDelay">1000</property>

        <!--连接关闭时默认将所有未提交的操作回滚。Default: false -->
        <property name="autoCommitOnClose">false</property>

        <!--c3p0将建一张名为Test的空表，并使用其自带的查询语句进行测试。如果定义了这个参数那么属性preferredTestQuery将被忽略。你不能在这张Test表上进行任何操作，它将只供c3p0测试使用。Default: null-->
        <property name="automaticTestTable">Test</property>

        <!--获取连接失败将会引起所有等待连接池来获取连接的线程抛出异常。但是数据源仍有效 保留，并在下次调用getConnection()的时候继续尝试获取连接。如果设为true，那么在尝试获取连接失败后该数据源将申明已断开并永久关闭。Default: false-->
        <property name="breakAfterAcquireFailure">false</property>

        <!--当连接池用完时客户端调用getConnection()后等待获取新连接的时间，超时后将抛出SQLException,如设为0则无限期等待。单位毫秒。Default: 0 -->
        <property name="checkoutTimeout">100</property>

        <!--通过实现ConnectionTester或QueryConnectionTester的类来测试连接。类名需制定全路径。Default: com.mchange.v2.c3p0.impl.DefaultConnectionTester-->
        <property name="connectionTesterClassName"></property>

        <!--指定c3p0 libraries的路径，如果（通常都是这样）在本地即可获得那么无需设置，默认null即可Default: null-->
        <property name="factoryClassLocation">null</property>

        <!--Strongly disrecommended. Setting this to true may lead to subtle and bizarre bugs.（文档原文）作者强烈建议不使用的一个属性-->
        <property name="forceIgnoreUnresolvedTransactions">false</property>

        <!--每60秒检查所有连接池中的空闲连接。Default: 0 -->
        <property name="idleConnectionTestPeriod">60</property>

        <!--初始化时获取三个连接，取值应在minPoolSize与maxPoolSize之间。Default: 3 -->
        <property name="initialPoolSize">3</property>

        <!--最大空闲时间,60秒内未使用则连接被丢弃。若为0则永不丢弃。Default: 0 -->
        <property name="maxIdleTime">60</property>

        <!--连接池中保留的最大连接数。Default: 15 -->
        <property name="maxPoolSize">15</property>

        <!--JDBC的标准参数，用以控制数据源内加载的PreparedStatements数量。但由于预缓存的statements属于单个connection而不是整个连接池。所以设置这个参数需要考虑到多方面的因素。如果maxStatements与maxStatementsPerConnection均为0，则缓存被关闭。Default: 0-->
        <property name="maxStatements">100</property>

        <!--maxStatementsPerConnection定义了连接池内单个连接所拥有的最大缓存statements数。Default: 0 -->
        <property name="maxStatementsPerConnection"></property>

        <!--c3p0是异步操作的，缓慢的JDBC操作通过帮助进程完成。扩展这些操作可以有效的提升性能通过多线程实现多个操作同时被执行。Default: 3-->
        <property name="numHelperThreads">3</property>

        <!--当用户调用getConnection()时使root用户成为去获取连接的用户。主要用于连接池连接非c3p0的数据源时。Default: null-->
        <property name="overrideDefaultUser">root</property>

        <!--与overrideDefaultUser参数对应使用的一个参数。Default: null-->
        <property name="overrideDefaultPassword">password</property>

        <!--密码。Default: null-->
        <property name="password"></property>

        <!--定义所有连接测试都执行的测试语句。在使用连接测试的情况下这个一显著提高测试速度。注意：测试的表必须在初始数据源的时候就存在。Default: null-->
        <property name="preferredTestQuery">select id from test where id=1</property>

        <!--用户修改系统配置参数执行前最多等待300秒。Default: 300 -->
        <property name="propertyCycle">300</property>

        <!--因性能消耗大请只在需要的时候使用它。如果设为true那么在每个connection提交的时候都将校验其有效性。建议使idleConnectionTestPerioautomaticTestTable 等方法来提升连接测试的性能。Default: false -->
        <property name="testConnectionOnCheckout">false</property>

        <!--如果设为true那么在取得连接的同时将校验连接的有效性。Default: false -->
        <property name="testConnectionOnCheckin">true</property>

        <!--用户名。Default: null-->
        <property name="user">root</property>

        <!--早期的c3p0版本对JDBC接口采用动态反射代理。在早期版本用途广泛的情况下这个参数允许用户恢复到动态反射代理以解决不稳定的故障。最新的非反射代理更快并且已经开始广泛的被使用，所以这个参数未必有用。现在原先的动态反射与新的非反射代理同时受到支持，但今后可能的版本可能不支持动态反射代理。Default: false-->
        <property name="usesTraditionalReflectiveProxies">false</property>

        <property name="automaticTestTable">con_test</property>
        <property name="checkoutTimeout">30000</property>
        <property name="idleConnectionTestPeriod">30</property>
        <property name="initialPoolSize">10</property>
        <property name="maxIdleTime">30</property>
        <!--最大连接池数   -->
        <property name="maxPoolSize">25</property>
        <property name="minPoolSize">10</property>
        <property name="maxStatements">0</property>
        <user-overrides user="swaldman">
        </user-overrides>
    </default-config>
    <named-config name="dumbTestConfig">
        <property name="maxStatements">200</property>
        <user-overrides user="poop">
            <property name="maxStatements">300</property>
        </user-overrides>
    </named-config>
</c3p0-config>
{% endhighlight %}

c3p0 configure properties
-----

{% highlight text %}
acquireIncrement
Default: 3
Determines how many connections at a time c3p0 will try to acquire when the pool is exhausted.

acquireRetryAttempts
Default: 30
Defines how many times c3p0 will try to acquire a new Connection from the database before giving up. If this value is less than or equal to zero, c3p0 will keep trying to fetch a Connection indefinitely.

acquireRetryDelay
Default: 1000
Milliseconds, time c3p0 will wait between acquire attempts.

autoCommitOnClose
Default: false
The JDBC spec is unforgivably silent on what should happen to unresolved, pending transactions on Connection close. C3P0's default policy is to rollback any uncommitted, pending work. (I think this is absolutely, undeniably the right policy, but there is no consensus among JDBC driver vendors.) Setting autoCommitOnClose to true causes uncommitted pending work to be committed, rather than rolled back on Connection close. [Note: Since the spec is absurdly unclear on this question, application authors who wish to avoid bugs and inconsistent behavior should ensure that all transactions are explicitly either committed or rolled-back before close is called.]

automaticTestTable
Default: null
If provided, c3p0 will create an empty table of the specified name, and use queries against that table to test the Connection. If automaticTestTable is provided, c3p0 will generate its own test query, therefore any preferredTestQuery set will be ignored. You should not work with the named table after c3p0 creates it; it should be strictly for c3p0's use in testing your Connection. (If you define your own ConnectionTester, it must implement the QueryConnectionTester interface for this parameter to be useful.)

breakAfterAcquireFailure
Default: false
If true, a pooled DataSource will declare itself broken and be permanently closed if a Connection cannot be obtained from the database after making acquireRetryAttempts to acquire one. If false, failure to obtain a Connection will cause all Threads waiting for the pool to acquire a Connection to throw an Exception, but the DataSource will remain valid, and will attempt to acquire again following a call to getConnection().

checkoutTimeout
Default: 0
The number of milliseconds a client calling getConnection() will wait for a Connection to be checked-in or acquired when the pool is exhausted. Zero means wait indefinitely. Setting any positive value will cause the getConnection() call to time-out and break with an SQLException after the specified number of milliseconds.

connectionCustomizerClassName
Default: null
The fully qualified class-name of an implememtation of the ConnectionCustomizer interface, which users can implement to set up Connections when they are acquired from the database, or on check-out, and potentially to clean things up on check-in and Connection destruction. If standard Connection properties (holdability, readOnly, or transactionIsolation) are set in the ConnectionCustomizer's onAcquire() method, these will override the Connection default values.

connectionTesterClassName
Default: com.mchange.v2.c3p0.impl.DefaultConnectionTester
The fully qualified class-name of an implememtation of the ConnectionTester interface, or QueryConnectionTester if you would like instances to have access to a user-configured preferredTestQuery. This can be used to customize how c3p0 DataSources test Connections, but with the introduction of automaticTestTable and preferredTestQuery configuration parameters, "rolling your own" should be overkill for most users.

contextClassLoaderSource
Default: caller
Must be one of caller, library, or none. Determines how the contextClassLoader (see java.lang.Thread) of c3p0-spawned Threads is determined. If caller, c3p0-spawned Threads (helper threads, java.util.Timer threads) inherit their contextClassLoader from the client Thread that provokes initialization of the pool. If library, the contextClassLoader will be the class that loaded c3p0 classes. If none, no contextClassLoader will be set (the property will be null), which in practice means the system ClassLoader will be used. The default setting of caller is sometimes a problem when client applications will be hot redeployed by an app-server. When c3p0's Threads hold a reference to a contextClassLoader from the first client that hits them, it may be impossible to garbage collect a ClassLoader associated with that client when it is undeployed in a running VM. Setting this to library can resolve these issues.

dataSourceName
Default: if configured with a named config, the config name, otherwise the pool's "identity token"
Every c3p0 pooled data source is given a dataSourceName, which serves two purposes. It helps users find DataSources via C3P0Registry, and it is included in the name of JMX mBeans in order to help track and distinguish between multiple c3p0 DataSources even across application or JVM restarts. dataSourceName defaults to the pool's configuration name, if a named config was used, or else to an "identity token" (an opaque, guaranteed unique String associated with every c3p0 DataSource). You may update this property to any name you find convenient. dataSourceName is not guaranteed to be unique — for example, multiple DataSource created from the same named configuration will share the same dataSourceName. But if you are going to make use of dataSourceName, you will probably want to ensure that all pooled DataSources within your JVM do have unique names.

debugUnreturnedConnectionStackTraces
Default: false
If true, and if unreturnedConnectionTimeout is set to a positive value, then the pool will capture the stack trace (via an Exception) of all Connection checkouts, and the stack traces will be printed when unreturned checked-out Connections timeout. This is intended to debug applications with Connection leaks, that is applications that occasionally fail to return Connections, leading to pool growth, and eventually exhaustion (when the pool hits maxPoolSize with all Connections checked-out and lost). This parameter should only be set while debugging, as capturing the stack trace will slow down every Connection check-out.

driverClass
Default: null
The fully-qualified class name of the JDBC driverClass that is expected to provide Connections. c3p0 will preload any class specified here to ensure that appropriate URLs may be resolved to an instance of the driver by java.sql.DriverManager. If you wish to skip DriverManager resolution entirely and ensure that an instance of the specified class is used to provide Connections, use driverClass in combination with forceUseNamedDriverClass.

extensions
Default: an empty java.util.Map
A java.util.Map (raw type) containing the values of any user-defined configuration extensions defined for this DataSource.
Does Not Support Per-User Overrides.

factoryClassLocation
Default: null
DataSources that will be bound by JNDI and use that API's Referenceable interface to store themselves may specify a URL from which the class capable of dereferencing a them may be loaded. If (as is usually the case) the c3p0 libraries will be locally available to the JNDI service, leave this set as null.
Does Not Support Per-User Overrides.
forceIgnoreUnresolvedTransactions
Default: false
Strongly disrecommended. Setting this to true may lead to subtle and bizarre bugs. This is a terrible setting, leave it alone unless absolutely necessary. It is here to workaround broken databases / JDBC drivers that do not properly support transactions, but that allow Connections' autoCommit flags to go to false regardless. If you are using a database that supports transactions "partially" (this is oxymoronic, as the whole point of transactions is to perform operations reliably and completely, but nonetheless such databases are out there), if you feel comfortable ignoring the fact that Connections with autoCommit == false may be in the middle of transactions and may hold locks and other resources, you may turn off c3p0's wise default behavior, which is to protect itself, as well as the usability and consistency of the database, by either rolling back (default) or committing (see c3p0.autoCommitOnClose above) unresolved transactions. This should only be set to true when you are sure you are using a database that allows Connections' autoCommit flag to go to false, but offers no other meaningful support of transactions. Otherwise setting this to true is just a bad idea.

forceSynchronousCheckins
Default: false
Setting this to true forces Connections to be checked-in synchronously, which under some circumstances may improve performance. Ordinarily Connections are checked-in asynchronously so that clients avoid any overhead of testing or custom check-in logic. However, asynchronous check-in contributes to thread pool congestion, and very busy pools might find clients delayed waiting for check-ins to complete. Expanding numHelperThreads can help manage Thread pool congestion, but memory footprint and switching costs put limits on practical thread pool size. To reduce thread pool load, you can set forceSynchronousCheckins to true. Synchronous check-ins are likely to improve overall performance when testConnectionOnCheckin is set to false and no slow work is performed in a ConnectionCustomizer's onCheckIn(...) method. If Connections are tested or other slow work is performed on check-in, then this setting will cause clients to experience the overhead of that work on Connection.close(), which you must trade-off against any improvements in pool performance.

forceUseNamedDriverClass
Default: false
Setting the parameter driverClass causes that class to preload and register with java.sql.DriverManager. However, it does not on its own ensure that the driver used will be an instance of driverClass, as DriverManager may (in unusual cases) know of other driver classes which can handle the specified jdbcUrl. Setting this parameter to true causes c3p0 to ignore DriverManager and simply instantiate driverClass directly.
Does Not Support Per-User Overrides.

idleConnectionTestPeriod
Default: 0
If this is a number greater than 0, c3p0 will test all idle, pooled but unchecked-out connections, every this number of seconds.

initialPoolSize
Default: 3
Number of Connections a pool will try to acquire upon startup. Should be between minPoolSize and maxPoolSize.

jdbcUrl
Default: null
The JDBC URL of the database from which Connections can and should be acquired. Should resolve via java.sql.DriverManager to an appropriate JDBC Driver (which you can ensure will be loaded and available by setting driverClass), or if you wish to specify which driver to use directly (and avoid DriverManager resolution), you may specify driverClass in combination with forceUseNamedDriverClass. Unless you are supplying your own unpooled DataSource, a must always be provided and appropriate for the JDBC driver, however it is resolved.
Does Not Support Per-User Overrides.

maxAdministrativeTaskTime
Default: 0
Seconds before c3p0's thread pool will try to interrupt an apparently hung task. Rarely useful. Many of c3p0's functions are not performed by client threads, but asynchronously by an internal thread pool. c3p0's asynchrony enhances client performance directly, and minimizes the length of time that critical locks are held by ensuring that slow jdbc operations are performed in non-lock-holding threads. If, however, some of these tasks "hang", that is they neither succeed nor fail with an Exception for a prolonged period of time, c3p0's thread pool can become exhausted and administrative tasks backed up. If the tasks are simply slow, the best way to resolve the problem is to increase the number of threads, via numHelperThreads. But if tasks sometimes hang indefinitely, you can use this parameter to force a call to the task thread's interrupt() method if a task exceeds a set time limit. [c3p0 will eventually recover from hung tasks anyway by signalling an "APPARENT DEADLOCK" (you'll see it as a warning in the logs), replacing the thread pool task threads, and interrupt()ing the original threads. But letting the pool go into APPARENT DEADLOCK and then recover means that for some periods, c3p0's performance will be impaired. So if you're seeing these messages, increasing numHelperThreads and setting maxAdministrativeTaskTime might help. maxAdministrativeTaskTime should be large enough that any resonable attempt to acquire a Connection from the database, to test a Connection, or to destroy a Connection, would be expected to succeed or fail within the time set. Zero (the default) means tasks are never interrupted, which is the best and safest policy under most circumstances. If tasks are just slow, allocate more threads. If tasks are hanging forever, try to figure out why, and maybe setting maxAdministrativeTaskTime can help in the meantime.
Does Not Support Per-User Overrides.

maxConnectionAge
Default: 0
Seconds, effectively a time to live. A Connection older than maxConnectionAge will be destroyed and purged from the pool. This differs from maxIdleTime in that it refers to absolute age. Even a Connection which has not been much idle will be purged from the pool if it exceeds maxConnectionAge. Zero means no maximum absolute age is enforced.

maxIdleTime
Default: 0
Seconds a Connection can remain pooled but unused before being discarded. Zero means idle connections never expire.

maxIdleTimeExcessConnections
Default: 0
Number of seconds that Connections in excess of minPoolSize should be permitted to remain idle in the pool before being culled. Intended for applications that wish to aggressively minimize the number of open Connections, shrinking the pool back towards minPoolSize if, following a spike, the load level diminishes and Connections acquired are no longer needed. If maxIdleTime is set, maxIdleTimeExcessConnections should be smaller if the parameter is to have any effect. Zero means no enforcement, excess Connections are not idled out.

maxPoolSize
Default: 15
Maximum number of Connections a pool will maintain at any given time.

maxStatements
Default: 0
The size of c3p0's global PreparedStatement cache. If both maxStatements and maxStatementsPerConnection are zero, statement caching will not be enabled. If maxStatements is zero but maxStatementsPerConnection is a non-zero value, statement caching will be enabled, but no global limit will be enforced, only the per-connection maximum. maxStatements controls the total number of Statements cached, for all Connections. If set, it should be a fairly large number, as each pooled Connection requires its own, distinct flock of cached statements. As a guide, consider how many distinct PreparedStatements are used frequently in your application, and multiply that number by maxPoolSize to arrive at an appropriate value. Though maxStatements is the JDBC standard parameter for controlling statement caching, users may find c3p0's alternative maxStatementsPerConnection more intuitive to use.

maxStatementsPerConnection
Default: 0
The number of PreparedStatements c3p0 will cache for a single pooled Connection. If both maxStatements and maxStatementsPerConnection are zero, statement caching will not be enabled. If maxStatementsPerConnection is zero but maxStatements is a non-zero value, statement caching will be enabled, and a global limit enforced, but otherwise no limit will be set on the number of cached statements for a single Connection. If set, maxStatementsPerConnection should be set to about the number distinct PreparedStatements that are used frequently in your application, plus two or three extra so infrequently statements don't force the more common cached statements to be culled. Though maxStatements is the JDBC standard parameter for controlling statement caching, users may find maxStatementsPerConnection more intuitive to use.

minPoolSize
Default: 3
Minimum number of Connections a pool will maintain at any given time.

numHelperThreads
Default: 3
c3p0 is very asynchronous. Slow JDBC operations are generally performed by helper threads that don't hold contended locks. Spreading these operations over multiple threads can significantly improve performance by allowing multiple operations to be performed simultaneously.
Does Not Support Per-User Overrides.

overrideDefaultUser
Default: null
Forces the username that should by PooledDataSources when a user calls the default getConnection() method. This is primarily useful when applications are pooling Connections from a non-c3p0 unpooled DataSource. Applications that use ComboPooledDataSource, or that wrap any c3p0-implemented unpooled DataSource can use the simple user property.
Does Not Support Per-User Overrides.

overrideDefaultPassword
Default: null
Forces the password that should by PooledDataSources when a user calls the default getConnection() method. This is primarily useful when applications are pooling Connections from a non-c3p0 unpooled DataSource. Applications that use ComboPooledDataSource, or that wrap any c3p0-implemented unpooled DataSource can use the simple password property.
Does Not Support Per-User Overrides.

password
Default: null
For applications using ComboPooledDataSource or any c3p0-implemented unpooled DataSources — DriverManagerDataSource or the DataSource returned by DataSources.unpooledDataSource( ... ) — defines the password that will be used for the DataSource's default getConnection() method.
Does Not Support Per-User Overrides.

preferredTestQuery
Default: null
Defines the query that will be executed for all connection tests, if the default ConnectionTester (or some other implementation of QueryConnectionTester, or better yet FullQueryConnectionTester) is being used. Defining a preferredTestQuery that will execute quickly in your database may dramatically speed up Connection tests. (If no preferredTestQuery is set, the default ConnectionTester executes a getTables() call on the Connection's DatabaseMetaData. Depending on your database, this may execute more slowly than a "normal" database query.) NOTE: The table against which your preferredTestQuery will be run must exist in the database schema prior to your initialization of your DataSource. If your application defines its own schema, try automaticTestTable instead.

privilegeSpawnedThreads
Default: false
If true, c3p0-spawned Threads will have the java.security.AccessControlContext associated with c3p0 library classes. By default, c3p0-spawned Threads (helper threads, java.util.Timer threads) inherit their AccessControlContext from the client Thread that provokes initialization of the pool. This can sometimes be a problem, especially in application servers that support hot redeployment of client apps. If c3p0's Threads hold a reference to an AccessControlContext from the first client that hits them, it may be impossible to garbage collect a ClassLoader associated with that client when it is undeployed in a running VM. Also, it is possible client Threads might lack sufficient permission to perform operations that c3p0 requires. Setting this to true can resolve these issues.
Does Not Support Per-User Overrides.

propertyCycle
Default: 0
Maximum time in seconds before user configuration constraints are enforced. Determines how frequently maxConnectionAge, maxIdleTime, maxIdleTimeExcessConnections, unreturnedConnectionTimeout are enforced. c3p0 periodically checks the age of Connections to see whether they've timed out. This parameter determines the period. Zero means automatic: A suitable period will be determined by c3p0. [You can call getEffectivePropertyCycle...() methods on a c3p0 PooledDataSource to find the period automatically chosen.]

statementCacheNumDeferredCloseThreads
Default: 0
If set to a value greater than 0, the statement cache will track when Connections are in use, and only destroy Statements when their parent Connections are not otherwise in use. Although closing of a Statement while the parent Connection is in use is formally within spec, some databases and/or JDBC drivers, most notably Oracle, do not handle the case well and freeze, leading to deadlocks. Setting this parameter to a positive value should eliminate the issue. This parameter should only be set if you observe that attempts by c3p0 to close() cached statements freeze (usually you'll see APPARENT DEADLOCKS in your logs). If set, this parameter should almost always be set to 1. Basically, if you need more than one Thread dedicated solely to destroying cached Statements, you should set maxStatements and/or maxStatementsPerConnection so that you don't churn through Statements so quickly.
Does Not Support Per-User Overrides.

testConnectionOnCheckin
Default: false
If true, an operation will be performed asynchronously at every connection checkin to verify that the connection is valid. Use in combination with idleConnectionTestPeriod for quite reliable, always asynchronous Connection testing. Also, setting an automaticTestTable or preferredTestQuery will usually speed up all connection tests.

testConnectionOnCheckout
Default: false
If true, an operation will be performed at every connection checkout to verify that the connection is valid. Be sure to set an efficient preferredTestQuery or automaticTestTable if you set this to true. Performing the (expensive) default Connection test on every client checkout will harm client performance. Testing Connections in checkout is the simplest and most reliable form of Connection testing, but for better performance, consider verifying connections periodically using idleConnectionTestPeriod.

unreturnedConnectionTimeout
Default: 0
Seconds. If set, if an application checks out but then fails to check-in [i.e. close()] a Connection within the specified period of time, the pool will unceremoniously destroy() the Connection. This permits applications with occasional Connection leaks to survive, rather than eventually exhausting the Connection pool. And that's a shame. Zero means no timeout, applications are expected to close() their own Connections. Obviously, if a non-zero value is set, it should be to a value longer than any Connection should reasonably be checked-out. Otherwise, the pool will occasionally kill Connections in active use, which is bad. This is basically a bad idea, but it's a commonly requested feature. Fix your $%!@% applications so they don't leak Connections! Use this temporarily in combination with debugUnreturnedConnectionStackTraces to figure out where Connections are being checked-out that don't make it back into the pool!

user
Default: null
For applications using ComboPooledDataSource or any c3p0-implemented unpooled DataSources — DriverManagerDataSource or the DataSource returned by DataSources.unpooledDataSource() — defines the username that will be used for the DataSource's default getConnection() method.
{% endhighlight %}

hibernate中c3p0的配置示例
-----

{% highlight text %}
<bean id="dataSource" class="com.mchange.v2.c3p0.ComboPooledDataSource" destroy-method="close">
    <property name="driverClass">
        <value>org.sqlite.JDBC</value>
    </property>
    <property name="jdbcUrl">
        <value>${hibernate.connection.url}</value>
    </property>
    <property name="user">
        <value>${hibernate.connection.username}</value>
    </property>
    <property name="password">
        <value>${hibernate.connection.password}</value>
    </property>
    <property name="acquireIncrement">
        <value>3</value>
    </property>
    <property name="initialPoolSize">
        <value>12</value>
    </property>
    <property name="minPoolSize">
        <value>7</value>
    </property>
    <property name="maxPoolSize">
        <value>20</value>
    </property>
    <property name="maxIdleTime">
        <value>600</value>
    </property>
    <property name="idleConnectionTestPeriod">
        <value>300</value>
    </property>
    <property name="maxStatements">
        <value>100</value>
    </property>
    <property name="numHelperThreads">
        <value>10</value>
    </property>
    <property name="acquireRetryAttempts">
        <value>3</value>
    </property>
</bean>
{% endhighlight %}

the most important configure properties with hibernate
-----

{% highlight text %}
initialPoolSize C3P0 default: 3

minPoolSize Must be set in hibernate.cfg.xml (or hibernate.properties), Hibernate default: 1

maxPoolSize Must be set in hibernate.cfg.xml (or hibernate.properties), Hibernate default: 100

idleTestPeriod Must be set in hibernate.cfg.xml (or hibernate.properties), Hibernate default: 0

If this is a number greater than 0, c3p0 will test all idle, pooled but unchecked-out connections, every this number of seconds.

timeout Must be set in hibernate.cfg.xml (or hibernate.properties), Hibernate default: 0

The seconds a Connection can remain pooled but unused before being discarded. Zero means idle connections never expire.

maxStatements Must be set in hibernate.cfg.xml (or hibernate.properties), Hibernate default: 0

The size of c3p0's PreparedStatement cache. Zero means statement caching is turned off.

propertyCycle Must be set in c3p0.properties, C3P0 default: 300

Maximum time in seconds before user configuration constraints are enforced. c3p0 enforces configuration constraints continually, and ignores this parameter. It is included for JDBC3 completeness.

acquireIncrement Must be set in hibernate.cfg.xml (or hibernate.properties), Hibernate default: 1

Determines how many connections at a time c3p0 will try to acquire when the pool is exhausted.

testConnectionOnCheckout Must be set in c3p0.properties, C3P0 default: false

Don't use it, this feature is very expensive. If set to true, an operation will be performed at every connection checkout to verify that the connection is valid. A better choice is to verify connections periodically using c3p0.idleConnectionTestPeriod.

autoCommitOnClose Must be set in c3p0.properties, C3P0 default: false

The JDBC spec is unfortunately silent on what should happen to unresolved, pending transactions on Connection close. C3P0's default policy is to rollback any uncommitted, pending work. (I think this is absolutely, undeniably the right policy, but there is no consensus among JDBC driver vendors.) Setting autoCommitOnClose to true causes uncommitted pending work to be committed, rather than rolled back on Connection close. [Note: Since the spec is absurdly unclear on this question, application authors who wish to avoid bugs and inconsistent behavior should ensure that all transactions are explicitly either committed or rolled-back before close is called.]

forceIgnoreUnresolvedTransactions Must be set in c3p0.properties, C3P0 default: false

Strongly disrecommended. Setting this to true may lead to subtle and bizarre bugs. This is a terrible setting, leave it alone unless absolutely necessary. It is here to work around broken databases / JDBC drivers that do not properly support transactions, but that allow Connections' autoCommit flags to be set to false regardless. If you are using a database that supports transactions "partially" (this is oxymoronic, as the whole point of transactions is to perform operations reliably and completely, but nevertheless, such databases exist), if you feel comfortable ignoring the fact that Connections with autoCommit == false may be in the middle of transactions and may hold locks and other resources, you may turn off c3p0's wise default behavior, which is to protect itself, as well as the usability and consistency of the database, by either rolling back (default) or committing (see c3p0.autoCommitOnClose above) unresolved transactions. This should only be set to true when you are sure you are using a database that allows Connections' autoCommit flag to go to false, but that it offers no other meaningful support of transactions. Otherwise setting this to true is just a bad idea.

numHelperThreads Must be set in c3p0.properties, C3P0 default: 3

c3p0 is very asynchronous. Slow JDBC operations are generally performed by helper threads that don't hold contended locks. Spreading these operations over multiple threads can significantly improve performance by allowing multiple operations to be performed simultaneously.

factoryClassLocation Must be set in c3p0.properties, C3P0 default: null

DataSources that will be bound by JNDI and use that API's Referenceable interface to store themselves may specify a URL from which the class capable of dereferencing a them may be loaded. If (as is usually the case) the c3p0 libraries will be locally available to the JNDI service, leave this set to null.
{% endhighlight %}

References
-----

1. [http://www.mchange.com/projects/c3p0/](http://www.mchange.com/projects/c3p0/)
1. [http://blog.csdn.net/lip8654/article/details/2121369](http://blog.csdn.net/lip8654/article/details/2121369)
1. [HowTo configure the C3P0 connection pool](https://developer.jboss.org/wiki/HowToConfigureTheC3P0ConnectionPool)
