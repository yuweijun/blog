---
layout: post
title:  "jQuery-1.4.2 dimensions部分源码分析"
date: "Wed Jul 31 2014 21:45:36 GMT+0800 (CST)"
categories: jquery src
---

jQuery.fn.width()和jQuery.fn.css("width")的区别
-----------------------------------------------

注意jQuery.fn.width()方法返回的是不带px单位的纯宽度数值，而jQuery.fn.css("width")方法返回的则是通过style计算得到的是带px单位的字符串，并且在chrome/safari中，这二个方法返回的数值也会因为DOM对象出现滚动条而不一样：jQuery.fn.width()是通过offsetWidth减去border和padding的宽度得到的，所以不会受滚动条的影响，而jQuery.fn.css("width")是由window.getComputedStyle()方法计算得到。

这二个方法运行结果的差异可以从`jQuery.fn.offset()`源码阅读笔记中查看。

{% highlight javascript %}
// Create innerHeight, innerWidth, outerHeight and outerWidth methods
jQuery.each([ "Height", "Width" ], function( i, name ) {

    var type = name.toLowerCase();

    // 因为jQuery.curCSS()方法中的elem.ownerDocument.defaultView这一行代码的约束，使得innerHeight, innerWidth, outerHeight, outerWidth这四个方法不能用于window/document二个对象上，使用width/height代替之
    // Get the current computed height for the first element in the set of matched elements, including padding but not border.
    // 计算包括padding在内的元素宽高的像素值(对比clientHeight/clientWidth，如果没有滚动条出现，clientWidth和innerWidth这二个值是相同的)
    // innerHeight and innerWidth
    jQuery.fn["inner" + name] = function() {
        return this[0] ?
            // 计算jQuery匹配到的第一个元素
            jQuery.css( this[0], type, false, "padding" ) :
            null;
    };

    // Get the current computed height for the first element in the set of matched elements, including padding and border.
    // 计算包括padding和border的元素宽高像素值(参考offsetWidth/offsetHeight)
    // 如果传入的margin为true，则计算包括margin的宽高
    // outerHeight and outerWidth
    jQuery.fn["outer" + name] = function( margin ) {
        return this[0] ?
            // 通过jQuery.css和jQuery.curCSS方法计算第一个匹配元素的宽高
            jQuery.css( this[0], type, false, margin ? "margin" : "border" ) :
            null;
    };

    // 区别jQuery.fn.css('height')和jQuery.fn.height()方法：
    // The difference between .css('height') and .height() is that the latter returns a unit-less pixel value (for example, 400) while the former returns a value with units intact (for example, 400px).
    // The .height() method is recommended when an element's height needs to be used in a mathematical calculation.
    // $(window).width();   // returns width of browser viewport
    // $(document).width(); // returns width of HTML document
    // $(window).height();   // returns height of browser viewport
    // $(document).height(); // returns height of HTML document
    // 参数size: 可以传一个function(index, height)方法作为size参数
    jQuery.fn[ type ] = function( size ) {
        // Get window width or height
        var elem = this[0];
        if ( !elem ) {
            // $().heigth()
            return size == null ? null : this;
        }

        if ( jQuery.isFunction( size ) ) {
            return this.each(function( i ) {
                var self = jQuery( this );
                // 如果size是方法，则以function(index, oldHeigthValue)形式调用此方法，将产生的结果作为新值赋给height
                self[ type ]( size.call( this, i, self[ type ]() ) );
            });
        }

        // 这个返回值用了3个3目运算符
        return ("scrollTo" in elem && elem.document) ? // does it walk and quack like a window?
            // Everyone else use document.documentElement or document.body depending on Quirks vs Standards mode
            elem.document.compatMode === "CSS1Compat" && elem.document.documentElement[ "client" + name ] ||
            elem.document.body[ "client" + name ]
            // 当elem为window对象时，并且为标准模型下，返回Document.documentElement.clientHeight，即当前的window窗口高度
            // 如果当前文档处于怪异模型下，则返回Document.body.clientHeight，这个值在标准模型中返回的是文件document.body的高度，在怪异模型中返回window窗口高度
            :
            // Get document width or height
            (elem.nodeType === 9) ? // is it a document
                // Either scroll[Width/Height] or offset[Width/Height], whichever is greater
                Math.max(
                    elem.documentElement["client" + name],
                    elem.body["scroll" + name], elem.documentElement["scroll" + name],
                    elem.body["offset" + name], elem.documentElement["offset" + name]
                )
                // 如果是计算document文档的高度，则从
                // Document.documentElement.clientHeight,
                // Document.documentElement.scrollHeight, Document.documentElement.offsetHeight,
                // Document.body.scrollHeight, Document.body.offsetHeight中取最大值
                :
                // Get or set width or height on the element
                size === undefined ?
                    // Get width or height on the element
                    jQuery.css( elem, type )
                    // 如果是计算elem的宽高，调用jQuery.css()方法并返回值
                    :
                    // 如果是设置elem的宽高属性，则调用jQuery.fn.css()方法并返回jquery对象
                    // Set the width or height on the element (default to pixels if value is unitless)
                    this.css( type, typeof size === "string" ? size : size + "px" );
    };

});
{% endhighlight %}

