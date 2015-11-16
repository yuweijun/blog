---
layout: post
title: "word-break:break-all和word-wrap:break-word的区别"
date: "Tue, 20 Mar 2012 15:31:46 +0800"
categories: css
---

它们的区别就在于
-----

1. `word-break: break-all`：例如div宽200px，它的内容就会到200px自动换行，如果该行末端有个英文单词很长（congratulation等），它会把单词截断，变成该行末端为conra(congratulation的前端部分)，下一行为tulation（conguatulation）的后端部分了。
2. `word-wrap: break-word`：例子与上面一样，但区别就是它会把congratulation整个单词看成一个整体，如果该行末端宽度不够显示整个单词，它会自动把整个单词放到下一行，而不会把单词截断掉的。
3. `word-break`原来是个ie专有的css属性，现在的浏览器多数都已经支持。

{% highlight text %}
word-break : normal | break-all | keep-all 参数:
normal : 依照亚洲语言和非亚洲语言的文本规则，允许在字内换行
break-all : 该行为与亚洲语言的normal相同。也允许非亚洲语言文本行的任意字内断开。该值适合包含一些非亚洲文本的亚洲文本。
keep-all : 与所有非亚洲语言的normal相同。对于中文，韩文，日文，不允许字断开。适合包含少量亚洲文本的非亚洲文本语法。
{% endhighlight %}

{% highlight text %}
word-wrap : normal | break-word 参数:
normal : 允许内容顶开指定的容器边界
break-word : 内容将在边界内换行。如果需要，词内换行（word-break）也行发生说明：设置或检索当当前行超过指定容器的边界时是否断开转行。
{% endhighlight %}

另外注意一下：`word-wrap`在css3中已经被一个[新属性名](http://www.w3.org/TR/css3-text/#overflow-wrap)`overflow-wrap`所替代。

建议
-----

如果允许英文单词内发生换行，推荐使用`word-break: break-all`，否则就使用`word-wrap`。

References
-----

1. [http://www.w3.org/TR/css3-text/#overflow-wrap](http://www.w3.org/TR/css3-text/#overflow-wrap)
2. [the difference between "word-break: break-all" versus "word-wrap: break-word"](http://stackoverflow.com/questions/1795109/what-is-the-difference-between-word-break-break-all-versus-word-wrap-break)
