---
layout: post
title: "php function get_class() in superclass"
date: "Sun Mar 08 2009 11:19:00 GMT+0800 (CST)"
categories: php
---

php function get_class()在类继承使用时需要特别注意。

{% highlight php %}
<?php
abstract class bar {
    public function __construct() {
        var_dump(get_class($this));
        var_dump(get_class());
    }
}

class foo extends bar {
}

new foo;
?>
{% endhighlight %}

The above example will output:

{% highlight text %}
string(3) "foo"
string(3) "bar"
{% endhighlight %}

References
-----

1. [http://cn.php.net/manual/en/function.get-class.php](http://cn.php.net/manual/en/function.get-class.php)
