---
layout: post
title: java database connection pools benchmark
date: Sat Oct 01 2016 22:19:26 GMT+0800 (CST)
categories: java
---

不同的数据库连接池`100000`条记录插入性能测试，以下结果基于mysql的jdbc驱动，代码参考`druid`项目的benchmark测试代码。

# 结论

`PROXOOL`出现大量异常造成插入数据不成功，所以没有计算结果。

从测试结果数据来看，`DBCP`和`DBCP2`的结果是最好的，其次是`DRUID`和`C3P0`。

|POOL       |   MILLIS  |  BLOCKED  |   WAITED |
|:---------:|----------:|----------:|---------:|
|DRUID      |   27545   |   27      |    2480  |
|DBCP       |   26121   |   4438    |    990   |
|DBCP2      |   26935   |   115     |    1078  |
|BONECP     |   29327   |   73      |    1568  |
|TOMCATJDBC |   39000   |   356     |    2061  |
|C3P0       |   28883   |   121141  |   81196  |

{% highlight java %}
import com.alibaba.druid.pool.DruidDataSource;
import com.jolbox.bonecp.BoneCPDataSource;
import com.mchange.v2.c3p0.ComboPooledDataSource;
import org.junit.Test;
import org.logicalcobwebs.proxool.ProxoolDataSource;

import javax.sql.DataSource;
import java.lang.management.ManagementFactory;
import java.lang.management.ThreadInfo;
import java.sql.Connection;
import java.sql.Statement;
import java.text.NumberFormat;
import java.util.concurrent.CountDownLatch;

/**
 * <pre>
 * CREATE TABLE `insert_test` (
 * `id` int(11) NOT NULL AUTO_INCREMENT,
 * `type` varchar(25) NOT NULL,
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * </pre>
 *
 * @author yuweijun 2016-09-29
 */
public class DataSourcePoolBenchmark {

    private String jdbcUrl = "jdbc:mysql://localhost/jdbc";
    private String user = "dbuser";
    private String password = "dbpass";
    private String driverClass = "com.mysql.jdbc.Driver";
    private int initialSize = 10;
    private int minPoolSize = 10;
    private int maxPoolSize = 50;
    private int maxActive = 50;
    private String validationQuery = "SELECT 1";
    private int threadCount = 50;
    private int loopCount = 10;

    final int LOOP_COUNT = 10000 * 1 * 1 / threadCount;

