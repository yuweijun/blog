---
layout: post
title: "不同版本ie css hack方法"
date: "Sun, 31 Jul 2011 16:16:27 +0800"
categories: css
---

ie6，在属性名前加下划线`_`
-----

{% highlight css %}
#underline { _color: blue }
{% endhighlight %}

ie6，ie7，在属性名前加星号`*`
-----

{% highlight css %}
#asterisk { *color: blue; }
{% endhighlight %}

ie6，ie7，ie8，在属性值后面加上`\9`
-----

{% highlight css %}
#forie { color: blue\9; }
{% endhighlight %}
