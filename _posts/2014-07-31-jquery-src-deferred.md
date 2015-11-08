---
layout: post
title: "jquery-1.5 deferred部分源码分析"
date: "Wed Jul 31 2014 21:35:36 GMT+0800 (CST)"
categories: jquery
---

关于1.5中新引进来的jQuery.Deferred方法，在官方文档中的说明摘录部分如下，这个方法实现是基于CommonJS Promises/A的设计模式，这个设计模式主要用于异步编程。其中一种处理模式称为promise，它代表了一种可能会长时间运行而且不一定必须完整的操作的结果。这种模式不会阻塞和等待长时间的操作完成，而是返回一个代表了承诺的（promised）结果的对象。

> One pattern is a promise, which represents the result of a potentially long running and not necessarily complete operation. Instead of blocking and waiting for the long-running computation to complete, the pattern returns an object which represents the promised result.

关于Deferred/Promise的一些外部资源：

1. [Deferred Object of jQuery](http://api.jquery.com/category/deferred-object/)
2. [dojo.Deferred](http://dojotoolkit.org/reference-guide/dojo/Deferred.html)
3. [JavaScript异步编程的Promise模式](http://www.infoq.com/cn/news/2011/09/js-promise)
4. [Asynchronous Programming in JavaScript with “Promises”](http://blogs.msdn.com/b/ie/archive/2011/09/11/asynchronous-programming-in-javascript-with-promises.aspx)
5. [CommonJS Promise/A](http://wiki.commonjs.org/wiki/Promises/A)
6. [jQuery.Deferred对象 -- JavaScript 标准参考教程（alpha）](http://javascript.ruanyifeng.com/jquery/deferred.html)，这份说明是后来网上看到补上的，写得很好。

jQuery的Deferred实现代码量不多，但要看懂源代码还是相当的费神，回调非常多:

{% highlight javascript %}
var // Promise methods
promiseMethods = "done fail isResolved isRejected promise then always pipe".split( " " ),
// Static reference to slice
sliceDeferred = [].slice;

jQuery.extend({
// Create a simple deferred (one callbacks list)
_Deferred: function() {
    var // callbacks list
        callbacks = [],
        // stored [ context , args ]
        fired,
        // to avoid firing when already doing so
        firing,
        // flag to know if the deferred has been cancelled
        cancelled,
        // the deferred itself
        deferred  = {

            // done( f1, f2, ...)
            done: function() {
                if ( !cancelled ) {
                    var args = arguments,
                        i,
                        length,
                        elem,
                        type,
                        _fired;
                    if ( fired ) {
                        _fired = fired;
                        fired = 0;
                    }
                    for ( i = 0, length = args.length; i < length; i++ ) {
                        elem = args[ i ];
                        type = jQuery.type( elem );
                        if ( type === "array" ) {
                            deferred.done.apply( deferred, elem );
                        } else if ( type === "function" ) {
                            callbacks.push( elem );
                        }
                    }
                    if ( _fired ) {
                        deferred.resolveWith( _fired[ 0 ], _fired[ 1 ] );
                    }
                }
                return this;
            },

            // resolve with given context and args
            resolveWith: function( context, args ) {
                if ( !cancelled && !fired && !firing ) {
                    // make sure args are available (#8421)
                    args = args || [];
                    firing = 1;
                    try {
                        while( callbacks[ 0 ] ) {
                            callbacks.shift().apply( context, args );
                        }
                    }
                    finally {
                        fired = [ context, args ];
                        firing = 0;
                    }
                }
                return this;
            },

            // resolve方法将deferred对象的状态从pending改为resolved，reject方法则将状态从pending改为rejected。
            // resolve with this as context and given arguments
            resolve: function() {
                deferred.resolveWith( this, arguments );
                return this;
            },

            // Has this deferred been resolved?
            isResolved: function() {
                return !!( firing || fired );
            },

            // Cancel
            cancel: function() {
                cancelled = 1;
                callbacks = [];
                return this;
            }
        };

    return deferred;
},

// Full fledged deferred (two callbacks list)
Deferred: function( func ) {
    var deferred = jQuery._Deferred(),
        failDeferred = jQuery._Deferred(),
        promise;

    // deferred对象在状态改变时，会触发回调函数。
    // done方法指定状态变为resolved（操作成功）时的回调函数；
    // fail方法指定状态变为rejected（操作失败）时的回调函数；
    // always方法指定，不管状态变为resolved或rejected，都会触发的方法。
    // Add errorDeferred methods, then and promise
    jQuery.extend( deferred, {
        then: function( doneCallbacks, failCallbacks ) {
            deferred.done( doneCallbacks ).fail( failCallbacks );
            return this;
        },
        always: function() {
            return deferred.done.apply( deferred, arguments ).fail.apply( this, arguments );
        },
        fail: failDeferred.done,
        rejectWith: failDeferred.resolveWith,
        reject: failDeferred.resolve,
        isRejected: failDeferred.isResolved,
        pipe: function( fnDone, fnFail ) {
            return jQuery.Deferred(function( newDefer ) {
                jQuery.each( {
                    done: [ fnDone, "resolve" ],
                    fail: [ fnFail, "reject" ]
                }, function( handler, data ) {
                    var fn = data[ 0 ],
                        action = data[ 1 ],
                        returned;
                    if ( jQuery.isFunction( fn ) ) {
                        deferred[ handler ](function() {
                            returned = fn.apply( this, arguments );
                            if ( returned && jQuery.isFunction( returned.promise ) ) {
                                returned.promise().then( newDefer.resolve, newDefer.reject );
                            } else {
                                newDefer[ action + "With" ]( this === deferred ? newDefer : this, [ returned ] );
                            }
                        });
                    } else {
                        deferred[ handler ]( newDefer[ action ] );
                    }
                });
            }).promise();
        },
        // Get a promise for this deferred
        // If obj is provided, the promise aspect is added to the object
        promise: function( obj ) {
            if ( obj == null ) {
                if ( promise ) {
                    return promise;
                }
                promise = obj = {};
            }
            var i = promiseMethods.length;
            while( i-- ) {
                obj[ promiseMethods[i] ] = deferred[ promiseMethods[i] ];
            }
            return obj;
        }
    });
    // Make sure only one callback list will be used
    deferred.done( failDeferred.cancel ).fail( deferred.cancel );
    // Unexpose cancel
    delete deferred.cancel;
    // Call given func if any
    if ( func ) {
        func.call( deferred, deferred );
    }
    return deferred;
},

// Deferred helper
when: function( firstParam ) {
    var args = arguments,
        i = 0,
        length = args.length,
        count = length,
        deferred = length <= 1 && firstParam && jQuery.isFunction( firstParam.promise ) ?
            firstParam :
            jQuery.Deferred();
    function resolveFunc( i ) {
        return function( value ) {
            args[ i ] = arguments.length > 1 ? sliceDeferred.call( arguments, 0 ) : value;
            if ( !( --count ) ) {
                // Strange bug in FF4:
                // Values changed onto the arguments object sometimes end up as undefined values
                // outside the $.when method. Cloning the object into a fresh array solves the issue
                deferred.resolveWith( deferred, sliceDeferred.call( args, 0 ) );
            }
        };
    }
    if ( length > 1 ) {
        for( ; i < length; i++ ) {
            if ( args[ i ] && jQuery.isFunction( args[ i ].promise ) ) {
                args[ i ].promise().then( resolveFunc(i), deferred.reject );
            } else {
                --count;
            }
        }
        if ( !count ) {
            deferred.resolveWith( deferred, args );
        }
    } else if ( deferred !== firstParam ) {
        deferred.resolveWith( deferred, length ? [ firstParam ] : [] );
    }
    return deferred.promise();
}
});
{% endhighlight %}

在jQuery中Promise对象不是通过构造函数来产生的，一般是通过jQuery.prototype.promise()方法和deferred.promise(obj)方法来产生的，对象的实例方法是Deferred对象的方法子集，这些方法不能修改promise对象的状态，但可以添加新的回调，官方说明摘录如下：

> The Promise exposes only the Deferred methods needed to attach additional handlers or determine the state (then, done, fail, isResolved, and isRejected), but not ones that change the state (resolve, reject, resolveWith, and rejectWith). As of jQuery 1.6, the Promise also exposes the always and pipe Deferred methods.

{% highlight javascript %}
// 一般情况下，从外部改变第三方完成的异步操作（比如Ajax）的状态是毫无意义的。为了防止用户这样做，可以在deferred对象的基础上，返回一个针对它的promise对象。
// 简单说，promise对象就是不能改变状态的deferred对象，也就是deferred的只读版。或者更通俗地理解成，promise是一个对将要完成的任务的承诺，排除了其他人破坏这个承诺的可能性，只能等待承诺方给出结果。
// 你可以通过promise对象，为原始的deferred对象添加回调函数，查询它的状态，但是无法改变它的状态，也就是说promise对象不允许你调用resolve和reject方法。
var // Promise methods
promiseMethods = "done fail isResolved isRejected promise then always pipe".split( " " ),
// Static reference to slice
sliceDeferred = [].slice;

jQuery.extend({
// 其实这个方法只是一个简单的回调对象的队列，加入了队列元素添加移除控制和状态控制，队列有状态标记：canceled, fired, firing
// 要理解下面Deferred对象二个队列(done和fail)是怎么添加回调对象的，其关键是在_Deferred的一些方法里引用了其定义时的变量deferred，比如在done, resole方法中，而非使用this来指向deferred这个对象，因为在Deferred这个方法里有2个deferred的实例对象(failDeferred和deferred)，其中failDeferred对象的done方法被另外deferred对象作为fail方法引用了，如果在done方法中使用this，这个时候，this是指向deferred的，而不是failDeferred，那就不能将fail使用的回调对象放到failDeferred的队列中了
// Create a simple deferred (one callbacks list)
_Deferred: function() {...},

// 这个方法调用返回jQuery.Deferred对象，这个对象中通过2个_Deferred对象，维护了2个回调对象的队列，一个是Deferred运行成功时执行的回调对象队列，另外一个则是在Deferred运行失败后执行的回调对象队列，另外为第1个deferred对象，添加了一些新方法，如： fail: failDeferred.done, rejectWith: failDeferred.resolveWith, reject: failDeferred.resolve
// Full fledged deferred (two callbacks list)
Deferred: function() {...},

// jQuery.when()方法执行后返回一个Promise对象实例，传给when的参数如果是回调对象列表，当回调全部执行完成并且成功之后，会调用promise.done()方法，否则调用promise.fail()方法
// Deferred helper
when: function() {...}
});
{% endhighlight %}

1.6中新添加的pipe方法:
{% highlight javascript %}
pipe: function( fnDone, fnFail ) {
    // 根据上面的代码说明，可以明白pipe方法中那个newDefer参数，就是其jQuery.Deferred()方法调用生成的deferred对象
    return jQuery.Deferred(function( newDefer ) {
        jQuery.each( {
            done: [ fnDone, "resolve" ],
            fail: [ fnFail, "reject" ]
        }, function( handler, data ) {
            var fn = data[ 0 ],
                action = data[ 1 ],
                returned;
            if ( jQuery.isFunction( fn ) ) {
                // 即deferred.done(fn), deferred.fail(fn)，为deferred添加done/fail回调对象
                deferred[ handler ](function() {
                    // 此处fnDone会收到deferred.resolve(arguments)传过来的参数，fnFail类似收到deferred.reject(arguments)的参数
                    returned = fn.apply( this, arguments );
                    if ( returned && jQuery.isFunction( returned.promise ) ) {
                        returned.promise().then( newDefer.resolve, newDefer.reject );
                    } else {
                        // 返回不是回调对象，则触发新的newDefer对象的resolveWith/rejectWith方法，进而触发newDefer上绑定的done/fail回调对象队列执行
                        newDefer[ action + "With" ]( this === deferred ? newDefer : this, [ returned ] );
                    }
                });
            } else {
                deferred[ handler ]( newDefer[ action ] );
            }
        });
    }).promise();
},
{% endhighlight %}

$.Deferred()的用例，可以查看jQuery-1.5.js版本中的ajax方法，在什么时机调用resolveWith：
{% highlight javascript %}
function done( status, statusText, responses, headers) {
    // ... 省略部分源码
    // Success/Error
    if ( isSuccess ) {
        // ajax完成之后触发deferred.resolveWith
        deferred.resolveWith( callbackContext, [ success, statusText, jXHR ] );
    } else {
        deferred.rejectWith( callbackContext, [ jXHR, statusText, error ] );
    }

    // Status-dependent callbacks
    jXHR.statusCode( statusCode );
    statusCode = undefined;

    if ( s.global ) {
        globalEventContext.trigger( "ajax" + ( isSuccess ? "Success" : "Error" ),
                [ jXHR, s, isSuccess ? success : error ] );
    }

    // Complete
    completeDeferred.resolveWith( callbackContext, [ jXHR, statusText ] );

    if ( s.global ) {
        globalEventContext.trigger( "ajaxComplete", [ jXHR, s] );
        // Handle the global AJAX counter
        if ( !( --jQuery.active ) ) {
            jQuery.event.trigger( "ajaxStop" );
        }
    }
}

// Attach deferreds
deferred.promise( jXHR );
{% endhighlight %}
