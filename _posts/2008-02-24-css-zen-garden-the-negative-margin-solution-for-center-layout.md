---
layout: post
title: "css zen garden - the negative margin solution for center layout"
date: "Sun Feb 24 2008 17:14:00 GMT+0800 (CST)"
categories: css
---

`margin-left` is half of #container width, and `position` of #container must be `absolute`.

{% highlight css %}
#container {
     background: #ffc;
     position: absolute;
     left: 50%;
     width: 900px;
     margin-left: -450px;
}
{% endhighlight %}
