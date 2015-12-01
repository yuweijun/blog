---
layout: post
title: "javascript中json对象及其方法说明"
date: "Tue, 01 Dec 2015 15:25:59 +0800"
categories: javascript
---

JSON对象示例
-----

{% highlight javascript %}
var json = {"bindings": [
        {"ircEvent": "PRIVMSG", "method": "newURI", "regex": "^http://.*"},
        {"ircEvent": "PRIVMSG", "method": "deleteURI", "regex": "^delete.*"},
        {"ircEvent": "PRIVMSG", "method": "randomURI", "regex": "^random.*"}
    ]
};
{% endhighlight %}

JSON文本转对象
-----

ie6/ie7中不支持`JSON.parse()`方法，所以使用以下方式将JSON文本转化为对象。

{% highlight javascript %}
var obj = eval('(' + myJSONtext + ')');
{% endhighlight %}

JSON.stringify Function
-----

`JSON.stringify()`方法可以将任意的javascript值序列化成JSON字符串。语法如下：

{% highlight javascript %}
JSON.stringify(value [, replacer] [, space])
{% endhighlight %}

# 参数说明

1. value: 将要序列化成 JSON 字符串的值，通常是对象或者数组。
2. replacer: 可选
    1. 如果该参数是一个函数，则在序列化过程中，对象属性的键和值会作为参数传入此函数，最后返回经过该函数的转换和处理后的值，替代原先传入的值。
    2. 如果返回`undefined`，则对应的属性键就会被忽略。
    3. 如果是根节点，返回空字符串`""`。
    4. 如果该参数是一个数组，则只有包含在这个数组中的属性名才会被序列化到最终的JSON字符串中，并且按此数组中的属性名顺序进行转化。
