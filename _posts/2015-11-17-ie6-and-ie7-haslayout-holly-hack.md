---
layout: post
title: "about haslayout of ie6 and ie7"
date: "Tue Nov 17 2015 17:03:49 GMT+0800 (CST)"
categories: web
---

什么是haslayout
-----

layout是windows ie的一个私有概念，它决定了元素如何对其内容定位和尺寸计算，以及与其他元素的关系和相互作用。当一个元素“拥有布局”时，它会负责本身及其子元素的尺寸和定位。而如果一个元素“没有拥有布局”，那么它的尺寸和位置由最近的拥有布局的祖先元素控制。

必须说明的是，ie8及以上浏览器使用了全新的显示引擎，已经不在使用haslayout属性，因此文中提到的haslayout属性只针对ie6和ie7。

为什么会有haslayout
-----

理论上说，每个元素都应该控制自己的尺寸和定位，即每个元素都应该“拥有布局”，当然这只是理想状态。而对于早期的ie显示引擎来说，如果所有元素都“拥有布局”的话，会导致很大的性能问题。因此ie开发团队决定使用布局概念来减少浏览器的性能开销，即只将布局应用于实际需要的那些元素，所以便出现了“拥有布局”和“没有拥有布局”两种情况。

默认情况下拥有布局的元素
-----

{% highlight text %}
body and html (in standards mode)
table, tr, th, td
img
hr
input, button, file, select, textarea, fieldset
marquee
frameset, frame, iframe
objects, applets, embed
{% endhighlight %}

查看和触发haslayout
-----

haslayout是windows ie私有的，而且它不是css属性，我们无法通过css显式的设置元素的haslayout。但我们可以通过javascript来查看一个元素是否拥有布局：

{% highlight html %}
<div id="div1">这是一个div</div>;
{% endhighlight %}

{% highlight javascript %}
var div1 = document.getElementById('div1');
console.log(div1.currentStyle.hasLayout);
// false
{% endhighlight %}

如果元素拥有布局，`obj.currentStyle.hasLayout`就会返回`true`，否则返回`false`。hasLayout是一个`只读属性`，所以也无法通过javascript进行设置。

ie6中可以触发元素haslayout的属性
-----

{% highlight css %}
float: left或right
display: inline-block
position: absolute
width: 除auto外任何值
height: 除auto外任何值
zoom: 处normal外任何值
writing-mode: tb-rl
{% endhighlight %}

ie7中可以触发元素的haslayout的属性
-----

{% highlight css %}
min-height: 任意值
min-width: 任意值
max-height: 除none 外任意值
max-width: 除none 外任意值
overflow: 除visible外任意值，仅用于块级元素
overflow-x: 除visible 外任意值，仅用于块级元素
overflow-y: 除visible 外任意值，仅用于块级元素
position: fixed
{% endhighlight %}

hasLayout测试
-----

如果在ie6/ie7中发生了一些与标准浏览器不一样的渲染行为，如内容出现错位甚至完全不可见。可以为元素设置一个触发haslayout的属性如`{zoom: 1}`后，看看之前的问题是否已经消失，这样就可以判断是否是因为haslayout造成的问题了。

haslayout引起的bug及解决方法
-----

1. ie6中可以使用`{zoom: 1}`或者`{height: 1%}`1来触发。
2. ie7中可以使用`{min-height: 0}`来触发haslayout，这个技术是无害的，因为0本来就是这个属性的初始值，而且没有必要对其他浏览器隐藏这个属性。

重置hasLayout
-----

在另一条规则中重设以下属性为默认值将重置(或撤销)hasLayout：

{% highlight css %}
width, height (设为"auto")
max-width, max-height (设为"none")(在ie7中)
position (设为"static")
float (设为"none")
overflow (设为"visible")(在ie7中)
zoom (设为"normal")
writing-mode (从"tb-rl"设为"lr-t)
{% endhighlight %}

有些属性则不能重置haslayout，也就是说设置haslayout这个操作`有时是不可逆`的。如：

`{display: inline-block`设置`display`属性导致haslayout为true之后，就算在一条独立的规则中覆盖这个属性为`block`或`inline`，haslayout这个标志位也不会被重置为false。

把`mid-width`，`mid-height`设为它们的默认值`0`，仍然会赋予haslayout，但是ie7却可以接受一个不合法的属性`auto`来重置haslayout。

Block Formatting Contexts（BFC）
-----

ie有它自己的haslayout属性，那么非ie浏览器呢？非IE浏览器采用的就是`BFC`（块格式化上下文）。

BFC概念说明
-----

BFC是W3C CSS 2.1规范中的一个概念，它决定了元素如何对其内容进行定位，以及与其他元素的关系和相互作用。

在创建了BFC的元素中，其子元素会一个接一个地放置。垂直方向上他们的起点是一个包含块的顶部，两个相邻的元素之间的垂直距离取决于`margin`特性。在BFC中相邻的块级元素的垂直边距会折叠（collapse）。

在BFC中，每一个元素左外边与包含块的左边相接触（对于从右到左的格式化，右外边接触右边），即使存在浮动也是如此（尽管一个元素的内容区域会由于浮动而压缩），除非这个元素也创建了一个新的BFC。

在CSS3中，对这个概念做了改动：http://www.w3.org/TR/css3-box/#block-level0CSS3中，将BFC叫做`low root`。

BFC触发方式
-----

{% highlight css %}
float: (任何值除了none)
overflow:（任何值除了visible）
display: (table-cell/table-caption/inline-block)
position: (任何值除了static/relative)
{% endhighlight %}

References
-----

1. [http://www.w3.org/TR/CSS21/visuren.html#block-formatting](http://www.w3.org/TR/CSS21/visuren.html#block-formatting)
2. [http://www.smallni.com/haslayout-block-formatting-contexts/](http://www.smallni.com/haslayout-block-formatting-contexts/)
3. [http://riny.net/2013/haslayout/](http://riny.net/2013/haslayout/)
4. [http://adamghost.com/2009/03/ie-has-layout-and-bugs/](http://adamghost.com/2009/03/ie-has-layout-and-bugs/)
5. [The Internet Explorer hasLayout Property](http://www.sitepoint.com/web-foundations/internet-explorer-haslayout-property/)
6. [Cascading Style Sheets - DOM Style APIs - hasLayout property](https://msdn.microsoft.com/en-us/library/ms530764.aspx)
7. ["HasLayout" Overview](https://msdn.microsoft.com/en-us/library/bb250481.aspx)
8. [9 Most Common IE Bugs and How to Fix Them](http://code.tutsplus.com/tutorials/9-most-common-ie-bugs-and-how-to-fix-them--net-7764)
