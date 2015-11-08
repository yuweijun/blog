---
layout: post
title: "jQuery-1.4.2 attribute部分源码分析"
date: "Wed Jul 30 2014 21:30:36 GMT+0800 (CST)"
categories: jquery
---

jQuery.fn.attr方法：

{% highlight javascript %}
attr: function( name, value ) {
    // access(elems, key, value, exec, fn)
    // elems = this, key = name, value = value, exec = true, fn = jQuery.attr
    return access(this, name, value, true, jQuery.attr);
},
{% endhighlight %}

调用access方法：

{% highlight javascript %}
// Mutifunctional method to get and set values to a collection
// The value/s can be optionally by executed if its a function
function access( elems, key, value, exec, fn ) {
    var l = elems.length;

    // Setting many attributes
    if ( typeof key === "object" ) {
            for (var k in key) {
                // 如果key是一个object，递归调用access方法
                access(elems, k, key[k], exec, fn);
            }
        return elems;
    }

    // Setting one attribute
    if (value !== undefined) {
        // Optionally, function values get executed if exec is true
        exec = exec && jQuery.isFunction(value);

        for (var i = 0; i < l; i++) {
            var elem = elems[i],
                val = exec ? value.call(elem, i) : value;
            // 对于jQuery.fn.attr()调用，这里的fn就是jQuery.attr()方法
            fn(elem, key, val);
        }
        return elems;
    }

    // Getting an attribute
    return l ? fn(elems[0], key) : null;
}
{% endhighlight %}

当获取jquery对象的某个属性值时，会调用fn(elems[0], key)，即jQuery.attr()方法：

{% highlight javascript %}
jQuery.extend({
    attr: function( elem, name, value ) {
        // don't set attributes on text and comment nodes
        if (!elem || elem.nodeType == 3 || elem.nodeType == 8)
            return undefined;

        // name可能是jQuery.fn的一个方法，如.text(), .css(), .click()
        // 把value作为name方法的参数进行调用，并返回调用结果
        // 这个可以结合jQuery()方法使用，如下面例子中的text这个key，a标签并没有text属性，将会调用jQuery.fn.text()方法：
        // jQuery('<a/>', { id: 'foo', href: 'http://www.google.com', title: 'a Googler', rel: 'external', text: 'Google!' });
        if ( name in jQuery.fn && name !== "attr" ) {
            return jQuery(elem)[name](value);
        }

        var notxml = elem.nodeType !== 1 || !jQuery.isXMLDoc( elem ),
            // Whether we are setting (or getting)
            set = value !== undefined;

        // Try to normalize/fix the name
        name = notxml && jQuery.props[ name ] || name;

        // Only do all the following if this is a node (faster for style)
        if ( elem.nodeType === 1 ) {

            // These attributes require special treatment
            var special = /href|src|style/.test( name );

            // Safari mis-reports the default selected property of a hidden option
            // Accessing the parent's selectedIndex property fixes it
            if ( name == "selected" && elem.parentNode )
                elem.parentNode.selectedIndex;

            // If applicable, access the attribute via the DOM 0 way
            if ( name in elem && notxml && !special ) {
                if ( set ){
                    // We can't allow the type property to be changed (since it causes problems in IE)
                    if ( name == "type" && /(button|input)/i.test(elem.nodeName) && elem.parentNode )
                        throw "type property can't be changed";

                    elem[ name ] = value;
                }

                // browsers index elements by id/name on forms, give priority to attributes.
                if( jQuery.nodeName( elem, "form" ) && elem.getAttributeNode(name) )
                    return elem.getAttributeNode( name ).nodeValue;

                // elem.tabIndex doesn't always return the correct value when it hasn't been explicitly set
                // http://fluidproject.org/blog/2008/01/09/getting-setting-and-removing-tabindex-values-with-javascript/
                if ( name == "tabIndex" ) {
                    var attributeNode = elem.getAttributeNode( "tabIndex" );
                    return attributeNode && attributeNode.specified
                        ? attributeNode.value
                        : /(button|input|object|select|textarea)/i.test(elem.nodeName)
                            ? 0
                            : /^(a|area)$/i.test(elem.nodeName) && elem.href
                                ? 0
                                : undefined;
                }

                return elem[ name ];
            }

            if ( !jQuery.support.style && notxml && name == "style" ) {
                if ( set )
                    elem.style.cssText = "" + value;

                return elem.style.cssText;
            }

            if ( set )
                // convert the value to a string (all browsers do this but IE) see #1070
                elem.setAttribute( name, "" + value );

            var attr = !jQuery.support.hrefNormalized && notxml && special
                    // Some attributes require a special call on IE
                    ? elem.getAttribute( name, 2 )
                    : elem.getAttribute( name );

            // Non-existent attributes return null, we normalize to undefined
            return attr === null ? undefined : attr;
        }

        // elem is actually elem.style ... set the style
        // Using attr for specific style information is now deprecated. Use style insead.
        return jQuery.style(elem, name, value);
    }
});
{% endhighlight %}