3. space: 可选，指定缩进用的空白字符串，用于美化输出（pretty-print）。
    1. space为空，则返回字符串不会包括多余的空白字符。
    2. space是个数字，则返回结果中每层的属性名前会有对应的空格数量，超过10个空格按10个计算。
    3. space是`\t`，则返回结果中每层会按`tab`进行缩进排版。
    4. space是其他字符，则返回结果中每层的属性名前会显示这些字符，只保留10个字符，多余的被忽略。
    5. 以下字符必须转义，`"`，`\`，`\b`，`\f`，`\n`，`\r`，`\t`，`\uhhhh`。

# toJSON 方法

如果一个被序列化的对象拥有`toJSON`方法，那么该`toJSON`方法就会覆盖该对象默认的序列化行为：不是那个对象被序列化，而是调用`toJSON`方法后的返回值会被序列化，例如：

{% highlight javascript %}
var obj = {
    foo: 'foo',
    toJSON: function () {
        return 'bar';
    }
};
JSON.stringify(obj);      // '"bar"'
JSON.stringify({x: obj}); // '{"x":"bar"}'
{% endhighlight %}

下面的例子使用制表符`\t`将对象格式化输出json字符串。

{% highlight javascript %}
var contact = new Object();
contact.firstname = "Jesper";
contact.surname = "Aaberg";
contact.phone = ["555-0100", "555-0120"];

var memberfilter = new Array();
memberfilter[0] = "surname";
memberfilter[1] = "phone";
var jsonText = JSON.stringify(contact, memberfilter, "\t");
console.log(jsonText);

// Output:
// { "surname": "Aaberg", "phone": [ "555-0100", "555-0120" ] }
{% endhighlight %}

下面这个例子将对象的属性值全部转成大写形式的。

{% highlight javascript %}
var continents = new Array();
continents[0] = "Europe";
continents[1] = "Asia";
continents[2] = "Australia";
continents[3] = "Antarctica";
continents[4] = "North America";
continents[5] = "South America";
continents[6] = "Africa";

var jsonText = JSON.stringify(continents, replaceToUpper);

function replaceToUpper(key, value) {
    return value.toString().toUpperCase();
}

// Output:
// "EUROPE,ASIA,AUSTRALIA,ANTARCTICA,NORTH AMERICA,SOUTH AMERICA,AFRICA"
{% endhighlight %}

以下例子使用`toJSON`方法实现属性值转大写。

{% highlight javascript %}
var contact = new Object();
contact.firstname = "Jesper";
contact.surname = "Aaberg";
contact.phone = ["555-0100", "555-0120"];

contact.toJSON = function(key) {
    var replacement = new Object();
    for (var val in this) {
        if (typeof (this[val]) === 'string') {
            replacement[val] = this[val].toUpperCase();
        } else {
            replacement[val] = this[val];
        }
    }
    return replacement;
};

var jsonText = JSON.stringify(contact);
console.log(jsonText);

// Output:
// {"firstname":"JESPER","surname":"AABERG","phone":["555-0100","555-0120"]}
{% endhighlight %}

JSON.parse Function
-----

JSON.parse() 方法可以将一个 JSON 字符串解析成为一个 javascript 值。在解析过程中，还可以选择性的篡改某些属性的原始解析值。语法如下：

{% highlight javascript %}
JSON.parse(text [, reviver])
{% endhighlight %}

# 参数说明

1. text: 要解析的JSON字符串。
2. reviver: 可选，一个函数，用来转换解析出的属性值。
    1. reviver返回合法值，使用此值替换原来的属性值。
    2. reviver返回`null`或者`undefined`，对应属性名被删除。

# 返回值说明

从text字符串解析出的一个javascript对象或者数组。

# 异常

如果被解析的JSON字符串包含语法错误，则会抛出`SyntaxError`异常。`JSON.parse()`不允许逗号结尾，如下例：

{% highlight javascript %}
JSON.parse('[1, 2, 3,]');
// (program):1 Uncaught SyntaxError: Unexpected token ](…)
{% endhighlight %}

示例一，字符串转对象
-----

{% highlight javascript %}
var jsontext = '{"firstname":"Jesper","surname":"Aaberg","phone":["555-0100","555-0120"]}';
var contact = JSON.parse(jsontext);
console.log(contact.surname + ", " + contact.firstname);

var arr = ["a", "b", "c"];
var str = JSON.stringify(arr);
console.log(str);

var newArr = JSON.parse(str);

while (newArr.length > 0) {
    console.log(newArr.pop() + "<br/>");
}
{% endhighlight %}

示例二，字符串转成对象
-----

{% highlight javascript %}
myData = JSON.parse(text, function (key, value) {
    var type;
    if (value && typeof value === 'object') {
        type = value.type;
        if (typeof type === 'string' && typeof window[type] === 'function') {
            // 可以通过这种方法，将日期字符串转化成js中的Date对象
            return new (window[type])(value);
        }
    }
    return value;
});
{% endhighlight %}

示例三，ISO日期格式字符串转化成UTC日期对象
-----

{% highlight javascript %}
var jsontext = '{ "hiredate": "2008-01-01T12:00:00Z", "birthdate": "2008-12-25T12:00:00Z" }';
var dates = JSON.parse(jsontext, dateReviver);
console.log(dates.birthdate.toUTCString());

function dateReviver(key, value) {
    var a;
    if (typeof value === 'string') {
        a = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
        if (a) {
            return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4], +a[5], +a[6]));
        }
    }
    return value;
};

// Output:
// Thu, 25 Dec 2008 12:00:00 UTC
{% endhighlight %}

示例四，使用reviver函数
-----

如果指定了`reviver`函数，则解析出的javascript值（解析值）会经过一次转换后才将被最终返回（返回值）。

更具体点讲就是：解析值本身以及它所包含的所有属性，会按照一定的顺序（从最最里层的属性开始，一级级往外，最终到达顶层，也就是解析值本身）分别的去调用`reviver`函数，在调用过程中，当前属性所属的对象会作为`this`值，当前属性名和属性值会分别作为第一个和第二个参数传入`reviver`中。如果`reviver`返回`undefined`，则当前属性会从所属对象中删除，如果返回了其他值，则返回的值会成为当前属性新的属性值。

{% highlight javascript %}
JSON.parse('{"p": 5}', function (k, v) {
    if(k === '') return v;     // 如果到了最顶层，则直接返回属性值，
    return v * 2;              // 否则将属性值变为原来的 2 倍。
});                            // { p: 10 }

JSON.parse('{"1": 1, "2": 2,"3": {"4": 4, "5": {"6": 6}}}', function (k, v) {
    console.log(k); // 输出当前的属性名，从而得知遍历顺序是从内向外的，
                    // 最后一个属性名会是个空字符串。
    return v;       // 返回原始属性值，相当于没有传递 reviver 参数。
});
{% endhighlight %}

References
-----

1. [JSON in javascript](http://www.json.org/js.html)
2. [JSON.stringify Function](https://msdn.microsoft.com/library/cc836459.aspx)
3. [JSON.parse Function](https://msdn.microsoft.com/en-us/library/cc836466.aspx)
4. [JSON.stringify](https://developer.mozilla.org/zh-CN/docs/Web/JavaScript/Reference/Global_Objects/JSON/stringify)
5. [JSON.parse](https://developer.mozilla.org/zh-CN/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse)

