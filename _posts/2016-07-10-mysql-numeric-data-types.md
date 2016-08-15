---
layout: post
title: "mysql numeric data types"
date: Sun, 10 Jul 2016 10:00:12 +0800
categories: mysql
---

INT数据类型
-----

整数类型包括：`TINYINT`、`SMALLINT`、`MEDIUMINT`、`INT`、`BIGINT`，分别需要`8`、`16`、`24`、`32`和`64`位存储空间。

有符号(`SIGNED`)和无符号数(`UNSIGNED`)类型占用的存储空间是一样的，性能也是一样的，所以可以根据实际情况采用合适的类型。

MySQL还可以对整数类型定义显示宽度，比如`INT(11)`。这对于大多数应用程序而言并没有实际意义，它并不是限制值的范围，只是规定了MySQL的交互工具，如客户端来显示数字的字符数的。对于实际的存储和计算而言，`INT(1)`和`INT(11)`是一样的。

常用的`INT`类型举例说明一下，像`SMALLINT`就要慎用，很容易发生数值超过上限而发生存储问题：

1. TINYINT能表示从`-128`到`127`的整型数据，存储大小为`1`字节，常用这个类型来表示`true/false`，虽然实际上`-127`一共有4个字符，但定义`TINYINT`字段最常用的形式是`TINYINT(1)`。
2. INT能存储计算`-2^31 (-2,147,483,648)`到`2^31–1 (2,147,483,647)`的整型数据，最常使用的字段类型，因为`-2147483648`字符长度为`11`，所以INT字段一般定义为`INT(11)`。
3. BIGINT能存储计算`-2^63 (-9223372036854775808)`到`2^63-1 (9223372036854775807)`的整型数据，UNSIGNED BIGINT范围为`0 ~ 18446744073709551615`，所以BIGINT字段一般定义为`BIGINT(20)`。

FLOAT/DOUBLE/DECIMAL/NUMERIC类型及区别
-----

1. MySQL中存在FLOAT，DOUBLE等非标准数据类型，也有DECIMAL这种标准数据类型。
2. 单精度FLOAT和双精度DECIMAL类型支持使用标准的浮点运算进行近似计算并保存，DECIMAL类型用于保存精确的小数，实际保存以字符串形式存在。
3. MySQL浮点类型可以用`type_name(M,D)`来表示，`M`表示该值的最多的所有数字个数（也就是不包括小数点和正负号的数字个数），`M`的有效范围为`1`到`65`，`D`表示小数点后面的数字长度，`D`的有效范围为`0`到`30`，并且不能大于`M`，`M`和`D`又称为精度`precision`和标度`scale`，如`FLOAT(7,4)`的可显示为`-999.9999`，MySQL保存值时进行四舍五入，如果插入`999.00009`，则结果为`999.0001`。
4. FLOAT和DOUBLE在不指定精度时，默认会按照实际的精度来显示，而DECIMAL在不指定精度时，默认整数为10，小数为0。
5. 这些类型实际上是定义数据的存储类型，在MySQL内部对浮点类型都使用DOUBLE进行计算。
6. 由于DECIMAL需要额外的空间和计算开销，只有在需要对小数进行精确计算的时候才使用DECIMAL，比如处理金融数据。
7. 在实际处理金融数据时，可以将数据放大100倍到分或者放大100,000倍到厘进行INT或者BIGINT整数存储和计算，可以避免用FLOAT、DOUBLE计算产生的误差。

#### 关于NUMERIC(M,D)和DECIMAL(M,D)的说明

> The SQL standard requires that the precision of NUMERIC(M,D) be exactly M digits. For DECIMAL(M,D), the standard requires a precision of at least M digits but permits more. In MySQL, DECIMAL(M,D) and NUMERIC(M,D) are the same, and both have a precision of exactly M digits.

实际测试结果及说明
-----

以下测试代码基于`MySQL 5.6.22`版本。

建表语句如下：

