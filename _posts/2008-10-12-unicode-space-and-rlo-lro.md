---
layout: post
title: "unicode spaces and rlo/lro"
date: "Sun Oct 12 2008 14:15:00 GMT+0800 (CST)"
categories: vim
---

看下面2行的区别并复制下面第一行到任何文本编辑器中查看效果。

{% highlight textt %}
‮tset (e202u\:值edocinu)OLR符字制控

‭控制字符LRO(unicode值:\u202d) test
{% endhighlight %}

其他Unicode空白字符

{% highlight textt %}
&#8234;
&#8235;
&#8236;
&#8237;
&#8238;
{% endhighlight %}

另：这个如果用vim看的话，能看到这些`unicode space characters`，如`\u202e`这个在vim里会是`<202e>`这样的一个字符，可以被复制粘贴。

References
-----

1. [http://www.cs.tut.fi/~jkorpela/chars/spaces.html](http://www.cs.tut.fi/~jkorpela/chars/spaces.html)
