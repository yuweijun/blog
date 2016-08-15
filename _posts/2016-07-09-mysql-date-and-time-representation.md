---
layout: post
title: "mysql-5.6.4 date and time representation"
date: Sat, 09 Jul 2016 11:46:08 +0800
categories: mysql
---

DATETIME和TIMESTAMP区别
-----

1. DATETIME能保存大范围的值，从`1000-01-01 00:00:00`到`9999-12-31 23:59:59`，精度为秒。它把日期和时间封装在一个格式为`YYYYMMDDHHMMSS`的整数中，与时区无关。
2. DATETIME在5.6.4版本之前使用了8字节的存储空间。默认情况下，MySQL以一种可排序，清楚的格式显示DATETIME值，如`2016-07-09 10:12:33`，这种方式也符合ANSI标准。
3. TIMESTAMP类型保存了自1970年1月1日以来的秒数，它和Unix的时间戳相同。
4. TIMESTAMP只使用了4个字节的存储空间。它能表示的范围比DATETIME小得多。它表示的日期范围只能从`1970-01-01 00:00:01`到`2038-01-19 03:14:07`(UTC)。
5. MySQL提供了`FROM_UNIXTIME()`函数把UNIX时间戳转换为日期，并提供了`UNIX_TIMESTAMP()`函数，把日期转换为UNIX时间戳。
6. TIMESTAMP显示的值也依赖于时区，MySQL服务器，操作系统和客户端连接都有时区设置。数据库里保存为0的值，在不同的时区的客户端显示的结果并不相同。
7. TIMESTAMP还有个自动更新的特殊功能。在默认情况下，更新和插入记录，如果没有显式设置TIMESTAMP列的值，MySQL会把它设置为当前时间。
8. 除第一个以外的TIMESTAMP列也可以设置到当前的日期和时间，只要将列设为`NULL`，或`NOW()`。
9. 最后，TIMESTAMP列的默认值为`NOT NULL`，这和其他的数据类型都不一样。

因为TIMESTAMP值不能比`1970-01-01`早，也不能比`2038-01-19`晚，这意味着，如果要存储一个生日日期`1968-01-01`，只能使用DATETIME或DATE，而不能使用TIMESTAMP数据类型进行存储！

MySQL-5.6.4版本前后变化
-----

`MySQL-5.6.4`版本升级对日期字段的精度和存储长度都有所变化。

下表为日期存储字段的长度变化：

|   Type    |   Storage before MySQL 5.6.4  |   Storage as of MySQL 5.6.4   |
|:---------:|:------------------------------|:------------------------------|
|YEAR       |1 byte, little endian          |Unchanged|
|DATE       |3 bytes, little endian         |Unchanged|
|TIME       |3 bytes, little endian         |3 bytes + fractional-seconds storage, big endian|
|TIMESTAMP  |4 bytes, little endian         |4 bytes + fractional-seconds storage, big endian|
|DATETIME   |8 bytes, little endian         |5 bytes + fractional-seconds storage, big endian|

`MySQL-5.6.4`之前的版本中日期和时间精度小数部分并不会被存储下来，如果需要存储时间的微秒或者毫秒级别的部分小数，需要用MySQL的时间处理函数，如`MICROSECOND()`截取小数部分，再存储到单独的一个整数字段中。

> mysql> SELECT MICROSECOND('2010-12-10 14:12:09.019473');
>
>
>
> | MICROSECOND('2010-12-10 14:12:09.019473') |
> |-------------------------------------------|
> |                                     19473 |

`MySQL-5.6.4`中对于`TIME`，`DATETIME`，和`TIMESTAMP`这3种数据类型做了升级，可以提供最高6位精度的小数位，也就是能存储最高到微秒(`microsecond`)级别的时间精度。

`TIMESTAMP(N)`的`N`在老版本的MySQL中是代表显示的宽度，其实际存储的值还是一样的，而不是精度的含义，这个特性在`MySQL-5.5.3`中被移除了。

使用如下语法定义时间精度：`type_name(fsp)`