    @Test
    public void druid() throws Exception {
        System.out.println("## DRUID\n");
        DruidDataSource dataSource = new DruidDataSource();

        dataSource.setInitialSize(initialSize);
        dataSource.setMaxActive(maxActive);
        dataSource.setMinIdle(minPoolSize);
        dataSource.setPoolPreparedStatements(true);
        dataSource.setDriverClassName(driverClass);
        dataSource.setUrl(jdbcUrl);
        dataSource.setPoolPreparedStatements(true);
        dataSource.setUsername(user);
        dataSource.setPassword(password);
        dataSource.setValidationQuery(validationQuery);
        dataSource.setTestOnBorrow(false);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "druid", threadCount);
        }
    }

    @Test
    public void dbcp() throws Exception {
        System.out.println("## DBCP\n");
        final org.apache.commons.dbcp.BasicDataSource dataSource = new org.apache.commons.dbcp.BasicDataSource();

        dataSource.setInitialSize(initialSize);
        dataSource.setMaxActive(maxActive);
        dataSource.setMinIdle(minPoolSize);
        dataSource.setMaxIdle(maxPoolSize);
        dataSource.setPoolPreparedStatements(true);
        dataSource.setDriverClassName(driverClass);
        dataSource.setUrl(jdbcUrl);
        dataSource.setPoolPreparedStatements(true);
        dataSource.setUsername(user);
        dataSource.setPassword(password);
        dataSource.setValidationQuery("SELECT 1");
        dataSource.setTestOnBorrow(false);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "dbcp", threadCount);
        }
    }

    @Test
    public void dbcp2() throws Exception {
        System.out.println("## DBCP2\n");
        final org.apache.commons.dbcp2.BasicDataSource dataSource = new org.apache.commons.dbcp2.BasicDataSource();

        dataSource.setInitialSize(initialSize);
        dataSource.setMaxTotal(maxActive);
        dataSource.setMinIdle(minPoolSize);
        dataSource.setMaxIdle(maxPoolSize);
        dataSource.setPoolPreparedStatements(true);
        dataSource.setDriverClassName(driverClass);
        dataSource.setUrl(jdbcUrl);
        dataSource.setPoolPreparedStatements(true);
        dataSource.setUsername(user);
        dataSource.setPassword(password);
        dataSource.setValidationQuery("SELECT 1");
        dataSource.setTestOnBorrow(false);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "dbcp2", threadCount);
        }
    }

    @Test
    public void bonecp() throws Exception {
        // Exception throws:
        // com.mysql.jdbc.exceptions.jdbc4.MySQLNonTransientConnectionException: No operations allowed after connection closed.
        System.out.println("## BONECP\n");

        BoneCPDataSource dataSource = new BoneCPDataSource();
        dataSource.setMinConnectionsPerPartition(minPoolSize);
        dataSource.setMaxConnectionsPerPartition(maxPoolSize);

        dataSource.setDriverClass(driverClass);
        dataSource.setJdbcUrl(jdbcUrl);
        dataSource.setStatementsCacheSize(100);
        dataSource.setServiceOrder("LIFO");
        dataSource.setUsername(user);
        dataSource.setPassword(password);
        dataSource.setConnectionTestStatement("SELECT 1");
        dataSource.setPartitionCount(1);
        dataSource.setAcquireIncrement(5);
        dataSource.setIdleConnectionTestPeriodInMinutes(0L);
        dataSource.setDisableConnectionTracking(true);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "boneCP", threadCount);
        }
    }

    @Test
    public void c3p0() throws Exception {
        System.out.println("## C3P0\n");

        ComboPooledDataSource dataSource = new ComboPooledDataSource();
        dataSource.setMinPoolSize(minPoolSize);
        dataSource.setMaxPoolSize(maxPoolSize);

        dataSource.setDriverClass(driverClass);
        dataSource.setJdbcUrl(jdbcUrl);
        dataSource.setUser(user);
        dataSource.setPassword(password);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "c3p0", threadCount);
        }
    }

    @Test
    public void proxool() throws Exception {
        // Exception throws
        // java.sql.SQLException: We are already in the process of making 50 connections and the number of simultaneous builds has been throttled to 10
        System.out.println("## PROXOOL\n");

        ProxoolDataSource dataSource = new ProxoolDataSource();
        // 这个值默认为10，高并发一直抛异常，调到50还是抛异常
        dataSource.setSimultaneousBuildThrottle(50);

        dataSource.setMinimumConnectionCount(minPoolSize);
        dataSource.setMaximumConnectionCount(maxPoolSize);

        dataSource.setDriver(driverClass);
        dataSource.setDriverUrl(jdbcUrl);
        dataSource.setUser(user);
        dataSource.setPassword(password);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "proxool", threadCount);
        }
    }

    @Test
    public void tomcat_jdbc() throws Exception {
        System.out.println("## TOMCAT JDBC\n");

        org.apache.tomcat.jdbc.pool.DataSource dataSource = new org.apache.tomcat.jdbc.pool.DataSource();
        dataSource.setMaxIdle(maxPoolSize);
        dataSource.setMinIdle(minPoolSize);
        dataSource.setMaxActive(maxPoolSize);

        dataSource.setDriverClassName(driverClass);
        dataSource.setUrl(jdbcUrl);
        dataSource.setUsername(user);
        dataSource.setPassword(password);

        for (int i = 0; i < loopCount; ++i) {
            run(dataSource, "tomcat-jdbc", threadCount);
        }
    }

    private void run(final DataSource dataSource, final String name, int threadCount) throws Exception {
        final CountDownLatch startLatch = new CountDownLatch(1);
        final CountDownLatch endLatch = new CountDownLatch(threadCount);
        final CountDownLatch dumpLatch = new CountDownLatch(1);

        Thread[] threads = new Thread[threadCount];
        for (int i = 0; i < threadCount; ++i) {
            Thread thread = new Thread() {

                public void run() {
                    try {
                        startLatch.await();
                        for (int i = 0; i < LOOP_COUNT; ++i) {
                            Connection conn = dataSource.getConnection();
                            // 并发插入数据测试，压入完可改成SELECT测试
                            Statement statement = conn.createStatement();
                            statement.executeUpdate("INSERT INTO insert_test(`type`) VALUES ('" + name + "')");
                            statement.close();
                            conn.close();
                        }
                    } catch (Exception ex) {
                        ex.printStackTrace();
                    }
                    endLatch.countDown();

                    try {
                        dumpLatch.await();
                    } catch (InterruptedException e) {
                        e.printStackTrace();
                    }
                }
            };
            threads[i] = thread;
            thread.start();
        }

        long startMillis = System.currentTimeMillis();
        startLatch.countDown();
        endLatch.await();

        long[] threadIdArray = new long[threads.length];
        for (int i = 0; i < threads.length; ++i) {
            threadIdArray[i] = threads[i].getId();
        }
        ThreadInfo[] threadInfoArray = ManagementFactory.getThreadMXBean().getThreadInfo(threadIdArray);

        dumpLatch.countDown();

        long blockedCount = 0;
        long waitedCount = 0;
        for (int i = 0; i < threadInfoArray.length; ++i) {
            ThreadInfo threadInfo = threadInfoArray[i];
            blockedCount += threadInfo.getBlockedCount();
            waitedCount += threadInfo.getWaitedCount();
        }

        long millis = System.currentTimeMillis() - startMillis;

        StringBuilder stringBuilder = new StringBuilder().append("thread ").append(threadCount)
                .append(" ").append(name).append(" millis ").append(NumberFormat.getInstance().format(millis))
                .append(" blockedCount ").append(NumberFormat.getInstance().format(blockedCount))
                .append(" waitedCount ").append(NumberFormat.getInstance().format(waitedCount));

        System.out.println(stringBuilder.toString());
    }

}
{% endhighlight %}

References
-----

1. [durid benchmark](https://github.com/alibaba/druid/blob/master/src/test/java/com/alibaba/druid/benckmark/pool/Case1.java)


