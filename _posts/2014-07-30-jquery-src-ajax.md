---
layout: post
title:  "jQuery-1.4.2 ajax部分源码分析"
date: "Wed Jul 30 2014 20:58:36 GMT+0800 (CST)"
categories: jquery src
---

jQuery.ajax在官方有非常详细的[文档说明](http://api.jquery.com/jQuery.ajax/)。其真正的核心方法是jQuery.ajax()方法，一般并不需要用此方法进行ajax操作，而使用更高级的方法，如jQuery.get()/jQuery.fn.load()/jQuery.post()/jQuery.getScript()等。

{% highlight javascript %}
jQuery.extend({
    // 关于 Last-Modified 和 ETag 请查阅以下二个链接
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.29
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.19
    // jQuery.lastModified和jQuery.etag用于缓存ajax请求，以ajax请求的url为key存储
    // Last-Modified header cache for next request
    lastModified: {},
    etag: {},

    // Perform an asynchronous HTTP (Ajax) request.
    // jQuery.ajax 核心方法
    ajax: function( origSettings ) {
        // 不改变jQuery.ajaxSettings和origSettings，并对这个二个对象进行深复制后，生成新对象s
        var s = jQuery.extend(true, {}, jQuery.ajaxSettings, origSettings);

        var jsonp, status, data,
            callbackContext = origSettings && origSettings.context || s,
            type = s.type.toUpperCase();

        // 如果data不是字符串，就将其转换为查询字符串格式，用于ajax的post请求的body，或者将其接到get请求的URL参数里
        // convert data if not already a string
        if ( s.data && s.processData && typeof s.data !== "string" ) {
            s.data = jQuery.param( s.data, s.traditional );
        }

        // 如果是使用jsonp协议的ajax请求，返回的文本Content-Type为script类型
        // 构建jsonp请求字符集串。jsonp是跨域请求，要加上callback=?后面将会加函数名
        // Handle JSONP Parameter Callbacks
        if ( s.dataType === "jsonp" ) {
            // 使get的url包含 callback=? 后面将会进行加函数名
            if ( type === "GET" ) {
                if ( !jsre.test( s.url ) ) {
                    s.url += (rquery.test( s.url ) ? "&" : "?") + (s.jsonp || "callback") + "=?";
                }
            } else if ( !s.data || !jsre.test(s.data) ) {
                s.data = (s.data ? s.data + "&" : "") + (s.jsonp || "callback") + "=?";
            }
            // 对于jsonp类型的ajax请求，先转化dataType为json类型
            s.dataType = "json";
        }

        // Build temporary JSONP function
        if ( s.dataType === "json" && (s.data && jsre.test(s.data) || jsre.test(s.url)) ) {
            // 生成回调方法名称并赋值给jsonp，后面还会以此变量是否赋值作为条件判断是否为jsonp请求
            jsonp = s.jsonpCallback || ("jsonp" + jsc++);

            // 生成一个临时的jsonp方法名，并以此名字替换s.data和s.url中的?占位符(jsre = /=\?(&|$)/)
            // Replace the =? sequence both in the query string and the data
            if ( s.data ) {
                s.data = (s.data + "").replace(jsre, "=" + jsonp + "$1");
            }

            s.url = s.url.replace(jsre, "=" + jsonp + "$1");

            // 我们需要保证jsonp类型响应能正确地执行
            // 将dataType为json的类型转变为script
            // We need to make sure that a JSONP style response is executed properly
            s.dataType = "script";

            // window下注册一个jsonp回调函数，让ajax请求返回的代码调用执行它
            // 在服务器端我们生成的代码以callback(data);形式传入data，当代码以script形式插入到head内后，会自动调用下面的window[jonsp]方法
            // Handle JSONP-style loading
            window[ jsonp ] = window[ jsonp ] || function( tmp ) {
                data = tmp;
                // jsonp类型的ajax请求完成后会自行调用success()和complete()这二个回调方法
                success();
                complete();
                // Garbage collect
                // 垃圾回收,释放联变量，删除jsonp的对象，除去head中添加的script元素
                window[ jsonp ] = undefined;

                try {
                    delete window[ jsonp ];
                } catch(e) {}

                // 此方法因为被window的对象引用，所以方法定义时所有变量都被保存在词法作用域链上，包括后面才定义的head:
                // var head = document.getElementsByTagName("head")[0] || document.documentElement
                if ( head ) {
                    // 服务器端返回的是callback(data)形式的文本，将其添加到head中的一个新插入的script中，从而运行callback(data)方法
                    // 此处在定义window[ jsonp ]方法，这个script是在后面定义的：var script = document.createElement("script")
                    head.removeChild( script );
                }
            };
        }

        // 前面的代码已经将jsonp和json这二个dataType类型转化为script类型
        if ( s.dataType === "script" && s.cache === null ) {
            s.cache = false;
        }

        // 如果ajax请求不需要缓存，则每次GET请求的url中替换或者多传一个"_"的参数，并传入当前的时间戳
        if ( s.cache === false && type === "GET" ) {
            var ts = now();

            // try replacing _= if it is there(rts = /(\?|&)_=.*?(&|$)/)
            var ret = s.url.replace(rts, "$1_=" + ts + "$2");

            // if nothing was replaced, add timestamp to the end
            s.url = ret + ((ret === s.url) ? (rquery.test(s.url) ? "&" : "?") + "_=" + ts : "");
        }

        // 如果ajax为GET请求，并且传入的options中有s.data，将s.data追加到s.url后面
        // If data is available, append data to url for get requests
        if ( s.data && type === "GET" ) {
            s.url += (rquery.test(s.url) ? "&" : "?") + s.data;
        }

        // 采用jQuery.event.trigger("ajaxStart");来触发全局的ajaxStart事件。
        // 这也是说只要注册了这个事件的元素，在任何的ajax的请求时ajaxStart都会执行元素注册的事件处理函数。
        // jQuery.each("ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","), function(i, o) {
        //    jQuery.fn[o] = function(f) { // f:function
        //        return this.bind(o, f);
        //    };
        // }
        // 上面的代码是为jquery对象注册了 ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend这几种ajax的事件方法，在jquery.ajax中不同的时刻都会触发这些事件。
        // 当然我们也可以采用s.global=false来设定不触发这些事件。
        // 因为这是全局的，其设计的目的就是为了在这些时候能以某种形式来告诉用户ajax的进行的状态。
        // 如在ajaxStart的时候，我们可能通过一个topest的div层（加上遮罩的效果）的元素注册一个ajaxstart事件的处理方法。
        // 该方法就是显示这个层和显示“你的数据正在提交。。。”这个的提示。
        // Watch for a new set of requests
        if ( s.global && ! jQuery.active++ ) {
            // 发起一个ajax请求，全局的ajax计数器jQuery.active加1，表示当前激活的ajax请求数量，完成一个ajax请求就会将jQuery.active减1
            jQuery.event.trigger( "ajaxStart" );
        }

        // 当http协议或者是host不同时，作为跨域远程ajax调用
        // Matches an absolute URL, and saves the domain
        // rurl = /^(\w+:)?\/\/([^\/?#]+)/
        var parts = rurl.exec( s.url ),
            remote = parts && (parts[1] && parts[1] !== location.protocol || parts[2] !== location.host);

        // If we're requesting a remote document
        // and trying to load JSON or Script with a GET
        if ( s.dataType === "script" && type === "GET" && remote ) {
            // ajax是不能跨域的，所以使用
            var head = document.getElementsByTagName("head")[0] || document.documentElement;
            var script = document.createElement("script");
            script.src = s.url;
            if ( s.scriptCharset ) {
                script.charset = s.scriptCharset;
            }

            // 如果datatype不是jsonp，但是url却是跨域的，采用scriptr的onload或onreadystatechange事件来触发回调函数。
            // 对于jsonp类型的ajax请求，会在window[jsonp]方法中调用success()和complete()这二个回调方法
            // 如果是jsonp，插入的head也需要置为null，清理内存
            // Handle Script loading
            if ( !jsonp ) {
                var done = false;

                // Attach handlers for all browsers
                script.onload = script.onreadystatechange = function() {
                    if ( !done && (!this.readyState ||
                            this.readyState === "loaded" || this.readyState === "complete") ) {
                        done = true;
                        success();
                        complete();

                        // 处理IE中内存泄漏问题
                        // Handle memory leak in IE
                        script.onload = script.onreadystatechange = null;
                        if ( head && script.parentNode ) {
                            head.removeChild( script );
                        }
                    }
                };
            }

            // 将head插入到head头最前面
            // Use insertBefore instead of appendChild  to circumvent an IE6 bug.
            // This arises when a base node is used (#2709 and #4378).
            head.insertBefore( script, head.firstChild );

            // 在此处理完所有dataType为script/json/jsonp跨域的ajax请求，非跨域的请求在后面继续处理
            // We handle everything using the script element injection
            return undefined;
        }

        var requestDone = false;

        // xhr()方法可查看ajaxSettings.xhr()方法中的说明
        // Create the request object
        var xhr = s.xhr();

        if ( !xhr ) {
            return;
        }

        // 创建一个请求的连接，在opera中如果用户名为null会弹出login窗口中。
        // Open the socket
        // Passing null username, generates a login popup on Opera (#2865)
        if ( s.username ) {
            xhr.open(type, s.url, s.async, s.username, s.password);
        } else {
            xhr.open(type, s.url, s.async);
        }

        // firefox3中支持跨域ajax请求，不过需要第三域下配置一个XML说明文件，允许当前域的ajax请求
        // Need an extra try/catch for cross domain requests in Firefox 3
        try {
            // Set the correct header, if data is being sent
            if ( s.data || origSettings && origSettings.contentType ) {
                xhr.setRequestHeader("Content-Type", s.contentType);
            }

            // 设定If-Modified-Since(Last-Modified)和If-None-Match(Etag)
            // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
            if ( s.ifModified ) {
                if ( jQuery.lastModified[s.url] ) {
                    xhr.setRequestHeader("If-Modified-Since", jQuery.lastModified[s.url]);
                }

                if ( jQuery.etag[s.url] ) {
                    xhr.setRequestHeader("If-None-Match", jQuery.etag[s.url]);
                }
            }

            // 这里是为了让服务器能判断这个请求是XMLHttpRequest
            // Set header so the called script knows that it's an XMLHttpRequest
            // Only send the header if it's not a remote XHR
            if ( !remote ) {
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            }

            // 为服务器端设定Accepts header，指能接收的Content-Type，告诉服务器当前请求所能接收的内容类型
            // Set the Accepts header for the server, depending on the dataType
            xhr.setRequestHeader("Accept", s.dataType && s.accepts[ s.dataType ] ?
                s.accepts[ s.dataType ] + ", */*" :
                s.accepts._default );
        } catch(e) {}

        // 拦截方法beforeSend(xhr, s)，我们可以在send之前进行拦截。返回false就不send
        // Allow custom headers/mimetypes and early abort
        if ( s.beforeSend && s.beforeSend.call(callbackContext, xhr, s) === false ) {
            // Handle the global AJAX counter
            // 减少ajax请求的计数器
            // 触发全局的ajaxStop事件
            if ( s.global && ! --jQuery.active ) {
                jQuery.event.trigger( "ajaxStop" );
            }

            // 此处ajaxSend事件还没有触发，xhr.abort()方法还没有被重定义，并且不会调用complete()方法
            // 后面xhr.abort()方法被重新定义，最后会调用complete()方法
            // close opended socket
            xhr.abort();
            return false;
        }

        // 触发全局的或者是s.context环境下的ajaxSend事件
        // 当ajaxSend之后，发生xhr.abort()时，仍会调用complete()方法
        if ( s.global ) {
            trigger("ajaxSend", [xhr, s]);
        }

        // 处理ajax请求的状态，并且利用后面的setTimeout和s.timeout来控制请求是否超时
        // 参数isTimeout可能传入的参数为：abort/timeout
        // Wait for a response to come back
        var onreadystatechange = xhr.onreadystatechange = function( isTimeout ) {
            // The request was aborted
            if ( !xhr || xhr.readyState === 0 || isTimeout === "abort" ) {
                // Opera doesn't call onreadystatechange before this point
                // 后面为了使opera执行xhr.abort之后，能执行complete()回调方法，传入了一个参数，并且在后面调用了方法： onreadystatechange( "abort" );
                // so we simulate the call
                if ( !requestDone ) {
                    complete();
                }

                requestDone = true;
                if ( xhr ) {
                    // 重置xhr.onreadystatechange为空方法，但onreadystatechange()这个方法还在，可以被后面代码直接调用
                    xhr.onreadystatechange = jQuery.noop;
                }

            // 请求正常完成并返回可用response，或者是请求超时
            // 分析status: tiemout/error/notmodified/success
            // The transfer is complete and the data is available, or the request timed out
            } else if ( !requestDone && xhr && (xhr.readyState === 4 || isTimeout === "timeout") ) {
                requestDone = true;
                xhr.onreadystatechange = jQuery.noop;

                status = isTimeout === "timeout" ?
                    "timeout" :
                    // 如果jQuery.httpSuccess( xhr )方法返回false，则表示ajax请求发生错误
                    !jQuery.httpSuccess( xhr ) ?
                        "error" :
                        s.ifModified && jQuery.httpNotModified( xhr, s.url ) ?
                            "notmodified" :
                            "success";

                var errMsg;

                // 如果success且返回了数据，那么分析这些数据
                if ( status === "success" ) {
                    // Watch for, and catch, XML document parse errors
                    try {
                        // 分析ajax返回的response body，XML文档解析可能会抛异常
                        // process the data (runs the xml through httpData regardless of callback)
                        data = jQuery.httpData( xhr, s.dataType, s );
                    } catch(err) {
                        status = "parsererror";
                        errMsg = err;
                    }
                }

                // 分析数据成功之后,进行last-modified和success的处理。
                // Make sure that the request was successful or notmodified
                if ( status === "success" || status === "notmodified" ) {
                    // JSONP handles its own success callback
                    if ( !jsonp ) {
                        success();
                    }
                } else {
                    // 调用jQuery.handleError处理ajax请求发生错误的情况，可以在ajax请求的options中传入error方法，并由handleError方法来调用
                    jQuery.handleError(s, xhr, status, errMsg);
                }

                // 除了在beforeSend()方法返回false不触发complete()回调方法，ajax请求不论成功失败都会触发ajax请求options中的complete()回调方法
                // Fire the complete handlers
                complete();

                // ajax请求超时处理，调用xhr.abort()方法，此处xhr.abort()方法已经被重写，执行之后会调用complete()方法
                if ( isTimeout === "timeout" ) {
                    xhr.abort();
                }

                // Stop memory leaks
                if ( s.async ) {
                    xhr = null;
                }
            }
        };
        // 结束ajax回调方法xhr.onreadystatechange()的定义

        // 在ajaxSend事件之后，重定义xhr.abort()方法，并且通过调用onreadystatechange("abort")方法，调用complete()方法
        // Override the abort handler, if we can (IE doesn't allow it, but that's OK)
        // Opera doesn't fire onreadystatechange at all on abort
        try {
            var oldAbort = xhr.abort;
            xhr.abort = function() {
                if ( xhr ) {
                    oldAbort.call( xhr );
                }

                // 直接调用前面定义的onreadystatechange()方法，并传入参数"abort"，使之调用complete()方法
                onreadystatechange( "abort" );
            };
        } catch(e) { }

        // Timeout checker
        if ( s.async && s.timeout > 0 ) {
            setTimeout(function() {
                // Check to see if the request is still happening
                if ( xhr && !requestDone ) {
                    // 当指定的s.timeout时间内，ajax请求仍然没有完成时，调用onreadystatechange("timeout")方法，执行被重写过的xhr.abort()方法
                    onreadystatechange( "timeout" );
                }
            }, s.timeout);
        }

        // 在此处真正发起ajax请求
        // Send the data
        try {
            xhr.send( type === "POST" || type === "PUT" || type === "DELETE" ? s.data : null );
        } catch(e) {
            jQuery.handleError(s, xhr, null, e);
            // 如果发生异常，而没有通过onreadystatechange()方法调用complete()方法，在这里保证complete()方法被正确执行
            // Fire the complete handlers
            complete();
        }

        // firefox 1.5 doesn't fire statechange for sync requests
        if ( !s.async ) {
            onreadystatechange();
        }

        // 下面的success()/complete()/trigger()方法，会在进入jQuery.ajax()方法体后先被解析
        // 触发ajax请求时，参数中传入的success回调方法和全局(或者是指定运行环境)的ajaxSuccess事件
        function success() {
            // If a local callback was specified, fire it and pass it the data
            if ( s.success ) {
                s.success.call( callbackContext, data, status, xhr );
            }

            // Fire the global callback
            if ( s.global ) {
                trigger( "ajaxSuccess", [xhr, s] );
            }
        }

        // 触发complete回调和全局(或者是指定运行环境)的ajaxComplete事件
        // 触发全局的ajaxStop事件，并减少一个ajax计数器值
        function complete() {
            // Process result
            if ( s.complete ) {
                s.complete.call( callbackContext, xhr, status);
            }

            // 在完成ajax请求options中的complete()回调方法之后，触发全局的ajaxComplete事件
            // The request was completed
            if ( s.global ) {
                trigger( "ajaxComplete", [xhr, s] );
            }

            // 这里要注意 "! --jQuery.active" 这句代码，只有当激活的ajax请求全部执行完成之后，jQuery.active == 0时才会触发全局的ajaxStop事件
            // Handle the global AJAX counter
            if ( s.global && ! --jQuery.active ) {
                jQuery.event.trigger( "ajaxStop" );
            }
        }

        // 触发type类型的全局ajax事件
        function trigger(type, args) {
            (s.context ? jQuery(s.context) : jQuery.event).trigger(type, args);
        }

        // 返回xhr，方便后续对这个对象进一步操作
        // return XMLHttpRequest to allow aborting the request etc.
        return xhr;
    }
}
{% endhighlight %}

一些jQuery.ajax相关的变量和正则表达式定义：
{% highlight javascript %}
var jsc = now(),
    // rscript二个尖括号被我转义过，注意
    rscript = /<script(.|\s)*?\/script>/gi,
    rselectTextarea = /select|textarea/i,
    rinput = /color|date|datetime|email|hidden|month|number|password|range|search|tel|text|time|url|week/i,
    jsre = /=\?(&|$)/,
    rquery = /\?/,
    rts = /(\?|&)_=.*?(&|$)/,
    rurl = /^(\w+:)?\/\/([^\/?#]+)/,
    r20 = /%20/g;
{% endhighlight %}

jQuery.fn.load方法定义：
{% highlight javascript %}
// Keep a copy of the old load method
var _load = jQuery.fn.load;
jQuery.fn.extend({
    // .load( url, [ data ], [ callback(responseText, textStatus, XMLHttpRequest) ] )
    // url: A string containing the URL to which the request is sent.
    // data: A map or string that is sent to the server with the request.
    // callback(responseText, textStatus, XMLHttpRequest): A callback function that is executed when the request completes.
    // .load()方法已经有一个默认的complete回调方法，将服务器加载的内容插入当前jquery对象匹配的DOM对象中，是最简单的一个ajax方法
    // This method is the simplest way to fetch data from the server. It is roughly equivalent to $.get(url, data, success) except that it is a method rather than global function and it has an implicit callback function. When a successful response is detected (i.e. when textStatus is "success" or "notmodified"), .load() sets the HTML contents of the matched element to the returned data.
    load: function( url, params, callback ) {
        if ( typeof url !== "string" ) {
            return _load.call( this, url );

        // 如果jquery对象没有匹配到任何DOM对象，则不会发起Ajax请求
        // Don't do a request if no elements are being requested
        } else if ( !this.length ) {
            return this;
        }

        // Loading Page Fragments
        // The .load() method, unlike $.get(), allows us to specify a portion of the remote document to be inserted. This is achieved with a special syntax for the url parameter. If one or more space characters are included in the string, the portion of the string following the first space is assumed to be a jQuery selector that determines the content to be loaded.
        // We could modify the example above to use only part of the document that is fetched:
        // $('#result').load('ajax/test.html #container');
        // When this method executes, it retrieves the content of ajax/test.html, but then jQuery parses the returned document to find the element with an ID of container. This element, along with its contents, is inserted into the element with an ID of result, and the rest of the retrieved document is discarded.
        // Note that the document retrieved cannot be a full HTML document; that is, it cannot include (for example) <html>, <title>, or <head> elements. jQuery uses the browser's innerHTML property on a <div> element to parse the document, and most browsers will not allow non-body elements to be parsed in this way.
        // .load()方法中传入的url字符串中可以用空格做为分隔符，空格前面的为url，后面的则作为url返回页面的selector
        // 将load得到的response body根据这些selector匹配后，再将匹配的元素插入当前jquery对象匹配的元素中
        var off = url.indexOf(" ");
        if ( off >= 0 ) {
            var selector = url.slice(off, url.length);
            url = url.slice(0, off);
        }

        // Default to a GET request
        var type = "GET";

        // If the second parameter was provided
        if ( params ) {
            // If it's a function
            if ( jQuery.isFunction( params ) ) {
                // We assume that it's the callback
                callback = params;
                params = null;

            // Otherwise, build a param string
            } else if ( typeof params === "object" ) {
                // 如果传入的params为一个object，则将之序列化为post的body体，并且.load()的ajax请求以POST方式提交
                params = jQuery.param( params, jQuery.ajaxSettings.traditional );
                type = "POST";
            }
        }

        var self = this;

        // Request the remote document
        jQuery.ajax({
            url: url,
            type: type,
            dataType: "html",
            data: params,
            complete: function( res, status ) {
                // If successful, inject the HTML into all the matched elements
                if ( status === "success" || status === "notmodified" ) {
                    // See if a selector was specified
                    // 是否从第一个字符串参数中提取到selector
                    self.html( selector ?
                        // Create a dummy div to hold the results
                        jQuery("< div />")
                            // inject the contents of the document in, removing the scripts
                            // to avoid any 'Permission Denied' errors in IE
                            // rscript = /<script(.|\s)*?\/script>/gi，过滤掉返回文本中的脚本内容
                            .append(res.responseText.replace(rscript, ""))

                            // 从ajax返回的文本中，找到匹配空格后面的selector部分的元素，并将这些元素添加到当前jquery对象匹配的元素中
                            // Locate the specified elements
                            .find(selector)
                        :
                        // If not, just inject the full result
                        res.responseText );
                }

                // 执行load完成之后的回调方法
                if ( callback ) {
                    self.each( callback, [res.responseText, status, res] );
                }
            }
        });

        return this;
    },

    // .serialize() Returns: String
    // Description: Encode a set of form elements as a string for submission.
    // For a form element's value to be included in the serialized string, the element must have a name attribute.
    // Data from file select elements is not serialized.
    // 将form表单中的元素name/value值拼成一个字符串(类似a=1&b=2&c=3&d=4&e=5&e=6)，用于ajax表单提交时使用
    serialize: function() {
        return jQuery.param(this.serializeArray());
    },

    // .serializeArray() Returns: Array
    // Description: Encode a set of form elements as an array of names and values.
    // This method creates a JavaScript array of objects, ready to be encoded as a JSON string.
    // It operates on a jQuery object representing a set of form elements.
    // 将form表单中的元素键值对放在一个数组中，其中e因为是多选select，所以有二个值，如:
    // [ { name: "a", value: 1 }, { name: "b", value: 2 }, { name: "c", value: 3 }, { name: "d", value: 4 }, { name: "e", value: 5 }, { name: "e", value: 6 } ]
    serializeArray: function() {
        return this.map(function() {
            return this.elements ? jQuery.makeArray(this.elements) : this;
        })
        .filter(function() {
            // 过滤掉form表单中被disabled，unchecked的元素的键值对，file控件也会被过滤掉，ajax不能进行文件上传操作，文件上传需要借助iframe
            return this.name && !this.disabled &&
                // rselectTextarea = /select|textarea/i,
                (this.checked || rselectTextarea.test(this.nodeName) ||
                    // rinput = /color|date|datetime|email|hidden|month|number|password|range|search|tel|text|time|url|week/i,
                    rinput.test(this.type));
        })
        .map(function( i, elem ) {
            var val = jQuery(this).val();

            return val == null ?
                null :
                // 如果表单元素的值是一个数组，如multi-select和checkbox多选
                jQuery.isArray(val) ?
                    jQuery.map( val, function( val, i ) {
                        return { name: elem.name, value: val };
                    }) :
                    // 返回元素的键值对
                    { name: elem.name, value: val };
        }).get();
    }
});
{% endhighlight %}

ajax相关的全局回调方法和事件的定义：
{% highlight javascript %}
// 定义jQuery.fn.ajaxStart()方法，并同时增加ajaxStart事件
// Attach a bunch of functions for handling common AJAX events
jQuery.each( "ajaxStart ajaxStop ajaxComplete ajaxError ajaxSuccess ajaxSend".split(" "), function( i, o ) {
    jQuery.fn[o] = function( f ) {
        // 将ajaxStart事件绑定到当前jquery对象上，当trigger ajaxStart事件时，会执行回调方法: f
        return this.bind(o, f);
    };
});
{% endhighlight %}

jQuery.get()/jQuery.post()/jQuery.getScript()/jQuery.getJSON()等高级ajax方法定义：
{% highlight javascript %}
jQuery.extend({

    // Load data from the server using a HTTP GET request.
    // jQuery.get( url, [ data ], [ callback(data, textStatus, XMLHttpRequest) ], [ dataType ] )
    // url: A string containing the URL to which the request is sent.
    // data: A map or string that is sent to the server with the request.
    // callback(data, textStatus, XMLHttpRequest): A callback function that is executed if the request succeeds.
    // dataType: The type of data expected from the server.
    get: function( url, data, callback, type ) {
        // 如果没有提供data参数，则将后面二个参数位置前移
        // shift arguments if data argument was omited
        if ( jQuery.isFunction( data ) ) {
            type = type || callback;
            callback = data;
            data = null;
        }

        return jQuery.ajax({
            type: "GET",
            url: url,
            data: data,
            success: callback,
            dataType: type
        });
    },

    // Load a JavaScript file from the server using a GET HTTP request, then execute it.
    // jQuery.getScript( url, [ success(data, textStatus) ] )
    // url: A string containing the URL to which the request is sent.
    // success(data, textStatus): A callback function that is executed if the request succeeds.
    getScript: function( url, callback ) {
        return jQuery.get(url, null, callback, "script");
    },

    getJSON: function( url, data, callback ) {
        return jQuery.get(url, data, callback, "json");
    },

    // jQuery.post( url, [ data ], [ success(data, textStatus, XMLHttpRequest) ], [ dataType ] )
    // url: A string containing the URL to which the request is sent.
    // data: A map or string that is sent to the server with the request.
    // success(data, textStatus, XMLHttpRequest): A callback function that is executed if the request succeeds.
    // dataType: The type of data expected from the server.
    post: function( url, data, callback, type ) {
        // 如果没有提供data参数，则将后面二个参数位置前移
        // shift arguments if data argument was omited
        if ( jQuery.isFunction( data ) ) {
            type = type || callback;
            callback = data;
            data = {};
        }

        return jQuery.ajax({
            type: "POST",
            url: url,
            data: data,
            success: callback,
            dataType: type
        });
    },

    // 通过此方法覆写全局的ajax请求的默认参数
    // Description: Set default values for future Ajax requests.
    // jQuery.ajaxSetup( options )
    // options: A set of key/value pairs that configure the default Ajax request. All options are optional.
    ajaxSetup: function( settings ) {
        jQuery.extend( jQuery.ajaxSettings, settings );
    },

    // jQuery.ajaxSettings默认值
    // 可以通过jQuery.ajaxSetup(settings)方法覆写全局的设置，使之对所有ajax请求都生效
    ajaxSettings: {
        url: location.href,
        global: true,
        type: "GET",
        contentType: "application/x-www-form-urlencoded",
        processData: true,
        async: true,
        // timeout: 0,
        // data: null,
        // username: null,
        // password: null,
        // traditional: false,

        // Create the request object; Microsoft failed to properly
        // implement the XMLHttpRequest in IE7 (can't request local files),
        // so we use the ActiveXObject when it is available
        // This function can be overriden by calling jQuery.ajaxSetup
        // 因为IE7中的window.XMLHttpRequest对象实现，对本地file协议的请求不支持，在window.ActiveXObject可用时，就用window.ActiveXObject
        xhr: window.XMLHttpRequest && (window.location.protocol !== "file:" || !window.ActiveXObject) ?
            function() {
                return new window.XMLHttpRequest();
            } :
            function() {
                try {
                    return new window.ActiveXObject("Microsoft.XMLHTTP");
                } catch(e) {}
            },
        // 客户端ajax接受服务器返回的Content-Type的类型
        accepts: {
            xml: "application/xml, text/xml",
            html: "text/html",
            script: "text/javascript, application/javascript",
            json: "application/json, text/javascript",
            text: "text/plain",
            _default: "*/*"
        }
    },

    // 可以在ajax请求的options里传进来一个error方法，在ajax请求发生错误之后运行此方法
    handleError: function( s, xhr, status, e ) {
        // If a local callback was specified, fire it
        if ( s.error ) {
            // 为当前的ajax请求指定一个error回调方法，并在ajax发生错误之后调用之
            s.error.call( s.context || s, xhr, status, e );
        }

        // 同时触发全局设置的ajaxError事件
        // Fire the global callback
        if ( s.global ) {
            (s.context ? jQuery(s.context) : jQuery.event).trigger( "ajaxError", [xhr, s, e] );
        }
    },

    // 当前激活的ajax请求数量
    // 这个计数器是用于控制全局的ajaxStop事件: if ( s.global && ! --jQuery.active )
    // Counter for holding the number of active queries
    active: 0,

    // 根据返回状态status，判断ajax请求是否成功
    // 对IE和Opera返回的状态做了特殊处理
    // Determines if an XMLHttpRequest was successful or not
    httpSuccess: function( xhr ) {
        try {
            // IE error sometimes returns 1223 when it should be 204 so treat it as success, see #1450
            return !xhr.status && location.protocol === "file:" ||
                // Opera returns 0 when status is 304
                ( xhr.status >= 200 && xhr.status < 300 ) ||
                xhr.status === 304 || xhr.status === 1223 || xhr.status === 0;
        } catch(e) {}

        return false;
    },

    // httpNotModified()方法获取ajax请求的response headers中的Etag/Last-Modified，并设置到jQuery.etag/jQuery.lastModified这二个缓存对象中
    // Determines if an XMLHttpRequest returns NotModified
    httpNotModified: function( xhr, url ) {
        var lastModified = xhr.getResponseHeader("Last-Modified"),
            etag = xhr.getResponseHeader("Etag");

        if ( lastModified ) {
            jQuery.lastModified[url] = lastModified;
        }

        if ( etag ) {
            jQuery.etag[url] = etag;
        }

        // 当服务器端返回status为304(Opera为0)时，表示当前请求的内容没有被修改，浏览器直接从缓存里获取页面内容
        // Opera returns 0 when status is 304
        return xhr.status === 304 || xhr.status === 0;
    },

    // ajax请求的options中，可以设置一个.dataFilter( data, type )方法，用于处理ajax的response body
    // httpData()第二个参数type即s.dataType，后面第三个参数又传了整个s对象进来，所以第二个参数其实无必要放在方法参数中
    httpData: function( xhr, type, s ) {
        var ct = xhr.getResponseHeader("content-type") || "",
            xml = type === "xml" || !type && ct.indexOf("xml") >= 0,
            // 如果response headers中的Content-Type为xml，或者ajax请求的options中dataType为xml时，返回xhr.responseXML，其他返回xhr.responseText
            data = xml ? xhr.responseXML : xhr.responseText;

        if ( xml && data.documentElement.nodeName === "parsererror" ) {
            jQuery.error( "parsererror" );
        }

        // Allow a pre-filtering function to sanitize the response
        // s is checked to keep backwards compatibility
        if ( s && s.dataFilter ) {
            // 利用.dataFilter( data, type )方法对response body进行预处理
            data = s.dataFilter( data, type );
        }

        // The filter can actually parse the response
        if ( typeof data === "string" ) {
            // Get the JavaScript object, if JSON is used.
            if ( type === "json" || !type && ct.indexOf("json") >= 0 ) {
                // 如果ajax请求返回的是json的返回类型，调用jQuery.parseJSON( data )方法处理data
                data = jQuery.parseJSON( data );

            // If the type is "script", eval it in global context
            } else if ( type === "script" || !type && ct.indexOf("javascript") >= 0 ) {
                // 如果s.dataType设置为script，或者是ajax请求返回的response headers中指明Content-Type为javascript类型时，使用jQuery.globalEval( data )方法运行返回的data
                jQuery.globalEval( data );
            }
        }

        return data;
    },

    // Create a serialized representation of an array or object, suitable for use in a URL query string or Ajax request.
    // jQuery.param( obj, traditional )
    // obj: An array or object to serialize.
    // traditional: A Boolean indicating whether to perform a traditional "shallow" serialization.

    // Serialize an array of form elements or a set of
    // key/values into a query string
    param: function( a, traditional ) {
        var s = [];

        // Set traditional to true for jQuery >= 1.3.2 behavior.
        if ( traditional === undefined ) {
            traditional = jQuery.ajaxSettings.traditional;
        }

        // 如果参数a传入的是一个form elements的数组，或者是form element的jquery对象
        // 将其键值对拼成一个"="号连接的字符串，放到s数组中，最后合并字符串后用于ajax请求时的post body或者跟在get请求的URL后面
        // If an array was passed in, assume that it is an array of form elements.
        if ( jQuery.isArray(a) || a.jquery ) {
            // Serialize the form elements
            jQuery.each( a, function() {
                add( this.name, this.value );
            });
        } else {
            // 如果传入的参数a是一个object直接量，调用buildParams()方法构造字符串，并放到s数组中
            // If traditional, encode the "old" way (the way 1.3.2 or older
            // did it), otherwise encode params recursively.
            for ( var prefix in a ) {
                buildParams( prefix, a[prefix] );
            }
        }

        // Return the resulting serialization
        return s.join("&").replace(r20, "+");

        // 用于构造key=value结构的字符串，对于traditional为true与false的区别，可以查看后面myObject的测试例子(即官网上的例子)
        function buildParams( prefix, obj ) {
            if ( jQuery.isArray(obj) ) {
                // Serialize array item.
                jQuery.each( obj, function( i, v ) {
                    if ( traditional || /\[\]$/.test( prefix ) ) {
                        // Treat each array item as a scalar.
                        // 当traditional为true时，将数组作为一个直接量构建key=value字符串
                        add( prefix, v );
                    } else {
                        // If array item is non-scalar (array or object), encode its
                        // numeric index to resolve deserialization ambiguity issues.
                        // Note that rack (as of 1.0.0) can't currently deserialize
                        // nested arrays properly, and attempting to do so may cause
                        // a server error. Possible fixes are to modify rack's
                        // deserialization algorithm or to provide an option or flag
                        // to force array serialization to be shallow.
                        // obj是数组时，如果数组中value是object/array时，生成的key会以方括号加上index值形式作为key
                        // 并且继续迭代调用buildParams()方法构建key，直到value为非object/array的直接量为止。
                        // 如果value不是object/value，则以"prefix+[]"形式作为key，如"b[]"这种形式
                        buildParams( prefix + "[" + ( typeof v === "object" || jQuery.isArray(v) ? i : "" ) + "]", v );
                    }
                });

            } else if ( !traditional && obj != null && typeof obj === "object" ) {
                // Serialize object item.
                // 当obj是{}形式的object直接量，则以"prefix[k]"形式作为key，如果value为object/array，迭代调用buildParams()方法
                jQuery.each( obj, function( k, v ) {
                    buildParams( prefix + "[" + k + "]", v );
                });

            } else {
                // Serialize scalar item.
                // 当obj不再是object/array，调用add方法构建key=value字符串
                // 或者当obj不是array，并且traditional为true时，也直接调用add方法构建key=value字符串，其中当value为object直接量时，用encodeURIComponent()方法返回"%5Bobject%20Object%5D"，用decodeURIComponent("%5Bobject%20Object%5D")可得到"[object Object]"，因为jQuery.param()方法最后返回值中将"%20"替换为"+"了，所以jQuery.param()方法返回值中表现为"%5Bobject+Object%5D"
                add( prefix, obj );
            }
        }

        function add( key, value ) {
            // If value is a function, invoke it and return its value
            value = jQuery.isFunction(value) ? value() : value;
            // key/value都使用encodeURIComponent()方法转义，转义后的"%20"空格会被替换为"+"
            s[ s.length ] = encodeURIComponent(key) + "=" + encodeURIComponent(value);
        }
    }
});
{% endhighlight %}


jQuery中ajax请求的options参数，关于beforeSend, error, dataFilter, success, complete这些ajax回调方法的说明

在ajax请求的options中可以设置这5个回调方法： beforeSend, error, dataFilter, success and complete
分别会在ajax请求的不同阶段中调用，如下说明：

> * beforeSend(xhr, options): 在ajax请求发出前被调用，如果返回false，可以取消ajax请求，并触发全局ajaxStop事件。
> * error(xhr, status, errMsg): 在ajax请求失败的时候被调用，并触发全局的ajaxError事件。
> * dataFilter(data, dataType): 在ajax请求返回success之后，用此方法对ajax请求返回的response body作预处理，并且必须返回一个新的data作为ajax请求的返回结果。
> * success(data, status, xhr): 在ajsx请求成功后调用此回调方法。
> * complete(xhr, status): 在ajax请求完成之后运行，注意此方法除了在beforeSend()回调返回false，取消ajax请求之后不触发，其他无论ajax请求成功失败都会调用。
