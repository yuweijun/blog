---
layout: post
title: "php中self关键字说明"
date: "Mon Dec 22 2008 21:29:00 GMT+0800 (CST)"
categories: php
---

`self`一般指向当前类的静态方法和常量，用`self::`加方法名和常量名方式引用。

`$this`则是指向当前类的实例对象，用`$this->`加方法名和实例变量方式引用。

在一些参数为`callback`的方法里，可以用字符串`self`形式指向当前类，而不要直接用`self`，如`call_user_func('self', $method)`中。

另外`self`引用的总是当前类的方法和常量，子类调用父类的静态方法，其中的父类方法中的`self`仍是指向父类本身的，如果子类的同名方法覆盖了父类方法，则可以用`parent::`来引用父类方法。

{% highlight php %}
<?php
interface AppConstants {
    const FOOBAR = 'Hello, World.';
}

class Example implements AppConstants {
    public function test() {
        echo self::FOOBAR;
    }
}

$obj = new Example();
$obj - > test(); // outputs "Hello, world."

class MyClass {
    const NAME = 'Foo';

    protected function myFunc() {
        echo "MyClass::myFunc()\n";
    }

    static public function display() {
        echo self::NAME;
    }

    static public function getInstance() {
        $instance = new self;
        return $instance;
    }
}

class ChildClass extends MyClass {
    const NAME = 'Child';

    // Override parent's definition
    public function myFunc() {
        // But still call the parent function
        parent::myFunc();
        echo "ChildClass::myFunc()\n";
    }
}

$class = new ChildClass();
$class - > myFunc();

echo('Class constant: ');
ChildClass::display();
echo('Object class: ');
echo(get_class(ChildClass::getInstance()));
?>
{% endhighlight %}

output
-----

{% highlight text %}
Hello, World.
MyClass::myFunc()
ChildClass::myFunc()
Class constant: Foo
Object class: MyClass
{% endhighlight %}

另外可以再参考一个php-5.3.0的运行时绑定(延迟绑定)的用法，地址是：[http://cn2.php.net/oop5.late-static-bindings](http://cn2.php.net/oop5.late-static-bindings)
