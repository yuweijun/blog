---
layout: post
title: "load a track link when window.onunload"
date: "Wed Apr 02 2008 23:24:00 GMT+0800 (CST)"
categories: javascript
---

When close this window or leave this page will get request below, this trick may be cause bad user experience, and Opera will ignore it:

{% highlight tex %}
192.168.0.1 - - [02/Apr/2008:23:10:18 +0800] "GET /track.html?1207149018624 HTTP/1.1" 301 253
{% endhighlight %}

{% highlight javascript %}
window.onunload = function(){
    var img = new Image(1,1), i = 0;
    img.src = "http://www.test.com/track.html?" + (new Date()).getTime();
    while(i < 1000000) i++ ; // for img src load
};
{% endhighlight %}

`window.onbeforeunload` will pop out a confirm dialog, so can't using `onbeforeunload`.
