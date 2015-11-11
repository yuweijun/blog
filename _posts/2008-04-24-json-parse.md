---
layout: post
title: "json 解析工具"
date: "Thu Apr 24 2008 10:18:00 GMT+0800 (CST)"
categories: javascript
---

当Ajax请求返回的数据为JSON格式的字符串,需要对此字符串进行解析,如果自己在JS中处理的话,按以下方式调用:

{% highlight javascript %}
eval('(' + json + ')');
{% endhighlight %}

用js的library的话，在prototypejs里String有个方法evalJSON()可以使用，Extjs里有一个JSON相关的Util组件，也可以用json官方网站提供的这个[js](http://www.json.org/json.js)。
