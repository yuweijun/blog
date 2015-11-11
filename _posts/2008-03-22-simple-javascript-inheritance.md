---
layout: post
title: "simple javascript inheritance"
date: "Sat Mar 22 2008 14:20:00 GMT+0800 (CST)"
categories: javascript
---

John Resig published a great article on his blog, which brought out a smiple javascript inheritance resolution:

{% highlight javascript %}
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // The base Class implementation (does nothing)
  this.Class = function(){};

  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    // "super" is a FutureReservedWord in ECMAScript v3.
    var _super = this.prototype;

    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    // SubClass may define init() function, "new this()" should prevent init() function to be excuted.
    initializing = true;
    var prototype = new this();
    initializing = false;

    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          // return a cloures, which return results of function prop[name](arguments)
          return function() {
            //save a reference to the old this._super (disregarding if it actually exists)
            //and restore it after we're done.
            var tmp = this._super;

            // Add a new ._super() method that is the same method
            // but on the super-class
            // fn function body has statement such as "this._super()"
            // this._super will be invoked by function fn
            this._super = _super[name];

            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);
            this._super = tmp;

            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }

    // The dummy SubClass constructor
    function SubClass() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }

    // Populate our constructed prototype object
    SubClass.prototype = prototype;

    // Enforce the constructor to be what we expect
    // else SubClass.constructor is Function
    SubClass.constructor = SubClass;

    // And make this SubClass extendable
    SubClass.extend = arguments.callee;

    return SubClass;
  };
})();
{% endhighlight %}

Example:
-----

{% highlight javascript %}
var A = Class.extend({
  value:"A",
  doStuff:function(){
    return this.value;
  }
});
var B = A.extend({});
var C = B.extend({
  doStuff:function(){
    return this._super();
  }
});
var c = new C;
console.log(c.doStuff());

var Person = Class.extend({
  init: function(isDancing){
    this.dancing = isDancing;
  },
  dance: function(){
    return this.dancing;
  }
});
console.log(Person.constructor);

var Ninja = Person.extend({
  init: function(){
    this._super( false );
  },
  dance: function(){
    // Call the inherited version of dance()
    return this._super();
  },
  swingSword: function(){
    return true;
  }
});
console.log(Ninja.constructor);

var p = new Person(true);
console.log(p.dance()); // => true

var n = new Ninja();
console.log(n.dance()); // => false
console.log(n.swingSword()); // => true

// Should all be true
console.log(p instanceof Person && p instanceof Class && n instanceof Ninja && n instanceof Person && n instanceof Class);
{% endhighlight %}
