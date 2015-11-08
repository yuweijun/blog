---
layout: post
title: "jquery-1.4.2 support部分源码分析"
date: "Fri Aug 01 2014 22:49:32 GMT+0800 (CST)"
categories: jquery
---

Event detection without browser sniffing
----------------------------------------

> Two more events are left to be handled: submit and change. Before jQuery applies fix for these two events, jQuery needs a way to detect if a browser allows submit and change events to bubble or not. jQuery team does not favor browser sniffing. So how to go about detecting event support without browser sniffing.
>
> Juriy Zaytsev posted an excellent blog titled Detecting event support without browser sniffing . Here is the a short and concise way he proposes to find out if an event is supported by a browser.

{% highlight javascript %}
// http://perfectionkills.com/detecting-event-support-without-browser-sniffing/
// 此文作者只是用下面这个方法来检查浏览器是否支持指定的事件类型，如input.onchange/form.onsubmit等，并不是用于事件是否支持冒泡的检查
// 不过jQuery利用这个方法，来检查浏览器中的change和submit这2个事件类型是否支持事件冒泡，因为在IE中会return false，而firefox却是返回true
// 但需要注意不能用这个方法检查其他的事件类型，如blur/focus，这些事件不支持事件冒泡
var isEventSupported = (function(){
    var TAGNAMES = {
        'select':'input','change':'input',
        'submit':'form','reset':'form',
        'error':'img','load':'img','abort':'img'
    }
    function isEventSupported(eventName) {
        var el = document.createElement(TAGNAMES[eventName] || 'div');
        eventName = 'on' + eventName;
        var isSupported = (eventName in el);
        if (!isSupported) {
            el.setAttribute(eventName, 'return;');
            isSupported = typeof el[eventName] == 'function';
        }
        el = null;
        return isSupported;
    }
    return isEventSupported;
})();
{% endhighlight %}

jQuery.support的源码，jQuery代码中不是根据嗅探浏览器，实现功能的兼容性和解决浏览器的兼容性问题，而是检查浏览器的功能特征，来实现功能兼容性。这种做法更加具有适应性，尽量减少因为浏览器的升级，功能改进而需要代码调整。比如到IE9时，IE也会按W3C的标准实现CSS的盒子模型，选择器，ECMAScript规范，那么IE9也会执行jQuery中通用部分的代码，而避免执行和维护那些专门为IE写的代码块，减少代码的维护：

