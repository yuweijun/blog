---
layout: post
title: "install git on mac os leopard"
date: "Wed May 21 2008 23:23:00 GMT+0800 (CST)"
categories: macos
---

Install asciidoc
-----

[http://www.methods.co.nz/asciidoc/asciidoc-8.2.6.tar.gz](http://www.methods.co.nz/asciidoc/asciidoc-8.2.6.tar.gz)

{% highlight bash %}
$> tar -xzf asciidoc-8.2.6.tar.gz
$> cd asciidoc-8.2.6
$> sudo ./install.sh
The uninstall.sh script (actually just a symlink to install.sh) will uninstall AsciiDoc.
{% endhighlight %}

Install git
-----

[http://kernel.org/pub/software/scm/git/git-1.5.5.1.tar.bz2](http://kernel.org/pub/software/scm/git/git-1.5.5.1.tar.bz2)

{% highlight bash %}
$> make configure
$> ./configure --prefix=/usr
$> make
$> sudo make install
{% endhighlight %}
