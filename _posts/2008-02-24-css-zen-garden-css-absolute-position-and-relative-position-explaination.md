---
layout: post
title: "css zen garden - css absolute position and relative position explaination"
date: "Sun Feb 24 2008 23:11:00 GMT+0800 (CST)"
categories: css
---

Absolute Positioning:
Understanding absolute positioning means understanding the concept of the document flow.
Absolute positioning provides the ability not only to move an element anywhere within the page, but also to remove it from the document flow. An absolutely positioned block no longer influences other elements within the document, and the linear flow continues as if that block doesn't exist.
Relative positioning:
Relative positioning, on the other hand, will not remove an element from the document flow. Based on the element's starting position, a relative position will offset it and then effectively leave a hole behind that other elements must negotiate, as if the element were still positioned there.
A relatively positioned element stays within the document flow; its originating position continues to affect other elements, whereas its new position is ignored by the document flow.
Relative positioning is mostly useful for offsetting elements; an element with a starting position inside a traditional grid may be easily moved outside the grid structure, and elements can be finely adjusted when other positioning options aren't possible. For example, Savarese employs relative positioning to move the dragon at the bottom of the design from a starting point below the white content area, to its final spot at the left of the footer area. Using absolute positioning to position elements near the bottom of a design is much more difficult than positioning them near the top, so this instance of relative positioning makes the adjustment easier.

{% highlight css %}
#extraDiv1 {
    background-image:url(Dragon.gif);
    background-position:left top;
    background-repeat:no-repeat;
    height:206px;
    left:-360px;
    margin:0pt auto;
    position:relative;
    top:-225px;
    width:96px;
}
{% endhighlight %}

References
-----

1. [http://www.csszengarden.com/?cssfile=070/070.css](http://www.csszengarden.com/?cssfile=070/070.css)
