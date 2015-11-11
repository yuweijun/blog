---
layout: post
title: "awk simple example"
date: "Sat Apr 05 2008 23:12:00 GMT+0800 (CST)"
categories: linux
---

awk的内置函数
-----

|V | 函数                                | 用途或返回值|
|:-|:------------------------------------|:-----------------------------------------|
|N | gsub(reg,string,target)             | 每次常规表达式reg匹配时替换target中的string|
|N | index(search,string)                | 返回string中search串的位置|
|A | length(string)                      | 求串string中的字符个数|
|N | match(string,reg)                   | 返回常规表达式reg匹配的string中的位置|
|N | printf(format,variable)             | 格式化输出，按format提供的格式输出变量variable。|
|N | split(string,store,delim)           | 根据分界符delim,分解string为store的数组元素|
|N | sprintf(format,variable)            | 返回一个包含基于format的格式化数据，variables是要放到串中的数据|
|G | strftime(format,timestamp)          | 返回一个基于format的日期或者时间串，timestmp是systime()函数返回的时间|
|N | sub(reg,string,target)              | 第一次当常规表达式reg匹配，替换target串中的字符串|
|A | substr(string,position,len)         | 返回一个以position开始len个字符的子串|
|P | totower(string)                     | 返回string中对应的小写字符|
|P | toupper(string)                     | 返回string中对应的大写字符|
|A | atan(x,y)                           | x的余切(弧度)|
|N | cos(x)                              | x的余弦(弧度)|
|A | exp(x)                              | e的x幂|
|A | int(x)                              | x的整数部分|
|A | log(x)                              | x的自然对数值|
|N | rand()                              | 0-1之间的随机数|
|N | sin(x)                              | x的正弦(弧度)|
|A | sqrt(x)                             | x的平方根|
|A | srand(x)                            | 初始化随机数发生器。如果忽略x，则使用system()|
|G | system()                            | 返回自1970年1月1日以来经过的时间（按秒计算）|

{% highlight bash %}
$> cat *.txt
php line 1
lin 2
line 3
php line 4

$> grep 'php' *.txt
php line 1
php line 4

$> cat *.txt|grep 'php'
php line 1
php line 4

$> awk -F " " '/php/' *.txt
php line 1
php line 4

$> awk -F " " '/php/{print NR": "$0}' *.txt
1: php line 1
4: php line 4

$> awk -F " " 'length($0)>7 {print NR": "$0}' *.txt
1: php line 1
4: php line 4

$> awk -F " " '/php|line 2/{print NR": "$0}' *.txt
1: php line 1
2: line 2
4: php line 4
{% endhighlight %}

在awk中，缺省的情况下总是将文本文件中的一行视为一个记录，而将一行中的某一部分作为记录中的一个字段。为了操作这些不同的字段，awk借用shell的方法，用`$1`，`$2`，`$3`……这样的方式来顺序地表示行（记录）中的不同字段。

特殊地，awk用`$0`表示整个行（记录）。不同的字段之间是用称作分隔符的字符分隔开的。系统默认的分隔符是空格。awk允许在命令行中用`-F re`的形式来改变这个分隔符。

附录: awk的常规表达式元字符
----

|字元               |描述    |
|:----------------- |:-------|
|\                  |换码序列|
|^                  |在字符串的开头开始匹配|
|$                  |在字符串的结尾开始匹配|
|.                  |与任何单个字符串匹配|
|[ABC]              |与[]内的任一字符匹配|
|[A-Ca-c]           |与A-C及a-c范围内的字符匹配（按字母表顺序）|
|[^ABC]             |与除[]内的所有字符以外的任一字符匹配|
|Desk\|Chair        |与Desk和Chair中的任一个匹配|
|[ABC][DEF]         |关联。与A、B、C中的任一字符匹配，且其后要跟D、E、F中的任一个字符|
|*                  |与A、B或C中任一个出现0次或多次的字符相匹配|
|+                  |与A、B或C中任何一个出现1次或多次的字符相匹配|
|？                 |与一个空串或A、B或C在任何一个字符相匹配|
|(Blue\|Black)berry |合并常规表达式，与Blueberry或Blackberry相匹配|


References
-----

1. [http://www.chinaunix.net/jh/7/16985.html](http://www.chinaunix.net/jh/7/16985.html)
