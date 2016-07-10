---
layout: post
title: "mysql indexing optimization"
date: Sun, 10 Jul 2016 10:00:12 +0800
categories: mysql
---

查询优化通常需要权衡取舍，例如，为了加快数据读取而添加的索引会减慢更新的速度，同样，非规范化的架构能加快某些类型的查询，但却会让其他类型的查询变慢。添加计数器和汇总表(Summary
Table)是优化查询的好办法，但它们的维护数据一致性代价也很高。

