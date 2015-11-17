---
layout: post
title: "解决ie6的浮动元素的双倍边距问题"
date: "Tue Aug 12 2008 12:46:00 GMT+0800 (CST)"
categories: css
---

ie6 bug
-----

{% highlight css %}
.ie-bug-div {
    float: left;
    margin-left: 100px;
}
{% endhighlight %}

解决方法
-----

这个是因为ie6的haslayout造成的bug，只需要多设置一个`display: inline;`即可，代码如下：

{% highlight css %}
.ie-bug-div {
    float: left;
    margin: 40px;
    display: inline;
}
{% endhighlight %}
