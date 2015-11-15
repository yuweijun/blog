---
layout: post
title: "trap of in_array in php"
date: "Sat Jan 03 2009 11:21:00 GMT+0800 (CST)"
categories: php
---

trap of php in_array function
-----

{% highlight php %}
$array = array('testing',0,'name');
var_dump($array);

// NOTICE: this will return true
var_dump(in_array('foo', $array));

// NOTICE: this will return false
var_dump(in_array('foo', $array, TRUE));
{% endhighlight %}

References
-----

1. [http://cn.php.net/manual/en/function.in-array.php](http://cn.php.net/manual/en/function.in-array.php)
