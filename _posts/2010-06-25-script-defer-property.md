---
layout: post
title: "script标签中的defer属性说明"
date: "Fri Jun 25 2010 15:24:00 GMT+0800 (CST)"
categories: javascript
---

`script`中的`defer`属性默认情况下是`false`的，其主要作用是为了提升页面性能，实际在目前的HTML 4.0中这个属性是个鸡肋，在各浏览器中表现也不一样，最好忽略此属性。

在[微软MSDN](http://msdn.microsoft.com/en-us/library/ms533055%28v=VS.85%29.aspx)中的文档说明摘录部分内容如下：

Remarks: Using the attribute at design time can improve the download performance of a page because the browser does not need to parse and execute the script and can continue downloading and parsing the page instead.

Standards Information: This property is defined in HTML 4.0 World Wide Web link and is defined in World Wide Web Consortium (W3C) Document Object Model (DOM) Level 1 World Wide Web link.

在[W3C中的说明](http://www.w3.org/TR/REC-html40/interact/scripts.html#adef-defer)摘录部分内容如下：

defer [CI]
-----

When set, this boolean attribute provides a hint to the user agent that the script is not going to generate any document content (e.g., no "document.write" in javascript) and thus, the user agent can continue parsing and rendering.

指示脚本不会生成任何的文档内容(不要在其中使用`document.write`命令，不要在`defer`型脚本程序段中包括任何立即执行脚本要使用的全局变量或者函数)，浏览器可以继续解析并绘制页面。但是`defer`的`script`在什么时候执行，执行顺序情况并无明确规定。

正在制定的HTML5有极大可能会完善`script`[标签](http://www.w3.org/TR/html5/semantics.html#attr-script-defer)的定义，这里有简单的HTML5中`defer`属性的[定义和用法](http://www.w3school.com.cn/html5/html5_script.asp)。

`async`和`defer`二个属性属性与`src`属性一起使用，`async`定义脚本是否异步执行。

{% highlight text %}
如果 async 属性为 true，则脚本会相对于文档的其余部分异步执行，这样脚本会可以在页面继续解析的过程中来执行。
如果 async 属性为 false，而 defer 属性为 true，则脚本会在页面完成解析时得到执行。
如果 async 和 defer 属性均为 false，那么脚本会立即执行，页面会在脚本执行完毕继续解析。
{% endhighlight %}

The async and defer attributes are boolean attributes that indicate how the script should be executed.

There are three possible modes that can be selected using these attributes. If the async attribute is present, then the script will be executed asynchronously, as soon as it is available. If the async attribute is not present but the defer attribute is present, then the script is executed when the page has finished parsing. If neither attribute is present, then the script is fetched and executed immediately, before the user agent continues parsing the page. The exact processing details for these attributes are described below.

The defer attribute may be specified even if the async attribute is specified, to cause legacy Web browsers that only support defer (and not async) to fall back to the defer behavior instead of the synchronous blocking behavior that is the default.

If one or both of the defer and async attributes are specified, the src attribute must also be specified.
