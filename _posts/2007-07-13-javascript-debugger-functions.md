---
layout: post
title: "javascript debugger functions"
date: "Fri Jul 13 2007 22:39:00 GMT+0800 (CST)"
categories: javascript
---

javascript调试错误时可以使用，来源互联网:

{% highlight javascript %}
// Helper function to parse out the name from the text of the function:
function getFunctionName(f) {
    if (/function (\w+)/.test(String(f))) {
        return RegExp.$1;
    } else {
        return "";
    }
}

// Manually piece together a stack trace using the caller property
function constructStackTrace(f) {
    if (!f) {
        return "";
    }

    var thisRecord = getFunctionName(f) + "(";

    for (var i = 0; i < f.arguments.length; i++) {
        thisRecord += String(f.arguments[i]);
        // add a comma if this isn’t the last argument
        if (i + 1 < f.arguments.length) {
            thisRecord += ", ";
        }
    }

    return thisRecord + ")\n" + constructStackTrace(f.caller);
}

// Retrieve a stack trace. Works in Mozilla and IE.
function getStackTrace() {
    var err = new Error;
    // if stack property exists, use it; else construct it manually
    if (err.stack) {
        return err.stack;
    } else {
        // alert(getStackTrace.caller);
        return constructStackTrace(getStackTrace.caller);
    }
}
{% endhighlight %}

测试如下：

{% highlight javascript %}
function a(x) {
    console.log(x);
    console.log("\n----Stack trace below----\n");
    console.log(getStackTrace());
    // console.log((new Error).stack);
}

function b(x) {
    a(x + 1);
}

function c(x) {
    b(x + 1);
}

c(10);
{% endhighlight %}
