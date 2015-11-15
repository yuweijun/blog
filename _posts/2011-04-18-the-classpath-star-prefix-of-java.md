---
layout: post
title: "the classpath*: prefix of java"
date: "Mon Apr 18 2011 18:34:00 GMT+0800 (CST)"
categories: java
---

When constructing an XML-based application context, a location string may use the special `classpath*:` prefix:

{% highlight java %}
ApplicationContext ctx = new ClassPathXmlApplicationContext("classpath*:conf/appContext.xml");
{% endhighlight %}

This special prefix specifies that all classpath resources that match the given name must be obtained (internally, this essentially happens via a ClassLoader.getResources(...) call), and then merged to form the final application context definition.

Classpath*: portability
-----

The wildcard classpath relies on the getResources() method of the underlying classloader. As most application servers nowadays supply their own classloader implementation, the behavior might differ especially when dealing with jar files. A simple test to check if `classpath*` works is to use the classloader to load a file from within a jar
on the classpath:

{% highlight java %}
getClass().getClassLoader().getResources("someFileInsideTheJar").
{% endhighlight %}

Try this test with files that have the same name but are placed inside two different locations.

In case an inappropriate result is returned, check the application server documentation for settings that might affect the classloader behavior.

The `classpath*:` prefix can also be combined with a PathMatcher pattern in the rest of the location path, for example `classpath*:META-INF/*-beans.xml`. In this case, the resolution strategy is fairly simple: a ClassLoader.getResources() call is used on the last non-wildcard path segment to get all the matching resources in the class loader hierarchy, and then off each resource the same PathMatcher resoltion strategy described above is used for the wildcard subpath.

Memo: This article content copied from spring 3.0 framework reference PDF file.

