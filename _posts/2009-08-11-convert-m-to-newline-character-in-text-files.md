---
layout: post
title: "convert ^M to newline character in text files"
date: "Tue Aug 11 2009 12:46:00 GMT+0800 (CST)"
categories: vim
---

If the `^M` character is showing up in files while opening the file concerned, then follow these steps to convert it to a new line. In vi use the following:

{% highlight vim %}
:%s/^M/\n/g
{% endhighlight %}

or with perl on the command line:

{% highlight bash %}
$> perl -pi.bak -e 's/^M/\n/g'
{% endhighlight %}

NOTE: Be sure to create the `^M` by typing `ctrl+V` followed by `ctrl+M`.

`^M` is `ASCII 13` (`Ctrl+M`), which is the `carriage return`.

Different operating systems use different symbols to set the end of a line/new line.

{% highlight text %}
Unix/Linux uses newline (\n)
MacOSX uses carriage return (\r)
Windows/DOS use both (\n\r)
{% endhighlight %}

To prevent the `^M` from showing up in files, be sure to use the `ASCII` (text) mode when transfering text files.

References
-----

1. [http://blog.eukhost.com/webhosting/convert-m-to-newline-character-in-text-files/](http://blog.eukhost.com/webhosting/convert-m-to-newline-character-in-text-files/)
