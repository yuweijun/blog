---
layout: post
title: "jQuery-1.4.2 data部分源码分析"
date: "Wed Jul 31 2014 20:32:36 GMT+0800 (CST)"
categories: jquery
---

jQuery本身的二个静态方法jQuery.data()和jQuery.removeData()，jQuery.fn.data()/jQuery.fn.removeData()都是调用这二个方法对jquery对象中所有匹配的DOM元素进行操作：

{% highlight javascript %}
var expando = "jQuery" + now(), uuid = 0, windowData = {};

jQuery.extend({
    cache: {},

    expando:expando,

    // The following elements throw uncatchable exceptions if you
    // attempt to add expando properties to them.
    noData: {
        "embed": true,
        "object": true,
        "applet": true
    },

    // jQuery.data()方法会有内存泄漏的问题，具体可参考以下二个链接文章说明，官方文档建议使用jQuery.fn.data()方法
    // http://www.denisdeng.com/?p=805
    // http://forum.jquery.com/topic/data-object-and-memory-leak
    // 一般是针对DOM对象，将一个key/value键值对存入此DOM对象的data中，也可以将jquery对象作为elem
    data: function( elem, name, data ) {
        // 如果想将data附在embed/object/applet上，将无法生效
        if ( elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()] ) {
            return;
        }

        elem = elem == window ?
            windowData :
            elem;

        // thisCache 只是cache[id]的引用，为了提高运行速度将cache[id]也缓存到thisCache上面
        // elem对象上只是多了一个expando属性，其中放了一个id，根据此id从jQuery.cache中获取data
        var id = elem[ expando ], cache = jQuery.cache, thisCache;

        if ( !id && typeof name === "string" && data === undefined ) {
            return null;
        }

        // 如果当前elem没有uuid，则生成之
        // Compute a unique ID for the element
        if ( !id ) {
            id = ++uuid;
        }

        // 当name为object，则缓存此object
        // jQuery.data( element, key ) Returns: Object
        // Description: Returns value at named data store for the element, as set by jQuery.data(element, name, value),
        // or the full data store for the element.
        // Avoid generating a new cache unless none exists and we want to manipulate it.
        if ( typeof name === "object" ) {
            elem[ expando ] = id;
            // thisCache不需要在此赋值，后面单独还有一句赋值操作: thisCache = cache[ id ]
            // 深拷贝name对象的所有属性到{}对象中，保证cache是一个plainObject
            thisCache = cache[ id ] = jQuery.extend(true, {}, name);

        } else if ( !cache[ id ] ) {
            elem[ expando ] = id;
            cache[ id ] = {};
        }

        thisCache = cache[ id ];

        // 避免被undefined覆写
        // Prevent overriding the named cache with undefined values
        if ( data !== undefined ) {
            thisCache[ name ] = data;
        }

        // name值可能为: undefined, string, object
        // 存储成功后返回data，或者返回查询缓存得到的data，如果name不是一个string，则返回cache[id]所对应的缓存结果
        return typeof name === "string" ? thisCache[ name ] : thisCache;
    },

    // Remove a previously-stored piece of data.
    removeData: function( elem, name ) {
        // 避免object/embed/object对象
        if ( elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()] ) {
            return;
        }

        elem = elem == window ?
            windowData :
            elem;

        var id = elem[ expando ], cache = jQuery.cache, thisCache = cache[ id ];

        // If we want to remove a specific section of the element's data
        if ( name ) {
            if ( thisCache ) {
                // 从缓存中删除指定的key
                // Remove the section of cache data
                delete thisCache[ name ];

                // 如果缓存中所有的key都被删除，则将cache从此元素上移除
                // If we've removed all the data, remove the element's cache
                if ( jQuery.isEmptyObject(thisCache) ) {
                    jQuery.removeData( elem );
                }
            }

        // Otherwise, we want to remove all of the element's data
        } else {
            if ( jQuery.support.deleteExpando ) {
                delete elem[ jQuery.expando ];

            } else if ( elem.removeAttribute ) {
                elem.removeAttribute( jQuery.expando );
            }

            // 从jQuery.cache中彻底删除此elem的data缓存
            // Completely remove the data cache
            delete cache[ id ];
        }
    }
});
{% endhighlight %}

jQuery.fn.data()/jQuery.fn.removeData()方法，操作jquery对象：

> Use $.data Instead of $.fn.data
>
> Using $.data on a DOM element instead of calling $.fn.data on a jQuery selection can be up to 10 times faster. Be sure you understand the difference between a DOM element and a jQuery selection before doing this, though.

{% highlight javascript %}
// regular
$(elem).data(key,value);  

// faster
$.data(elem,key,value);
{% endhighlight %}