注意其中二句
{% highlight javascript %}
// These attributes require special treatment
var special = /href|src|style/.test( name );
{% endhighlight %}

当传给attr方法第二个参数时，如果不是一个function，则以第二个参数给elem的属性赋值，调用fn(elem, key, val)，即jQuery.attr(elem, key, val)方法。

如果第二个参数是一个function，则将此方法做为当前元素elem的方法调用，参数则为当前elem的index值，用此方法计算返回的值赋给此elem。

{% highlight javascript %}
// Optionally, function values get executed if exec is true
exec = exec && jQuery.isFunction(value);

for (var i = 0; i < l; i++) {
    var elem = elems[i],
        val = exec ? value.call(elem, i) : value;
    fn(elem, key, val);
}
{% endhighlight %}

注意jQuery.attr方法中的这段代码，因为IE的原因，所以不能改变一个input元素的type值。如果要更改type属性，可以进行元素替换。jQuery没有在此进行替换操作，而是扔了一个异常出来给调用者。

{% highlight javascript %}
if ( set ){
    // We can't allow the type property to be changed (since it causes problems in IE)
    if ( name == "type" && /(button|input)/i.test(elem.nodeName) && elem.parentNode )
        throw "type property can't be changed";

    elem[ name ] = value;
}
{% endhighlight %}

最后由access()方法中的return elems返回原来的jquery对象。
{% highlight javascript %}
// Setting one attribute
if (value !== undefined) {
    // Optionally, function values get executed if exec is true
    exec = exec && jQuery.isFunction(value);

    for (var i = 0; i < l; i++) {
        var elem = elems[i],
            val = exec ? value.call(elem, i) : value;
        fn(elem, key, val);
    }
    return elems;
}
{% endhighlight %}


当将最后一个class name从元素的class中移除时，其class=" "，为包含一个空格的字符串。

{% highlight javascript %}
removeClass: function( value ) {
    if ( (value && typeof value === "string") || value === undefined ) {
        var classNames = (value || "").split(/\s+/);

        for ( var i = 0, l = this.length; i < l; i++ ) {
            var elem = this[i];

            if ( elem.nodeType === 1 && elem.className ) {
                if ( value ) {
                var className = " " + elem.className + " ";
                    for ( var c = 0, cl = classNames.length; c < cl; c++ ) {
                        className = className.replace(" " + classNames[c] + " ", " ");
                    }
                    elem.className = className.substring(1, className.length - 1);
                } else {
                    elem.className = "";
                }
            }
        }
    }

    return this;
},
{% endhighlight %}

注意其中的replace语句，这个最后使得className被置为一个空格字符串" "，最后返回jquery对象：
{% highlight javascript %}
className = className.replace(" " + classNames[c] + " ", " ");
{% endhighlight %}


attr( properties )，同时为某个jquery对象设置多个属性，这里其实是用了递归调用access()方法本身。
{% highlight javascript %}
// Setting many attributes
if ( typeof key === "object" ) {
        for (var k in key) {
            access(elems, k, key[k], exec, fn);
        }
    return elems;
}
{% endhighlight %}

jquery.toggleClass()方法调用时进入this.each( fn, arguments )，进行toggleClass方法定义，这种做法在jquery源码中很多见，利用jQuery.each()方法将一个匿名对象的方法移到另一个对象的原型链中：

另外注意: `state = isBool ? state : !jQuery(this).hasClass( className );`

这句，如果传入的status为false，则不管这个jquery对象是否有这个className都会调用removeClass进行移除操作。

{% highlight javascript %}
jQuery.each({
    // 删除DOM元素中名为name的属性
    removeAttr: function( name ) {
        jQuery.attr( this, name, "" );
        if (this.nodeType == 1)
            this.removeAttribute( name );
    },

    toggleClass: function( classNames, state ) {
        var type = typeof classNames;
        if ( type === "string" ) {
            // toggle individual class names
            var isBool = typeof state === "boolean", className, i = 0,
                classNames = classNames.split( /\s+/ );
            while ( (className = classNames[ i++ ]) ) {
                // check each className given, space seperated list
                state = isBool ? state : !jQuery(this).hasClass( className );
                jQuery(this)[ state ? "addClass" : "removeClass" ]( className );
            }
        } else if ( type === "undefined" || type === "boolean" ) {
            if ( this.className ) {
                // store className if set
                jQuery.data( this, "__className__", this.className );
            }
            // toggle whole className
            this.className = this.className || classNames === false ? "" : jQuery.data( this, "__className__" ) || "";
        }
    }
}, function(name, fn){
    // 这个function被jQuery.each方法调用执行，方法体本身没有返回结果，只是为了定义二个新方法给jquery原型对象。
    // 下面这3行被用于定义新方法。
    jQuery.fn[ name ] = function(){
        return this.each( fn, arguments );
    };
});
{% endhighlight %}

