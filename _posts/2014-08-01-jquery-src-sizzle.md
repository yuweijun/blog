---
layout: post
title: "jquery-1.4.2 sizzle部分源码分析"
date: "Fri Aug 01 2014 22:47:32 GMT+0800 (CST)"
categories: jquery
---

Sizzle部分源码分析
------------------

{% highlight javascript %}
jQuery.multiFilter = function( expr, elems, not ) {
    if ( not ) {
        expr = ":not(" + expr + ")";
    }

    return Sizzle.matches(expr, elems);
};
// multiFilter return Array.
{% endhighlight %}

调用Sizzle.matches()方法：

{% highlight javascript %}
Sizzle.matches = function(expr, set){
    return Sizzle(expr, null, null, set);
};
{% endhighlight %}

Firefox3.5 中因为支持document.querySelectorAll()方法，所以jQuery为利用了浏览器的原生API方法，而重写了之前实现的Sizzle，原来实现的Sizzle对象被存储在临时变量oldSizzle中。

{% highlight javascript %}
if ( document.querySelectorAll ) (function(){
    var oldSizzle = Sizzle, div = document.createElement("div");
    div.innerHTML = "<p class='TEST'></p>";

    // Safari can't handle uppercase or unicode characters when
    // in quirks mode.
    if ( div.querySelectorAll && div.querySelectorAll(".TEST").length === 0 ) {
        return;
    }

    Sizzle = function(query, context, extra, seed){
        context = context || document;

        // Only use querySelectorAll on non-XML documents
        // (ID selectors don't work in non-HTML documents)
        if ( !seed && context.nodeType === 9 && !isXML(context) ) {
            try {
                return makeArray( context.querySelectorAll(query), extra );
            } catch(e){}
        }

        return oldSizzle(query, context, extra, seed);
    };

    Sizzle.find = oldSizzle.find;
    Sizzle.filter = oldSizzle.filter;
    Sizzle.selectors = oldSizzle.selectors;
    Sizzle.matches = oldSizzle.matches;
})();
{% endhighlight %}

如果传给Sizzle的只有selector一个参数，即seed为null，context则为document，则调用原生的querySelectorAll这个API方法。

如$.multiFilter('#d1')。

因为传入的seed是一个数组集合，不是null，所以又重新定向到原来jQuery实现的Sizzle对象上(oldSizzle)：

{% highlight javascript %}
var Sizzle = function(selector, context, results, seed) {
    results = results || [];
    context = context || document;

    if ( context.nodeType !== 1 && context.nodeType !== 9 )
        return [];

    if ( !selector || typeof selector !== "string" ) {
        return results;
    }

    var parts = [], m, set, checkSet, check, mode, extra, prune = true;

    // Reset the position of the chunker regexp (start from head)
    chunker.lastIndex = 0;

    while ( (m = chunker.exec(selector)) !== null ) {
        parts.push( m[1] );

        if ( m[2] ) {
            extra = RegExp.rightContext;
            break;
        }
    }

    if ( parts.length > 1 && origPOS.exec( selector ) ) {
        if ( parts.length === 2 && Expr.relative[ parts[0] ] ) {
            set = posProcess( parts[0] + parts[1], context );
        } else {
            set = Expr.relative[ parts[0] ] ?
                [ context ] :
                Sizzle( parts.shift(), context );

            while ( parts.length ) {
                selector = parts.shift();

                if ( Expr.relative[ selector ] )
                    selector += parts.shift();

                set = posProcess( selector, set );
            }
        }
    } else {
        var ret = seed ?
            { expr: parts.pop(), set: makeArray(seed) } :
            Sizzle.find( parts.pop(), parts.length === 1 && context.parentNode ? context.parentNode : context, isXML(context) );
        set = Sizzle.filter( ret.expr, ret.set );

        if ( parts.length > 0 ) {
            checkSet = makeArray(set);
        } else {
            prune = false;
        }

        while ( parts.length ) {
            var cur = parts.pop(), pop = cur;

            if ( !Expr.relative[ cur ] ) {
                cur = "";
            } else {
                pop = parts.pop();
            }

            if ( pop == null ) {
                pop = context;
            }

            Expr.relative[ cur ]( checkSet, pop, isXML(context) );
        }
    }

    if ( !checkSet ) {
        checkSet = set;
    }

    if ( !checkSet ) {
        throw "Syntax error, unrecognized expression: " + (cur || selector);
    }

    if ( toString.call(checkSet) === "[object Array]" ) {
        if ( !prune ) {
            results.push.apply( results, checkSet );
        } else if ( context.nodeType === 1 ) {
            for ( var i = 0; checkSet[i] != null; i++ ) {
                if ( checkSet[i] && (checkSet[i] === true || checkSet[i].nodeType === 1 && contains(context, checkSet[i])) ) {
                    results.push( set[i] );
                }
            }
        } else {
            for ( var i = 0; checkSet[i] != null; i++ ) {
                if ( checkSet[i] && checkSet[i].nodeType === 1 ) {
                    results.push( set[i] );
                }
            }
        }
    } else {
        makeArray( checkSet, results );
    }

    if ( extra ) {
        Sizzle( extra, context, results, seed );

        if ( sortOrder ) {
            hasDuplicate = false;
            results.sort(sortOrder);

            if ( hasDuplicate ) {
                for ( var i = 1; i < results.length; i++ ) {
                    if ( results[i] === results[i-1] ) {
                        results.splice(i--, 1);
                    }
                }
            }
        }
    }

    return results;
};
{% endhighlight %}