其中，`type_name`是`TIME`，`DATETIME`或者`TIMESTAMP`，而`fsp`是时间小数的精度部分（0-6），如下例所示：

{% highlight sql %}
CREATE TABLE t1 (t TIME(3), dt DATETIME(6));
CREATE TABLE fractest( c1 TIME(2), c2 DATETIME(2), c3 TIMESTAMP(2) );

INSERT INTO fractest VALUES ('17:51:04.777', '2014-09-08 17:51:04.777', '2014-09-08 17:51:04.777');
{% endhighlight %}

MySQL精度相关的时间函数
-----

 MySQL中一些时间函数，如果没有传参数的话，跟老版本一样，返回的结果是以秒为精度，不带后面精度小数部分，以`NOW()`为例，其结果如果存储到`TIMESTAMP(6)`数据类型的字段中时，则显示为`2016-07-09 17:29:42.000000`，后面6位精度都是0。如果调用`NOW(3)`则显示为`2016-07-09 17:29:42.017`，如下所示：

> mysql> SELECT NOW(), NOW(3), NOW(6) \G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
>  NOW(): 2016-07-09 17:29:42
>
> NOW(3): 2016-07-09 17:29:42.017
>
> NOW(6): 2016-07-09 17:29:42.017278
>
> 1 row in set (0.00 sec)
>
> mysql> SELECT CURTIME(), CURTIME(3), CURTIME(6) \G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
>  CURTIME(): 20:55:45
>
> CURTIME(3): 20:55:45.183
>
> CURTIME(6): 20:55:45.183201
>
> 1 row in set (0.00 sec)
>
> mysql> SELECT SYSDATE(), SYSDATE(3), SYSDATE(6) \G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
>  SYSDATE(): 2016-07-09 20:56:26
>
> SYSDATE(3): 2016-07-09 20:56:26.301
>
> SYSDATE(6): 2016-07-09 20:56:26.301777
>
> 1 row in set (0.00 sec)
>
> mysql> SELECT UTC_TIMESTAMP(), UTC_TIMESTAMP(3), UTC_TIMESTAMP(6)\G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
>  UTC_TIMESTAMP(): 2016-07-09 12:57:35
>
> UTC_TIMESTAMP(3): 2016-07-09 12:57:35.477
>
> UTC_TIMESTAMP(6): 2016-07-09 12:57:35.477775
>
> 1 row in set (0.00 sec)

关于TIMESTAMP字段自动更新的SQL测试脚本
-----

