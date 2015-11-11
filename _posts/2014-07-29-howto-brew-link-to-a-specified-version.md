---
layout: post
title: "how can i brew link to a specific version"
date: "Wed Jul 29 2014 11:40:26 GMT+0800 (CST)"
categories: macos
---

The usage info
-----

{% highlight bash %}
Usage: `brew switch <formula> <version>`
Example: `brew switch mysql 5.5.29`
{% endhighlight %}

The versions installed on your system you can find with info: `brew info mysql`

And to see the available versions to install, use versions: `brew versions mysql`

And to install an older version of a formula read the answers [here](http://stackoverflow.com/questions/3987683/homebrew-install-specific-version-of-formula).

References
-----

1. [http://stackoverflow.com/questions/13477363/how-can-i-brew-link-a-specific-version](http://stackoverflow.com/questions/13477363/how-can-i-brew-link-a-specific-version)
