---
layout: post
title: "javascript变量覆盖及作用域"
date: "Wed Aug 13 2008 23:11:00 GMT+0800 (CST)"
categories: javascript
---

注意代码中的变量名和function名的关系，书写一定要分清楚各个变量的词法作用域。

第一种情况，虽然名字相同，但词法作用域不同，不相关系。
-----

{% highlight javascript %}
function Tweak() { // 这里的Tweak是window的一个全局方法
    var Tweak = function() { // 这里的Tweak是一个局部变量名，与window.Tweak不相关
        this.init(arguments[0]);
    };

    Tweak.prototype = { // 局部变量名
        args: arguments,
        init: function() {
            console.log(arguments[0]); //firefox [1, 2] //safari: [object Arguments]
            return this;
        }
    }
    return new Tweak(arguments);
}

var t = Tweak(1, 2);
console.log(t.args); // [1, 2] // [object Arguments]
{% endhighlight %}

第二种是变量名覆写了方法名
-----

{% highlight javascript %}
var Tweak = Tweak(1, 2); // javascript中的function会先被解析器解析，这里Tweak(1, 2)已经在调用下面的Tweak方法，但此方法调用完成后马上被变量Tweak覆写
function Tweak() {
    var Tweak = function() { // 这里的Tweak是一个局部变量名，与window.Tweak不相关
        this.init(arguments[0]);
    };

    Tweak.prototype = { // 局部变量名
        args: arguments,
        init: function() {
            console.log(arguments[0]); //firefox [1, 2] //safari: [object Arguments]
            return this;
        }
    }
    return new Tweak(arguments);
}

// Tweak(1, 2); // 这句代码已经执行错误，因为Tweak已经是一个对象，而不是方法了。
{% endhighlight %}

第三种是方法名覆写了变量名
----

{% highlight javascript %}
var Tweak = {};
Tweak = function() { // Tweak 变量被重新赋为一个function对象
    var Tweak = function() { // 这里的Tweak是一个局部变量名，与window.Tweak不相关
        this.init(arguments[0]);
    };

    Tweak.prototype = { // 局部变量名
        args: arguments,
        init: function() {
            console.log(arguments[0]); //firefox [1, 2] //safari: [object Arguments]
            return this;
        }
    }
    return new Tweak(arguments);
}

Tweak(1, 2); // 这句代码可以执行
{% endhighlight %}