{% highlight sql %}
DROP TABLE IF EXISTS `date_time`;
CREATE TABLE `date_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `t1` time(3),
  `t2` timestamp(6),
  `t3` datetime(1),
  `t4` datetime,
  `t5` timestamp(3) NULL DEFAULT 0,
  `t6` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `t7` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT `date_time` (t1, t2, t3, t4, t5) VALUES (now(3), now(6), now(1), now(), now(3));
INSERT `date_time` (t1, t3, t4, t6) VALUES (now(3), now(1), now(), null);

SELECT * FROM `date_time`\G
{% endhighlight %}

表字段定义从上到下，**第一个非空`TIMESTAMP`字段**，会被**自动更新**，如上所示的SQL执行结果：

> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 1
>
> t1: 17:21:54.792
>
> t2: 2016-07-09 17:21:54.792551  ==> 注意这个时间精度是用 NOW(6) 得到的
>
> t3: 2016-07-09 17:21:54.7
>
> t4: 2016-07-09 17:21:54
>
> t5: 2016-07-09 17:21:54.792     ==> 注意这个值是SQL写入的
>
> t6: 2016-07-09 17:21:54         ==> 注意这个值是MySQL自动更新的
>
> t7: 2016-07-09 17:21:54         ==> 注意这个值是MySQL自动更新的
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 2. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 2
>
> t1: 17:21:54.794
>
> t2: 2016-07-09 17:21:54.794616  ==> 注意这个值是MySQL自动更新的
>
> t3: 2016-07-09 17:21:54.7
>
> t4: 2016-07-09 17:21:54
>
> t5: 0000-00-00 00:00:00.000     ==> 注意这个默认值
>
> t6: 2016-07-09 17:21:54         ==> 注意这个值被设置为NULL后，由MySQL自动更新的
>
> t7: 2016-07-09 17:21:54         ==> 注意这个值是MySQL自动更新的
>
> 2 rows in set (0.00 sec)

而将TABLE中的`t2`和`t5`字段定义稍做改变，则这2个字段都不会被自动更新，除非像`t6`和`t7`这2个字段定义那样，显式声明为非空，并且用当前时间戳为默认值。

另外需要特别注意一下，对于`TIMESTAMP(6)`这种定义的字段，如果需要自动更新，最好是第一个`TIMESTAMP`字段，MySQL中不能对`TIMESTAMP(N)`使用如下形式的字段定义：

{% highlight sql %}
`t8` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
{% endhighlight %}

> -- ERROR 1067 (42000): Invalid default value for 't8'

{% highlight sql %}
`t8` timestamp(6) NOT NULL ON UPDATE CURRENT_TIMESTAMP,
{% endhighlight %}

> -- ERROR 1294 (HY000): Invalid ON UPDATE clause for 't8' column

测试脚本二
-----

{% highlight sql %}
DROP TABLE IF EXISTS `date_time`;
CREATE TABLE `date_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `t1` time(3),
  `t2` timestamp(6) NULL DEFAULT 0,
  `t3` datetime(1),
  `t4` datetime,
  `t5` timestamp(3),
  `t6` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `t7` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT `date_time` (t1, t2, t3, t4, t5) VALUES (now(3), now(6), now(1), now(), now(3));
INSERT `date_time` (t1, t3, t4, t6) VALUES (now(3), now(1), now(), null);

SELECT * FROM `date_time`\G
{% endhighlight %}

执行结果如下所示：

> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 1
>
> t1: 17:20:41.948
>
> t2: 2016-07-09 17:20:41.948262  ==> 注意这个值是SQL写入的
>
> t3: 2016-07-09 17:20:41.9
>
> t4: 2016-07-09 17:20:41
>
> t5: 2016-07-09 17:20:41.948     ==> 注意这个值是SQL写入的
>
> t6: 2016-07-09 17:20:41         ==> 注意这个值是MySQL自动更新的
>
> t7: 2016-07-09 17:20:41         ==> 注意这个值是MySQL自动更新的
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 2. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 2
>
> t1: 17:20:41.948
>
> t2: 0000-00-00 00:00:00.000000  ==> 注意这个是默认值
>
> t3: 2016-07-09 17:20:41.9
>
> t4: 2016-07-09 17:20:41
>
> t5: 0000-00-00 00:00:00.000     ==> 注意这个TIMESTAMP字段并没有被自动更新
>
> t6: 2016-07-09 17:20:41         ==> 注意这个值被设置为NULL后，由MySQL自动更新的
>
> t7: 2016-07-09 17:20:41         ==> 注意这个值是MySQL自动更新的
>
> 2 rows in set (0.00 sec)

spring-boot test case
-----

{% highlight java %}
import com.example.spring.jdbc.SpringJdbcApplication;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.SpringApplicationConfiguration;
import org.springframework.boot.test.WebIntegrationTest;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.transaction.annotation.Transactional;

import java.sql.Timestamp;

@RunWith(SpringJUnit4ClassRunner.class)
@SpringApplicationConfiguration(SpringJdbcApplication.class)
@WebIntegrationTest(randomPort = true)
@Transactional
public class MysqlTimestampPrecisionTest {

    private static final Logger LOGGER = LoggerFactory.getLogger(MysqlTimestampPrecisionTest.class);

    @Autowired
    private JdbcTemplate jdbcTemplate;

