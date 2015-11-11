---
layout: post
title: "replacehtml instead innerhtml"
date: "Tue Apr 08 2008 12:27:00 GMT+0800 (CST)"
categories: javascript
---

This is much faster than using (`el.innerHTML = value`) when there are many existing descendants, because in some browsers, `innerHTML` spends much longer removing existing elements than it does creating new ones.

{% highlight javascript %}
function replaceHtml(el, html) {
    var oldEl = (typeof el === "string" ? document.getElementById(el) : el);
    /*@cc_on // Pure innerHTML is slightly faster in IE
oldEl.innerHTML = html;
return oldEl;
@*/
    var newEl = oldEl.cloneNode(false);
    newEl.innerHTML = html;
    oldEl.parentNode.replaceChild(newEl, oldEl);
    /* Since we just removed the old element from the DOM, return a reference
to the new element, which can be used to restore variable references. */
    return newEl;
};
{% endhighlight %}

References
-----

1. [When innerHTML isn’t Fast Enough](http://blog.stevenlevithan.com/archives/faster-than-innerhtml)
