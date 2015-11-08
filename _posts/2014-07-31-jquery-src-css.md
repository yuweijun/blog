---
layout: post
title: "jQuery-1.4.2 css部分源码分析"
date: "Wed Jul 31 2014 20:23:36 GMT+0800 (CST)"
categories: jquery
---

CSS 相关的jQuery方法官方文档如下所列，各方法逐一分析：

> .addClass()
>
> Adds the specified class(es) to each of the set of matched elements.
>
> .css()
>
> Get the value of a style property for the first element in the set of matched elements.
>
> .hasClass()
>
> Determine whether any of the matched elements are assigned the given class.
>
> .removeClass()
>
> Remove a single class, multiple classes, or all classes from each element in the set of matched elements.
>
> .toggleClass()
>
> Add or remove one or more classes from each element in the set of matched elements, depending on either the class's presence or the value of the switch argument.

addClass方法除传入一个新的class名为参数外，还在1.4版本中，新加了一个可接收function为参数的addClass:

{% highlight javascript %}
addClass: function( value ) {
    if ( jQuery.isFunction(value) ) {
        return this.each(function(i) {
            var self = jQuery(this);
            // value = function(index, class)
            // A function returning one or more space-separated class names to be added.
            // Receives the index position of the element in the set and the old class value as arguments.
            // 当value为一个方法时，返回一个字符串，作为className被加入所有匹配的元素中，递归调用addClass本身
            // 这个方法接受二个参数，匹配的元素位置和此元素的当前className
            self.addClass( value.call(this, i, self.attr("class")) );
        });
    }

    if ( value && typeof value === "string" ) {
        var classNames = (value || "").split( rspace );

        for ( var i = 0, l = this.length; i < l; i++ ) {
            var elem = this[i];

            if ( elem.nodeType === 1 ) {
                if ( !elem.className ) {
                    // 如果元素没有className，则设置value为此元素的className
                    elem.className = value;
                } else {
                    var className = " " + elem.className + " ", setClass = elem.className;
                    for ( var c = 0, cl = classNames.length; c < cl; c++ ) {
                        // 如果传进来的value中的className在元素中不存在，则将此className加到原来的className后面
                        if ( className.indexOf( " " + classNames[c] + " " ) < 0 ) {
                            setClass += " " + classNames[c];
                        }
                    }
                    elem.className = jQuery.trim( setClass );
                }
            }
        }
    }

    return this;
},
{% endhighlight %}

jQuery.fn.removeClass()方法：
{% highlight javascript %}
removeClass: function( value ) {
    if ( jQuery.isFunction(value) ) {
        return this.each(function(i) {
            var self = jQuery(this);
            // 与addClass一样，当value是一个function时，将value的调用结果作为参数，递归调用自身
            self.removeClass( value.call(this, i, self.attr("class")) );
        });
    }

    if ( (value && typeof value === "string") || value === undefined ) {
        var classNames = (value || "").split(rspace);

        for ( var i = 0, l = this.length; i < l; i++ ) {
            var elem = this[i];

            // 当前elem有className时
            if ( elem.nodeType === 1 && elem.className ) {
                if ( value ) {
                    // 将回车符和制表符替换为空格
                    var className = (" " + elem.className + " ").replace(rclass, " ");
                    for ( var c = 0, cl = classNames.length; c < cl; c++ ) {
                        // 遍历当前value中的每个className，如果元素中出现，将之移除
                        className = className.replace(" " + classNames[c] + " ", " ");
                    }
                    elem.className = jQuery.trim( className );

                } else {
                    elem.className = "";
                }
            }
        }
    }

    return this;
},
{% endhighlight %}

