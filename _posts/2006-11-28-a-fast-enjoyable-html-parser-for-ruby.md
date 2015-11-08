---
layout: post
title: "A Fast, Enjoyable HTML Parser for Ruby"
date: "Tue Nov 28 2006 22:24:00 GMT+0800 (CST)"
categories: ruby
---

Hpricot is a very flexible HTML parser, based on Tanaka Akira's HTree and John Resig's JQuery, but with the scanner recoded in C (using Ragel for scanning.) I've borrowed what I believe to be the best ideas from these wares to make Hpricot heaps of fun to use.

{% highlight ruby %}
require 'hpricot'
require 'open-uri'
# load the RedHanded home page
doc = Hpricot(open("http://redhanded.hobix.com/index.html"))
# change the CSS class on links
(doc/"span.entryPermalink").set("class", "newLinks")
# remove the sidebar
(doc/"#sidebar").remove
# print the altered HTML
puts doc
{% endhighlight %}

