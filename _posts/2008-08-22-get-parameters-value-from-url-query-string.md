---
layout: post
title: "get parameters value from url query string "
date: "Fri Aug 22 2008 17:09:00 GMT+0800 (CST)"
categories: javascript
---

javascript
-----

{% highlight javascript %}
function getParams(param) {
    var search = window.location.search;
    if (search == "") return;
    else {
        var varsList = search.slice(1).split(/&(?:amp;)*/);
        for (var i = varsList.length - 1; i >= 0; i--) {
            var equalArray = varsList[i].split("=");
            if (equalArray[0] == param) return equalArray[1];
        }
    }
}
{% endhighlight %}
