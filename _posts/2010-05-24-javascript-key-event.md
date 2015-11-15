---
layout: post
title: "javascript中keyevent按键事件说明(录自javascript权威指南第5版)"
date: "Mon May 24 2010 12:30:00 GMT+0800 (CST)"
categories: javascript
---

有3种按键类型，分别是`keydown`、`keypress`和`keyup`，它们分别对应`onkeydown`、`onkeypress`和`onkeyup`这几个事件处理器。

一个按键操作会产生这3个事件，依次是`keydown`、`keypress`，然后在按键释放的时候`keyup`。

这3个事件类型中，`keypress`事件是最为用户友好的：和它们相关的事件对象包含了所产生的实际字符的编码。

`keydown`和`keyup`事件是较底层的，它们的按键事件包含一个和键盘所生成的硬件编码相关的`虚拟按键码`。对于`ASCII`字符集中的数字和字符，这些`虚拟按键码`和`ASCII码`相同。

如果按下`shift`键并按下`数字2`，`keydown`事件将通知发生了`shitf-2`的按键事件。`keypress`事件会解释这一事件，说明这次按键产生了一个可打印的字符`@`。

对于不能打印的功能按键，如`backspace`、`enter`、`escape`和`箭头方向键`、`page up`、`page down`以及`f1`到`f12`，它们会产生`keydown`和`keyup`事件。

在不同的浏览器中，按键事件的一些细节区别如下
-----

1. 对于不能打印的功能按键，在firefox中，也会产生keypress事件，在ie和chrome中，则不会触发keypress事件，只有当按键有一个ascii码的时候，即此字符为可打印字符或者一个控制字符的时候，keypress事件才会发生。对于这些不能打印的功能按键，可通过和keydown事件相关的keycode来获取。
2. 作为一条通用的规则，keydown事件对于功能按键来说是最有用的，而keypress事件对于可打印的按键来说是最有用的。
3. 在ie中，alt按键组合被认为是无法打印的，所以并不会触发keypress事件。
4. 在Firefox中，按键事件定义有二个属性，keyCode存储了一个按键的较低层次的虚拟按键码，并且和keydown事件一起发送。charCode存储了按下一个键时所产生的可打印的字符的编码，并且和keypress事件一起发送。在Firefox中，功能按键会产生一个keypress事件，在这种情况下，charCode是0，而keyCode包含了虚拟按键码。在Firefox中，发生keydown事件时，charCode都为0，所以在keydown时获取charCode是无意义的。
5. 在IE中，只有一个keyCode属性，并且它的解释也取决于事件的类型。对于keydown事件来说，keyCode是一个虚拟按键码，对于keypress事件来说，keyCode是一个字符码。
6. 在Chrome中，功能键与IE中表现一样，不会触发keypress事件，对于keydown事件，也会在事件的keyCode中存储虚拟按键码，而charCode为0，与IE和Firefox表现一样，然而在发生可打印字符的keypress事件时，除了与Firefox一样，会在事件的charCode中存储实际按键编码之外，也会在keyCode中存储实际按键码，这二个值相同。
7. charCode字符码可以使用静态方数String.fromCharCode()转为字符。
