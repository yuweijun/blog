---
layout: post
title: "对于inline elements使用的几点说明"
date: "Fri Nov 21 2008 11:14:00 GMT+0800 (CST)"
categories: web
---

关于inline elements的几个css属性说明
-----

1. 设置`margin-top`，`margin-bottom`这二个属性是无效的，但是有`margin-left`，`margin-right`。
2. 设置以下几个属性也是无效的：`min-width`，`min-heigth`，`width`，`heigth`，`max-width`，`max-height`，另`width`的几个属性应用于table rows也是无效的，而`height`的几个属性应用于table columns也是无效的。
3. 对于`vertical-align`，则只能对`inline elements`和`table-cell elements`生效。
