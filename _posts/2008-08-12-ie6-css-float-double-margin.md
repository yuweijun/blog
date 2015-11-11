---
layout: post
title: "解决IE6的浮动元素的双倍边距问题"
date: "Tue Aug 12 2008 12:46:00 GMT+0800 (CST)"
categories: css
---

对一个div设置了`float:left`和`margin-left:100px`那么在IE6中，这个bug就会出现。您只需要多设置一个`display`即可，代码如下：

{% highlight css %}
div {
    float:left;
    margin:40px;
    display:inline;
}
{% endhighlight %}
