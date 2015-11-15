---
layout: post
title: "collapsing margins of table"
date: "Wed Feb 03 2010 16:46:00 GMT+0800 (CST)"
categories: css
---

`display=block`的元素，垂直方向的collapsing margins会发生重合。

ie8/safari/chrome中table默认的`display=table`，其渲染时vertical margin会发生重合，与`display=block`的元素效果一样，但在firefox中测试其效果与float元素的效果一样，不会发生垂直方向的margin重合。

个人的解决方法是将table设置为`float=left`，并在其后设置一个`clear=both`的div，避免table的margin被重合。

关于collapsing margins详细说明可查看CSS21规范的`8.3.1 Collapsing margins`。

另外对于一个`maring:10px`的div元素，如果此元素没有内容，并没有设置padding和border值，其上部的margin与其下部的margin也会重合，也就是此div在垂直方向上只会占据10px的高度。如果此div的top或者bottom与别的元素发生过重合，则其本身的top和bottom margin不会再重合。

规范原文说明如下
-----

If the top and bottom margins of a box are adjoining, then it is possible for margins to collapse through it. In this case, the position of the element depends on its relationship with the other elements whose margins are being collapsed.

If the element’s margins are collapsed with its parent’s top margin, the top border edge of the box is defined to be the same as the parent’s.

Otherwise, either the element’s parent is not taking part in the margin collapsing, or only the parent’s bottom margin is involved. The position of the element’s top border edge is the same as it would have been if the element had a non-zero top border.
