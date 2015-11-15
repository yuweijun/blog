---
layout: post
title: "ie中将tr appendchild到table的问题及解决方法"
date: "Fri Jul 31 2009 11:35:00 GMT+0800 (CST)"
categories: javascript
---

将一个TableRow对象appendChild到Table，在firefox3/safari4中测试都正常，在ie6中则不能正常显示。

Table无论是否有写TBODY，生成的DOM中都会自动生成，在IE中，TR对象被`appendChild`到了Table中，而非TBODY中，所以无法显示，所以可以将TR对象`appendChild`到`TBODY`中。

还有就是可以使用`Table.insertRow()`方法添加一个新行。


