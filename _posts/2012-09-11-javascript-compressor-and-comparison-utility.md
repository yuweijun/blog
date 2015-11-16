---
layout: post
title: "javascript compressor and obfuscator tools"
date: "Tue, 11 Sep 2012 14:16:42 +0800"
categories: javascript
---

javascript compressor
-----

1. [JSMin](http://crockford.com/javascript/jsmin)
2. [Dojo shrinksafe](http://dojotoolkit.org/docs/shrinksafe)
3. [Packer](http://dean.edwards.name/packer/)
4. [the YUI Compressor](http://developer.yahoo.com/yui/compressor/)

javascript obfuscator
-----

1. [http://yuilibrary.com/download/yuicompressor/](http://yuilibrary.com/download/yuicompressor/)
2. [http://dean.edwards.name/packer/](http://dean.edwards.name/packer/)
3. [https://developers.google.com/closure/](https://developers.google.com/closure/)
4. [https://github.com/mishoo/UglifyJS](https://github.com/mishoo/UglifyJS)

google closure compiler usage
-----

{% highlight bash %}
$> java -jar compiler.jar --js hello.js --js_output_file hello-compiled.js
{% endhighlight %}

perl jsPacker
-----

参数说明：`-e`是混淆的程度

1. 0=None
2. 10=Numeric
3. 62=Normal(alphanumeric)
4. 95=High-ascii

一般使用62即可。

{% highlight bash %}
$> perl jsPacker.pl -i input.js -o output.js -e62
{% endhighlight %}
