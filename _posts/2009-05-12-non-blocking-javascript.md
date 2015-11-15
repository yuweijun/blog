---
layout: post
title: "non-blocking javascript"
date: "Tue May 12 2009 16:51:00 GMT+0800 (CST)"
categories: javascript
---

Include via DOM
-----

{% highlight javascript %}
var js = document.createElement('script');
js.src = 'myscript.js';

var h = document.getElementsByTagName('head')[0];
h.appendChild(js);
{% endhighlight %}

Non-blocking JavaScript
-----

1. And what about my inline scripts?
2. Setup a collection (registry) of inline scripts

Step 1: inline it in the head
-----

{% highlight javascript %}
var myapp = {
    stuff: []
};
{% endhighlight %}

Step 2: Add to the registry
-----

{% highlight javascript %}
// Instead of:
alert('boo!');

// Do:
myapp.stuff.push(function(){
    alert('boo!');
);
{% endhighlight %}

Step 3: Execute all
-----

{% highlight javascript %}
var l = myapp.stuff.length;
var l = myapp.stuff.length;
for(var i = 0, i < l; i++) {
    myapp.stuff[i]();
}
{% endhighlight %}
