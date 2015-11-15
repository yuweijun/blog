---
layout: post
title: "howto maven2 download java source jar package"
date: "Thu Mar 05 2009 00:15:00 GMT+0800 (CST)"
categories: java
---

maven2 download java sources
-----

{% highlight bash %}
$> mvn dependency:sources -Dsilent=true filename
dependency:sources tells Maven to resolve all dependencies and their source attachments, and displays the version.
{% endhighlight %}

maven plugins lists page
-----

1. [http://maven.apache.org/plugins/maven-dependency-plugin/](http://maven.apache.org/plugins/maven-dependency-plugin/)
2. [http://maven.apache.org/plugins/index.html](http://maven.apache.org/plugins/index.html)

References
-----

1. [http://groups.google.com/group/maven-zh/browse_thread/thread/20494aa10d7818ba?pli=1](http://groups.google.com/group/maven-zh/browse_thread/thread/20494aa10d7818ba?pli=1)
