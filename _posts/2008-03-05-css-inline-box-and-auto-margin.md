---
layout: post
title: "css inline-box and auto margin"
date: "Wed Mar 05 2008 16:37:00 GMT+0800 (CST)"
categories: css
---

`line-height`只对内联元素有效，对于块元素设置其实是作用在其内部的内联元素上。

水平七属性中`margin-left`、`margin-right`、`width`的值可以设置为`auto`，其他四个属性不能设置为`auto`。

水平七属性相加的值总是等于其父元素的`width`值。

`margin`的值可以取负值，`padding`和`width`不能取负值。

一行中，不同内联元素的内联框位置并不一定相同，根据`line-hight`、`font-size`计算，并受`vertical-align`影响。以下公式`在vertical-align`基于baseline的计算方法：

内联框的上边框位置：`font-size - (font-size - line-height) / 2`

内联框的下边框位置：`font-size + (font-size - line-height) / 2`

行间距等于该行中所有内联元素的内联框(inline-box)的最高位置减最低位置。
