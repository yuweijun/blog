---
layout: post
title: "arguments.callee写递归匿名函数"
date: "Mon Jan 21 2008 23:39:00 GMT+0800 (CST)"
categories: javascript
---


Early versions of javascript did not allow named function expressions, and because of that we could not make a recursive function expression:

{% highlight javascript %}
// This snippet will work:
function factorial(n) {
    return (!(n > 1)) ? 1 : factorial(n - 1) * n;
}
[1, 2, 3, 4, 5].map(factorial);

// But this snippet will not:
[1, 2, 3, 4, 5].map(function(n) {
    return (!(n > 1)) ? 1 : /* what goes here? */ (n - 1) * n;
});
{% endhighlight %}

To get around this, arguments.callee was added so we could do :

{% highlight javascript %}
[1, 2, 3, 4, 5].
map(function(n) {
    return (!(n > 1)) ? 1 : arguments.callee(n - 1) * n;
});
{% endhighlight %}

{% highlight text %}
Warning: The 5th edition of ECMAScript (ES5) forbids use of arguments.callee() in strict mode. Avoid using arguments.callee() by either giving function expressions a name or use a function declaration where a function must call itself.
{% endhighlight %}

References
-----

1. [Why was the arguments.callee.caller property deprecated in Javascript?](http://stackoverflow.com/questions/103598/why-was-the-arguments-callee-caller-property-deprecated-in-javascript)
2. [arguments.callee of MDN](https://developer.mozilla.org/en-US/docs/Web/javascript/reference/functions/arguments/callee)
