---
layout: post
title: "keywords of this in javascript methods"
date: "Mon Jan 21 2008 23:49:00 GMT+0800 (CST)"
categories: javascript
---

javascript的闭包函数(如将函数做为另一个函数/方法的参数或者是返回结果为函数)中的this会因为闭包函数使用环境不同而变化。

prototype.js里加了一个bind方法可以把this重新绑回到原对象上(如fx方法所示)，但如果返回的是闭包函数，则其中的this会指向新的object(如rf2方法所示指向了window)。

{% highlight javascript %}
var o = {};
var obj = {
    name: 'A nice demo in obj',
    fx: function() {
        return this.name + " " + $A(arguments).join(', ');
    },
    rf: function() {
        return function() {
            return this.name + " " + $A(arguments).join(', ');
        }
    }
};
console.log(obj.fx);

window.name = 'I am such a beautiful window!';
function runfx(f) {
    return f();
}
var fx2 = obj.fx.bind(obj, 'otherarg1', 'otherarg2');
var rf2 = obj.rf.bind(obj, 'otherarg1', 'otherarg2');
console.log(fx2);
console.log(runfx(obj.fx));
console.log(runfx(fx2));
console.log(obj.rf()());
console.log(rf2()());
{% endhighlight %}
