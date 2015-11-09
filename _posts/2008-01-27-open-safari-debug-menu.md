---
layout: post
title: "open safari debug menu"
date: "Sun Jan 27 2008 21:49:00 GMT+0800 (CST)"
categories: javascript
---

open Terminal Application, and input:

{% highlight bash %}
$> defaults write com.apple.Safari IncludeDebugMenu 1
{% endhighlight %}

then reopen safari, debug menu will display at the end of menu bar.
