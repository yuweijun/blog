---
layout: post
title: "css zen garden - centered design using auto margins"
date: "Sun Feb 24 2008 16:51:00 GMT+0800 (CST)"
categories: css
---

The preferred way to horizontally center any element is to use the margin property and set left and right values to auto. For this to work with layouts, you'll create a containing div. You must include a width for your containing div:

{% highlight css %}
div#container {
    margin-left: auto;
    margin-right: auto;
    width: 168px;
}
{% endhighlight %}
