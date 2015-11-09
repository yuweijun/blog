---
layout: post
title: "firefox和ie的本地hosts域名解析区别"
date: "Fri May 11 2007 22:39:00 GMT+0800 (CST)"
categories: firefox
---

Firefox会在启动时就读取`C:/windows/system32/drivers/etc/hosts`里的设置，如果修改了这个文件，IE是即时生效的，而firefox只有再次重启才会使`C:/windows/system32/drivers/etc/hosts`文件里的解析生效。

linux系统这个文件一般是`/etc/hosts`。
