---
layout: post
title: "ie6 throw null object exception when flash call javascript function"
date: "Fri Jan 28 2011 15:39:00 GMT+0800 (CST)"
categories: javascript
---

用open flash chart第一次正常载入一个图表到页面之后，当使用jQuery.fn.empty()方法移除此图表时，IE中会抛出一个错误，其他浏览器都是正常的，内容如下：

{% highlight text %}
JScript - script block, line 1 character 124
'null' is null or not an object
{% endhighlight %}

通过ie8的debug工具可以看到出错时，javascript正在执行的代码如下：

{% highlight javascript %}
try {
    document.getElementById("report-charts").SetReturnValue(__flash__toXML(ofc_resize([66, -96, 66, -87])));
} catch (e) {
    document.getElementById("report-charts").SetReturnValue("<undefined/>");
}
{% endhighlight %}

这个并不是页面中的javascript代码，应该是flash中调用外部javascript方法时，所使用的javascript代码，在ie8中断点调试，可以发现document.getElementById("report-charts")的结果为null，所以抛出了以上错误。

避过此问题的办法是在陊除已经载入的图表时，不调用jQuery.fn.empty()方法，而是直接使用jQuery.fn.html('')，将flash所在父元素的内容置空：

{% highlight javascript %}
$('#report-charts').parent().html('');
// 如果使用$('#report-charts').parent().empty();在IE中则会报错
// ... 重新生成report-charts对象并载入新的flash图表
swfobject.embedSWF(ofc, "report-charts", "100%", "300", "9.0.0", "expressInstall.swf", {"get-data":"get_chart_0"});
{% endhighlight %}
