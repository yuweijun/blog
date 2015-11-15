---
layout: post
title: "howto set width/height/font/fontsize of vim window"
date: "Sat Oct 11 2008 20:25:00 GMT+0800 (CST)"
categories: vim
---

vim config for width/height/font/fontSize on MacOSX
-----

{% highlight vim %}
" set the screen width and height, the same as set columns and lines
win 80 25
" columns width of the display
" set co=80
" lines number of lines in the display
" set lines=24
" For Mac OS X you can use something like this: >
set guifont=Monaco:h12
{% endhighlight %}


