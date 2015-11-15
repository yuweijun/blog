---
layout: post
title: "maven:install在命令行构建成功而m2eclipse中构建失败"
date: "Mon Aug 30 2010 11:29:00 GMT+0800 (CST)"
categories: java
---

在命令行中用

{% highlight bash %}
$> mvn package
{% endhighlight %}

能构建成功，但在eclipse中用`m2eclipse`插件则构建失败，提示信息摘录部分如下：

{% highlight text %}
[ERROR] FATAL ERROR
[INFO] ------------------------------------------------------------------------
[INFO] dependenciesInfo : dependenciesInfo
---- Debugging information ----
message : dependenciesInfo : dependenciesInfo
cause-exception : com.thoughtworks.xstream.mapper.CannotResolveClassException
cause-message : dependenciesInfo : dependenciesInfo
class : org.apache.maven.plugin.war.util.WebappStructure
required-type : org.apache.maven.plugin.war.util.WebappStructure
path : /webapp-structure/dependenciesInfo
{% endhighlight %}

在网上查了一些资料，主要参考：[http://jira.codehaus.org/browse/MWAR-187](http://jira.codehaus.org/browse/MWAR-187)

之所以命令行能成功，而插件不成功，主要原因是二者的maven版本不一样造成的，因为`2eclipse`使用`embedded Maven 3`(Maven Embedder 3.0.-SNAPSHOT)，可以通过`window->perferences->Maven->Installations`查看到。

解决这个问题的方法在上文链接中也有提到：

1. Maven版本降级
2. 最快捷的方式是禁用缓存(webapp-cache.xml)
3. 在eclipse中用m2eclipse插件，先运行mvn clean后(这样会删除target目录)，再运行mvn install

{% highlight html %}
<configuration>
    <useCache>false</useCache>
</configuration>
{% endhighlight %}


References
-----

1. [http://jira.codehaus.org/browse/MWAR-187](http://jira.codehaus.org/browse/MWAR-187)