toggleClass方法在attributes中有过分析。不过版本是1.3的，在新的1.4版本中可以传function给toggleClass:
{% highlight javascript %}
// .toggleClass( className, switch )
// className: One or more class names (separated by spaces) to be toggled for each element in the matched set.
// switch: A boolean value to determine whether the class should be added or removed.
toggleClass: function( value, stateVal ) {
    var type = typeof value, isBool = typeof stateVal === "boolean";

    // 当value为一个function时，与addClass/removeClass类似处理
    if ( jQuery.isFunction( value ) ) {
        return this.each(function(i) {
            var self = jQuery(this);
            self.toggleClass( value.call(this, i, self.attr("class"), stateVal), stateVal );
        });
    }

    return this.each(function() {
        if ( type === "string" ) {
            // toggle individual class names
            var className, i = 0, self = jQuery(this),
                state = stateVal,
                classNames = value.split( rspace );

            while ( (className = classNames[ i++ ]) ) {
                // check each className given, space seperated list
                // 如果没有stateVal传进来，则根据self.hasClass(className)来判断是否已经有此className，有则removeClass，无则addClass
                state = isBool ? state : !self.hasClass( className );
                // 根据stateVal进行判断用addClass或者是removeClass，jQuery-1.3版本中添加的
                self[ state ? "addClass" : "removeClass" ]( className );
            }
        } else if ( type === "undefined" || type === "boolean" ) {
            // 如果toggleClass传入一个布尔值，或者是不传参数调用时，toggle整个className
            if ( this.className ) {
                // store className if set
                // 利用jQuery.data将元素原来的className放到此元素的jQuery data中
                jQuery.data( this, "__className__", this.className );
            }

            // toggle whole className
            this.className = this.className || value === false ? "" : jQuery.data( this, "__className__" ) || "";
        }
    });
},
{% endhighlight %}

hasClass方法判断DOM元素是否拥有某个className：
{% highlight javascript %}
// 传入一个className作为参数进行查询
hasClass: function( selector ) {
    var className = " " + selector + " ";
    for ( var i = 0, l = this.length; i < l; i++ ) {
        if ( (" " + this[i].className + " ").replace(rclass, " ").indexOf( className ) > -1 ) {
            return true;
        }
    }

    return false;
},
{% endhighlight %}

