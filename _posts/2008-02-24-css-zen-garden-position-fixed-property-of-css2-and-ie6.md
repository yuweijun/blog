---
layout: post
title: "css zen garden - position fixed property of css2 and ie6"
date: "Sun Feb 24 2008 21:14:00 GMT+0800 (CST)"
categories: css
---

Exploiting the fact that Internet Explorer lacks support for fixed positioning and for child selectors, a series of rules were created to deliver the optimal design to browsers that comply with the rules of CSS, and to deliver the alternate design to Internet Explorer:

{% highlight css %}
body#css-zen-garden>div#extraDiv2 {
    background-image: url(bg_face.jpg);
    background-repeat: no-repeat;
    background-position: left bottom;
    position: fixed;
    left: 0;
    bottom: 0;
    height: 594px;
    width: 205px;
    z-index: 2;
}
{% endhighlight %}

The following CSS is applied only in browsers that don't understand the previous rule, in this case Internet Explorer. Because the child selectors imply greater specificity, the former rule takes precedence, but only if the browser understands it.

{% highlight css %}
div#extraDiv2 {
    background-image: url(bg_face_ie.jpg);
    background-repeat: no-repeat;
    background-position: left bottom;
    position: absolute;
    left: 0;
    bottom: 0;
    height: 600px;
    width: 265px;
}
{% endhighlight %}


While the fixed-position image scrolls off the page in Internet Explorer 6.0, the design is still attractive and acceptable.

IE7 has supported position fixed property if it’s in standard mode.

References
-----

1. [http://www.csszengarden.com/?cssfile=037/037.css](http://www.csszengarden.com/?cssfile=037/037.css)
