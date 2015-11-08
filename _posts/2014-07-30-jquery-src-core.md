---
layout: post
title: "jQuery-1.4.2 core部分源码分析"
date: "Wed Jul 30 2014 21:40:26 GMT+0800 (CST)"
categories: jquery
---

jQuery()方法，这其实是jQuery构造方法的封装，在new关键词漏写时也是正确的返回jquery对象的，看后面关于jQuery.fn.init构造方法说明：

{% highlight javascript %}
// Define a local copy of jQuery
var jQuery = function( selector, context ) {
        // jQuery.fn.init才是jQuery真正的构造函数，因为后面jQuery.fn.init.prototype = jQuery.fn，所以以后添加到jQuery.fn上的新方法也同样被添加入jQuery.fn.init.prototype上，这里jQuery.fn.init.prototype和jQuery.fn.init这二个对象彼此循环引用了，这个对象循环引用在javascript中很常见，这个不同于方法的循环调用，循环调用就死循环了
        // javascript中的构造方法可以学习jQuery这个模式来生成object，避免使用new关键字，参考《javascript: The Good Pards》第B.11. new章说明，如果不小心没有用new操作符，也可以正常运行构造函数的，不会报错，但function内的this会指向全局对象，这是javascript语言设计的一个大失误
        // The jQuery object is actually just the init constructor 'enhanced'
        return new jQuery.fn.init( selector, context );
    },

    // Map over jQuery in case of overwrite
    _jQuery = window.jQuery,

    // Map over the $ in case of overwrite
    _$ = window.$,

    // Use the correct document accordingly with window argument (sandbox)
    document = window.document,

    // A central reference to the root jQuery(document)
    rootjQuery,

    // A simple way to check for HTML strings or ID strings
    // (both of which we optimize for)
    quickExpr = /^[^<]*(<[\w\W]+>)[^>]*$|^#([\w-]+)$/,

    // Is it a simple selector
    isSimple = /^.[^:#\[\.,]*$/,

    // Check if a string has a non-whitespace character in it
    rnotwhite = /\S/,

    // Used for trimming whitespace
    rtrim = /^(\s|\u00A0)+|(\s|\u00A0)+$/g,

    // Match a standalone tag
    rsingleTag = /^<(\w+)\s*\/?>(?:<\/\1>)?$/,

    // Keep a UserAgent string for use with jQuery.browser
    userAgent = navigator.userAgent,

    // For matching the engine and version of the browser
    browserMatch,

    // Has the ready events already been bound?
    readyBound = false,

    // The functions to execute on DOM ready
    readyList = [],

    // The ready event handler
    DOMContentLoaded,

    // Save a reference to some core methods
    toString = Object.prototype.toString,
    hasOwnProperty = Object.prototype.hasOwnProperty,
    push = Array.prototype.push,
    slice = Array.prototype.slice,
    indexOf = Array.prototype.indexOf;
{% endhighlight %}

_jQuery和_$二个临时变量保存了window环境中的二个全局同名变量，会在方法中还原临时变量回原来的二个变量，并将当前的jquery对象返回给noConflict()方法调用的结果
{% highlight javascript %}
jQuery.extend({
    noConflict: function( deep ) {
        window.$ = _$;

        if ( deep ) {
            window.jQuery = _jQuery;
        }

        return jQuery;
    }
});
{% endhighlight %}

rootjQuery此外只是声明变量，没有初始化，即为undefined，在源码定义完jQuery方法之后才赋值，在使用jQuery方法查找对象时，如果没有传入context，则默认context即为rootjQuery：

{% highlight javascript %}
// All jQuery objects should point back to these
rootjQuery = jQuery(document);
{% endhighlight %}

对于slice 方法，ECMAScript 262 中 15.4.4.10 Array.prototype.slice (start, end) 章节有备注：

> NOTE: The slice function is intentionally generic; it does not require that its this value be an Array object. Therefore it can be transferred to other kinds of objects for use as a method. Whether the slice function can be applied successfully to a host object is implementation-dependent.

几个正则表达式暂略说明，后面几个Class的实例方法的简写，主要是方便代码书写，其中Array.prototype.slice方法最为常用，将一个类数组的对象转化为真正的array对象，如function中的Arguments对象。

Array.prototype.push方法用得非常巧妙，利用push方法快速构造出一个类数组的object，可查看下面ArrayList的例子说明。

与slice方法一样，push/pop等数组方法也可以用在非数组对象上，15.4.4.7 Array.prototype.push ( [ item1 [ , item2 [ , … ] ] ] )上备注：

> NOTE: The push function is intentionally generic; it does not require that its this value be an Array object. Therefore it can be transferred to other kinds of objects for use as a method. Whether the push function can be applied successfully to a host object is implementation-dependent.

{% highlight javascript %}
var ArrayList = function() {
    // 这里利用Array push快速构造一个类数组的对象
    // Array push( value, ...) Appends the specified value or values to the end of the array, and returns the new length of the array.
    Array.prototype.push.apply(this, [1,2,3]);
    return this;
};
var a = new ArrayList;
$.console.dir(a); // {0:1, 1:2, 2:3, length:3}
{% endhighlight %}

{% highlight javascript %}
var ArrayList = function() {
    // 这里利用Array push快速构造一个类数组的对象
    // Array push( value, ...) Appends the specified value or values to the end of the array, and returns the new length of the array.
    Array.prototype.push.apply(this, [1,2,3]);
    return this;
};
var a = new ArrayList;
$.console.dir(a); // {0:1, 1:2, 2:3, length:3}
{% endhighlight %}

重点分析jQuery()方法中的new jQuery.fn.init( selector, context )：

{% highlight javascript %}
// Define a local copy of jQuery
var jQuery = function( selector, context ) {
        // The jQuery object is actually just the init constructor 'enhanced'
        return new jQuery.fn.init( selector, context );
    }
{% endhighlight %}

以下这段代码是在定义完jQuery.fn之后，再将其赋给jQuery.fn.init.prototype的，将所有的jQuery.prototype对象赋给了init的原型链，其中也包括了init方法本身

{% highlight javascript %}
// javascript中所有的对象都是通过引用reference操作的，后面通过jQuery.fn.extend()方法添加给jQuery.fn的方法同样也会影响init.prototype
// 在firebug中的jquery对象中，已经看不到init方法，其实是存在的，不知道是否firebug做过什么处理。
// 在chrome/safari的console中则可以看到init这个构造方法，并且其prototype链上还有init构造方法。
// Give the init function the jQuery prototype for later instantiation
jQuery.fn.init.prototype = jQuery.fn;
{% endhighlight %}

jQuery.fn即为jQuery.prototype，查看jQuery.fn.init源码:

{% highlight javascript %}
// 除了代码中所示，select可以是css选择器，DOM元素，HTML字符串，"body"，function之后，还可以是一个数组对象，如果是传入一个数组做为selector，最后通过jQuery.makeArray()方法，将数组转化为一个jquery对象，但此对象的jquery.selector为空字符串
init: function( selector, context ) {
    var match, elem, ret, doc;

    // Handle $(""), $(null), or $(undefined)
    if ( !selector ) {
        return this;
    }

    // 处理DOM对象，如果传进来的是DOM对象，而不是CSS选择器之类的字符串，则设置jquery对象的属性之后，返回jquery对象
    // Handle $(DOMElement)
    if ( selector.nodeType ) {
        this.context = this[0] = selector;
        this.length = 1;
        return this;
    }

    // The body element only exists once, optimize finding it
    if ( selector === "body" && !context ) {
        this.context = document;
        this[0] = document.body;
        this.selector = "body";
        this.length = 1;
        return this;
    }

    // 传进来是字符串，这是最常见的使用方法，通常是传进来CSS选择器和HTML代码片断
    // Handle HTML strings
    if ( typeof selector === "string" ) {
        // Are we dealing with HTML string or an ID?
        // quickExpr = /^[^<]*(<[\w\W]+>)[^>]*$|^#([\w-]+)$/
        // 如果是HTML代码则会出现配对的<>尖括号，匹配结果的match[1]就不会是undefined
        // 相反如果是个#id形式的字符串，则match[1]为undefined，match[2]为匹配的ID号
        // 如果传入的是element TAG NAME的话，则match为null，不会匹配上quickExpr
        // 这个正则主要是为了提高jquery查询对象的速度
        match = quickExpr.exec( selector );

        // Verify a match, and that no context was specified for #id
        if ( match && (match[1] || !context) ) {

            // HANDLE: $(html) -> $(array)
            if ( match[1] ) {
                doc = (context ? context.ownerDocument || context : document);

                // 如果是单标签如<div>或者是<div />这样，只是在context环境中创建此元素
                // If a single string is passed in and it's a single tag
                // just do a createElement and skip the rest
                ret = rsingleTag.exec( selector );

                if ( ret ) {
                    // jQuery('<a/>', { id: 'foo', href: 'http://www.google.com', title: 'a Googler', rel: 'external', text: 'Google!' });
                    if ( jQuery.isPlainObject( context ) ) {
                        selector = [ document.createElement( ret[1] ) ];
                        jQuery.fn.attr.call( selector, context, true );
                    } else {
                        selector = [ doc.createElement( ret[1] ) ];
                    }

                } else {
                    ret = buildFragment( [ match[1] ], [ doc ] );
                    selector = (ret.cacheable ? ret.fragment.cloneNode(true) : ret.fragment).childNodes;
                }

                return jQuery.merge( this, selector );

            // HANDLE: $("#id")
            } else {
                elem = document.getElementById( match[2] );

                if ( elem ) {
                    // 处理IE和Opera中的问题，document.getElementById实际上返回的却是按name查找返回的元素
                    // Handle the case where IE and Opera return items
                    // by name instead of ID
                    if ( elem.id !== match[2] ) {
                        return rootjQuery.find( selector );
                    }

                    // Otherwise, we inject the element directly into the jQuery object
                    this.length = 1;
                    this[0] = elem;
                }

                this.context = document;
                this.selector = selector;
                return this;
            }

        // HANDLE: $("TAG")
        } else if ( !context && /^\w+$/.test( selector ) ) {
            this.selector = selector;
            this.context = document;
            selector = document.getElementsByTagName( selector );
            return jQuery.merge( this, selector );

        // 如果context是jquery对象
        // HANDLE: $(expr, $(...))
        } else if ( !context || context.jquery ) {
            return (context || rootjQuery).find( selector );

        // 如果context有值并且不是jquery对象时，而是一个CSS选择器
        // HANDLE: $(expr, context)
        // (which is just equivalent to: $(context).find(expr)
        } else {
            return jQuery( context ).find( selector );
        }

    // 当传进来的selector是一个方法时，则此方法会在文档加载完成之后调用，更详细的查看jQuery.ready()方法
    // HANDLE: $(function)
    // Shortcut for document ready
    } else if ( jQuery.isFunction( selector ) ) {
        return rootjQuery.ready( selector );
    }

    if (selector.selector !== undefined) {
        this.selector = selector.selector;
        this.context = selector.context;
    }

    // jQuery.inArray和jQuery.makeArray是二个关于Array的jQuery静态工具方法
    return jQuery.makeArray( selector, this );
}
{% endhighlight %}

在jQuery1.4版本中，这个jQuery()核心方法多了以下这种用法：

> Pre 1.4, jQuery supported adding attributes to an element collection via the useful "attr" method, which can be passed both an attribute name and value, or an object specifying several attributes. jQuery 1.4 adds support for passing an attributes object as the second argument to the jQuery function itself, upon element creation.

{% highlight javascript %}
// examples:
jQuery('<a/>', { id: 'foo', href: 'http://google.com', title: 'Become a Googler', rel: 'external', text: 'Go to Google!' });

jQuery('<div/>', {
    id: 'foo',
    css: {
        fontWeight: 700,
        color: 'green'
    },
    click: function(){
        alert('Foo has been clicked!');
    }
});

// 第二个例子中的写法，在之前的jQuery版本中则需要用以下写法：
jQuery('<div/>')
.attr('id', 'foo')
.css({
    fontWeight: 700,
    color: 'green'
})
.click(function(){
    alert('Foo has been clicked!');
});

$("<div/>", {
  "class": "test",
  text: "Click me!",
  click: function(){
    $(this).toggleClass("test");
  }
}).appendTo("body");

$("<input>", {
  type: "text",
  val: "Test",
  focusin: function() {
    $(this).addClass("active");
  },
  focusout: function() {
    $(this).removeClass("active");
  }
}).appendTo("form");
{% endhighlight %}

这个方法的最后是调用jQuery.fn.setArray()方法返回一个类Array的jquery对象：

{% highlight javascript %}
// Force the current matched set of elements to become
// the specified array of elements (destroying the stack in the process)
// You should use pushStack() in order to do this, but maintain the stack
setArray: function( elems ) {
    // Resetting the length to 0, then using the native Array push
    // is a super-fast way to populate an object with array-like properties
    this.length = 0; // 清空当前jquery对象中的元素
    push.apply( this, elems ); // 将新的elems数组利用Array.prototype.push方法压入jquery对象中

    return this;
}
{% endhighlight %}

如果selector传进来是HTML代码片断时，会调用到DOM操作(manipulation.js)中的buildFragment()方法：

{% highlight javascript %}
function buildFragment( args, nodes, scripts ) {
    var fragment, cacheable, cacheresults,
        doc = (nodes && nodes[0] ? nodes[0].ownerDocument || nodes[0] : document);

    // Only cache "small" (1/2 KB) strings that are associated with the main document
    // Cloning options loses the selected state, so don't cache them
    // IE 6 doesn't like it when you put <object> or <embed> elements in a fragment
    // Also, WebKit does not clone 'checked' attributes on cloneNode, so don't cache
    if ( args.length === 1 && typeof args[0] === "string" && args[0].length < 512 && doc === document &&
        !rnocache.test( args[0] ) && (jQuery.support.checkClone || !rchecked.test( args[0] )) ) {

        cacheable = true;
        // 到jQuery.fragments对象中查找fragement的缓存，如果找到并且其值不为1，则返回缓存中的fragment
        cacheresults = jQuery.fragments[ args[0] ];
        if ( cacheresults ) {
            if ( cacheresults !== 1 ) {
                fragment = cacheresults;
            }
        }
    }

    if ( !fragment ) {
        fragment = doc.createDocumentFragment();
        // 缓存中没有找到fragment，在jQuery.clean()方法中生成此对象
        // clean这个方法是为了生成fragment，处理浏览器差异性的javascript DOM操作
        jQuery.clean( args, doc, fragment, scripts );
    }

    if ( cacheable ) {
        // 如果生成的fragment可被缓存，则将之缓存到jQuery.fragments对象中，以备后用
        jQuery.fragments[ args[0] ] = cacheresults ? fragment : 1;
    }

    return { fragment: fragment, cacheable: cacheable };
}
{% endhighlight %}

如果context传进来是一个CSS的selector时，jQuery.fn.init()方法最后调用的是:

{% highlight javascript %}
return jQuery( context ).find( selector );
{% endhighlight %}

而jQuery.fn.find方法如下，搜索所有与指定表达式匹配的元素，这个函数是找出正在处理的元素的后代元素的好方法，并且可回退到前一个jquery对象：

{% highlight javascript %}
find: function( selector ) {
    // jQuery.fn.pushStack()方法会另行说明，这个方法使得jquery对象得以链式操作，是非常巧妙却很简单的方法
    for ( var i = 0, l = this.length; i < l; i++ ) {
        length = ret.length;
        // jQuery.find = Sizzle;
        jQuery.find( selector, this[i], ret );

        if ( i > 0 ) {
            // 查询结果去重
            // Make sure that the results are unique
            for ( var n = length; n < ret.length; n++ ) {
                for ( var r = 0; r < length; r++ ) {
                    if ( ret[r] === ret[n] ) {
                        ret.splice(n--, 1);
                        break;
                    }
                }
            }
        }
    }

    return ret;
}
{% endhighlight %}

jQuery.fn中定义的其他一些属性和方法说明：

{% highlight javascript %}

// Start with an empty selector
selector: "",

// The current version of jQuery being used
jquery: "1.4.2",

// The default length of a jQuery object is 0
length: 0,

// The number of elements contained in the matched element set
size: function() {
    return this.length;
},

// 1.4新增加的方法:Retrieve all the DOM elements contained in the jQuery set, as an array.
toArray: function() {
    return slice.call( this, 0 );
},

// Get the Nth element in the matched element set OR
// Get the whole matched element set as a clean array
get: function( num ) {
    return num == null ?

        // 如果jquery.get()方法不传参数，则返回一个DOM对象的集合，真实的array对象
        // Return a 'clean' array
        this.toArray() :

        // Return just the object
        ( num < 0 ? this.slice(num)[ 0 ] : this[ num ] );
},

// 这个方法使得jquery对象可以链式进行操作，结合jQuery.fn.end方法回退到前一个jquery对象上
// Take an array of elements and push it onto the stack
// (returning the new matched element set)
pushStack: function( elems, name, selector ) {
    // Build a new jQuery matched element set
    var ret = jQuery();

    if ( jQuery.isArray( elems ) ) {
        push.apply( ret, elems );
    } else {
        // 将第2个参数中的数组元素追加到ret中，在后面数组的jQuery方法里说明
        jQuery.merge( ret, elems );
    }

    // 将当前的jquery对象，做为新查询得到的jquery对象ret的前一个对象
    // Add the old object onto the stack (as a reference)
    ret.prevObject = this;

    ret.context = this.context;

    // 如果没有name传入，则不设置selector
    if ( name === "find" ) {
        // 如果是find方法，则构造一个CSS子选择器
        ret.selector = this.selector + (this.selector ? " " : "") + selector;
    } else if ( name ) {
        // 举例name为slice筛选方法，selector为"-1"
        ret.selector = this.selector + "." + name + "(" + selector + ")";
    }

    // 返回新jquery对象
    // Return the newly-formed element set
    return ret;
},

// Execute a callback for every element in the matched set.
// (You can seed the arguments with an array of args, but this is only used internally.)
each: function( callback, args ) {
    return jQuery.each( this, callback, args );
},

// 当DOM就绪就会调用fn，并传入document, jQuery这二个参数
// 如果没有就绪则记录到readList数组中，在DOM就绪后会被逐个执行
ready: function( fn ) {
    // Attach the listeners
    jQuery.bindReady();

    // If the DOM is already ready
    if ( jQuery.isReady ) {
        // Execute the function immediately
        fn.call( document, jQuery );

    // Otherwise, remember the function for later
    } else if ( readyList ) {
        // Add the function to the wait list
        readyList.push( fn );
    }

    return this;
},

// 此方法返回一个新的过滤后的jquery对象，与get()方法不同，get()方法是返回指定的DOM对象
eq: function( i ) {
    return i === -1 ?
        this.slice( i ) :
        this.slice( i, +i + 1 );
},

first: function() {
    return this.eq( 0 );
},

last: function() {
    return this.eq( -1 );
},

// jQuery.fn.slice()方法返回Array.prototype.slice.apply(this, arguments)这个新的jquery对象
slice: function() {
    return this.pushStack( slice.apply( this, arguments ),
        "slice", slice.call(arguments).join(",") );
},

map: function( callback ) {
    // 没有给pushStack方法传入name，所以map产生的jquery对象没有selector值
    return this.pushStack( jQuery.map(this, function( elem, i ) {
        return callback.call( elem, i, elem );
    }));
},

// 与pushStack方法相对应，可回退到前一个jquery对象上，官方文档解释为：
// End the most recent filtering operation in the current chain and return the set of matched elements to its previous state
// If there was no destructive operation before, an empty set is returned.
// A 'destructive' operation is any operation that changes the set of matched jQuery elements,
// which means any Traversing function that returns a jQuery object - including
// 'add', 'andSelf', 'children', 'filter', 'find', 'map', 'next', 'nextAll', 'not', 'parent', 'prevUntil', 'parentUntil', 'nextUntil',
// 'parents', 'prev', 'prevAll', 'siblings' and 'slice' - plus the 'clone' function (from Manipulation).
end: function() {
    return this.prevObject || jQuery(null);
},

// For internal use only.
// Behaves like an Array's method, not like a jQuery method.
push: push,
sort: [].sort,
splice: [].splice
{% endhighlight %}

jquery对象的selector是后来版本中加上的，是为了方便插件开发人员从jquery对象中拿到selector。

jquery属性是指当前jQuery的版本号。

length指明当前jquery对象中包括的DOM对象数量，这个值会被Array.prototype.push方法自动设置。

toArray()方法是将jquery对象转化为一个真实数组。这里调用了Array.prototype.slice方法。

get()方法是返回当前jquery对象中的某个DOM对象，支持负数做为参数，从数组末尾开始查找对象。

get()方法里用到了jQuery.fn.slice方法，这是jQuery的一个筛选方法，与jQuery.fn.find方法性质一样，需要说明的是Array.prototype.slice方法：

> start可以为负数，从start位置开始到end位置，并且end位置的值不包括进来，数据位置是以0开始计数的。
>
> slice(start, end): Returns a new array that contains the elements of the array from the element numbered start, up to, but not including, the element numbered end.

jQuery.fn.each方法是用于遍历jquery对象中DOM对象，代码实际是调用了jQuery静态工具方法jQuery.each，这里不细说明此方法。

jQuery.fn.index方法是找到当前jquery对象的第一个DOM对象，在根据selector找到的jquery对象中的位置，
其中用到 jQuery.inArray 这个jQuery的数组方法：

{% highlight javascript %}
// 确定第一个参数在数组中的位置(如果没有找到则返回 -1 )。
inArray: function( elem, array ) {
    for ( var i = 0, length = array.length; i < length; i++ ) {
        if ( array[ i ] === elem ) {
            return i;
        }
    }

    return -1;
},
{% endhighlight %}

jQuery.fn.is方法是用一个表达式来检查当前选择的元素集合，如果其中至少有一个元素符合这个给定的表达式就返回true，见后面的inputs.parent().is('form')例子。

jQuery.filter方法最后是调用Sizzle，关于Sizzle另外再详细分析。

{% highlight javascript %}
jQuery.filter = jQuery.multiFilter = function( expr, elems, not ) {
    if ( not ) {
        expr = ":not(" + expr + ")";
    }

    return Sizzle.matches(expr, elems);
};
{% endhighlight %}

jQuery核心方法extend，可以通过此方法给对象添加新属性和方法，也可以通过此方法给jQuery添加静态方法，为jquery对象添加新方法，可利用extend为jQuery写插件：

{% highlight javascript %}
jQuery.extend = jQuery.fn.extend = function() {
    // copy reference to target object
    var target = arguments[0] || {}, i = 1, length = arguments.length, deep = false, options, name, src, copy;

    // 如果第一个参数是true/false，则表示是否是深拷贝对象，并且调整target为第二个参数
    // Handle a deep copy situation
    if ( typeof target === "boolean" ) {
        deep = target;
        target = arguments[1] || {};
        // skip the boolean and the target
        i = 2;
    }

    // Handle case when target is a string or something (possible in deep copy)
    if ( typeof target !== "object" && !jQuery.isFunction(target) ) {
        target = {};
    }

    // 如果传进来只有一个参数或者是一个参数加上true/false时，扩展jQuery本身
    // extend jQuery itself if only one argument is passed
    if ( length === i ) {
        target = this;
        --i;
    }

    for ( ; i < length; i++ ) {
        // Only deal with non-null/undefined values
        if ( (options = arguments[ i ]) != null ) {
            // Extend the base object
            for ( name in options ) {
                src = target[ name ];
                copy = options[ name ];

                // javascript的对象是通过引用操作的，防止自身引用自身造成死循环
                // Prevent never-ending loop
                if ( target === copy ) {
                    continue;
                }

                // 不对DOM对象进行深拷贝操作
                // Recurse if we're merging object literal values or arrays
                if ( deep && copy && ( jQuery.isPlainObject(copy) || jQuery.isArray(copy) ) ) {
                    var clone = src && ( jQuery.isPlainObject(src) || jQuery.isArray(src) ) ? src
                        : jQuery.isArray(copy) ? [] : {};

                    // 这里调用了jQuery.extend静态方法，如果是给jQuery本身扩展，那这里就是个递归方法调用
                    // Never move original objects, clone them
                    target[ name ] = jQuery.extend( deep, clone, copy );

                // Don't bring in undefined values
                } else if ( copy !== undefined ) {
                    target[ name ] = copy;
                }
            }
        }
    }

    // 返回第一个对象参数，可能是jQuery/jQuery.fn
    // Return the modified object
    return target;
};
{% endhighlight %}

这个方法的官方文档说明比较详细，其中几个例子在此页面有测试：

> jQuery.extend( target, [ object1 ], [ objectN ] ) Returns: Object
>
> version added: 1.0
>
> Description: Merge the contents of two or more objects together into the first object.
>
> jQuery.extend( target, [ object1 ], [ objectN ] )
>
> version added: 1.1.4
>
> target: An object that will receive the new properties if additional objects are passed in or that will extend the jQuery namespace if it is the sole argument.
>
> object1: An object containing additional properties to merge in.
>
> objectN: Additional objects containing properties to merge in.
>
> jQuery.extend( [ deep ], target, object1, [ objectN ] )
>
> deep: If true, the merge becomes recursive (aka. deep copy).
>
> target: The object to extend. It will receive the new properties.
>
> object1: An object containing additional properties to merge in.
>
> objectN: Additional objects containing properties to merge in.
>
> When we supply two or more objects to $.extend(), properties from all of the objects are added to the target object.
>
> If only one argument is supplied to $.extend(), this means the target argument was omitted. In this case, the jQuery object itself is assumed to be the target. By doing this, we can add new functions to the jQuery namespace. This can be useful for plugin authors wishing to add new methods to JQuery.
>
> Keep in mind that the target object (first argument) will be modified, and will also be returned from $.extend(). If, however, we want to preserve both of the original objects, we can do so by passing an empty object as the target:
>
> var object = $.extend({}, object1, object2);
>
> The merge performed by $.extend() is not recursive by default; if a property of the first object is itself an object or array, it will be completely overwritten by a property with the same key in the second object. The values are not merged. This can be seen in the example below by examining the value of banana. However, by passing true for the first function argument, objects will be recursively merged.
>
> Undefined properties are not copied. However, properties inherited from the object's prototype will be copied over.
>
> Examples:
>
> Example: Merge two objects, modifying the first.
>
> {% highlight javascript %}
var object1 = {
  apple: 0,
  banana: {weight: 52, price: 100},
  cherry: 97
};
var object2 = {
  banana: {price: 200},
  durian: 100
};
$.extend(object1, object2);
{% endhighlight %}
> Result:
>
> object1 === {apple: 0, banana: {price: 200}, cherry: 97, durian: 100}
>
> Example: Merge two objects recursively, modifying the first.
>
> {% highlight javascript %}
var object1 = {
  apple: 0,
  banana: {weight: 52, price: 100},
  cherry: 97
};
var object2 = {
  banana: {price: 200},
  durian: 100
};
$.extend(true, object1, object2);
{% endhighlight %}
>
> Result:
>
> object1 === {apple: 0, banana: {weight: 52, price: 200}, cherry: 97, durian: 100}
>
> Example: Merge settings and options, modifying settings.
>
> {% highlight javascript %}
var settings = { validate: false, limit: 5, name: "foo" };
var options = { validate: true, name: "bar" };
jQuery.extend(settings, options);
{% endhighlight %}
>
> Result:
>
> settings == { validate: true, limit: 5, name: "bar" }
>
> Example: Merge defaults and options, without modifying the defaults. This is a common plugin development pattern.
>
> {% highlight javascript %}
var empty = {}
var defaults = { validate: false, limit: 5, name: "foo" };
var options = { validate: true, name: "bar" };
var settings = $.extend(empty, defaults, options);
{% endhighlight %}
>
> Result:
>
> settings == { validate: true, limit: 5, name: "bar" }
>
> empty == { validate: true, limit: 5, name: "bar" }

更多关于jQuery通过extend方法添加进来的静态方法，可查看关于静态方法部分的源码分析

> ID-Based Selectors: Beginning your selector with an ID is always best.
>
> The $.fn.find approach is faster because the first selection is handled without going through the Sizzle selector engine — ID-only selections are handled using document.getElementById(), which is extremely fast because it is native to the browser.

{% highlight javascript %}
// fast
$('#container div.robotarm');

// super-fast
$('#container').find('div.robotarm');
{% endhighlight %}

