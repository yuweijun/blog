---
layout: post
title: "maven2学习笔记"
date: "Fri Sep 17 2010 18:24:00 GMT+0800 (CST)"
categories: java
---

maven的pom文件有一份默认的super pom，所有新建的pom文件都是继承自此pom，这个super pom的build配置部分如下：

{% highlight text %}
<build>
    <directory>target</directory>
    <outputDirectory>target/classes</outputDirectory>
    <finalName>${artifactId}-${version}</finalName>
    <testOutputDirectory>target/test-classes</testOutputDirectory>
    <sourceDirectory>src/main/java</sourceDirectory>
    <scriptSourceDirectory>src/main/scripts</scriptSourceDirectory>
    <testSourceDirectory>src/test/java</testSourceDirectory>
    <resources>
        <resource>
            <directory>src/main/resources</directory>
        </resource>
    </resources>
    <testResources>
        <testResource>
            <directory>src/test/resources</directory>
        </testResource>
    </testResources>
</build>
{% endhighlight %}

在web项目中会希望在`mvn package`之后将maven导入的lib文件自动复制到`wepapp/WEB-INF/lib`下方便测试，如果maven导入的依赖文件发生变化时，将lib目录下的文件先清理之后，再重新加入，同时也可以将其他的lib目录的文件一起复制到`webapp/WEB-INF/lib`下，要达到这个功能，可以用maven去运行ant的可执行脚本，`maven-antrun-plugin`就达到这个目的，只要将ant的任务放到tasks标签里，格式如：

{% highlight text %}
<project>
    <build>
        <plugins>
            <plugin>
                <artifactId>maven-antrun-plugin</artifactId>
                <version>1.4</version>
                <executions>
                    <execution>
                        <phase>
                            <!-- a lifecycle phase -->
                        </phase>
                        <configuration>
                            <tasks>

                                <!--
                                  Place any Ant task here. You can add anything
                                  you can add between <target> and </target> in a
                                  build.xml.
                                -->

                            </tasks>
                        </configuration>
                        <goals>
                            <goal>run</goal>
                        </goals>
                    </execution>
                </executions>
            </plugin>
        </plugins>
    </build>
</project>
{% endhighlight %}

在linux中，因为默认使用了`openjdk`，所以在使用maven时，会遇到提示`tools.jar`包的依赖问题，可以在POM中添加下面的配置，引入`tools.jar`:

{% highlight text %}
<profiles>
    <profile>
        <id>default-tools.jar</id>
        <activation>
            <property>
                <name>java.vendor</name>
                <value>Sun Microsystems Inc.</value>
            </property>
        </activation>
        <dependencies>
            <dependency>
                <groupId>com.sun</groupId>
                <artifactId>tools</artifactId>
                <version>1.4.2</version>
                <scope>system</scope>
                <systemPath>${java.home}/../lib/tools.jar</systemPath>
            </dependency>
        </dependencies>
    </profile>
</profiles>
{% endhighlight %}

在项目目录下运行mvn命令时，如果看到`jvm 1.3`的编译错误，则需要为maven指定源码和jvm的版本，如下：

{% highlight text %}
<build>
    <plugins>
        <plugin>
            <groupId>org.apache.maven.plugins</groupId>
            <artifactId>maven-compiler-plugin</artifactId>
            <version>2.0.2</version>
            <configuration>
                <source>1.6</source>
                <target>1.6</target>
            </configuration>
        </plugin>
    </plugins>
</build>
{% endhighlight %}

在项目下面运行`jUnit test`时，如果运行`mvn test`是成功的，但是单独在一个test文件是运行jUnit test时则报错说`Class not found`(或者二者相反)，这个是因为maven默认为test的java文件生成的class放到`target/test-classes`，项目本身的classes output目录可能是设置在`webapp/WEB-INF/classes`目录中的，二者不一致才造成这个问题，需要在build标签下面设置`outputDirectory`和`testOutputDirectory`这二个标签，让maven也到`webapp/WEB-INF/classes`目录中查找编译后的文件。

maven中常用命令说明，项目打包和安装到仓库中，在项目下运行:

{% highlight bash %}
$> mvn package
{% endhighlight %}

