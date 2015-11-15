---
layout: post
title: "usage of call_user_func_array function in php"
date: "Mon Dec 08 2008 11:03:00 GMT+0800 (CST)"
categories: php
---

php function call_user_func_array
-----

{% highlight php %}
<?php
class Foo {
    static public function test($name) {
        print "Hello {$name}!\n";
    }

    public function bar($name) {
        print "Hello {$name}\n";
    }
}

// call static Class methods.
call_user_func_array('Foo::test', array('Hannes'));
// Hello Hannes!
call_user_func_array(array('Foo', 'test'), array('Philip'));
// Hello Philip!

$foo = new Foo();
// call object instance methods.
call_user_func_array(array($foo, 'bar'), array('World!'));
call_user_func_array(array($foo, 'bar'), 'World!');
// Hello World!
?>
{% endhighlight %}

参考php手册
-----

callback有些诸如`call_user_function()`或`usort()`的函数接受用户自定义的函数作为一个参数。

callback函数不仅可以是一个简单的函数，它还可以是一个对象的方法，包括静态类的方法。

一个php函数用函数名字符串来传递。可以传递任何内置的或者用户自定义的函数，除了 `array()`， `echo()`， `empty()`， `eval()`， `exit()`， `isset()`， `list()`， `print()`和`unset()`。

一个对象的方法以数组的形式来传递，数组的下标`0`指明对象名，下标`1`指明方法名。对于没有实例化为对象的静态类，要传递其方法，将数组`0`下标指明的对象名换成该类的名称即可。

References
-----

1. [http: //cn.php.net/manual/en/function.call-user-func-array.php](http: //cn.php.net/manual/en/function.call-user-func-array.php)
