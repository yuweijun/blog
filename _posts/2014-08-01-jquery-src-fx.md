---
layout: post
title: "jQuery-1.4.2 fx部分源码分析"
date: "Fri Aug 01 2014 21:12:52 GMT+0800 (CST)"
categories: jquery
---

###Examples of jQuery.fn.fx

{% highlight html %}
<div>
    <input type="button" id="go" value="go"/>
    <input type="button" id="stop" value="stop"/>
    <input type="button" id="back" value="back"/>
</div>
<div class="block">block</div>
<div>
    <input type="button" value="toggle block1" id="block1_animation"/>
    <input type="button" value="toggle block2" id="block2_animation"/>
    <input type="button" value="toggle block3" id="block3_animation"/>
</div>
<div id="block1">block1</div>
<div id="block2">block2</div>
<div id="block3">block3</div>
<div>
    <input type="button" value="hide logo" id="clickme1"/>
    <input type="button" value="show logo" id="clickme2"/>
    <img id="logo" src="https://www.google.com.hk/images/srpr/logo11w.png" alt="google logo" />
</div>
<div id="examples">
    <h2>Example of Animations</h2>
    <p>Click on any of the yellow headers to see the default easing method in action (I've set as easeOutBounce for the demo, just because it's obviously different). All done with a straight animate call, no need to specify the animation type at all.</p>
    <p>Select easing types for the demo first one for down, second one for up. Then click the clicker.</p>
    <p id="controls"></p>
    <p id="example" class="big"><a href="#example" id="toggle">The Clicker</a></p>
</div>
<button id="hidr">Hide spans</button>
<button id="showr">Show spans</button>
<div id="spans">
    <span>Once</span> <span>upon</span> <span>a</span>
    <span>time</span> <span>there</span> <span>were</span>
    <span>three</span> <span>programmers...</span>
</div>
{% endhighlight %}


{% highlight javascript %}
// Start animation
var block = $(".block");
$("#go").click(function(){
    // 完成一个前进步骤之后，停留2秒后，开始下一步动画，如果在动画过程中被停止，则在动画停止2秒后开始下一步动画
    block.animate({left: ['+=300px', 'linear']}, 2000, function(){$.console.log("animation GO finished.")}).delay(2000);
});

// Stop animation when button is clicked
$("#stop").click(function(){
    block.stop();
});

// Start animation in the opposite direction
$("#back").click(function(){
    block.animate({left: '-=300px'}, 2000).delay(2000);
});
{% endhighlight %}

> As of jQuery version 1.4, we can set per-property easing functions within a single .animate() call. In the first version of .animate(), each property can take an array as its value: The first member of the array is the CSS property and the second member is an easing function. If a per-property easing function is not defined for a particular property, it uses the value of the .animate() method's optional easing argument. If the easing argument is not defined, the default swing function is used.
>
> We can, for example, simultaneously animate the width and height with the swing easing function and the opacity with the linear easing function:

{% highlight javascript %}
$('#block1_animation').click(function() {
    $('#block1').animate({
        width: ['toggle', 'swing'],
        height: ['toggle', 'easeOutBounce'],
        opacity: 'toggle'
    }, 5000, 'linear', function() {
        $.console.log('Block1 Animation complete.');
    });
});

// The block2_animation button shows how an unqueued animation works. It expands the div out to 50% width while the font-size is increasing. Once the boder change is complete, the font-size animation will begin.
$("#block2_animation").toggle(
    function(){
        $("#block2")
            .animate({ width:"50%" }, { queue:false, duration: 15000})
            .animate({ borderRightWidth:"150px" }, 15000)
            .animate({ fontSize:"24px" }, 15000);
    },
    function(){
        $("#block2")
            .animate({width:[100, 'easeOutBounce']}, {queue:false, duration: 15000})
            .animate({borderRightWidth:[1, 'swing']}, {duration: 15000})
            .animate({fontSize:[14, 'swing']}, {duration: 15000});
    }
);

// In the second version of .animate(), the options map can include the specialEasing property, which is itself a map of CSS properties and their corresponding easing functions. We can simultaneously animate the width using the linear easing function and the height using the easeOutBounce easing function.
$('#block3_animation').click(function() {
    $('#block3').animate({
        width: 'toggle',
        height: 'toggle',
        opacity: 'toggle'
    }, {
        duration: 5000,
        specialEasing: {
            width: 'linear',
            height: 'easeOutBounce',
            opacity: 'easeInOutBounce'
        },
        complete: function() {
            $.console.log('Block3 Animation complete.');
        }
    });
});
{% endhighlight %}


###Example of jQuery.fn.show()/hide()

{% highlight javascript %}
var clickme1 = $('#clickme1');
var clickme2 = $('#clickme2');
var logo = $('#logo');
clickme1.click(function() {
    logo.hide();
});

clickme2.click(function() {
    // logo.show();
    logo.show('slow', function() {
        $.console.log("Animation complete.");
    });
});
{% endhighlight %}

###Example of Animations

> Click on any of the yellow headers to see the default easing method in action (I've set as easeOutBounce for the demo, just because it's obviously different). All done with a straight animate call, no need to specify the animation type at all.
>
> Select easing types for the demo first one for down, second one for up. Then click the clicker.

{% highlight javascript %}
$("#hidr").click(function () {
    $("span:last-child", "#spans").hide("fast", function () {
        // use callee so don't have to name the function
        $(this).prev().hide("fast", arguments.callee);
    });
});
$("#showr").click(function () {
    $("span").show(2000);
});
{% endhighlight %}

jQuery.fn.fx源码分析
--------------------

在定义jQuery的效果方法时用到的一些变量定义及初始化

{% highlight javascript %}
var elemdisplay = {},
    rfxtypes = /toggle|show|hide/,
    rfxnum = /^([+-]=)?([\d+-.]+)(.*)$/,
    timerId,
    fxAttrs = [
        // 不包括border相关的属性，七要素中只余五个属性
        // height animations
        [ "height", "marginTop", "marginBottom", "paddingTop", "paddingBottom" ],
        // width animations
        [ "width", "marginLeft", "marginRight", "paddingLeft", "paddingRight" ],
        // opacity animations
        [ "opacity" ]
    ];
{% endhighlight %}

{% highlight javascript %}
var sel1 = $("<select>").appendTo('#controls');
for (x in jQuery.easing)
{
    if (x != 'linear' && x != 'swing')
        sel1.append($('<option>').attr('value', x).text(x));
}
sel2 = sel1.clone().appendTo('#controls');
sel1.val('easeInOutCirc');
sel2.val('easeOutBounce');
$("#toggle").click(function(e){
    e.preventDefault();
    this.blur();
    // var el = $('#' + this.href.split('#')[1]);
    var method1 = sel1.val();
    var method2 = sel2.val();
    $('#example').animate({height:200}, {duration: 1000, easing: method1}).animate({height:100}, {duration: 1000, easing: method2});
});
{% endhighlight %}

其中.show()和.hide()等方法中使用的genFx()方法，是最外围匿名方法中的一个内部方法