为当前项目生成一个jar文件或者是war文件，这个由pom文件配置中的packaging参数决定是jar还是war，运行成功后，会在`${basedir}/target`目录下生成文件。如果需要将生成的jar文件放到maven仓库中(默认是:`~/.m2/repository`)，则运行命令:

{% highlight bash %}
$> mvn install
{% endhighlight %}

为eclipse ide生成maven项目，配合eclipse maven插件如:`m2e - http://m2eclipse.sonatype.org/sites/m2e`一起使用：

{% highlight bash %}
$> mvn eclipse:eclipse
{% endhighlight %}

运行当前项目中的junit test，maven是使用`maven-surefire-plugin`完成test的，关于这个插件，可以查看[这篇文章](http://tianya23.blog.51cto.com/1081650/386012)中的说明，可以忽略或者指定测试文件的名字，以及在test发生错误如何不影响打包:

{% highlight bash %}
$> mvn test
{% endhighlight %}

根据上面的maven知识点，配置出符合项目需求的pom配置的build部分如下：

{% highlight text %}
<build>
    <sourceDirectory>src/main/java</sourceDirectory>
    <resources>
        <resource>
            <directory>src/main/resources</directory>
        </resource>
    </resources>

    <outputDirectory>src/main/webapp/WEB-INF/classes</outputDirectory>
    <testOutputDirectory>src/main/webapp/WEB-INF/classes</testOutputDirectory>

    <plugins>
        <plugin>
            <artifactId>maven-compiler-plugin</artifactId>
            <configuration>
                <source>1.6</source>
                <target>1.6</target>
                <encoding>UTF-8</encoding>
            </configuration>
        </plugin>
        <plugin>
            <groupId>org.apache.maven.plugins</groupId>
            <artifactId>maven-war-plugin</artifactId>
            <configuration>
                <webXml>src/main/webapp/WEB-INF/web.xml</webXml>
            </configuration>
        </plugin>

        <plugin>
            <artifactId>maven-antrun-plugin</artifactId>
            <executions>
                <execution>
                    <id>copy-lib-src-webapp</id>
                    <phase>install</phase>
                    <configuration>
                        <tasks>
                            <delete dir="src/main/webapp/WEB-INF/lib" />
                            <copy todir="src/main/webapp/WEB-INF/lib">
                                <fileset dir="target/package-name-0.0.1-SNAPSHOT/WEB-INF/lib">
                                    <include name="*" />
                                </fileset>
                            </copy>
                            <copy todir="src/main/webapp/WEB-INF/lib">
                                <fileset dir="lib">
                                    <include name="*" />
                                </fileset>
                            </copy>
                        </tasks>
                    </configuration>
                    <goals>
                        <goal>run</goal>
                    </goals>
                </execution>
            </executions>
        </plugin>
    </plugins>

</build>
{% endhighlight %}

可以从下面的maven仓库中查找包的依赖和maven配置：

* [http://repository.apache.org](http://repository.apache.org)
* [http://www.artifact-repository.org](http://www.artifact-repository.org)
* [http://mvnrepository.com](http://mvnrepository.com)
* [http://www.mvnbrowser.com](http://www.mvnbrowser.com)
* [http://www.jarvana.com](http://www.jarvana.com)
* [http://mavensearch.net](http://mavensearch.net)

References
-----

1. [http://maven.apache.org/general.html](http://maven.apache.org/general.html)
1. [http://maven.apache.org/plugins/](http://maven.apache.org/plugins/)
1. [http://maven.apache.org/pom.html](http://maven.apache.org/pom.html)
1. [http://maven.apache.org/settings.html](http://maven.apache.org/settings.html)
1. [http://maven.apache.org/guides/getting-started/index.html](http://maven.apache.org/guides/getting-started/index.html)
1. [http://maven.apache.org/plugins/maven-antrun-plugin/usage.html](http://maven.apache.org/plugins/maven-antrun-plugin/usage.html)
1. [http://maven.apache.org/plugins/maven-surefire-plugin/usage.html](http://maven.apache.org/plugins/maven-surefire-plugin/usage.html)
1. [http://maven.apache.org/guides/introduction/introduction-to-the-pom.html](http://maven.apache.org/guides/introduction/introduction-to-the-pom.html)
