---
layout: post
date: "Thu Jul 31 2014 12:15:30 GMT+0800 (CST)"
title: jQuery debug plugin
categories: jquery
---

jQuery debug plugin source:

{% highlight javascript %}
// jquery.debug.js plugin
(function($) {

    if (window.console) {
        // can't using Function.apply() for safari/chrome console.
        // extend $.console using window.console, don't work in safari/chrome using $.extend()
        $.console = window.console;
    } else {
        // define $.console object for browsers without window.console.
        $.console = {};
        $.each(['assert', 'clear', 'info', 'log', 'warn', 'error'], function(index, name) {
            $.console[name] = $.noop;
        });
    }

    $.fn.debug = function(msg) {
        return this.each(function() {
            if ($.isFunction(msg)) {
                msg.apply(this);
            } else if (msg) {
                $.console.log(msg);
            } else {
                $.console.log(this);
            }
        });
    };

})(jQuery);

{% endhighlight %}

