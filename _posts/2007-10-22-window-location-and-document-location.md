---
layout: post
title: "window.location and document.location"
date: "Mon Oct 22 2007 14:51:00 GMT+0800 (CST)"
categories: javascript
---

`window.location`和`window.location.herf`为可读写的属性，对此赋值可定向页面去指定的URL。

`document.location`和`document.URL`等价，是可只读属性，并且推荐使用`document.URL`，`document.location`已废弃。不过多数浏览器还是可以对`document.location `和`document.location.href`赋值定向到新的URL，此做法不推荐使用。