{% highlight sql %}
DROP TABLE IF EXISTS real_numeric;
CREATE TABLE real_numeric (
    id int(11) NOT NULL AUTO_INCREMENT,
    n1 FLOAT(5,2) NOT NULL DEFAULT 0,
    n2 DOUBLE(5,2) NOT NULL DEFAULT 0,
    n3 DECIMAL(5,2) NOT NULL DEFAULT 0,
    n4 NUMERIC(5,2) NOT NULL DEFAULT 0,
    n5 DOUBLE(9,5) NOT NULL DEFAULT 0,
    n6 DECIMAL(9,5) NOT NULL DEFAULT 0,
    n7 REAL(10,5) NOT NULL DEFAULT 0,
    n8 REAL NOT NULL DEFAULT 0 COMMENT 'NOT DEFINE PRECISION',
    n9 DECIMAL NOT NULL DEFAULT 0 COMMENT 'NOT DEFINE PRECISION',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{% endhighlight %}

> mysql> SHOW CREATE TABLE real_numeric\G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
>        Table: real_numeric
>
> Create Table: CREATE TABLE `real_numeric` (
>
>   `id` int(11) NOT NULL AUTO_INCREMENT,
>
>   `n1` float(5,2) NOT NULL DEFAULT '0.00',
>
>   `n2` double(5,2) NOT NULL DEFAULT '0.00',
>
>   `n3` decimal(5,2) NOT NULL DEFAULT '0.00',
>
>   `n4` decimal(5,2) NOT NULL DEFAULT '0.00',
>
>   `n5` double(9,5) NOT NULL DEFAULT '0.00000',
>
>   `n6` decimal(9,5) NOT NULL DEFAULT '0.00000',
>
>   `n7` double(10,5) NOT NULL DEFAULT '0.00000',
>
>   `n8` double NOT NULL DEFAULT '0' COMMENT 'NOT DEFINE PRECISION',
>
>   `n9` decimal(10,0) NOT NULL DEFAULT '0' COMMENT 'NOT DEFINE PRECISION',
>
>   PRIMARY KEY (`id`)
>
> ) ENGINE=InnoDB DEFAULT CHARSET=utf8

通过`SHOW CREATE TABLE`可以看到，表定义说一句中`n9`这个字段已经默认加了小数位长度为0，总长度为10位的精度定义：`decimal(10,0)`，`REAL`直接被`double`所代替。

> mysql> INSERT INTO real_numeric(n1, n2, n3, n4, n5, n6, n7, n8, n9) VALUES(123.45, 123.45, 123.45, 123.45, 1234.56789, 1234.56789, 12345.56789, 12345.56789, 1234567890);
>
> Query OK, 1 row affected (0.00 sec)
>
> mysql> SELECT * from real_numeric\G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 1
>
> n1: 123.45
>
> n2: 123.45
>
> n3: 123.45
>
> n4: 123.45
>
> n5: 1234.56789
>
> n6: 1234.56789
>
> n7: 12345.56789
>
> n8: 12345.56789
>
> n9: 1234567890
>
> 1 row in set (0.00 sec)

可以看到上面的输出结果，`n8`虽然没有定义字段精度，但是会如实保存数据，但`n9`则只能保存最多`10`个数字的整型数值。

> mysql> TRUNCATE TABLE real_numeric;
>
> Query OK, 0 rows affected (0.04 sec)
>
> -- 注意下面n1, n2, n3, n4这4个值的变化，插入数据库里产生的结果变化。
>
> mysql> INSERT INTO real_numeric(n1, n2, n3, n4, n5, n6, n7, n8, n9) VALUES(123.456, 1234.56, 1234.56, 1234.5, 1234.56789, 1234.56789, 12345.56789, 12345.56789, 1234567890);
>
> Query OK, 1 row affected, 3 warnings (0.01 sec)
>
> mysql> SHOW WARNINGS;
>
> | Level   | Code | Message                                     |
> |---------|------|---------------------------------------------|
> | Warning | 1264 | Out of range value for column 'n2' at row 1 |
> | Warning | 1264 | Out of range value for column 'n3' at row 1 |
> | Warning | 1264 | Out of range value for column 'n4' at row 1 |
>
> 3 rows in set (0.00 sec)
>
>
>
> mysql> SELECT * from real_numeric\G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 1
>
> n1: 123.46
>
> n2: 999.99
>
> n3: 999.99
>
> n4: 999.99
>
> n5: 1234.56789
>
> n6: 1234.56789
>
> n7: 12345.56789
>
> n8: 12345.56789
>
> n9: 1234567890
>
> 1 row in set (0.00 sec)

由结果可以看到，`n1`最后小数位被四舍五入了，`n2`，`n3`，`n4`由于小数点前面的精度已经超过了字段定义的数值范围，最后被保存为字段所允许的最大值`999.99`。

> mysql> TRUNCATE TABLE real_numeric;
>
> Query OK, 0 rows affected (0.01 sec)
>
> -- 注意下面n1, n2, n3, n4, n9字段的变化。
>
> mysql> INSERT INTO real_numeric(n1, n2, n3, n4, n5, n6, n7, n8, n9) VALUES(123.456, 123.456, 123.456, 123.456, 1234.56789, 1234.56789, 12345.56789, 12345.56789, 123456789.6);
>
> Query OK, 1 row affected, 3 warnings (0.05 sec)
>
> mysql> SHOW WARNINGS;
>
> | Level | Code | Message                                 |
> |-------|------|-----------------------------------------|
> | Note  | 1265 | Data truncated for column 'n3' at row 1 |
> | Note  | 1265 | Data truncated for column 'n4' at row 1 |
> | Note  | 1265 | Data truncated for column 'n9' at row 1 |
>
> 3 rows in set (0.00 sec)
>
> mysql> SELECT * FROM real_numeric\G
>
> \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\* 1. row \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> id: 1
>
> n1: 123.46
>
> n2: 123.46
>
> n3: 123.46
>
> n4: 123.46
>
> n5: 1234.56789
>
> n6: 1234.56789
>
> n7: 12345.56789
>
> n8: 12345.56789
>
> n9: 123456790
>
> 1 row in set (0.00 sec)

从结果可以看到，`n1`，`n2`，`n3`，`n4`和`n9`最后小数位精度都超过了字段定义时的限制，最后数值保存时，小数位被四舍五入了。

References
-----

1. [13.21.2 DECIMAL Data Type Characteristics](https://dev.mysql.com/doc/refman/5.7/en/precision-math-decimal-characteristics.html)
2. [12.2.3 Floating-Point Types Approximate Value - FLOAT, DOUBLE](https://dev.mysql.com/doc/refman/5.7/en/floating-point-types.html)
