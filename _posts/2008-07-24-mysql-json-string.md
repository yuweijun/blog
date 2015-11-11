---
layout: post
title: "mysql输出json字符串"
date: "Thu Jul 24 2008 21:51:00 GMT+0800 (CST)"
categories: mysql
---

假如有一个表users中有二个字段username和email，可以用以下语句获取一个json字符串。

{% highlight sql %}
SELECT
     CONCAT("[",
          GROUP_CONCAT(
               CONCAT("{username:'",username,"'"),
               CONCAT(",email:'",email),"'}")
          )
     ,"]")
AS json FROM users;
{% endhighlight %}