在Sizzle中解析selector，并且会转到Sizzle.find()和Sizzle.filter()方法中查到符合条件的元素。

{% highlight javascript %}
Sizzle.find = function(expr, context, isXML){
    var set, match;

    if ( !expr ) {
        return [];
    }

    for ( var i = 0, l = Expr.order.length; i < l; i++ ) {
        var type = Expr.order[i], match;

        if ( (match = Expr.match[ type ].exec( expr )) ) {
            var left = RegExp.leftContext;

            if ( left.substr( left.length - 1 ) !== "\\" ) {
                match[1] = (match[1] || "").replace(/\\/g, "");
                set = Expr.find[ type ]( match, context, isXML );
                if ( set != null ) {
                    expr = expr.replace( Expr.match[ type ], "" );
                    break;
                }
            }
        }
    }

    if ( !set ) {
        set = context.getElementsByTagName("*");
    }

    return {set: set, expr: expr};
};

Sizzle.filter = function(expr, set, inplace, not){
    var old = expr, result = [], curLoop = set, match, anyFound,
        isXMLFilter = set && set[0] && isXML(set[0]);

    while ( expr && set.length ) {
        for ( var type in Expr.filter ) {
            if ( (match = Expr.match[ type ].exec( expr )) != null ) {
                var filter = Expr.filter[ type ], found, item;
                anyFound = false;

                if ( curLoop == result ) {
                    result = [];
                }

                if ( Expr.preFilter[ type ] ) {
                    match = Expr.preFilter[ type ]( match, curLoop, inplace, result, not, isXMLFilter );

                    if ( !match ) {
                        anyFound = found = true;
                    } else if ( match === true ) {
                        continue;
                    }
                }

                if ( match ) {
                    for ( var i = 0; (item = curLoop[i]) != null; i++ ) {
                        if ( item ) {
                            found = filter( item, match, i, curLoop );
                            var pass = not ^ !!found;

                            if ( inplace && found != null ) {
                                if ( pass ) {
                                    anyFound = true;
                                } else {
                                    curLoop[i] = false;
                                }
                            } else if ( pass ) {
                                result.push( item );
                                anyFound = true;
                            }
                        }
                    }
                }

                if ( found !== undefined ) {
                    if ( !inplace ) {
                        curLoop = result;
                    }

                    expr = expr.replace( Expr.match[ type ], "" );

                    if ( !anyFound ) {
                        return [];
                    }

                    break;
                }
            }
        }

        // Improper expression
        if ( expr == old ) {
            if ( anyFound == null ) {
                throw "Syntax error, unrecognized expression: " + expr;
            } else {
                break;
            }
        }

        old = expr;
    }

    return curLoop;
};
{% endhighlight %}

