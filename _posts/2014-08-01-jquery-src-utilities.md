---
layout: post
title: "jQuery-1.4.2 utilities部分源码分析"
date: "Fri Aug 01 2014 22:52:32 GMT+0800 (CST)"
categories: jquery
---

jQuery.extend方法是扩展jQuery本身的静态对象，各方法解释直接写在jQuery源码注释前:

{% highlight javascript %}
jQuery.extend({
    // 为了兼容别的javascript框架或者老版本的jQuery
    noConflict: function( deep ) {
        window.$ = _$;

        if ( deep ) {
            window.jQuery = _jQuery;
        }

        return jQuery;
    },

    // Is the DOM ready to be used? Set to true once it occurs.
    isReady: false,

    // 常用的jQuery方法$.ready()
    // Handle when the DOM is ready
    ready: function() {
        // 通过jQuery.isReady这个标记位控制此方法只会被执行一次
        // 因为jQuery绑了二个事件在DOM上，一是DOM就绪时，二是window.onload完成时，都会调用jQuery.ready方法
        // Make sure that the DOM is not already loaded
        if ( !jQuery.isReady ) {
            // Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
            if ( !document.body ) {
                // 针对IE，延时13毫秒执行jQuery.ready()方法
                return setTimeout( jQuery.ready, 13 );
            }

            // Remember that the DOM is ready
            jQuery.isReady = true;

            // 遍历readyList数组(这是最外层的匿名闭包的内部局部变量)，将数组内的方法逐个执行，参考jQuery.fn.ready方法
            // If there are functions bound, to execute
            if ( readyList ) {
                // Execute all of them
                var fn, i = 0;
                while ( (fn = readyList[ i++ ]) ) {
                    // 将方法fn作为document对象的方法进行调用，并将jQuery作为参数
                    fn.call( document, jQuery );
                }

                // Reset the list of functions
                readyList = null;
            }

            // 执行触发器中自定义的ready事件
            // Trigger any bound ready events
            if ( jQuery.fn.triggerHandler ) {
                jQuery( document ).triggerHandler( "ready" );
            }
        }
    },

    bindReady: function() {
        if ( readyBound ) {
            return;
        }

        readyBound = true;

        // IE8/safari/chrome中支持document.readyState属性，在文件加载完成后状态值为"complete"
        // 这个在IE中也可用于判断iframe的加载状态，IE中iframe.onload方法无效
        // iframe.onreadystatechange = function(){
        //    if (iframe.readyState == 'complete') {
        //        // doing something;
        //    }
        // };
        // Catch cases where $(document).ready() is called after the
        // browser event has already occurred.
        if ( document.readyState === "complete" ) {
            return jQuery.ready();
        }

        // DOMContentLoaded方法因浏览器不同而不同
        // Mozilla, Opera and webkit nightlies currently support this event
        if ( document.addEventListener ) {
            // Use the handy event callback
            document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );

            // 在Mozilla/Opera/webkit这几个浏览器上可以通过绑定window.onload事件，执行jQuery.ready方法
            // 这是备用策略，避免DOMContentLoaded方法没有被执行时，可以在window.onload之后运行jQuery.ready方法
            // A fallback to window.onload, that will always work
            window.addEventListener( "load", jQuery.ready, false );

        // If IE event model is used
        } else if ( document.attachEvent ) {
            // ensure firing before onload,
            // maybe late but safe also for iframes
            document.attachEvent("onreadystatechange", DOMContentLoaded);

            // A fallback to window.onload, that will always work
            window.attachEvent( "onload", jQuery.ready );

            // If IE and not a frame
            // continually check to see if the document is ready
            var toplevel = false;

            try {
                toplevel = window.frameElement == null;
                // 这句代码在浏览器中测试过，不知如何才能发生异常
            } catch(e) {}

            if ( document.documentElement.doScroll && toplevel ) {
                doScrollCheck();
            }
        }
    },

    // 下面5个方法是为了测试javascript对象的类型
    // Determine if the argument passed is a Javascript function object.
    // See test/unit/core.js for details concerning isFunction.
    // Since version 1.3, DOM methods and functions like alert
    // aren't supported. They return false on IE (#2968).
    isFunction: function( obj ) {
        return toString.call(obj) === "[object Function]";
    },

    // Determine whether the argument is an array.
    isArray: function( obj ) {
        return toString.call(obj) === "[object Array]";
    },

    // Check to see if an object is a plain object (created using "{}" or "new Object").
    isPlainObject: function( obj ) {
        // 检查obj是否一个object直接量，用{}或者new Object生成
        // 避免是DOM对象和window对象
        // Must be an Object.
        // Because of IE, we also have to check the presence of the constructor property.
        // Make sure that DOM nodes and window objects don't pass through, as well
        if ( !obj || toString.call(obj) !== "[object Object]" || obj.nodeType || obj.setInterval ) {
            return false;
        }

        // Not own constructor property must be Object
        if ( obj.constructor
            && !hasOwnProperty.call(obj, "constructor")
            && !hasOwnProperty.call(obj.constructor.prototype, "isPrototypeOf") ) {
            return false;
        }

        // Own properties are enumerated firstly, so to speed up,
        // if last one is own, then all properties are own.

        var key;
        for ( key in obj ) {}

        // 如果obj={}，那么key=undefined，要不然key就是obj对象的最后一个属性，符合条件返回true
        return key === undefined || hasOwnProperty.call( obj, key );
    },

    // 测试是否空对象，没有任何自身属性和继承属性
    // Check to see if an object is empty (contains no properties).
    isEmptyObject: function( obj ) {
        for ( var name in obj ) {
            return false;
        }
        return true;
    },

    error: function( msg ) {
        throw msg;
    },

    // 将JSON字符串解析后返回一个合法的javascript Object
    // JSON字符串内的所有字符串需要用双引号括起来，不能用单引号，如'{"test": 1}'，而不能是"{'test': 1}"
    // Takes a well-formed JSON string and returns the resulting JavaScript object.
    parseJSON: function( data ) {
        if ( typeof data !== "string" || !data ) {
            return null;
        }

        // Make sure leading/trailing whitespace is removed (IE can't handle it)
        data = jQuery.trim( data );

        // For example, the following are all malformed JSON strings:
        // * {test: 1} (test does not have double quotes around it).
        // * {'test': 1} ('test' is using single quotes instead of double quotes).

        // Make sure the incoming data is actual JSON
        // Logic borrowed from http://json.org/json2.js
        if ( /^[\],:{}\s]*$/.test(data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
            .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
            .replace(/(?:^|:|,)(?:\s*\[)+/g, "")) ) {

            // 如果浏览器本身提供了window.JSON.parse方法，则使用之，否则构造匿名函数并调用之
            // Try to use the native JSON parser first
            return window.JSON && window.JSON.parse ?
                window.JSON.parse( data ) :
                (new Function("return " + data))();

        } else {
            jQuery.error( "Invalid JSON: " + data );
        }
    },

    // noop: 无操作，停止操作指令
    // 一个空方法，这对想提供一个回调参数的插件开发者很有用，如果使用者没有传入callback时，可以用noop代替
    noop: function() {},

    // 在全局环境中执行data组成的javascript字符串，这个方法不同于javascript中的Global方法eval()
    // 可以通过这个方法跨域加载外部javascript文件，参考jQuery.ajax中type="script"的调用方式
    globalEval: function( data ) {
        if ( data && rnotwhite.test(data) ) {
            // Inspired by code by Andrea Giammarchi
            // 这个方法在javascript权威指南中已经有出现
            // http://webreflection.blogspot.com/2007/08/global-scope-evaluation-and-dom.html
            var head = document.getElementsByTagName("head")[0] || document.documentElement,
                script = document.createElement("script");

            script.type = "text/javascript";

            if ( jQuery.support.scriptEval ) {
                script.appendChild( document.createTextNode( data ) );
            } else {
                script.text = data;
            }

            // 将script插入head的头部之后，浏览器会自动运行script标签中的javascript代码，在执行之后立即将此script对象从head中移除
            // Use insertBefore instead of appendChild to circumvent an IE6 bug.
            // This arises when a base node is used (#2709).
            head.insertBefore( script, head.firstChild );
            head.removeChild( script );
        }
    },

    // 判断元素的标签名称是否与传入的name一致
    nodeName: function( elem, name ) {
        return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
    },

    // jQuery.each( collection, callback(indexInArray, valueOfElement) )
    // collection: 可以是array，object，或者是NodeList等集合对象
    // 非常重要的方法，用于遍历数组或者对象，回调方法中第一个参数是数组的index值或者对象的name属性，第二个参数是对应的Value值
    // 另外此处需要注意的是，传给each方法的object是对方法外部对象的一个引用，如果在callback内对此object作了修改，会导致运行错误，如用此方法删除jQuery匹配的节点，因为object中的结点被删除，而导致object本身被修改，可以each方法中的i仍然在增加，最后就会部分节点未被删除，而且最后因尝试删除object中已经不存在的index对应的节点而抛错：
    // $.each(document.getElementsByTagName('div'), function(){this.parentNode.removeChild(this)});

    // A generic iterator function, which can be used to seamlessly iterate over both objects and arrays.
    // Arrays and array-like objects with a length property (such as a function's arguments object) are iterated by numeric index,
    // from 0 to length-1. Other objects are iterated via their named properties.
    // args is for internal usage only
    each: function( object, callback, args ) {
        var name, i = 0,
            length = object.length,
            isObj = length === undefined || jQuery.isFunction(object);

        if ( args ) {
            if ( isObj ) {
                for ( name in object ) {
                    // callback是作为object[name]对象的方法调用的，所以在callback中的this就是指向object[name]的
                    if ( callback.apply( object[ name ], args ) === false ) {
                        break;
                    }
                }
            } else {
                for ( ; i < length; ) {
                    if ( callback.apply( object[ i++ ], args ) === false ) {
                        break;
                    }
                }
            }

        // A special, fast, case for the most common use of each
        } else {
            if ( isObj ) {
                for ( name in object ) {
                    if ( callback.call( object[ name ], name, object[ name ] ) === false ) {
                        break;
                    }
                }
            } else {
                for ( var value = object[0];
                    i < length && callback.call( value, i, value ) !== false; value = object[++i] ) {}
            }
        }

        return object;
    },

    // jQuery对字符串操作的一个功能扩写
    // Remove the whitespace from the beginning and end of a string.
    trim: function( text ) {
        return (text || "").replace( rtrim, "" );
    },

    // 将一个类数组的对象转化为一个真正的javascript数组
    // results is for internal usage only
    makeArray: function( array, results ) {
        var ret = results || [];

        if ( array != null ) {
            // The window, strings (and functions) also have 'length'
            // The extra typeof function check is to prevent crashes
            // in Safari 2 (See: #3039)
            if ( array.length == null || typeof array === "string" || jQuery.isFunction(array) || (typeof array !== "function" && array.setInterval) ) {
                push.call( ret, array );
            } else {
                jQuery.merge( ret, array );
            }
        }

        return ret;
    },

    // 在一个数组中搜索指定的值elem，如果找到返回其位置，没找到返回-1
    // 所以最后要用方法执行结果与-1比较，看此elem是否在此数组中
    inArray: function( elem, array ) {
        if ( array.indexOf ) {
            return array.indexOf( elem );
        }

        for ( var i = 0, length = array.length; i < length; i++ ) {
            if ( array[ i ] === elem ) {
                return i;
            }
        }

        return -1;
    },

    // jQuery源码sizzle部分代码关于makeArray()方法有如下注释说明：
    // Perform a simple check to determine if the browser is capable of converting a NodeList to an array using builtin methods.
    // Also verifies that the returned array holds DOM nodes (which is not the case in the Blackberry browser)
    // 在IE中，NodeList集合并不是一个object，不能直接用Array.prototype.slice.call(NodeList)转化成一个真实的数组，必须使用for循环重新创建新数组
    // In Internet Explorer it throws an error that it can't run Array.prototype.slice.call(nodes) because a DOM NodeList is not a JavaScript object.
    // 将第二个数组(对象)合并到第一个数组(对象)中，最后返回第一个数组(对象)
    merge: function( first, second ) {
        var i = first.length, j = 0;

        if ( typeof second.length === "number" ) {
            for ( var l = second.length; j < l; j++ ) {
                first[ i++ ] = second[ j ];
            }
        } else {
            // 如果第2个参数不是数组，而是一个object，但有数字的key，并且key值从0开始找
            // example: $.merge([], {a: 1, "0": "jquery", "2": "javascript"});
            while ( second[j] !== undefined ) {
                first[ i++ ] = second[ j++ ];
            }
        }

        first.length = i;

        return first;
    },

    // function(elementOfArray, indexInArray)
    // The function to process each item against. The first argument to the function is the item, and the second argument is the index.
    // The function should return a Boolean value. this will be the global window object.
    // invert: If "invert" is false, or not provided, then the function returns an array consisting of all elements for which "callback" returns true.
    // If "invert" is true, then the function returns an array consisting of all elements for which "callback" returns false.
    // jQuery.grep( array, function(elementOfArray, indexInArray), [ invert ] )
    // 从一个array中查找符合回调函数的元素，返回一个新的数组，原来的数组不会被影响
    grep: function( elems, callback, inv ) {
        var ret = [];

        // Go through the array, only saving the items
        // that pass the validator function
        for ( var i = 0, length = elems.length; i < length; i++ ) {
            if ( !inv !== !callback( elems[ i ], i ) ) {
                ret.push( elems[ i ] );
            }
        }

        return ret;
    },

    // 将一个类数组的对象，用回调函数对每个元素进行map，返回一个新的对象
    // arg is for internal usage only
    map: function( elems, callback, arg ) {
        var ret = [], value;

        // Go through the array, translating each of the items to their
        // new value (or values).
        for ( var i = 0, length = elems.length; i < length; i++ ) {
            value = callback( elems[ i ], i, arg );

            if ( value != null ) {
                ret[ ret.length ] = value;
            }
        }

        return ret.concat.apply( [], ret );
    },

    // 在jQuery事件处理中会用到此值，每个事件处理器都有唯一的guid
    // A global GUID counter for objects
    guid: 1,

    // jQuery.proxy()方法有2个作用，因为javascript中没有重载，所以通过参数位数和类型在代码里区分这个方法要执行的功能
    // 一个用法是，将指定的方法绑定到其他作用域上，并返回这个代理过的方法
    // 参考Prototype中的Function.bind()方法，可以处理方法中this的引用问题
    // 另一个用法是起到事件代理的作用，通过handler.guid控制
    // jQuery.proxy( function, context )
    // function: The function whose context will be changed.
    // context: The object to which the context (`this`) of the function should be set.
    // jQuery.proxy( context, name )
    // context: The object to which the context of the function should be set.
    // name: The name of the function whose context will be changed (should be a property of the 'context' object.
    // This method is most useful for attaching event handlers to an element where the context is pointing back to a different object.
    // Additionally, jQuery makes sure that even if you bind the function returned from jQuery.proxy() it will still unbind the correct function, if passed the original.
    proxy: function( fn, proxy, thisObject ) {
        // 这个方法的使用一般是传入2个参数的
        if ( arguments.length === 2 ) {
            // 传入2个参数，第二个参数为String时，则将第一个参数视为context，第二个参数则必须为其所属的方法名
            if ( typeof proxy === "string" ) {
                // jQuery.proxy( context, name )
                // context: The object to which the context of the function should be set.
                // name: The name of the function whose context will be changed (should be a property of the 'context' object).
                thisObject = fn;
                fn = thisObject[ proxy ];
                proxy = undefined;
            } else if ( proxy && !jQuery.isFunction( proxy ) ) {
                // jQuery.proxy( function, context )
                // function: The function whose context will be changed.
                // context: The object to which the context (`this`) of the function should be set.
                // 第一个参数是一个function，第二个参数是此function要执行的环境
                thisObject = proxy;
                proxy = undefined;
            }
            // 还有一种情况，就是传入的二个参数都是function，这里暂时不作处理，后面会另外处理
        }

        // 如果是传入了3个参数，并且第二个参数proxy的值取反为true，则将fn绑定作用域到第三个参数thisObject上，一般不会这么使用，这个if主要还是处理2个参数的情况，改变方法的执行作用域，但是jQuery的源码没有将这段代码放进前面的if块中，也可以用之处理只传进来1个参数的情况和3个参数的情况
        // 如果是传入了2个参数，经上面的if处理之后，proxy为undefined
        // 如果只传了1个参数，proxy也是undefined，那就可以直接将之做为全局方法运行(this指向window)
        if ( !proxy && fn ) {
            // 重新设置proxy，这里和Prototype.js中的bind方法作用相类似
            proxy = function() {
                // 将fn作为thisObject的方法调用，即fn中的this是指向thisObject的
                // 如果没有thisObject，则this指向调用proxy方法的对象或者是window(如果在全局环境中直接调用proxy)
                return fn.apply( thisObject || this, arguments );
            };
        }

        // 如果当前方法是传入3个参数，并且proxy实际传入了一个对象(取反不为true，最后仍要返回这个proxy对象)，则只是为了fn和proxy添加一个全局唯一的标识符后，返回proxy，这时就没有绑定作用域到thisObject上，也不会绑定到proxy对象上
        // 如果当前方法是传入2个参数，并且二个参数都是function，则为fn和proxy添加一个全局唯一的标识符后，返回proxy
        // 在2个方法上绑定相同的guid时，当这2个方法都做为事件监听器时，移除其中任意一个监听器，会因为它们具有相同的guid，同时将另一个监听器一起移除，具体可查看jQuery.event.remove()方法
        // 每个proxy方法都有一个全局唯一标识符对应，可以此标识符删除proxy
        // Set the guid of unique handler to the same of original handler, so it can be removed
        if ( fn ) {
            // 在原来的版本中，jQuery.event.proxy()方法只是用于事件处理器的代理，将多个事件处理器绑定相同的guid，这样可以同时被删除
            proxy.guid = fn.guid = fn.guid || proxy.guid || jQuery.guid++;
        }

        // So proxy can be declared as an argument
        return proxy;
    },

    // userAgent匹配
    // Use of jQuery.browser is frowned upon. 不建议使用jQuery.browser
    // More details: http://docs.jquery.com/Utilities/jQuery.browser
    uaMatch: function( ua ) {
        ua = ua.toLowerCase();

        var match = /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
            /(opera)(?:.*version)?[ \/]([\w.]+)/.exec( ua ) ||
            /(msie) ([\w.]+)/.exec( ua ) ||
            !/compatible/.test( ua ) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec( ua ) ||
            [];

        return { browser: match[1] || "", version: match[2] || "0" };
    },

    browser: {}
});
{% endhighlight %}

在bindReady方法中用到的DOMContentLoaded方法，是根据浏览器不同而设置的事件处理器：
{% highlight javascript %}
// Cleanup functions for the document ready method
if ( document.addEventListener ) {
    DOMContentLoaded = function() {
        document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
        jQuery.ready();
    };

} else if ( document.attachEvent ) {
    DOMContentLoaded = function() {
        // Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
        if ( document.readyState === "complete" ) {
            document.detachEvent( "onreadystatechange", DOMContentLoaded );
            jQuery.ready();
        }
    };
}
{% endhighlight %}

