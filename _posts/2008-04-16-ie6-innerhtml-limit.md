---
layout: post
title: "ie6中innerhtml的局限性"
date: "Wed Apr 16 2008 21:59:00 GMT+0800 (CST)"
categories: javascript
---

IE的相关文档表明，在IE中，innerHTML在以下封闭标签中为只读属性:

1. col
2. colgroup
3. frameset
4. html
5. style
6. table
7. tbody
8. thead
9. tfoot
10. title
11. tr

在这些标签中只能读取到innerHTML内容却无法设置，在其他浏览器却都是可以的。

另外在select标签对中用innerHTML写入option，在IE中是可以写入，但无法正常显示，写入的内容与innerHTML的内容已经不一样，不过将其innerHTML=''赋空值却是可以正确将select原来的options移除。

换一种写法，如:

{% highlight javascript %}
document.getElementById('select_elem_id').appendChild(new Option(text, value));
{% endhighlight %}

这个在IE中也是不可行，在其他浏览器中一切正常。

在IE中，往select中写入options可以用select对象的options属性写入:

{% highlight javascript %}
document.getElementById('select_elem_id').options[0] = new Option(text, value);
{% endhighlight %}

这种方法在其他浏览器里也是一样正确执行。
