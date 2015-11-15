---
layout: post
title: "string compare int value trap in php"
date: "Sun Mar 08 2009 22:49:00 GMT+0800 (CST)"
categories: php
---

If string compare with an integer, php will evaluate string to integer `intval()` and compare it with that integer.

php
-----

{% highlight php %}
if('test' != 0) {
    echo "true";
} else {
    echo "false"; // return false
}
if('0test' != 0) {
    echo "true";
} else {
    echo "false"; // return false
}
if('1test' != 0) {
    echo "true"; // return true
} else {
    echo "false";
}
{% endhighlight %}
