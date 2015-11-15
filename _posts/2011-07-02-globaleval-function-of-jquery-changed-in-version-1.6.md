---
layout: post
title: "globaleval function of jquery changed in version 1.6"
date: "Sat Jul 02 2011 14:53:00 GMT+0800 (CST)"
categories: jquery
---

`globalEval` function of jquery before version 1.6, such as 1.5.2 and 1.2.6, source code:

{% highlight javascript %}
// Evalulates a script in a global context
globalEval: function(data) {
    if (data && rnotwhite.test(data)) {
        // Inspired by code by Andrea Giammarchi
        // http://webreflection.blogspot.com/2007/08/global-scope-evaluation-and-dom.html
        var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement,
            script = document.createElement("script");

        if (jQuery.support.scriptEval()) {
            script.appendChild(document.createTextNode(data));
        } else {
            script.text = data;
        }

        // Use insertBefore instead of appendChild to circumvent an IE6 bug.
        // This arises when a base node is used (#2709).
        head.insertBefore(script, head.firstChild);
        head.removeChild(script);
    }
},
{% endhighlight %}

`globalEval` function of jquery-1.6 source code:

{% highlight javascript %}
// Evaluates a script in a global context
// Workarounds based on findings by Jim Driscoll
// http://weblogs.java.net/blog/driscoll/archive/2009/09/08/eval-javascript-global-context
globalEval: function(data) {
    if (data && rnotwhite.test(data)) {
        // We use execScript on Internet Explorer
        // We use an anonymous function so that context is window
        // rather than jQuery in Firefox
        (window.execScript || function(data) {
            window["eval"].call(window, data);
        })(data);
    }
},
{% endhighlight %}