{% highlight javascript %}
(function() {

    jQuery.support = {};

    var root = document.documentElement,
        script = document.createElement("script"),
        div = document.createElement("div"),
        id = "script" + now();

    div.style.display = "none";
    div.innerHTML = "   <link/><table></table><a href='/a' style='color:red;float:left;opacity:.55;'>a</a><input type='checkbox'/>";

    // all应该为[link, table, a /a, input]
    var all = div.getElementsByTagName("*"),
        a = div.getElementsByTagName("a")[0];

    // 最基础的测试，过不了的话，jQuery.support即为空对象
    // Can't get basic test support
    if ( !all || !all.length || !a ) {
        return;
    }

    jQuery.support = {
        // 在IE中，在使用.innerHTML时，会将字符串头尾空格删除
        // 如果头尾空格不会被删除，则jQuery.support.leadingWhitespace为true
        // IE strips leading whitespace when .innerHTML is used
        leadingWhitespace: div.firstChild.nodeType === 3,

        // IE中会为空table自动插入一个tbody
        // 如果tbody不会自动被插入，则jQuery.support.tbody为true
        // Make sure that tbody elements aren't automatically inserted
        // IE will insert them into empty tables
        tbody: !div.getElementsByTagName("tbody").length,

        // 确保在innerHTML后，link元素被正确创建
        // 如果link元素创建成功，则jQuery.support.htmlSerialize为true
        // Make sure that link elements get serialized correctly by innerHTML
        // This requires a wrapper element in IE
        htmlSerialize: !!div.getElementsByTagName("link").length,

        // 获取元素的style属性值，在IE中需要用.cssText()方法获取元素的style属性值
        // 如果可以获取元素的style属性值，则jQuery.support.style为true
        // Get the style information from getAttribute
        // (IE uses .cssText insted)
        style: /red/.test( a.getAttribute("style") ),

        // 确保url没有被浏览器重写，可获取url原始值，IE中会修改url值
        // 如果可以返回url原始值，则jQuery.support.hrefNormalized为true
        // Make sure that URLs aren't manipulated
        // (IE normalizes it by default)
        hrefNormalized: a.getAttribute("href") === "/a",

        // 检查是否支持opacity，在IE中是通过filter来控制opacity的
        // 如果支持opacity，则jQuery.support.opacity为true
        // Make sure that element opacity exists
        // (IE uses filter instead)
        // Use a regex to work around a WebKit issue. See #5145
        opacity: /^0.55$/.test( a.style.opacity ),

        // CSS中的float属性，在javascript中对应的属性名为cssFloat，而IE使用styleFloat代替cssFloat
        // 如查是使用cssFloat，则jQuery.support.cssFloat为true
        // Verify style float existence
        // (IE uses styleFloat instead of cssFloat)
        cssFloat: !!a.style.cssFloat,

        // 检查checkbox的默认值是否为"on"
        // 如果默认值为"on"，则jQuery.support.checkOn为true
        // Make sure that if no value is specified for a checkbox
        // that it defaults to "on".
        // (WebKit defaults to "" instead)
        checkOn: div.getElementsByTagName("input")[0].value === "on",

        // 对于单选select组件，总有一个option被默认选中
        // 如果有option被选中，则jQuery.support.optSelected为true
        // Make sure that a selected-by-default option has a working selected property.
        // (WebKit defaults to false instead of true, IE too, if it's in an optgroup)
        optSelected: document.createElement("select").appendChild( document.createElement("option") ).selected,

        // .removeChild()方法返回被删除的元素，并且元素的parentNode为null
        parentNode: div.removeChild( div.appendChild( document.createElement("div") ) ).parentNode === null,

        // Will be defined later
        deleteExpando: true,
        checkClone: false,
        scriptEval: false,
        // 在IE中，clone一个元素会将元素上的事件监听器，也一起复制到新clone出来的元素上，所以在IE上，jQuery.support.noCloneEvent为false
        noCloneEvent: true,
        boxModel: null
    };

    script.type = "text/javascript";
    try {
        script.appendChild( document.createTextNode( "window." + id + "=1;" ) );
    } catch(e) {}

    root.insertBefore( script, root.firstChild );

    // 通过为script元素appendChild/createTextNode，可以插入脚本内容，并且被浏览器解析执行，在IE中使用.text()方法达到此目的
    // 如果浏览器支持appendChild/createTextNode为script元素添加脚本，则jQuery.support.scriptEval为true
    // Make sure that the execution of code works by injecting a script
    // tag with appendChild/createTextNode
    // (IE doesn't support this, fails, and uses .text instead)
    if ( window[ id ] ) {
        jQuery.support.scriptEval = true;
        delete window[ id ];
    }

    // Test to see if it's possible to delete an expando from an element
    // Fails in Internet Explorer
    try {
        // 可以删除对象上的一个不存在的属性名，firefox返回true，IE会抛出异常
        delete script.test;
    } catch(e) {
        // 有异常抛出，说明不支持delete对象一个不存在的属性
        jQuery.support.deleteExpando = false;
    }

    root.removeChild( script );

    // IE的.cloneNode()方法会复制对象上的事件，如果将clone出的对象删除之后，原来的被clone的元素上的事件监听器也被删除了，所以以下代码可以在chrome/firefox中运行，却不能在IE中运行
    // 参见jQuery源码manipulation部分中的jQuery.fn.clone()方法的说明：
    // IE copies events bound via attachEvent when using cloneNode. Calling detachEvent on the clone will also remove the events from the orignal, In order to get around this, we use innerHTML.  Unfortunately, this means some modifications to attributes in IE that are actually only stored as properties will not be copied (such as the the name attribute on an input).
    if ( div.attachEvent && div.fireEvent ) {
        div.attachEvent("onclick", function click() {
            // Cloning a node shouldn't copy over any
            // bound event handlers (IE does this)
            jQuery.support.noCloneEvent = false;
            div.detachEvent("onclick", click);
        });
        div.cloneNode(true).fireEvent("onclick");
    }

    div = document.createElement("div");
    div.innerHTML = "<input type='radio' name='radiotest' checked='checked'/>";

    var fragment = document.createDocumentFragment();
    fragment.appendChild( div.firstChild );

    // Node.cloneNode(deep): The cloneNode() method makes and returns a copy of the node on which it is called. If passed the argument true, it recursively clones all descendants of the node as well. Otherwise, it clones only the node and none of its children. The returned node is not part of the document tree, and its parentNode property is null. When an Element node is cloned, all of its attributes are also cloned. Note, however, that event-listener functions registered on a node are not cloned.

    // 检查.cloneNode是否将radio的状态也复制下来，如果状态也被复制下来，则jQuery.support.checkClone为true
    // WebKit doesn't clone checked state correctly in fragments
    jQuery.support.checkClone = fragment.cloneNode(true).cloneNode(true).lastChild.checked;

    // Figure out if the W3C box model works as expected
    // document.body must exist before we can do this
    jQuery(function() {
        var div = document.createElement("div");
        div.style.width = div.style.paddingLeft = "1px";

        document.body.appendChild( div );
        // 标准的盒子模型中，width是padding box里content的宽度，offsetWidth为paddingLeft + width + paddingRight的像素值
        // 如果div.offsetWidth为2，则说明浏览器默认盒子模型是使用w3c的标准盒子模型
        jQuery.boxModel = jQuery.support.boxModel = div.offsetWidth === 2;
        // 将div从document.body中移除，并将其隐藏，最后将div置为null，释放内存
        document.body.removeChild( div ).style.display = 'none';

        div = null;
    });

    // Technique from Juriy Zaytsev
    // http://thinkweb2.com/projects/prototype/detecting-event-support-without-browser-sniffing/
    var eventSupported = function( eventName ) {
        var el = document.createElement("div");
        eventName = "on" + eventName;

        // 在safari/chrome中直接通过eventName in el获得isSupported为true
        var isSupported = (eventName in el);
        if ( !isSupported ) {
            // 在firefox中通过el.setAttribute()设置eventName属性之后判断出isSupported为true
            el.setAttribute(eventName, "return;");
            isSupported = typeof el[eventName] === "function";
        }
        el = null;

        return isSupported;
    };

    // eventSupported 方法只用于测试submit/change二个事件类型，不要用于blur/focus/load/unload这四个不支持冒泡的事件类型
    jQuery.support.submitBubbles = eventSupported("submit");
    jQuery.support.changeBubbles = eventSupported("change");

    // release memory in IE
    root = script = div = all = a = null;
})();

// 用于jQuery.attr(elem, name, value, pass)方法，用于修正html属性名与javascript中的属性名对应关系
// 参考jQuery.attr的说明
jQuery.props = {
    "for": "htmlFor",
    "class": "className",
    readonly: "readOnly",
    maxlength: "maxLength",
    cellspacing: "cellSpacing",
    rowspan: "rowSpan",
    colspan: "colSpan",
    tabindex: "tabIndex",
    usemap: "useMap",
    frameborder: "frameBorder"
};
{% endhighlight %}


