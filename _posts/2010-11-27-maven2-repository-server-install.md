---
layout: post
title: "maven2 repository server install"
date: "Sat Nov 27 2010 13:06:00 GMT+0800 (CST)"
categories: java
---

nexus maven2安装
-----

以下为安装免费版本的nexus maven2服务器和简单的设置过程：

{% highlight bash %}
$> wget http://nexus.sonatype.org/downloads/nexus-oss-webapp-1.8.0-bundle.tar.gz
$> tar zxvf nexus-oss-webapp-1.8.0-bundle.tar.gz
$> mv nexus-webapp-1.8.0-bundle  /usr/local/nexus
$> cd /usr/local/nexus
$> ls bin/jsw/
$> bin/jsw/linux-x86-32/nexus start
$> tail -f logs/wrapper.log
{% endhighlight %}

启动服务后默认url为：`http://localhost:8081/nexus`

默认的登录名和密码：`admin/admin123`

nexus默认是关闭远程索引下载功能的，主要是担心会造成对服务器的巨大负担，需要我们手工开启。

开启的方式
-----

点击Administration菜单下面的Repositories，将这4个仓库Apache Snapshots，Google code，Codehaus Snapshots，Maven Central的Configuration - Download Remote Indexes修改为true。然后在这三个仓库上分别右键，选择reIndex，这样Nexus就会去下载远程的索引文件。

部署构件至Nexus
-----

Nexus提供了两种方式来部署构件，你可以从UI直接上传，也可以配置Maven部署构件，在上传一个版本时，可以将jar包和source jar包一起上传。

本地maven仓库配置文件，默认位置在用户根目录下的`.m2`目录，文件名为`settings.xml`，如果没有，则创建一份，更新内容如下：

{% highlight xml %}
<?xml version="1.0" encoding="UTF-8"?>

<settings xmlns="http://maven.apache.org/SETTINGS/1.0.0"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://maven.apache.org/SETTINGS/1.0.0 http://maven.apache.org/xsd/settings-1.0.0.xsd">

  <pluginGroups>
    <!-- pluginGroup
     | Specifies a further group identifier to use for plugin lookup.
    <pluginGroup>com.your.plugins</pluginGroup>
    -->
    <pluginGroup>org.mortbay.jetty</pluginGroup>
  </pluginGroups>

  <profiles>
    <profile>
      <id>nexus</id>
      <repositories>
        <repository>
          <id>nexus</id>
          <name>local nexus</name>
          <url>http://localhost:8081/nexus/content/groups/public/</url>
          <releases>
            <enabled>true</enabled>
          </releases>
          <snapshots>
            <enabled>true</enabled>
          </snapshots>
        </repository>
      </repositories>
      <pluginRepositories>
        <pluginRepository>
            <id>nexus</id>
            <name>local nexus</name>
            <url>http://localhost:8081/nexus/content/groups/public/</url>
            <releases><enabled>true</enabled></releases>
            <snapshots><enabled>false</enabled></snapshots>
        </pluginRepository>
       </pluginRepositories>
    </profile>
  </profiles>
  <activeProfiles>
    <activeProfile>nexus</activeProfile>
  </activeProfiles>
</settings>
{% endhighlight %}

References
-----

1. [http://juvenshun.javaeye.com/blog/349534](http://juvenshun.javaeye.com/blog/349534)
2. [http://wj98127.javaeye.com/blog/306358](http://wj98127.javaeye.com/blog/306358)
