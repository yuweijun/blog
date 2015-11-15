---
layout: post
title: "firebug command line api 和 console api 中比较常用到的几个方法"
date: "Sun Oct 05 2008 22:40:00 GMT+0800 (CST)"
categories: javascript
---

1. `$(id)`与`prototype`用法类似，只能传字符串的Element Id。
2. `$$(selector)`与`prototype`用法类似，传一个CSS Selector字符串，另专门还有一个`$x("xPath")`方法接受xpath方式查询DOM节点。
3. `dir(object)`遍历object所有属性，在console中打印出来。
4. `clear()`清空console。
5. `debug(fn)`在对应的fn方法第一行加一个断点，当此方法调用时就会进入此方法第一行，可进行断点调试此方法。对应的有`undebug(fn)`取消断点。
6. `console.log(object[, object, ...])`，`console`是Firebug在Firefox中添加的一个全局变量名。其`log()`方法会将对象打印到firebug控制台中，支持类似`printf()`的功能。
7. 同`console.log()`类似的还有 `console.debug()`， `console.info()`， `console.warn()`， `console.error()`方法。
8. `console.assert(expression[, object, ...])`断言功能，用来检查表达式是否正确执行。

References

1. [http://getfirebug.com/commandline.html](http://getfirebug.com/commandline.html)
2. [http://getfirebug.com/console.html](http://getfirebug.com/console.html)
