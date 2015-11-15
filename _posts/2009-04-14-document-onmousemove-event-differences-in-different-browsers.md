---
layout: post
title: "document onmousemove event differences in different browsers"
date: "Tue Apr 14 2009 20:37:00 GMT+0800 (CST)"
categories: javascript
---

当前文档document中如果有一个iframe时，当mouse滚过当前文档进入到iframe区域中时，不同的浏览器的表现形式有所不同，safari4/opera9.6中仍然能触发`mouseover`事件，但是在firefox3/ie中则不触发此`mouseover`事件，因为mouse已经离开了当前文档。