jQuery.fn.css()方法的几种用法[css API of jQuery](http://api.jquery.com/css/)：

> .css( propertyName ) Returns: String, Description: Get the value of a style property for the first element in the set of matched elements.
>
> .css( propertyName, value ) propertyName: A CSS property name.  value: A value to set for the property.
>
> .css( propertyName, function(index, value) ) function(index, value): A function returning the value to set. Receives the index position of the element in the set and the old value as arguments.
>
> .css( map ) map: A map of property-value pairs to set.


其中access方法在attributes中也已经有过分析，这个access方法是最外层的那个匿名方法内部的一个方法，所以是被封装在这个闭包中的一个私有方法，外部不能直接访问，但jQuery源码在jQuery.fn.attr/css方法上调用了此方法，但也是比较简单的使用此方法，并没有用到第6个pass参数:
{% highlight javascript %}
// Mutifunctional method to get and set values to a collection
// The value/s can be optionally by executed if its a function
function access( elems, key, value, exec, fn, pass ) {
    var length = elems.length;

    // 如果key是一个object时，则将object展开，递归调用access方法，如使用jQuery.fn.css({color: 'red', background: '#ddd'})
    // Setting many attributes
    if ( typeof key === "object" ) {
        for ( var k in key ) {
            // 在这里将原来的value值当pass使用
            access( elems, k, key[k], exec, fn, value );
        }
        // 这里返回jquery对象
        return elems;
    }

    // Setting one attribute
    if ( value !== undefined ) {
        // Optionally, function values get executed if exec is true
        // exec这个参数是为了控制value这个function是否执行，这里又多了个pass来控制方法是否执行，此处代码写得不好
        exec = !pass && exec && jQuery.isFunction(value);

        for ( var i = 0; i < length; i++ ) {
            // 当value为function时，将其作为DOM对象的方法调用，把其在jquery对象中的位置和原来的属性值传给此方法，将value方法返回值作为value进行属性赋值
            fn( elems[i], key, exec ? value.call( elems[i], i, fn( elems[i], key ) ) : value, pass );
        }

        // 在对属性赋值操作完成后，这里返回jquery对象
        return elems;
    }

    // 如果没有传value值，则调用传入的回调方法fn，返回jquery对象第一个元素的key属性值
    // Getting an attribute
    return length ? fn( elems[0], key ) : undefined;
}
{% endhighlight %}

需要注意jQuery.fn.width()方法返回的是不带px单位的纯宽度数值，而jQuery.fn.css("width")方法返回的则是通过style计算得到的是带px单位的字符串，并且在chrome/safari中，这二个方法返回的数值也会因为DOM对象出现滚动条而不一样：jQuery.fn.width()是通过offsetWidth减去border和padding的宽度得到的，所以不会受滚动条的影响，而jQuery.fn.css("width")是由window.getComputedStyle()方法计算得到。


这二个方法运行结果的差异可以从jQuery.fn.offset()源码阅读笔记中查看。
{% highlight javascript %}
// The .css() method is a convenient way to get a style property from the first matched element, especially in light of the different ways browsers access most of those properties (the getComputedStyle() method in standards-based browsers versus the currentStyle and runtimeStyle properties in Internet Explorer) and the different terms browsers use for certain properties. For example, Internet Explorer's DOM implementation refers to the float property as styleFloat, while W3C standards-compliant browsers refer to it as cssFloat. The .css() method accounts for such differences, producing the same result no matter which term we use.
jQuery.fn.css = function( name, value ) {
    return access( this, name, value, true, function( elem, name, value ) {
        if ( value === undefined ) {
            // 如果只是传了一个参数name，则返回元素此css属性
            return jQuery.curCSS( elem, name );
        }

        // rexclude中的几个CSS属性不需要以px为单位
        if ( typeof value === "number" && !rexclude.test(name) ) {
            value += "px";
        }

        // 如果有传value值，在处理之后调用jQuery.style()方法，并返回其结果
        jQuery.style( elem, name, value );
    });
};
{% endhighlight %}

jQuery.style()和jQuery.curCSS()方法定义如下：
{% highlight javascript %}
jQuery.extend({
    // 为元素elem设置名为name的属性
    style: function( elem, name, value ) {
        // 忽略text和comment节点
        // don't set styles on text and comment nodes
        if ( !elem || elem.nodeType === 3 || elem.nodeType === 8 ) {
            return undefined;
        }

        // 如果width/height属性设置为负值的话，重置Value
        // ignore negative width and height values #1599
        if ( (name === "width" || name === "height") && parseFloat(value) < 0 ) {
            value = undefined;
        }

        var style = elem.style || elem, set = value !== undefined;

        // 处理IE
        // IE uses filters for opacity
        if ( !jQuery.support.opacity && name === "opacity" ) {
            // value有值的情况，进行属性赋值操作
            if ( set ) {
                // IE has trouble with opacity if it does not have layout
                // Force it by setting the zoom level
                style.zoom = 1;

                // Set the alpha filter to set the opacity
                var opacity = parseInt( value, 10 ) + "" === "NaN" ? "" : "alpha(opacity=" + value * 100 + ")";
                var filter = style.filter || jQuery.curCSS( elem, "filter" ) || "";
                style.filter = ralpha.test(filter) ? filter.replace(ralpha, opacity) : opacity;
            }

            return style.filter && style.filter.indexOf("opacity=") >= 0 ?
                (parseFloat( ropacity.exec(style.filter)[1] ) / 100) + "":
                "";
        }

        // 处理float属性
        // Make sure we're using the right name for getting the float value
        if ( rfloat.test( name ) ) {
            name = styleFloat;
        }

        // 将line-height这种属性转为驼峰词lineHeight
        name = name.replace(rdashAlpha, fcamelCase);

        if ( set ) {
            // 有value值，进行style属性赋值
            style[ name ] = value;
        }

        // 最后返回此属性值
        return style[ name ];
    },

    // 这个方法只处理DOM对象的width/height相关属性值，如clientWidth/offsetWidth，返回值为数值，无"px"单位，同时还受extra影响
    // 这个方法主要与CSS中的水平七要素有关
    // jQuery.css()方法在API文档中并没有放出来，主要是jQuery内部在使用
    // extra: border/margin/padding/undefined
    css: function( elem, name, force, extra ) {
        if ( name === "width" || name === "height" ) {
            var val, props = cssShow, which = name === "width" ? cssWidth : cssHeight;

            // 内部方法，用于计算DOM对象的height/width，方法改变了变量作用域链上的val值
            // 如extra为border，则直接返回elem.offsetWidth/offsetHeight
            // 如extra为margin，则将marginLeft/marginRight加到val中
            // 如extra为padding，则计算包括padding的宽高，将elem.offsetWidth减去borderLeft/borderRight
            // 如extra未设置的话，则只是计算width，将减去paddingLeft/paddingRight
            function getWH() {
                val = name === "width" ? elem.offsetWidth : elem.offsetHeight;

                if ( extra === "border" ) {
                    return;
                }

                jQuery.each( which, function() {
                    // 计算DOM对象的paddingLeft/marginLeft/boarderLeftWidth等值
                    if ( !extra ) {
                        // 如extra未设置的话，则只是计算width，将减去paddingLeft/paddingRight
                        val -= parseFloat(jQuery.curCSS( elem, "padding" + this, true)) || 0;
                    }

                    if ( extra === "margin" ) {
                        // 如extra为margin，则将marginLeft/marginRight加到val中
                        val += parseFloat(jQuery.curCSS( elem, "margin" + this, true)) || 0;
                    } else {
                        // 如extra为padding，则计算包括padding的宽高，将elem.offsetWidth减去borderLeftWidth/borderRightWidth
                        val -= parseFloat(jQuery.curCSS( elem, "border" + this + "Width", true)) || 0;
                    }
                });
            }

            if ( elem.offsetWidth !== 0 ) {
                getWH();
            } else {
                jQuery.swap( elem, props, getWH );
            }

            return Math.max(0, Math.round(val));
        }

        return jQuery.curCSS( elem, name, force );
    },

    curCSS: function( elem, name, force ) {
        var ret,
            style = elem.style,
            filter;

        // 处理IE的opacity值
        // IE uses filters for opacity
        if ( !jQuery.support.opacity && name === "opacity" && elem.currentStyle ) {
            ret = ropacity.test(elem.currentStyle.filter || "") ?
                (parseFloat(RegExp.$1) / 100) + "" :
                "";

            return ret === "" ?
                "1" :
                ret;
        }

        // styleFloat = "cssFloat"
        // Make sure we're using the right name for getting the float value
        // styleFloat = "cssFloat"
        if ( rfloat.test( name ) ) {
            name = styleFloat;
        }

        // 使用force=true的话，则强制属性值用getComputedStyle()方法计算
        // 否则可直接从style中获取属性值
        if ( !force && style && style[ name ] ) {
            ret = style[ name ];

        // window.getComputedStyle()方法，在IE中没有实现
        } else if ( getComputedStyle ) {

            // Only "float" is needed here
            if ( rfloat.test( name ) ) {
                name = "float";
            }

            // 将lineHeight之类的驼峰词分为line-height
            name = name.replace( rupper, "-$1" ).toLowerCase();

            // Node.ownerDocument: The Document object of which this Node is a part. For Document nodes, this property is null. Read-only.
            // Document.defaultView: The Window in which the document is displayed.
            var defaultView = elem.ownerDocument.defaultView;

            // document.ownerDocument is null
            // window.ownerDocument is undefined
            if ( !defaultView ) {
                return null;
            }

            // window.getComputedStyle(elt)
            // Returns a read-only Style object that contains all CSS styles (not just inline styles) that apply to the specified document element elt.
            // Positioning attributes such as left, top, and width queried from this computed style object are always returned as pixel values.
            var computedStyle = defaultView.getComputedStyle( elem, null );

            if ( computedStyle ) {
                ret = computedStyle.getPropertyValue( name );
            }

            // We should always get a number back from opacity
            if ( name === "opacity" && ret === "" ) {
                ret = "1";
            }

        //  IE中有Element.currentStyle这个属性，根据Dean Edwards提供的方法返回CSS的值
        } else if ( elem.currentStyle ) {
            var camelCase = name.replace(rdashAlpha, fcamelCase);

            ret = elem.currentStyle[ name ] || elem.currentStyle[ camelCase ];

            // From the awesome hack by Dean Edwards
            // http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

            // If we're not dealing with a regular pixel number
            // but a number that has a weird ending, we need to convert it to pixels
            if ( !rnumpx.test( ret ) && rnum.test( ret ) ) {
                // Remember the original values
                var left = style.left, rsLeft = elem.runtimeStyle.left;

                // Put in the new values to get a computed value out
                elem.runtimeStyle.left = elem.currentStyle.left;
                style.left = camelCase === "fontSize" ? "1em" : (ret || 0);
                ret = style.pixelLeft + "px";

                // Revert the changed values
                style.left = left;
                elem.runtimeStyle.left = rsLeft;
            }
        }

        return ret;
    },

    // A method for quickly swapping in/out CSS properties to get correct calculations
    swap: function( elem, options, callback ) {
        var old = {};

        // Remember the old values, and insert the new ones
        for ( var name in options ) {
            old[ name ] = elem.style[ name ];
            elem.style[ name ] = options[ name ];
        }

        callback.call( elem );

        // Revert the old values
        for ( var name in options ) {
            elem.style[ name ] = old[ name ];
        }
    }
});
{% endhighlight %}