{% highlight javascript %}
jQuery.fn.extend({
    // jQuery.data和jQuery.removeData是比较底层的方法，不建议使用，一般是通过jQuery.fn.data/removeData方法进行操作，如果出于性能考虑，则可以直接使用jQuery.data()方法
    data: function( key, value ) {
        if ( typeof key === "undefined" && this.length ) {
            // .data()
            // 返回jquery对象中的第一个DOM元素中的data
            return jQuery.data( this[0] );
        } else if ( typeof key === "object" ) {
            // 如果key为一个object的话，jquery对象中所有的DOM对象中的data内容会全部被重置为obj
            // .data( obj ) Setting an element's data object with .data(obj) replaces all data previously stored with that element.
            // obj: An object of key-value pairs of data to set.
            return this.each(function() {
                jQuery.data( this, key );
            });
        }

        var parts = key.split(".");
        parts[1] = parts[1] ? "." + parts[1] : "";

        // 以下代码中涉及自定义事件的处理，在jQuery.event中详细分析jQuery.fn.triggerHandler/trigger
        if ( value === undefined ) {
            // .data( key )
            // key: Name of the data stored.
            // 根据key获取存储在此DOM对象上的data
            var data = this.triggerHandler("getData" + parts[1] + "!", [parts[0]]);

            if ( data === undefined && this.length ) {
                data = jQuery.data( this[0], key );
            }
            return data === undefined && parts[1] ?
                this.data( parts[0] ) :
                data;
        } else {
            // .data( key, value ) Returns: jQuery
            // Description: Store arbitrary data associated with the matched elements.
            // 设置key/value键值对到elem对象的data中
            return this.trigger("setData" + parts[1] + "!", [parts[0], value]).each(function() {
                jQuery.data( this, key, value );
            });
        }
    },

    removeData: function( key ) {
        // 删除jquery对象中匹配到的每个DOM对象中指定key的data
        return this.each(function() {
            jQuery.removeData( this, key );
        });
    }
})
{% endhighlight %}

###建议jQuery.fn.queue()和jQuery.fn.dequeue()这2个方法使用Chrome进行调试

jQuery.queue()和jQuery.dequeue()方法，通过jQuery.data()来缓存每个DOM对象上的事件处理方法和fx方法:

> Note that when adding a function with .queue(), we should ensure that .dequeue() is eventually called so that the next function in line executes.
>
> In jQuery 1.4 the function that's called is passed in another function, as the first argument, that when called automatically dequeues the next item and keeps the queue moving. You would use it like so:

{% highlight javascript %}
$("#test").queue(function(next) {
    // Do some stuff...
    next();
});
{% endhighlight %}

{% highlight javascript %}
jQuery.extend({
// 显示匹配到的elem中，待执行的方法列表，或者追加新回调方法到queue列表中，并返回新的queue
// Show the queue of functions to be executed on the matched elements.
queue: function( elem, type, data ) {
    if ( !elem ) {
        return;
    }

    // 默认为fx，指jQuery的效果
    type = (type || "fx") + "queue";

    // 根据type将此待执行方法列表从jQuery.data中取出
    var q = jQuery.data( elem, type );

    // Speed up dequeue by getting out quickly if this is just a lookup
    if ( !data ) {
        // 如果是jQuery.quene(elem, [queueName])，则返回正在等待执行的方法列表，或者是空数组
        // Show the queue of functions to be executed on the matched elements.
        return q || [];
    }

    if ( !q || jQuery.isArray(data) ) {
        // jQuery.queue( element, queueName, newQueue )
        // element: A DOM element where the array of queued functions is attached.
        // queueName: A string containing the name of the queue. Defaults to fx, the standard effects queue.
        // newQueue: An array of functions to replace the current queue contents.
        // 如果此type的function列表原来不存在，或者data是一个function数组，则重置此elem的此type的function列表
        q = jQuery.data( elem, type, jQuery.makeArray(data) );
    } else {
        // jQuery.queue( element, queueName, callback )
        // element: A DOM element on which to add a queued function.
        // queueName: A string containing the name of the queue. Defaults to fx, the standard effects queue.
        // callback: The new function to add to the queue.
        // 如果此elem中，已经存在此type的执行方法列表，并且data不是数组时，将data(一个callback)追加到待执行的方法列表中
        q.push( data );
    }

    return q;
},

// 从方法队列中移出并调用一个方法(当前无其他方法在执行时)
// 对于fx类型的方法队列，在取出一个方法执行后，会往队列头添加一个字符串"inprogress"标记当前队列正在运行中
dequeue: function( elem, type ) {
    type = type || "fx";

    // 获取此type的待执行方法列表，并取出第一个方法赋个fn
    var queue = jQuery.queue( elem, type ), fn = queue.shift();

    // 在elem对象中可能已经加入多个fx效果方法，queue队列的第一个值为inprogress时，再从quene数组中弹出一个对象
    // If the fx queue is dequeued, always remove the progress sentinel
    if ( fn === "inprogress" ) {
        fn = queue.shift();
    }

    if ( fn ) {
        // Add a progress sentinel to prevent the fx queue from being
        // automatically dequeued
        // 如果是效果类的列表，再次压入一个"inprogress"到列表数组头部
        if ( type === "fx" ) {
            queue.unshift("inprogress");
        }

        // 如果用jQuery.fn.queue(fn)为方法队列增加了一个方法，必须在fn中调用一次jQuery.fn.dequeue()方法，使得方法队列中的下一个方法能被调用
        // jQuery 1.4版本中，fn(next)可以传入一个参数，并在fn中调用next()，如：
        // $("#test").queue(function(next) { /* Do some stuff...;*/ next(); });
        // 将得到的fn作为此elem对象的方法调用，并传入一个回调方法(next)作为参数，因为在fn中有next()这样的调用，所以会触发方法列表中的下一个方法
        fn.call(elem, function() {
            jQuery.dequeue(elem, type);
        });
    }
}
});
{% endhighlight %}