    @Test
    public void testTimestamp() {
        Timestamp now = new Timestamp(System.currentTimeMillis());
        // now is 2016-07-09 17:57:11.207
        LOGGER.info("now is {}", now);
        jdbcTemplate.execute("truncate table date_time");

        /*
         * mysql> SELECT * FROM `date_time`\G
         * ************************** 1. row ***************************
         * id: 1
         * t1: 17:57:11.207
         * t2: 2016-07-09 17:57:11.207000
         * t3: 2016-07-09 17:57:11.2
         * t4: 2016-07-09 17:57:11
         * t5: 2016-07-09 17:57:11.207
         * t6: 2016-07-09 17:57:11
         * t7: 2016-07-09 17:57:11
         * 1 row in set (0.00 sec)
         */
        int inserted = jdbcTemplate.update("insert into date_time(t1, t2, t3, t4, t5, t6) values (?, ?, ?, ?, ?, ?)", now, now, now, now, now, now);
        Assert.assertEquals(1, inserted);

        int countByT2 = jdbcTemplate.queryForObject("select count(*) from date_time where id = ? and t2 = ?", Integer.class, 1, now);
        Assert.assertEquals(1, countByT2);
        int countByT3 = jdbcTemplate.queryForObject("select count(*) from date_time where id = ? and t3 = ?", Integer.class, 1, now);
        Assert.assertEquals(0, countByT3);
        int countByT5 = jdbcTemplate.queryForObject("select count(*) from date_time where id = ? and t5 = ?", Integer.class, 1, now);
        Assert.assertEquals(1, countByT5);
    }

    @Test
    public void testDate() {
        SimpleDateFormat format = new SimpleDateFormat();
        format.applyPattern("yyyy-MM-dd HH:mm:ss.SSS");
        Date now = new Date();
        // now is 2016-07-09 20:25:07.815
        LOGGER.info("now is {}", format.format(now));
        jdbcTemplate.execute("truncate table date_time");

        /*
         * mysql> SELECT * FROM `date_time`\G
         * ************************** 1. row ***************************
         * id: 1
         * t1: 20:25:07.815
         * t2: 2016-07-09 20:25:07.815000
         * t3: 2016-07-09 20:25:07.8
         * t4: 2016-07-09 20:25:08
         * t5: 2016-07-09 20:25:07.815
         * t6: 2016-07-09 20:25:08
         * t7: 2016-07-09 20:25:07
         * 1 row in set (0.00 sec)
         * -- t6和t7有点意思啊, 同时写入的, 一个是MySQL自动设置的值, t6被四舍五入为08, 而t7自动设置的值居然是07秒。
         * -- SQL插入时间肯定在java代码之后发生，也就是t7获取的时间戳CURRENT_TIMESTAMP值与后面的精度并没有关系，因此也不会四舍五入到08秒。
         */
        int inserted = jdbcTemplate.update("insert into date_time(t1, t2, t3, t4, t5, t6) values (?, ?, ?, ?, ?, ?)", now, now, now, now, now, now);
        // insert 1 records
        LOGGER.info("insert {} records", inserted);

        int countByT2 = jdbcTemplate.queryForObject("select count(*) from date_time where id = ? and t2 = ?", Integer.class, 1, now);
        Assert.assertEquals(1, countByT2);
        int countByT3 = jdbcTemplate.queryForObject("select count(*) from date_time where id = ? and t3 = ?", Integer.class, 1, now);
        Assert.assertEquals(0, countByT3);
        int countByT5 = jdbcTemplate.queryForObject("select count(*) from date_time where id = ? and t5 = ?", Integer.class, 1, now);
        Assert.assertEquals(1, countByT5);
    }
}
{% endhighlight %}

运行结果中可以看到`java.sql.Timestamp`和`java.util.Date`提供到毫秒(`millisecond`)级别的精度`2016-07-09 17:57:11.207`，保存到MySQL中`TIMESTAMP(3)`精度以上数据类型的字段中都不会丢失精度，所以上面根据`t2`和`t5`都可以查找到刚插入的记录，而使用`t3`就无法查到刚插入的这条记录，因为数据库保存这条记录的时候，已经将后面的精度舍弃掉了，所以带着精度去查找记录时就匹配不到记录了。

