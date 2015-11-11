---
layout: post
title: "margin和内联元素inline elements的关系"
date: "Mon Mar 03 2008 22:07:00 GMT+0800 (CST)"
categories: css
---

当将margin应用于内联元素时，margin的上下边距将不会影响到行高——产生的margin是存在的，不过因其是透明的故不能看得到，左右边距则会影响内联元素的左右间距。

如果将一个将粗的border应用于内联元素上时，行高也不会发生变化，border将会覆盖其他元素的显示。

能够改变全文本的行间距的CSS属性只有:

1. line-height
2. font-size
3. vertical-align