jQuery.fn.queue()/jQuery.fn.dequeue()，针对jquery对象进行queue/dequeue操作，其内部都是在调用jQuery.queue()/jQuery.dequeue()进行管理:
{% highlight javascript %}
// .queue( [ queueName ], newQueue )
// queueName: A string containing the name of the queue. Defaults to fx, the standard effects queue.
// newQueue: An array of functions to replace the current queue contents.

// .queue( [ queueName ], callback( next ) )
// queueName: A string containing the name of the queue. Defaults to fx, the standard effects queue.
// callback( next ): The new function to add to the queue, with a function to call that will dequeue the next item.
jQuery.fn.extend({
queue: function( type, data ) {
    // 如果只传了一个参数进来，那就当是一个fx类型的方法，并将第一个参数作为data
    if ( typeof type !== "string" ) {
        data = type;
        type = "fx";
    }

    if ( data === undefined ) {
        // .queue( [ queueName ] ) queueName: A string containing the name of the queue.
        // Defaults to fx, the standard effects queue.
        // 不传参数进来，则返回jquery对象中第一个匹配的DOM对象中，此type的待执行方法列表
        return jQuery.queue( this[0], type );
    }
    return this.each(function( i, elem ) {
        // 将此type的data压入jquery对象中每个匹配的DOM元素的缓存中
        var queue = jQuery.queue( this, type, data );

        // 对于fx类型的方法队列，在每次添加一个data到queue队列中时，给jQuery.fn.queue()一次开始运行queue队列中的方法的时机
        if ( type === "fx" && queue[0] !== "inprogress" ) {
            // 当前elem对象正在运行队列中的效果方法(animate)时，queue的第一个值为inprogress
            // 如queue的第一个值不是inprogress，即开始运行queue队列中的方法
            jQuery.dequeue( this, type );
        }
    });
},
dequeue: function( type ) {
    // When .dequeue() is called, the next function on the queue is removed from the queue, and then executed.
    // This function should in turn (directly or indirectly) cause .dequeue() to be called, so that the sequence can continue.
    // 遍历jquery对象匹配的DOM对象，触发元素上的方法队列
    return this.each(function() {
        jQuery.dequeue( this, type );
    });
},
{% endhighlight %}

Using the standard effects queue, we can, for example, set an 800-millisecond delay between the .slideUp() and .fadeIn() of `<div id="foo">`:

{% highlight javascript %}
$('#foo').slideUp(300).delay(800).fadeIn(400);
{% endhighlight %}

When this statement is executed, the element slides up for 300 milliseconds and then pauses for 800 milliseconds before fading in for 400 milliseconds.

{% highlight javascript %}
// Based off of the plugin by Clint Helfers, with permission.
// http://blindsignals.com/index.php/2009/07/jquery-delay/
delay: function( time, type ) {
    time = jQuery.fx ? jQuery.fx.speeds[time] || time : time;
    type = type || "fx";

    // 往当前queue压入一个新的function，在此function被触发后，调用setTimeout，延时time毫秒之后，触发此type的方法队列中的后续方法执行
    return this.queue( type, function() {
        var elem = this;
        setTimeout(function() {
            jQuery.dequeue( elem, type );
        }, time );
    });
},

clearQueue: function( type ) {
    return this.queue( type || "fx", [] );
}
});
{% endhighlight %}

通过jQuery.fn.queue()或者jQuery.queue()方法加入elem对象中的待执行效果方法，是在opt.complete方法被调用时执行，这里细节在jQuery.fx中另行说明：

{% highlight javascript %}
// Queueing
opt.old = opt.complete;
opt.complete = function() {
if ( opt.queue !== false ) {
    jQuery(this).dequeue();
}
if ( jQuery.isFunction( opt.old ) ) {
    opt.old.call( this );
}
};
{% endhighlight %}