JDBC URL连接参数设置
-----
`MySQL Connector/J 5.1.37`发布时增加了一个新的MySQL连接属性`sendFractionalSeconds`，用这个变量表示服务器是否支持高精度的`TIMESTAMP`，并实现了`JDBC 4.2 API`的支持，注意下面参数说明，只有`Statement`中的时间精度会被JDBC处理。

> sendFractionalSeconds
>
> Send fractional part from TIMESTAMP seconds. If set to false, the nanoseconds value of TIMESTAMP values will be truncated before sending any data to the server. This option applies only to prepared statements, callable statements or updatable result sets.
>
> Default: true
>
> Since version: 5.1.37

#### JDBC URL Format

> jdbc:mysql://[host1][:port1][,[host2][:port2]]...[/[database]][?propertyName1=propertyValue1[&propertyName2=propertyValue2]...]

#### This is mandatory for IPv6 connections

> jdbc:mysql://address=(key1=value)[(key2=value)]...[,address=(key3=value)[(key4=value)]...]...[/[database]][?propertyName1=propertyValue1[&propertyName2=propertyValue2]...]

根据上面所说，假如使用`sendFractionalSeconds=false`形式的`JDBC URL`:

> jdbc:mysql://localhost/test?sendFractionalSeconds=false

再运行上面java的测试代码，就发现测试中三个查询语句都可以正确检索到记录行，并且数据库里生成的记录如下所示（因为以上测试会自动`ROLLBACK`，数据表中看不到记录，如果需要保存测试数据，需要加上`@Commit`），所有精度位都为`0`。

> mysql> SELECT * FROM `date_time`\G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 1
>
> t1: 22:38:43.000
>
> t2: 2016-07-09 22:38:43.000000
>
> t3: 2016-07-09 22:38:43.0
>
> t4: 2016-07-09 22:38:43
>
> t5: 2016-07-09 22:38:43.000
>
> t6: 2016-07-09 22:38:43
>
> t7: 2016-07-09 22:38:43
>
> 1 row in set (0.00 sec)

JDBC相关代码可查看以下几个接口和实现类：

1. com.mysql.jdbc.ConnectionPropertie
2. com.mysql.jdbc.ConnectionPropertieImpl
3. com.mysql.jdbc.Statement
4. com.mysql.jdbc.StatementImpl (sendFractionalSeconds定义)
5. com.mysql.jdbc.ServerPreparedStatement (sendFractionalSeconds使用)
6. com.mysql.jdbc.TimeUtil

`ServerPreparedStatement`中的相关代码：

{% highlight java %}
if (!this.sendFractionalSeconds) {
    x = TimeUtil.truncateFractionalSeconds(x);
}
{% endhighlight %}

`TimeUtil.truncateFractionalSeconds()`代码如下：

{% highlight java %}
public static Timestamp truncateFractionalSeconds(Timestamp timestamp) {
    Timestamp truncatedTimestamp = new Timestamp(timestamp.getTime());
    truncatedTimestamp.setNanos(0);
    return truncatedTimestamp;
}
{% endhighlight %}

References
-----

1. [10.9 Date and Time Data Type Representation](http://dev.mysql.com/doc/internals/en/date-and-time-data-type-representation.html)
2. [MySQL升级之timestamp的坑](http://louishust.github.io/mysql/2014/09/05/timestamp-bug/)
3. [11.3.1 The DATE, DATETIME, and TIMESTAMP Types](https://dev.mysql.com/doc/refman/5.6/en/datetime.html)
4. [11.3.1 The DATE, DATETIME, and TIMESTAMP Types](https://dev.mysql.com/doc/refman/5.5/en/datetime.html)
5. [11.3.6 Fractional Seconds in Time Values](https://dev.mysql.com/doc/refman/5.6/en/fractional-seconds.html)
6. [Changes in MySQL Connector/J 5.1.37 2015-10-15](https://dev.mysql.com/doc/relnotes/connector-j/5.1/en/news-5-1-37.html)
7. [MySQL Connector/J 5.1.37 has been released](http://insidemysql.com/mysql-connectorj-5-1-37-has-been-released/)
