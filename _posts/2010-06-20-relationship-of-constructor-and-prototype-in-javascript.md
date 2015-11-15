---
layout: post
title: "relationship of constructor and prototype in javascript"
date: "Sun Jun 20 2010 21:17:00 GMT+0800 (CST)"
categories: javascript
---

javascript构造方法与原型关系。

{% highlight bash %}
var animal = function(status) {
    this.status = status;
    this.breathes = "yes";
    this.action = function() {
        console.log('flying...')
    };
},
human = function() {
    this.name = 'human';
},
cat = function() {
    this.type = 'cat';
};

// javascript支持原型继承，这种方式比类继承更强大，类继承中一个对象可以继承结构和行为，而原型继承可以继承结构和行为之外，并可以继承一个对象的状态
// new一个animal的实例对象作为cat.prototype的原型，这个animal实例对象就成为cat的实例对象原型链上的一员
// __proto__这个魔法属性在这些浏览器不能工作： ie 6/7/8, safari < 5, opera < 10.50
// 当在cat的某个实例上检索一个属性时，如果在其本身中没有找到，则会延着原型链向上检索，如下例子中的c.__proto__即为一个animal对象
// 如果检索c.breathes，如果在c对象本身没有找到此属性，则会检索t.__proto__.breathes、t.__proto__.__proto__.breathes等原型链上的对象，直到找到为止，没找到返回undefined
cat.prototype = new animal("live");
// cat继承的原型对象是具有特定状态的animal对象
var c = new cat();
console.log(cat.prototype);
console.log(cat.prototype.constructor.tostring());
console.log(c.constructor.tostring());
console.log("cat breathes:" + c.breathes);
console.log("c.__proto__:", c.__proto__);

// ie不支持此属性
// 你可以利用object.__proto__这个魔法属性修改当前对象的原型，下面将一只猫猫化为人形
var d = new cat();
d.__proto__ = new human();
console.log("d.__proto__:", d.__proto__);

// 从上面结果可以看到cat的实例c.constructor不是指向cat这个构造函数，而是animal构造函数
// 需要修改对象的constructor为其构造函数本身
// 当一个函数对象被创建时，function构造器产生的函数对象会运行类似这样的一些代码：this.prototype = {constructor: this}，参考javascript: the good parts 5.1节说明
// 新函数对象被赋予一个prototype属性，其值是包含一个constuctor属性，并且其属性值为此新函数对象本身
// 但是通过原型方式继承时，会给prototype重新赋予一个新对象，此prototype对象中的constructor是指向其自身的构造函数，而不是新函数的，所以需要重置其fn.prototype.constructor = this
// 参考javascript权威指南第五版example 9-3. subclassing a javascript class

cat.prototype.constructor = cat;
console.log(cat.prototype.constructor.tostring());
console.log(c.constructor.tostring());
console.log(c.__proto__);
var tostring = object.prototype.tostring;
language = function() {
    this.type = "programming";
    return {
        "locale": "en",
        "class-free": function() {
            return false
        },
        "tostring": function() {
            return tostring.apply(this, arguments)
        }
        // 如果tostring方法被重写成非function对象，则后面console中无法输出对象j
    }
},
javascript = function() {
    this.value = "javascript";
    this["class-free"] = function() {
        return true
    };
};
language.prototype = {
    a: 1,
    b: 2
};
javascript.prototype = new language();
var j = new javascript();
console.log(j);
console.log(j.__proto__);
// locale: en，此处因为language构造函数返回不是this，而是另一个object直接量，而object直接的构造方法为object()，因此language的原型被丢失了
console.log(language.prototype);
console.log(javascript.prototype);
console.log(j.constructor.tostring());
// function object() { [native code] }
{% endhighlight %}

构造函数与其返回值
-----

构造函数会返回一个对象，如果没有直接`return`语句，构造函数会自动返回当前对象：`return this;`，也可以返回一个对象直接量，而不返回`this`，这样会中断正常的原型链。

`prototype.js`中class对象定义是封装在一个匿名函数里的，从而使得其内部变量和方法与外界隔离，其中有二句代码为：

{% highlight javascript %}
function subclass() {};
subclass.prototype = parent.prototype;
{% endhighlight %}

因为`parent`的构造可能返回语句不是返回`this`对象，而是返回了一个其他的对象，如`{tostring: true}`，如果不用`subclass.prototype = parent.prototype`这样写，可能这样会丢失原型链上的方法和属性，通过`subclass`这个空构造将`parent.prototype`引用到自身的`prototype`上，从而保持住部分原型链。这其实也已经不是原型继承了，因为它不是通过`new parent()`来获取原型对象，丢失了`new parent`所得对象中的属性和方法。

`prototype`中的`class`其实放弃了原型对象，只是简单的继承了`parent.prototype`对象，已经失去原型继承可以继承对象状态的功能，这样操作其实是很好的模似了类继承方式。

{% highlight javascript %}
var class = (function() {
    function subclass() {};

    function create() {
        var parent = null,
            properties = $a(arguments);
        if (object.isfunction(properties[0])) parent = properties.shift();

        function klass() {
            this.initialize.apply(this, arguments);
        }

        object.extend(klass, class.methods);
        klass.superclass = parent;
        klass.subclasses = [];

        if (parent) {
            // 因为parent的构造可能返回对象直接量，而不是返回this，如{tostring:true}
            subclass.prototype = parent.prototype;
            klass.prototype = new subclass;
            parent.subclasses.push(klass);
        }

        for (var i = 0; i < properties.length; i++) klass.addmethods(properties[i]);
        if (!klass.prototype.initialize) klass.prototype.initialize = prototype.emptyfunction;
        klass.prototype.constructor = klass;
        return klass;
    }

    function addmethods(source) {
        var ancestor = this.superclass && this.superclass.prototype;
        var properties = object.keys(source);
        if (!object.keys({
            tostring: true
        }).length) {
            if (source.tostring != object.prototype.tostring) properties.push("tostring");
            if (source.valueof != object.prototype.valueof) properties.push("valueof");
        }
        for (var i = 0, length = properties.length; i < length; i++) {
            var property = properties[i],
                value = source[property];
            if (ancestor && object.isfunction(value) && value.argumentnames().first() == "$super") {
                var method = value;
                value = (function(m) {
                    return function() {
                        return ancestor[m].apply(this, arguments);
                    };
                })(property).wrap(method);
                value.valueof = method.valueof.bind(method);
                value.tostring = method.tostring.bind(method);
            }
            this.prototype[property] = value;
        }
        return this;
    }
    return {
        create: create,
        methods: {
            addmethods: addmethods
        }
    };
})();
{% endhighlight %}
