---
layout: post
title: "open flash chart io error loading test data error #2032"
date: "Thu Jan 27 2011 17:24:00 GMT+0800 (CST)"
categories: web
---

在ie6中使用open flash chart2加载图表的json数据时，第一次载入数据，图表渲染正常，第二次就会报一个错误，提示数据加载错误：

{% highlight text %}
Open Flash Chart
IO ERROR
Loading test data
Error #2032
{% endhighlight %}

而在其他的浏览器，如ie8/firefox/chrome中都是正常的，在网上搜索了一些回答，其中有人提到说这是因为浏览器的缓存造成的问题，只要在用swfobject加载open-flash-chart.swf时，在url后面加上一个动态参数，让浏览器不要使用本地缓存：

{% highlight javascript %}
swfobject.embedSWF("open-flash-chart.swf?t=" + (new Date()).getTime(), "charts-div-id", "100%", "300", "9.0.0", "expressInstall.swf", {"get-data":"get_chart_0"});
{% endhighlight %}

References
-----

1. [Open Flash Chart IO ERROR Loading test data Error #2032](http://www.sodiy.com.cn/blog/201011/Open_Flash_Chart_IO_ERROR_2032.html)
