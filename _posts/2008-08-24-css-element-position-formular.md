---
layout: post
title: "css element position 计算公式"
date: "Sun Aug 24 2008 14:46:00 GMT+0800 (CST)"
categories: javascript
---

以下部分`element.style`的属性值需要用到`parseInt(value || 0)`来转换。

{% highlight javascript %}
clientWidth ＝ element.style.paddingLeft + element.style.width + element.style.paddingRight
clientHeight = element.style.paddingTop + element.style.height + element.style.paddingBottom
clinetLeft = borderLeftWidth
clientTop = borderTopWidth
clientRight = offsetWidth - clientWidth - clientLeft
clientBottom = offsetHeight - clientHeight - clientTop

element.style.left = element.offsetLeft - element.style.marginLeft
element.style.top ＝ element.offsetTop - element.style.marginTop
{% endhighlight %}

当元素的offsetParent不是body时，上面最后二个公式需要特别注意。
