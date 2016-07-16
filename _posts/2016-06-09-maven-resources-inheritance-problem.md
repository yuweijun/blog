---
layout: post
title: "maven resources inheritance problem"
date: "Thu, 09 Jun 2016 14:12:33 +0800"
categories: java
---

如果maven项目中父项目pom.xml里的`build > resources`项有配置，而子项目pom.xml中的`build > resources`没有配置时，则子项目直接继承父项目的配置内容。

如果子项目中有配置`build > resources`，则会覆盖父项目中的全部配置，即父项目中pom.xml中关于`build > resources`的配置就无效了，不会再被子项目继承，这不同于`dependencies > dependence`和`build > plugins > plugin`，这二个会完整的继承父项目的设置，并且会覆盖相同的配置，合并不同的配置。

所以maven[官方JIRA](https://issues.apache.org/jira/browse/MNG-2751)中很多人认为这是一个bug，因为这与继承的直观印象相左，很容易误用，不过这个问题并没有被进一步修正过，状态为CLOSED。

以下就采用[stackoverflow.com](http://stackoverflow.com/questions/3008065/maven-2-resources-inheritance-parent-child-project)上用户的提问来简单说明一下：

Example
-----

项目结构如下：

> parent-pom.xml
>
> └── child-pom.xml

parent-pom.xml
=====

{% highlight xml %}
<resources>
    <resource>
        <directory>src/main/resources</directory>
        <excludes>
            <exclude>${dev-config.path}</exclude>
        </excludes>
    </resource>
    <resource>
        <directory>src/main/rules</directory>
    </resource>
    <resource>
        <directory>src/test/rules</directory>
    </resource>
<resources>
{% endhighlight %}

如果以上配置之后，而子项目中不配置`<resources>`，那么`parent-pom.xml`中的设置在子项目完全有效。但是如果在子项目中有如下配置的话，就会完全无视`parent-pom.xml`中的配置了。

child-pom.xml
=====

{% highlight xml %}
<resources>
    <resource>
        <directory>src/main/resources</directory>
        <excludes>
            <exclude>${dev-config.path}</exclude>
        </excludes>
    </resource>
</resources>
{% endhighlight %}

如果在子项目`child-pom.xml`中有如上配置，虽然只是简单重复了`parent-pom.xml`中的部分配置，但结果会发现`parent-pom.xml`中如下所示的2个`resource`的配置最后发布时没有被打包进生成的jar/war包中。

{% highlight xml %}
<resources>
    <resource>
        <directory>src/main/rules</directory>
    </resource>
    <resource>
        <directory>src/test/rules</directory>
    </resource>
</resources>
{% endhighlight %}

这个问题在stackoverflow和maven的官方JIRA里都有很多人提问，可以参考一下，另外官方也没有文档说明`resources`的继承使用方式。

References
-----

1. [Maven 2 resource inheritance](http://stackoverflow.com/questions/3008065/maven-2-resources-inheritance-parent-child-project)
2. [Resource inheritance isn't additive](https://issues.apache.org/jira/browse/MNG-2751)
3. [parent pom and it's children](https://issues.apache.org/jira/browse/MNG-5054)
4. [Introduction to the POM](http://maven.apache.org/guides/introduction/introduction-to-the-pom.html)
5. [POM Reference](https://maven.apache.org/pom.html)