{% highlight javascript %}
// fxAttrs数组中有3个数组对象，getFx方法的第2个参数num是指取这3个数组对象中前面几个对象，一般都是取num为3，只有在只操作高度属性的slideUp/slideDown只要使用第1组数组中的5个css属性就可以，取num为1
function genFx( type, num ) {
    var obj = {};

    // 利用Array.prototype.concat方法将fxAttrs中的二维数组打平成一维数组
    jQuery.each( fxAttrs.concat.apply([], fxAttrs.slice(0,num)), function() {
        obj[ this ] = type;
    });

    return obj;
}
{% endhighlight %}

扩展jQuery.fn原型，加jquery对象添加show/hide/toggle/animate等js效果方法
{% highlight javascript %}
jQuery.fn.extend({
    // Display the matched elements. 将jquery对象匹配到的全部节点都显示出来
    // .show( duration, [ callback ] )
    // duration: A string or number determining how long the animation will run.
    // callback: A function to call once the animation is complete.
    show: function( speed, callback ) {
        // 如果有参数speed传入，可以是"fast"，"slow"字符串，也可传入毫秒为单位的整数:200/600，这里200/600就对应fast/slow
        // 则调用.animate()效果方法
        if ( speed || speed === 0) {
            return this.animate( genFx("show", 3), speed, callback);
        } else {
            // 如果是直接调用.show()方法，就修改元素css的display属性
            // 注意元素原来的display属性值，jQuery是通过在body后追加一个相同的元素，来获取在浏览器中其原生的display属性，并将此属性存入elemdisplay这个缓存对象中，以便复用
            // elemdisplay是定义jQuery对象的最外围的匿名函数局部变量，而jQuery和$这二个对象被全局对象window引用，因而作用域链上的变量都被维持在内存中，所以elemdisplay对象的变化被继续维持在内存中，第二次.show()方法调用时，会看到第一次变化后的elemdisplay，这实际上就是得益于闭包的好处
            for ( var i = 0, l = this.length; i < l; i++ ) {
                // 获取缓存在jQuery.data中当前元素原来的display属性
                var old = jQuery.data(this[i], "olddisplay");

                // 如果获取到old值，则将此元素的display属性设置为old
                // 将display设置为空字符串""，不会影响其原来的display状态
                this[i].style.display = old || "";

                // 如果当前元素处于隐藏状态，则分析元素是内联(inline)元素还是块(block)元素
                // 将分析结果记在elemdisplay对象中
                if ( jQuery.css(this[i], "display") === "none" ) {
                    var nodeName = this[i].nodeName, display;

                    // 检查elemdisplay缓存对象中，是否已经有此类元素的原始display值
                    if ( elemdisplay[ nodeName ] ) {
                        display = elemdisplay[ nodeName ];

                    } else {
                        // 通过在body尾添加一个相同的元素，分析出是内联元素还是块元素
                        var elem = jQuery("<" + nodeName + " />").appendTo("body");

                        display = elem.css("display");

                        if ( display === "none" ) {
                            display = "block";
                        }

                        // 移除添加的元素
                        elem.remove();

                        // 将此类元素的原始display值记到elemdisplay缓存对象中
                        elemdisplay[ nodeName ] = display;
                    }

                    // olddisplay值原来是"none"的话，这里会被display覆写成"inline"或者"block"
                    // 将当前元素的原始display属性值记到jQuery.data缓存对象上，此值为"inline"或者"block"，不会是"none"
                    jQuery.data(this[i], "olddisplay", display);
                }
            }

            // Set the display of the elements in a second loop
            // to avoid the constant reflow
            for ( var j = 0, k = this.length; j < k; j++ ) {
                // 再次设置当前元素style的display属性，前面一次设置可能会取不到olddisplay，或者值为none
                this[j].style.display = jQuery.data(this[j], "olddisplay") || "";
            }

            return this;
        }
    },

    hide: function( speed, callback ) {
        if ( speed || speed === 0 ) {
            return this.animate( genFx("hide", 3), speed, callback);

        } else {
            for ( var i = 0, l = this.length; i < l; i++ ) {
                // 获取当前元素在隐藏之前，其style的display属性值，如果在jQuery缓存中没有找到，则old为null
                var old = jQuery.data(this[i], "olddisplay");
                // 下面这个if判断写法不正确，因为!"none"这个肯定直接为false了，不会比较到old != "none"
                if ( !old && old !== "none" ) {
                    // 如果之前没有为当前元素设置olddisplay的缓存数据，则将元素当前的display属性值赋给olddisplay缓存
                    // 如果当前当素style.display为"none"，则此时olddisplay会先缓存"none"，但当元素下一次show时，olddisplay缓存值会被重写为"inline"或者"block"
                    jQuery.data(this[i], "olddisplay", jQuery.css(this[i], "display"));
                }
            }

            // Set the display of the elements in a second loop
            // to avoid the constant reflow
            for ( var j = 0, k = this.length; j < k; j++ ) {
                // 将当前元素隐藏，即使其原来的style.display为none
                this[j].style.display = "none";
            }

            return this;
        }
    },

    // jQuery.fn.toggle( handler(eventObject), handler(eventObject), [ handler(eventObject) ] ) Returns: jQuery
    // Description: Bind two or more handlers to the matched elements, to be executed on alternate clicks.
    // 在jQuery的event相关的机制中，已经定义了一个jQuery.fn.toggle(fn1, fn2)这样的方法，每次点击会依次执行里面的回调方法
    // Save the old toggle function
    _toggle: jQuery.fn.toggle,

    toggle: function( fn, fn2 ) {
        // 第一个参数fn是否为布尔值
        var bool = typeof fn === "boolean";

        if ( jQuery.isFunction(fn) && jQuery.isFunction(fn2) ) {
            // js不支持像java一样的方法重载，所以如果要一个方法名实现不同功能时，是依据方法传入的参数进行判断
            // 如果当前方法传入了至少二个参数，并且都是function时，则调用jQuery事件机制中的toggle()方法
            this._toggle.apply( this, arguments );

        } else if ( fn == null || bool ) {
            // 这个jQuery.fn.toggle方法是用于控制jquery对象匹配的元素的显示与隐藏
            // .toggle( showOrHide )
            // showOrHide: A Boolean indicating whether to show or hide the elements.
            this.each(function() {
                // 如果是布尔值，则state取传入的第一个参数值
                // 否则检查当前元素是否是隐藏元素，如果当前是隐藏元素，则state为true，反之为false
                var state = bool ? fn : jQuery(this).is(":hidden");
                // 根据state调用jQuery.fn.show()或者是jQuery.fn.hide()
                jQuery(this)[ state ? "show" : "hide" ]();
            });

        } else {
            // .toggle( [ duration ], [ callback ] )
            // duration: A string or number determining how long the animation will run.
            // callback: A function to call once the animation is complete.
            this.animate(genFx("toggle", 3), fn, fn2);
        }

        return this;
    },

    // 调整jquery对象匹配的元素的透明度
    fadeTo: function( speed, to, callback ) {
        // 此方法先利用.filter()方法筛选出隐藏元素，将其置为完全透明并显示
        return this.filter(":hidden").css("opacity", 0).show().end()
                    // 再用.end()方法返回原来jquery对象，进行.animate()动画调整透明度
                    .animate({opacity: to}, speed, callback);
    },
{% endhighlight %}

###javascript: closures, lexical scope and scope chain

[closure of javascript](http://jibbering.com/faq/notes/closures/)

在这里插讲一下javascript中常说的闭包，闭包的定义(javascript权威指南)如下：

> JavaScript functions are a combination of code to be executed and the scope in which to execute them. This combination of code and scope is known as a closure in the computer science literature. All JavaScript functions are closures.

javascript的function定义了将要被执行的代码，并且指出在哪个作用域中执行这个方法，这种代码和作用域的组合体就是一个闭包，在代码中的变量是自由的未绑定的。闭包就像一个独立的生命体，有其自身要运行的代码，同时其自身携带了运行时所需要的环境。

javascript所有的function都是闭包
--------------------------------

function对象，即闭包，本身具有可运行的代码，不像其他对象如array实例对象，只是一种数据结构，包含了一些方法，但其本身不具有可运行的代码，只有function对象自身就具有了可运行的代码。

闭包中包含了其代码运行的作用域，那这个作用域又是什么样子的呢，这就引入了词法作用域(lexical scope)的概念:

**词法作用域**是指方法运行的作用域是在方法定义时决定的，而不是方法运行时决定的。

所以在javascript中，function运行的作用域其实是一个**static scope**。
但也有二个例外，就是**with**和**eval**，在这2者中的代码处于**dynamic scope**中，这给javascript带来额外的复杂度和计算量，因而也效率低下，避免使用。

当闭包在其词法作用域中运行过程中，如何检索其中的变量名？这就再引入了一个概念，作用域链(scope chain)：

当一个方法function定义完成，其**作用域链**就是固定的了，并被保存成为方法内部状态的一部分，只是这个作用域链中调用对象的属性值不是固定的。作用域链是"活"的。

当一个方法在被调用时，会生成一个**调用对象**(call object，在javascript规范中称为activation object，注意这个对象不是闭包，虽然在Chrome控制台中将之表示为Cloure)，并将此call object加到其定义时确认下来的作用域链的顶端。

在这个call object上，方法的参数和方法内定义的局部变量名和值都会存在这个call object中，如果调用结束，这个call object会从作用域链的顶端移除，再没有被其他对象引用，内存也会被自动回收。

在此call object中使用的变量名会先从此方法局部变量和传入参数中检索，如果没有找到，就会向作用域链上的前一个对象查询，如此向上追溯，一直检索到global object(即window对象上)，如果在整个作用域链上没有找到此变量名，则会返回undefined(没有指定对象直接查询变量名，没找到则抛出异常变量未定义)。

如此通过作用域链，javascrip就实现了call object中变量名检索。

在全局对象中一个方法调用完成之后，生成的call object会被回收，这看不出闭包(即当前被调用的方法)有什么功用。但是当一个外部方法的内部返回一个嵌套方法，并且返回的嵌套方法被全局对象引用时，或者是外部方法内将嵌套方法赋给全局对象的属性(jQuery构造方法就是在匿名方法内设置在window.jQuery上)，外部方法调用生成的call object就会引用这个嵌套方法，而同时嵌套方法被全局对象引用，所以这个外部方法调用产生的call object及其属性就会继续生存在内存中，这时闭包的功用才被显示出来，下面以jQuery.fn.animation()方法调用过程为例进行说明:

1、当载入整个jquery.js文件时，会运行最外面的匿名方法(通过这个匿名方法形成一个命名空间，所有的变量名都是匿名方法内部定义的局部变量名):

{% highlight javascript %}
    (function( window, undefined ) {
        // ......jQuery source code;
        // Expose jQuery to the global object
        window.jQuery = window.$ = jQuery;
    })(window);
{% endhighlight %}

2、因为匿名方法内部有一个内部方法jQuery被全局对象window的属性jQuery和$引用，这里变量名很搞，一个是匿名方法内嵌套的构造方法jQuery，另一个window对象的属性名jQuery。因为这个匿名方法内部的jQuery构造方法被全局对象window.jQuery引用，所以外围的匿名方法在运行时产生的call object会继续生存在内存中。此时，这个call object可以利用Firebug或者Chrome的debug工具可以看到，在Firebug中的scopeChain中称之为"Object"，在Chrome的console中称之为"Closure"，该对象中记录了当前这个最外围的匿名方法被调用后生成的call object上变量的值，这些变量是未绑定的，是自由的，其值可以被修改并保存在作用域链上。运行此匿名方法时，会将其call object置于global object之上，形成作用域链。

这里注意一点，这匿名方法是一个闭包，但运行方法生成的call object对象只是作用域链顶端的一个对象，记录了方法中的变量名和值。闭包不但包括这个运行的作用域，还包括其运行所需的代码。

3、页面不关闭，这个匿名方法调用生成的call object就会一直驻在内存中，接下来当页面发生了一个jQuery.fn.animate()方法的调用，这个时候javascript又会为.animate()方法生成一个call object，这个对象拥有传进来的参数名和值，以及在.animate()方法内部定义的一个局部变量opt和它的值。

同时，javascript会将生成的这个call object置于其作用域链(scope chain)的最前端，即此时的作用域链为：global object->anonymous function call object->animate call object。

4、接下来会调用jQuery.fn.queue()->jQuery.fn.each()->jQuery.fn.dequeue()，在这些方法调用过程也都会接触到第2步中所提到的那个匿名方法调用后生成的闭包，这中间过程略过，当运行到最后传参给.queue(function)的function时，因为这个匿名方法是定义在jQuery.fn.animate()方法内部的，所以其作用域链(scope chain)也就已经确定了，即global object->anonymous function call object->animate call object，当此匿名方法调用生成一个call object，会将此call object再置于animate call object之上。

5、对于最后的匿名function运行完成之后，如果这个匿名function对象还被其他element的queue数组引用，则第3步中运行.animate()方法生成的闭包将继续生存在内存之中，直到所有的效果方法运行完成，此匿名function没有其他引用时，.animate()调用生成的call object就会被回收。

jQuery.fn.animate()是jQuery效果库的核心代码，根据指定的css属性集，执行指定的操作。

jQuery.fn.aniamte()方法会根据传入的parameters返回一个闭包，将闭包加入到jQuery.fn.queue()队列中，或者是调用.each()方法在每个元素上运行此闭包，关于jQuery.fn.queue()可参考data部分的源码说明。

 关于jQuery.fn.animate()方法，可以参考james padolsey的一篇[文章](http://james.padolsey.com/javascript/easing-in-jquery-1-4a2/)，james是jQuery Easing Plugin作者，OReilly jquery cookbook作者之一。

官方文档中关于jQuery.fn.animate()使用说明如下：

> .animate( properties, [ duration ], [ easing ], [ callback ] )
>
> properties (Options) : 一组包含作为动画属性和终值的样式属性和及其值的集合
>
> duration (String,Number) : (可选) 三种预定速度之一的字符串(slow, normal, fast)或表示动画时长的毫秒数值(如：1000)
>
> easing (String) : (可选) 要使用的擦除效果的名称(需要插件支持)，默认jQuery提供"linear"和"swing".
>
> callback (Function) : (可选) 在动画完成时执行的函数


> .animate( properties, options )
>
> properties: A map of CSS properties that the animation will move toward.
>
> options: A map of additional options to pass to the method. Supported keys:
>
> duration: A string or number determining how long the animation will run.
>
> easing: A string indicating which easing function to use for the transition.
>
> complete: A function to call once the animation is complete.
>
> step: A function to be called after each step of the animation.
>
> queue: A Boolean indicating whether to place the animation in the effects queue. If false, the animation will begin immediately.
>
> specialEasing: A map of one or more of the CSS properties defined by the properties argument and their corresponding easing functions (added 1.4).

{% highlight javascript %}
    // jQuery.fn.animate()
    // The position attribute of the element must not be static if we wish to animate the left property as we do in the example.
    // The jQuery UI project extends the .animate() method by allowing some non-numeric styles such as colors to be animated. The project also includes mechanisms for specifying animations through CSS classes rather than individual attributes.
    animate: function( prop, speed, easing, callback ) {
        var optall = jQuery.speed(speed, easing, callback);

        // prop为空对象，optall.complete是function对象，这一般是jQuery.fn.animate({}, fn)形式调用的
        // 跟jQuery.fn.each(fn)效果一样，这种写法来进行动画调用不太合理
        if ( jQuery.isEmptyObject( prop ) ) {
            return this.each( optall.complete );
        }

        // 如果第2个参数speed是以object直接量方式传参进来的，并且其中有属性queue为false，表示不要将function放入this.queue()对列中，而是直接通过this.each(function)运行效果方法，这样同一个元素上可以同时开始多个动画效果
        // return this.each(fn)或者this.queue(fn)
        // 方法执行过程：.animate()->.queue()->.each()->.dequeue()->function，最后的function就是指下面做为参数传给.each()或者.queue()方法的匿名方法
        // 注：jQuery.fn.each/jQuery.fn.queue这2个方法运行完都是返回jquery对象本身，所以animate方法可以进行链式操作
        return this[ optall.queue === false ? "each" : "queue" ](function() {
            // 当前这个匿名方法被运行时，其运行环境，作用域链相关的描述参考上面的详细说明

            // 这里定义的opt变量，在分析完所有的prop属性之后，可能会再为opt对象再添加以下这些属性：opt.display, opt.overflow, opt.curAnim, opt.specialEasing
            // opt中原来的属性有：opt.duration, opt.complete, opt.easing
            // opt对象最后会作为参数用于构造jQuery.fx的实例对象
            var opt = jQuery.extend({}, optall), p,
                // 检查当前element是否为隐藏元素
                hidden = this.nodeType === 1 && jQuery(this).is(":hidden"),
                // 当前这个匿名方法是作为element对象的方法调用的，this指向当前element
                // 为了后面的匿名方法能引用当前element，所以设置了别名self，因为javascript语言设计的失误，匿名方法运行时，其中的this是指向全局对象的
                self = this;

            for ( p in prop ) {
                // rdashAlpha = /-([a-z])/ig,
                // 将中划线连接的字符串转为驼峰格式的字符串，如line-height转为lineHeight
                var name = p.replace(rdashAlpha, fcamelCase);

                if ( p !== name ) {
                    // 如果属性p发生了变化，则调整prop对象，并重置属性p
                    prop[ name ] = prop[ p ];
                    delete prop[ p ];
                    p = name;
                }

                if ( prop[p] === "hide" && hidden || prop[p] === "show" && !hidden ) {
                    // 如果当前元素本来就为hidden状态，并且动画效果仍然是想将元素置为隐藏状态(prop[p]为hide)，这个就没必要有什么其他的执行代码了，元素已经处于动画的完成状态了，直接运行效果完成之后的回调方法，即可。另外show方法也是同理，对于本来就是处理非隐藏状态的元素执行.show()方法，只是直接运行回调方法
                    // 注意opt.complete在jQuery.speed()方法中被重写了
                    return opt.complete.call(this);
                }

                // 如果p属性是针对元素的height/width进行调整，则将元素原来的display/overflow属性记录到opt对象中
                if ( ( p === "height" || p === "width" ) && this.style ) {
                    // Store display property
                    opt.display = jQuery.css(this, "display");

                    // 防止动画过程中，元素内容溢出或者出现滚动条，需要在后面将元素style.overflow置为hidden，所以这里先将元素原来的overflow值放在opt.overflow中，在动画完成之后，再将其取出还原到元素上
                    // Make sure that nothing sneaks out
                    opt.overflow = this.style.overflow;
                }

                // Per-property Easing
                // As of jQuery version 1.4, we can set per-property easing functions within a single .animate() call. In the first version of .animate(), each property can take an array as its value: The first member of the array is the CSS property and the second member is an easing function. If a per-property easing function is not defined for a particular property, it uses the value of the .animate() method's optional easing argument. If the easing argument is not defined, the default swing function is used.
                // the options map can include the specialEasing property, which is itself a map of CSS properties and their corresponding easing functions. We can simultaneously animate the width using the linear  easing function and the height using the easeOutBounce easing function.
                // 如果属性值是一个数组，则数组中的第1个值重置给prop[p]，第2个值作为opt.specialEasing[p]属性的值
                // 如prop中有属性{height: ['toggle', 'swing'], left: [500, 'swing']}
                if ( jQuery.isArray( prop[p] ) ) {
                    // Create (if needed) and add to specialEasing
                    (opt.specialEasing = opt.specialEasing || {})[p] = prop[p][1];
                    prop[p] = prop[p][0];
                }
            }

            // opt.overflow没有进入前面的一个if判断，此处为undefined，并且undefined == null为true
            // 如果元素原来有设置overflow，此处重置为hidden
            if ( opt.overflow != null ) {
                this.style.overflow = "hidden";
            }

            // 拷贝prop对象给opt.curAnim属性，而不是直接引用prop，因为接下来会用jQuery.each()遍历prop对象，并在循环中会修改opt.curAnim对象
            opt.curAnim = jQuery.extend({}, prop);

            // 为prop对象中每个属性调用匿名方法function(name, val)
            jQuery.each( prop, function( name, val ) {
                // 初始化一个jQuery.fx对象，第1个参数为element对象，第2个参数为前面整理得到的opt对象，第3个为CSS属性名(也可以是元素属性名)
                // 这里不能使用this，因为当前这个匿名方法是作为prop[name]对象的方法进行调用的(查看jQuery.each方法)，this指向prop[name]对象
                var e = new jQuery.fx( self, opt, name );

                // rfxtypes = /toggle|show|hide/
                if ( rfxtypes.test(val) ) {
                    // 在调用jQuery.fn.show('slow')方法之后生成的prop: {left: show, right: show, height: show, width: show}
                    // 如果效果方法是show/hide/toggle，则调用jQuery.fx对象的方法jQuery.fx.prototype.show()/jQuery.fx.prototype.hide()，其中toggle会根据元素的hidden状态选择show/hide方法执行
                    e[ val === "toggle" ? hidden ? "show" : "hide" : val ]( prop );
                } else {
                    // rfxnum = /^([+-]=)?([\d+-.]+)(.*)$/,
                    // 如val为字符串："+=300px", "-= 300px"，使元素产生相对运动
                    var parts = rfxnum.exec(val),
                        // 效果执行的起始值
                        start = e.cur(true) || 0;

                    if ( parts ) {
                        // 如果rfxnum正则匹配到val，则
                        // 设置效果执行的结束值，以其单位，默认单位为px
                        var end = parseFloat( parts[2] ),
                            unit = parts[3] || "px";

                        // 如果单位不是px，重新计算起始值
                        // We need to compute starting value
                        if ( unit !== "px" ) {
                            self.style[ name ] = (end || 1) + unit;
                            start = ((end || 1) / e.cur(true)) * start;
                            self.style[ name ] = start + unit;
                        }

                        // 如果var中设置了+=/-=的增量操作符，结合start起始值，重新计算实际的结束位置
                        // If a +=/-= token was provided, we're doing a relative animation
                        if ( parts[1] ) {
                            end = ((parts[1] === "-=" ? -1 : 1) * end) + start;
                        }

                        // 调用e.custom()方法执行最终动画效果
                        e.custom( start, end, unit );

                    } else {
                        e.custom( start, val, "" );
                    }
                }
            });

            // 在jQuery.each(object, fn)循环中，希望fn返回true/false值，只要中间一次循环返回false，可以中断循环
            // For JS strict compliance
            return true;
        });
    },
{% endhighlight %}

`jQuery.fn.stop( [ clearQueue ], [ gotoEnd ] )`: 停止当前jquery对象匹配的元素上正在运行的动画效果

`clearQueue`: A Boolean indicating whether to remove queued animation as well. Defaults to false.

`gotoEnd`: A Boolean indicating whether to complete the current animation immediately. Defaults to false.

> When .stop() is called on an element, the currently-running animation (if any) is immediately stopped. If, for instance, an element is being hidden with .slideUp() when .stop()  is called, the element will now still be displayed, but will be a fraction of its previous height. Callback functions are not called.


如果clearQueue没有设置为true，在当前动画被停止之后，后续一个动画方法立即被执行。


如果.stop()方法调用时，没有传入gotoEnd=true，则不会调用当前动画完成的callback回调方法，如果gotoEnd为true，则元素会被置于动画完成时的状态，回调也会被调用。

> If more than one animation method is called on the same element, the later animations are placed in the effects queue for the element. These animations will not begin until the first one completes. When .stop() is called, the next animation in the queue begins immediately. If the clearQueue parameter is provided with a value of true, then the rest of the animations in the queue are removed and never run.
>
> If the gotoEnd property is provided with a value of true, the current animation stops, but the element is immediately given its target values for each CSS property. In our above .slideUp() example, the element would be immediately hidden. The callback function is then immediately called, if provided.

{% highlight javascript %}
    // jQuery.fn.stop()
    // 这个方法是停止当前jquery对象匹配的元素上的动画效果，另外还有一个全局的效果停止方法，jQuery.fx.stop()
    stop: function( clearQueue, gotoEnd ) {
        var timers = jQuery.timers;

        // 如果在停止动画的同时，想移除jquery对象上所有排队中的动作，则将第1个参数clearQueue设置为true
        if ( clearQueue ) {
            this.queue([]);
        }

        this.each(function() {
            // 将数组倒过来执行，这样在当前方法运行时，添加到this.queue方法队列中的方法将被忽略
            // go in reverse order so anything added to the queue during the loop is ignored
            for ( var i = timers.length - 1; i >= 0; i-- ) {
                // 检查jQuery.timers[i]中的效果对象操作的元素是否与当前jquery对象匹配的元素是否一致，参考jQuery.fx.prototype.custom方法: t.elem = this.elem;
                if ( timers[i].elem === this ) {
                    // 如果没有设置gotoEnd，则当前动画被中止后，不会调用动画完成时的回调方法
                    if (gotoEnd) {
                        // 如果gotoEnd为true，动画直接跳过，元素将被置于与动画结束相同的状态，并且调用动画完成时的回调方法
                        // 参考jQuery.fx.prototype.step()方法说明
                        // force the next step to be the last
                        timers[i](true);
                    }

                    // 从全局的定时队列jQuery.timers中移除当前效果执行对象
                    timers.splice(i, 1);
                }
            }
        });

        // 如果第2个参数gotoEnd为默认值false，表示不立即结束当前的动画，执行queue队列中的下一个方法
        // start the next in the queue if the last step wasn't forced
        if ( !gotoEnd ) {
            this.dequeue();
        }

        return this;
    }

});
{% endhighlight %}

jQuery库定义的几个常用的效果方法: slideDown/slideUp/slideToggle/fadeIn/fadeOut

{% highlight javascript %}
// 通过genFx()方法来生成props属性集
// Generate shortcuts for custom animations
jQuery.each({
    slideDown: genFx("show", 1), // 只针对高度的5个属性进行调整
    slideUp: genFx("hide", 1), // 同上
    slideToggle: genFx("toggle", 1), // 同上
    fadeIn: { opacity: "show" },
    fadeOut: { opacity: "hide" }
}, function( name, props ) {
    jQuery.fn[ name ] = function( speed, callback ) {
        return this.animate( props, speed, callback );
    };
});
{% endhighlight %}

扩展jQuery本身，添加了jQuery.speed/easing/timers/fx等属性和方法，jQuery.fx是jQuery动画效果的构造函数

{% highlight javascript %}
jQuery.extend({
    speed: function( speed, easing, fn ) {
        // 传入的speed为一个对象，则opt设置为speed
        var opt = speed && typeof speed === "object" ? speed
            :
            // 否则根据传入的参数构造opt对象
            {
                // complete对应的callback在效果执行完成之后，会被执行
                // jQuery.speed(speed, easing, fn)中第3个如果设置了，则callback取fn，如果第3个参数没有设置，则检查第2个参数easing
                complete: fn || !fn && easing ||
                    // 如果第2个参数easing也没有设置，则检查第1个参数speed，是否为一个function对象，如果是则将complete的callback设置为speed
                    jQuery.isFunction( speed ) && speed,
                duration: speed,
                // easing是根据传入的参数进行进行设置的，如果第3个参数fn和第2个参数easing都有传入，则easing取第2个参数的值
                // 如果没有传入第3个参数，但传入了第2个参数easing，此时要判断easing是否为function对象，如果easing不是function对象，则easing取第2个参数的值
                easing: fn && easing || easing && !jQuery.isFunction(easing) && easing
            };

        // 如果全局参数jQuery.fx.off设置为true，则会中止动画效果，并且所有动画效果的方法会将element的状态直接设置为效果结束时的状态
        // 当jQuery.fx.off为真时，opt.duration被设置为0
        // 否则检查opt.duration是否为number类型的，因为当前方法接收的第1个参数可能是字符串：fast/slow
        opt.duration = jQuery.fx.off ? 0 : typeof opt.duration === "number" ? opt.duration :
            // 检查opt.duration是否为fast/slow，并读取相应的200/600数值，设置给opt.duration
            // 否则opt.duration取默认值400，单位为毫秒
            jQuery.fx.speeds[opt.duration] || jQuery.fx.speeds._default;

        // Queueing 一个效果运行结束时，调用jQuery.fn.dequeue()触发队列中的另一个方法继续被运行
        opt.old = opt.complete;
        // opt.complete方法最后会被作为elem元素的方法调用，方法中的this指向此elem对象
        opt.complete = function() {
            // 先将opt.complete这个效果完成之后需要调用的回调方法，存在过渡的opt.old中
            // 然后检查是否有设置opt.queue属性值为false，如果为false，则会只执行当前的回调方法opt.old之后，中断当前效果方法队列的进一步执行
            // 如果不是false或者没有设置此属性，则调用jQuery.fn.dequeue()方法，从对象的queue方法队列中弹出一个方法并执行
            // 正是因为此处有jQuery帮助调用jQuery.fn.dequeue()方法，所以在使用.animate().animate().animate()这样的链式操作时(queue不能为false)，在一个动画完成之后，通过完成之后的回调opt.complete触发下一个动画开始
            if ( opt.queue !== false ) {
                jQuery(this).dequeue();
            }
            // 检查效果结束要执行的callback是否是一个function对象，如果是则作为当前elem元素的方法调用之: this.options.complete.call( this.elem );
            if ( jQuery.isFunction( opt.old ) ) {
                // .animate()方法中动画完成的callback回调方法只作为jquery对象所匹配的元素的方法进行调用，匹配几个元素就调用几次callback，没有传入其他参数了
                // If multiple elements are animated, it is important to note that the callback is executed once per matched element, not once for the animation as a whole.
                opt.old.call( this );
            }
        };

        return opt;
    },


    // 第3方的动画插件：[jQuery Easing Plugin]http://gsgd.co.uk/sandbox/jquery/easing/
    // james关于easing效果的演示说明：http://james.padolsey.com/demos/jquery/easing/
    // easing的插件接口说明，根据浙变效果的调用方式：jQuery.easing[specialEasing || defaultEasing](this.state, n, 0, 1, this.options.duration)
    // jQuery.easing的新方法接口为：jQuery.easing.method(current_position, current_time, start_value, end_value, total_time)
    // p(current_position)表示为动画的当前执行进度，其值为n/this.options.duration
    // n(current_time)为动画已经执行了的时间
    // firstNum(start_value)表示为动画属性的初始值
    // diff(end_value)表示为动画属性的结束值
    // this.options.duration最后还有一个效果持续总时间，其实有了这个值，第一个参数p就是多余的了
    // jQuery源码提供的2种渐变效果(字面直译为擦除效果)
    easing: {
        linear: function( p, n, firstNum, diff ) {
            return firstNum + diff * p;
        },
        swing: function( p, n, firstNum, diff ) {
            return ((-Math.cos(p*Math.PI)/2) + 0.5) * diff + firstNum;
        }
    },

    // 在jQuery.timers数组中存入了这个function对象：function t( gotoEnd ) { return self.step(gotoEnd); }，其中self是指jQuery.fx实例对象
    // jQuery.timers[i](totoEnd)方法其实就是使其中一个动画执行一帧
    // 定时队列，将所有元素的所有不同属性的fx动画对象的执行方法t(gotoEnd)，导入此定时队列，最后通过 timerId = setInterval(jQuery.fx.tick, 13) 调用jQuery.fx.tick()方法，在tick方法中遍历此定时队列，并执行其中的动画，参考jQuery.fx.tick()方法说明
    // 因为javascript是单线程运行的，如果不使用timers方式进行控制，只能在一个动画运行完成之后，开始下一个动画，这样无法达到期望的动画效果的
    // 所以这个定时队列的最大作用，是模拟了动画的并行计算(当然不是真的并行运行，javascript是单线程的)，多个动画(或者一个动画的多个属性)可以在页面上一起渲染，整个timers是通过一个setInterval来控制的
    timers: [],

    // 抽象出不同 css 属性随时间变化得公共计算，css 属性以及变化值做为变量处理。
    // jQuery.fx对象的构造方法，初始化fx对象的属性：options/elem/prop
    // 在animate 函数中，对参数prop中的每个 css 属性计算开始，结束以及持续时间，对应每个元素以及元素的每个变化css属性构造出jquery.fx类型 对象 ，由该对象调用其 custom 方面来具体实施动画效果
    fx: function( elem, options, prop ) {
        // 动画执行所需的选项信息
        this.options = options;
        // 动画执行所在element对象
        this.elem = elem;
        // 动画执行是针对哪个CSS属性的(也有可能是元素本身的某个属性值)
        this.prop = prop;

        if ( !options.orig ) {
            // 这个orig对象中保存了执行动画的元素的一些最原始的信息，可以此信息，在动画执行完成后，还原到元素上
            options.orig = {};
        }
    }

});
{% endhighlight %}

定义jQuery.fx对象原型，为jQuery.fx.prototype定义了update/cur/custom/show/hide/step等方法:

{% highlight javascript %}
jQuery.fx.prototype = {
    // Simple function for setting a style value
    update: function() {
        // jQuery.fx构造方法会传入3个参数：function(elem, options, prop){}
        // 如果第2个参数options中传入了一个step属性，其值为function对象，则在动画的每一帧执行后，额外运行这个this.options.setp(this.now, this)方法
        if ( this.options.step ) {
            // this.now为this.prop当前的属性值
            this.options.step.call( this.elem, this.now, this );
        }

        // jQuery.fx.step中默认提供的2个属性为：opacity和_default
        // jQuery.fx.step[this.prop](this)这种写法就预留了空间给插件开发者，为this.prop属性的动画每一帧额外执行一个方法，并且接收当前jQuery.fx对象作为参数
        // _default()在动画这一帧中，默认的动画执行效果是将this.prop属性值this.now更新到this.elem元素上或者是this.elem.style属性中
        (jQuery.fx.step[this.prop] || jQuery.fx.step._default)( this );

        // 如果当前动画效果是针对"height/width"这2个属性时，需要先将elem显示出来
        // Set display property to block for height/width animations
        if ( ( this.prop === "height" || this.prop === "width" ) && this.elem.style ) {
            this.elem.style.display = "block";
        }
    },

    // 每个jQuery.fx对象都是以一个元素(this.elem)的CSS属性名(this.prop)，结合动画选项(this.options)为基础形成
    // 在这个CSS属性的动画中，要先计算此CSS属性的当前值
    // Get the current size
    cur: function( force ) {
        // 举例如this.elem['offsetHeight']，offsetHeight垂直方向偏移量是element上的属性值，实际上并非一个CSS属性名
        if ( this.elem[this.prop] != null && (!this.elem.style || this.elem.style[this.prop] == null) ) {
            // 则直接返回this.elem.offsetHeight
            return this.elem[ this.prop ];
        }

        // 如果当前对象的this.prop是一个CSS属性名，则通过jQuery.css()方法获取此属性的实际值
        // 因为force的值为true，所以jQuery.css()方法最后返回的值是经window.getComputedStyle()方法计算后得到，不是从this.elem.style.prop属性上得到的
        var r = parseFloat(jQuery.css(this.elem, this.prop, force));
        // 尝试从this.elem.style.prop属性上得到这个值
        return r && r > -10000 ? r : parseFloat(jQuery.curCSS(this.elem, this.prop)) || 0;
    },

    // Start an animation from one number to another
    custom: function( from, to, unit ) {
        // 动画开始的时间戳
        this.startTime = now();
        this.start = from;
        this.end = to;
        this.unit = unit || this.unit || "px";
        // 当前的CSS属性名this.prop对应的值
        this.now = this.start;
        this.pos = this.state = 0;

        // 下面在一个方法的内部直接调用t()运行，此时没有明确的方法调用者，实际就是全局对象window
        // 因为在t()方法内实际是要运行jQuery.fx对象的step()方法，所以用self别名指代当前的jQuery.fx对象
        var self = this;
        function t( gotoEnd ) {
            // 执行一帧动画，此方法会在下面运行一次之外，会被置于jQuery.timers数组中，并且在jQuery.fx.tick()方法中通过jQuery.timers[i]()再次调用运行
            return self.step(gotoEnd);
        }

        // 为这个闭包添加一个属性，说明闭包是作用于哪个元素上的，这个属性是为了jQuery.fn.stop()方法设置的，参考jQuery.fn.stop()方法
        t.elem = this.elem;

        // 先执行一帧动画，如果动画没有完成，t()会返回true，并将闭包t放入jQuery.timers定时队列，由jQuery控制统一调度动画运行
        if ( t() && jQuery.timers.push(t) && !timerId ) {
            // timerId是定义jQuery对象的最外围的匿名方法中的内部变量，是所有jQuery.fx实例对象都共享访问的，所以只会有一个jQuery.fx实例对象会触发setInterval中的jQuery.fx.tick方法
            // 直到调用jQuery.fx.stop()方法，清除timerId，停止全部动画。之后再由任何一个动画对象fx调用costom方法，产生一个新的timerId
            timerId = setInterval(jQuery.fx.tick, 13);
        }
    },

    // 在.animate(prop, [ duration ], [ easing ], [ callback ])的方法中，会检查prop对象的值是否满足正则： rfxtypes = /toggle|show|hide/
    // 如果需要对CSS属性执行toggle/show/hide操作，则最后会调用下面这二个方法show/hide
    // Simple 'show' function
    show: function() {
        // Remember where we started, so that we can go back to it later
        this.options.orig[this.prop] = jQuery.style( this.elem, this.prop );
        this.options.show = true;

        // 对于width/height的show动画，给其一个较小的初始值1
        // Begin the animation
        // Make sure that we start at a small width/height to avoid any
        // flash of content
        this.custom(this.prop === "width" || this.prop === "height" ? 1 : 0, this.cur());

        // Start by showing the element
        jQuery( this.elem ).show();
    },

    // Simple 'hide' function
    hide: function() {
        // Remember where we started, so that we can go back to it later
        this.options.orig[this.prop] = jQuery.style( this.elem, this.prop );
        this.options.hide = true;

        // Begin the animation
        this.custom(this.cur(), 0);
    },

    // Each step of an animation
    step: function( gotoEnd ) {
        var t = now(), done = true;

        if ( gotoEnd || t >= this.options.duration + this.startTime ) {
            // this.prop属性值设置为动画结果的最后结束值
            this.now = this.end;
            // 标记当前jQuery.fx对象的state状态值，由0改为1，this.state = n / this.options.duration，this.state记录了动画运行完成的时间比例
            this.pos = this.state = 1;
            // 调用jQuery.fx对象的update()方法
            this.update();

            // 标记在this.prop属性上的动画执行完成
            this.options.curAnim[ this.prop ] = true;

            for ( var i in this.options.curAnim ) {
                // 遍历需要执行动画效果的CSS属性，是否每个属性的动画都已经完成
                if ( this.options.curAnim[i] !== true ) {
                    // 只要其有有一个属性的动画没有完成，都将标记done为false
                    done = false;
                }
            }

            if ( done ) {
                // 动画完成之后，进行的修复处理
                if ( this.options.display != null ) {
                    // 还原动画对象this.elem.style.overflow值
                    // Reset the overflow
                    this.elem.style.overflow = this.options.overflow;

                    // 还原this.elem.style.display属性值，缓存中的olddisplay键对应的值是经由jQuery.fn.show()/jQuery.fn.hide()方法操作后设置的
                    // Reset the display
                    var old = jQuery.data(this.elem, "olddisplay");
                    this.elem.style.display = old ? old : this.options.display;

                    // 如果元素最后的display值为"none"，则置为"block"显示元素
                    if ( jQuery.css(this.elem, "display") === "none" ) {
                        this.elem.style.display = "block";
                    }
                }

                // 如在调用jQuery.fn.hide('slow')方法后，通过jQuery.fx对象的hide()方法，将this.options.hide设置为true
                // Hide the element if the "hide" operation was done
                if ( this.options.hide ) {
                    // 元素隐藏的动画结束后，将当前元素隐藏掉
                    jQuery(this.elem).hide();
                }

                // Reset the properties, if the item has been hidden or shown
                if ( this.options.hide || this.options.show ) {
                    for ( var p in this.options.curAnim ) {
                        // 根据this.options.orig中的属性值还原this.elem元素的CSS属性值
                        jQuery.style(this.elem, p, this.options.orig[p]);
                    }
                }

                // 执行效果结束后的回调方法，此处的complete()是经过jQuery代理之后的方法
                // Execute the complete function
                this.options.complete.call( this.elem );
            }

            // 所有动画完成，jQuery.fx对象的step()方法返回false，表示不需要再执行下一帧了
            return false;

        } else {
            var n = t - this.startTime;
            // 动画已经运行时间n毫秒，将之除以动画总时间duration，计算动画进行的状态，动画完成时，this.state=1
            this.state = n / this.options.duration;

            // Perform the easing function, defaults to swing
            var specialEasing = this.options.specialEasing && this.options.specialEasing[this.prop];
            var defaultEasing = this.options.easing || (jQuery.easing.swing ? "swing" : "linear");

            // 根据运行时间的比率与运行位置的比率的关系变化，产生不同的动画效果
            // 根据运行时间n和效果要执行的总时间duration，以及元素起止位置点的相对位置(0-1)，计算当前step之后的相对位置点。默认是swing的算法
            this.pos = jQuery.easing[specialEasing || defaultEasing](this.state, n, 0, 1, this.options.duration);
            // 根据相对位置点this.pos计算属性的当前值，并通过this.update()方法将此当前值更新到元素中，完成一帧动画效果
            this.now = this.start + ((this.end - this.start) * this.pos);

            // Perform the next step of the animation
            this.update();
        }

        // 动画还没有执行完，后面一步step仍需要执行，返回true
        return true;
    }
};
{% endhighlight %}

扩展jQuery.fx对象本身，添加了tick/stop/step/speeds等静态方法和属性，这是控制全局动画的方法，其中tick方法用于控制动画定时队列的执行，stop方法用于停止全部动画执行，step方法则将动画执行一步，计算所得的属性值更新到元素上，产生动画效果

{% highlight javascript %}
jQuery.extend( jQuery.fx, {
    // jQuery.fx.tick()方法在setInterval()中调用：timerId = setInterval(jQuery.fx.tick, 13);
    tick: function() {
        var timers = jQuery.timers;

        for ( var i = 0; i < timers.length; i++ ) {
            // timers[i]即为：function t( gotoEnd ) { return self.step(gotoEnd); }，触发jQuery.fx实例对象的step()方法运行，执行一帧动画效果
            // 如果最后step()方法返回false，表示动画执行完成
            // 如果最后step()方法返回true，表示动画还没执行完成，仍需要执行下一步动画
            if ( !timers[i]() ) {
                // 当其中一个动画效果完成时，从timers队列中移除
                timers.splice(i--, 1);
            }
        }

        if ( !timers.length ) {
            // 当定时队列jQuery.timers中全部效果执行完成时，停止动画效果，清除timerId
            jQuery.fx.stop();
        }
    },

    stop: function() {
        // 清除timerId，停止jQuery.fx.tick()方法的调用，中止动画执行
        // 需要注意这个全局的jQuery.fx.stop方法与jQuery.fn.stop方法的区别，这个全局的stop方法中止动画执行，但jQuery.queue缓存的动画方法队列中的对象仍然存在，碰到新的动画执行时，原来在queue中的方法仍然会被再次执行，如果不是期望的效果，可能要考虑jQuery.fn.stop方法或者是jQuery.fn.clearQueue方法清除queue队列的中全部对象
        clearInterval( timerId );
        timerId = null;
    },

    speeds: {
        // 动画的执行速度
        slow: 600,
        fast: 200,
        // Default speed
        _default: 400
    },

    step: {
        // jQuery.fx.step.opacity更新fx效果对象执行所在元素的透明值为fx.now
        opacity: function( fx ) {
            jQuery.style(fx.elem, "opacity", fx.now);
        },

        // jQuery.fx.step._default这个step的默认方法，更新元素属性值或者其CSS属性值为fx.now
        _default: function( fx ) {
            if ( fx.elem.style && fx.elem.style[ fx.prop ] != null ) {
                // 对于CSS属性width和height，其值不能取负值
                fx.elem.style[ fx.prop ] = (fx.prop === "width" || fx.prop === "height" ? Math.max(0, fx.now) : fx.now) + fx.unit;
            } else {
                fx.elem[ fx.prop ] = fx.now;
            }
        }
    }
});
{% endhighlight %}


{% highlight javascript %}
// jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
    def: 'easeOutQuad',
    swing: function (x, t, b, c, d) {
        //alert(jQuery.easing.default);
        return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
    },
    easeInQuad: function (x, t, b, c, d) {
        return c*(t/=d)*t + b;
    },
    easeOutQuad: function (x, t, b, c, d) {
        return -c *(t/=d)*(t-2) + b;
    },
    easeInOutQuad: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t + b;
        return -c/2 * ((--t)*(t-2) - 1) + b;
    },
    easeInCubic: function (x, t, b, c, d) {
        return c*(t/=d)*t*t + b;
    },
    easeOutCubic: function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t + 1) + b;
    },
    easeInOutCubic: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    },
    easeInQuart: function (x, t, b, c, d) {
        return c*(t/=d)*t*t*t + b;
    },
    easeOutQuart: function (x, t, b, c, d) {
        return -c * ((t=t/d-1)*t*t*t - 1) + b;
    },
    easeInOutQuart: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
        return -c/2 * ((t-=2)*t*t*t - 2) + b;
    },
    easeInQuint: function (x, t, b, c, d) {
        return c*(t/=d)*t*t*t*t + b;
    },
    easeOutQuint: function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t*t*t + 1) + b;
    },
    easeInOutQuint: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
        return c/2*((t-=2)*t*t*t*t + 2) + b;
    },
    easeInSine: function (x, t, b, c, d) {
        return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
    },
    easeOutSine: function (x, t, b, c, d) {
        return c * Math.sin(t/d * (Math.PI/2)) + b;
    },
    easeInOutSine: function (x, t, b, c, d) {
        return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
    },
    easeInExpo: function (x, t, b, c, d) {
        return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
    },
    easeOutExpo: function (x, t, b, c, d) {
        return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
    },
    easeInOutExpo: function (x, t, b, c, d) {
        if (t==0) return b;
        if (t==d) return b+c;
        if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
        return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
    },
    easeInCirc: function (x, t, b, c, d) {
        return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
    },
    easeOutCirc: function (x, t, b, c, d) {
        return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
    },
    easeInOutCirc: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
        return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
    },
    easeInElastic: function (x, t, b, c, d) {
        var s=1.70158;var p=0;var a=c;
        if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
        if (a < Math.abs(c)) { a=c; var s=p/4; }
        else var s = p/(2*Math.PI) * Math.asin (c/a);
        return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
    },
    easeOutElastic: function (x, t, b, c, d) {
        var s=1.70158;var p=0;var a=c;
        if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
        if (a < Math.abs(c)) { a=c; var s=p/4; }
        else var s = p/(2*Math.PI) * Math.asin (c/a);
        return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
    },
    easeInOutElastic: function (x, t, b, c, d) {
        var s=1.70158;var p=0;var a=c;
        if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
        if (a < Math.abs(c)) { a=c; var s=p/4; }
        else var s = p/(2*Math.PI) * Math.asin (c/a);
        if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
        return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
    },
    easeInBack: function (x, t, b, c, d, s) {
        if (s == undefined) s = 1.70158;
        return c*(t/=d)*t*((s+1)*t - s) + b;
    },
    easeOutBack: function (x, t, b, c, d, s) {
        if (s == undefined) s = 1.70158;
        return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
    },
    easeInOutBack: function (x, t, b, c, d, s) {
        if (s == undefined) s = 1.70158;
        if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
        return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
    },
    easeInBounce: function (x, t, b, c, d) {
        return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
    },
    easeOutBounce: function (x, t, b, c, d) {
        if ((t/=d) < (1/2.75)) {
            return c*(7.5625*t*t) + b;
        } else if (t < (2/2.75)) {
            return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
        } else if (t < (2.5/2.75)) {
            return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
        } else {
            return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
        }
    },
    easeInOutBounce: function (x, t, b, c, d) {
        if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
        return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
    }
});
{% endhighlight %}



