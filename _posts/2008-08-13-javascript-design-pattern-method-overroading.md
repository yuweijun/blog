---
layout: post
title: "javascript method overloading"
date: "Wed Aug 13 2008 23:04:00 GMT+0800 (CST)"
categories: javascript
---

javascript中的方法重载实现：

{% highlight javascript %}
// addMethod - By John Resig (MIT Licensed)

function addMethod(object, name, fn) {
    var old = object[name];
    if (old) {
        object[name] = function() {
            // fn.length: 是指方法fn的参数个数
            if (fn.length == arguments.length) {
                return fn.apply(this, arguments);
            } else if (typeof old == 'function') {
                return old.apply(this, arguments);
            }
        };
    } else {
        object[name] = fn;
    }
}

function Users() {}
addMethod(Users.prototype, "find", function() {
    // Find all users...
});
addMethod(Users.prototype, "find", function(name) {
    // Find a user by name
});
addMethod(Users.prototype, "find", function(first, last) {
    // Find a user by first and last name
});

var users = new Users();
users.find(); // Finds all
users.find("John"); // Finds users by name
users.find("John", "Resig"); // Finds users by first and last name
users.find("John", "E", "Resig"); // Finds all
{% endhighlight %}

References
-----

1. [John Resig](http://ejohn.org/blog/javascript-method-overloading/)
