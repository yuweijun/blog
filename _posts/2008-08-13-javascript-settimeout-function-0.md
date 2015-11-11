---
layout: post
title: "javascript setTimeout(fn, 0)使用"
date: "Wed Aug 13 2008 01:05:00 GMT+0800 (CST)"
categories: javascript
---

在javascript中，程序是一行行命令式执行的，在上一行返回结果后执行下一行的语句，并且在客户端浏览器里是单线程运行的。

如果在执行一条要耗时的操作，而接下来的程序操作与此条执行语句并无关系，则可将此语句放在一个闭包里去执行，用`setTimeout(function(){}, 0)`来激活，这个技巧可使浏览器javascript引擎在运行完前面的任务后第一时间运行`setTimeout`中的function，并不是真是延时0毫秒就运行的意思。

javascript
-----

{% highlight javascript %}
function doSave(id, doYield) {
    document.getElementById(id).innerHTML = '<span style="font-style: italic;">Saving...</span>';
    if (doYield) {
        var startTime = (new Date()).getTime();
        setTimeout(function() {
            doSaveImpl(id);
        }, 0); // runing in clousres
        var endTime = (new Date()).getTime();
        console.log("yield time is:" + (endTime - startTime));
    } else {
        var startTime = (new Date()).getTime();
        doSaveImpl(id);
        var endTime = (new Date()).getTime();
        alert(endTime - startTime); // firebug console.log()
    }
}

function doSaveImpl(id) {
    var numIters = 10000000;
    for (var i = 0; i < numIters; i++) {
        var j = Math.sqrt(i); // slow operation
    }

    document.getElementById(id).innerHTML = '<span style="color: #090">Saved!</span>';

    setTimeout(function() {
        reset(id);
    }, 3000);
}

function reset(id) {

    document.getElementById(id).innerHTML = 'Ready';

}
{% endhighlight %}

html
-----

{% highlight html %}
<h2>Without yielding:</h2>
<div id="noyield" class="status_msg">Ready</div>

<h2>With yielding:</h2>
<a href="javascript:;" onmousedown="doSave('yield', true); return false;" class="button">Save</a>
<div id="yield" class="status_msg">Ready</div>
{% endhighlight %}

References
-----

1. [http://josephsmarr.com/oscon-js/yield.html](http://josephsmarr.com/oscon-js/yield.html)