还有一种toggleClass()的用法就是不传参数（其实看源码可以看到还可传个布尔值）时调用，
如果元素有className属性值，则会将此值存到此元属对应的data中，并且将className置空，另外如果传入的是false的话就不管此元素的className是否有值，都会将此className置空。

如果传入的值为true，则会toggle class。

这里需要注意它这句话，因为或运算符`||`比三目运算符`?:`的优先级别高，而全等`===`运算符又比或运算符`||`优先级高。

> 也就是说先计算classNames === false，
> 再计算this.className || classNames === false，
> 和jQuery.data( this, "__className__" ) || ""，
> 最后进行三目运算。

{% highlight javascript %}
// toggle whole className
this.className = this.className || classNames === false ? "" : jQuery.data( this, "__className__" ) || "";
// 这句话写得看上去简单，实际上理解有点难。
{% endhighlight %}

jquery.val()方法，不传参数是得到匹配的元素集中的第一个对象的值。如果传了一个String参数的话，会将此值赋给所有的匹配到的元素。

如果传了一个Array的参数，则可以用于多选择框，或者是选中符合数组值的匹配元素。

{% highlight javascript %}
val: function( value ) {
    if ( value === undefined ) {
        var elem = this[0];

        if ( elem ) {
            if( jQuery.nodeName( elem, 'option' ) )
                return (elem.attributes.value || {}).specified ? elem.value : elem.text;

            // We need to handle select boxes special
            if ( jQuery.nodeName( elem, "select" ) ) {
                var index = elem.selectedIndex,
                    values = [],
                    options = elem.options,
                    one = elem.type == "select-one";

                // Nothing was selected
                if ( index < 0 )
                    return null;

                // Loop through all the selected options
                // 这段for循环可以把one这块内容移出来，不要放在for里面，直接在外面拿select.options[select.selectedIndex]这个option对象的value
                // for只是处理一下多项选择就可以更清晰。
                for ( var i = one ? index : 0, max = one ? index + 1 : options.length; i < max; i++ ) {
                    var option = options[ i ];

                    if ( option.selected ) {
                        // Get the specifc value for the option
                        value = jQuery(option).val();

                        // We don't need an array for one selects
                        if ( one )
                            return value;

                        // Multi-Selects return an array
                        values.push( value );
                    }
                }

                return values;
            }

            // Everything else, we just grab the value
            return (elem.value || "").replace(/\r/g, "");

        }

        return undefined;
    }

    // Typecast once if the value is a number
    if ( typeof value === "number" )
        value += '';

    var val = value;

    return this.each(function(){
        // 传进来的value也可以是一个方法，将方法做为此元素的方法来调用（作用域就设置在此元素之上），将调用结果做为新的value。
        if(jQuery.isFunction(value)) {
            val = value.call(this);
            // Typecast each time if the value is a Function and the appended
            // value is therefore different each time.
            if( typeof val === "number" ) val += '';
        }

        // 这句判断应该是在最前面就可以被执行掉。
        if ( this.nodeType != 1 )
            return;

        // 传个数组进来用于选中对应的checkbox/radio/select，这个方法并不太好，用处不大并会造成一些误操作。
        // 如果传进来的value为一个数组时，当元素的值在此数组中时，则选中radio/checkbox。
        if ( jQuery.isArray(val) && /radio|checkbox/.test( this.type ) )
            this.checked = jQuery.inArray(this.value || this.name, val) >= 0;

        else if ( jQuery.nodeName( this, "select" ) ) {
            var values = jQuery.makeArray(val);

            jQuery( "option", this ).each(function(){
                // 当option的值在数组中时，选中此option。
                this.selected = jQuery.inArray( this.value || this.text, values ) >= 0;
            });

            if ( !values.length )
                this.selectedIndex = -1;

        } else
            // set此对象的值为val，如果value是个function，则这个val是调用结果。
            this.value = val;
    });
}
{% endhighlight %}

对于option，如果没有明确指定option value这个属性，则返回text做为值，另外其源码里这个写法可以少掉一个if/else语句，非常清晰明了：
(elem.attributes.value || {}).specified

因为value如果在option中没有指定，则这个elem.attribtues.value为undefined，不会有specified属性，但在空对象获取这个属性则不会报错，只是返回一个undefined而已。
{% highlight javascript %}
return (elem.attributes.value || {}).specified ? elem.value : elem.text;
{% endhighlight %}

