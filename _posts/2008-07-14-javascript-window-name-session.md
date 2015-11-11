---
layout: post
title: "利用window.name保存传递页面session状态"
date: "Thu Jul 24 2008 23:07:00 GMT+0800 (CST)"
categories: javascript
---

当在网页浏览中，所有的网页都是在同一个浏览器窗口（或者是tab）中浏览时，可以利用`window.name`进行页面状态的传递，相当于页面的session被持久在当前窗口的`window.name`中，不同的页面都可以读取到存在`window.name`中的值。

[测试页地址](http://www.thomasfrank.se/sessvarsTestPage1.html)

References
------

1. [http://www.thomasfrank.se/sessionvars.html](http://www.thomasfrank.se/sessionvars.html)
