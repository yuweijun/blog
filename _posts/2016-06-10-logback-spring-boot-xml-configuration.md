---
layout: post
title: "logback.xml configuration example"
date: "Fri, 10 Jun 2016 22:28:17 +0800"
categories: java
---

根据`spring-boot`项目中的`logback`配置，定义了一份自己测试环境中使用的`logback`配置文件，并且对其中主要配置添加了备注说明。

{% highlight xml %}
<?xml version="1.0" encoding="UTF-8"?>
<configuration debug="true">

    <!--
        Defining variables
        The syntax of variable substitution is similar to that of Unix shells.
        The string between an opening ${ and closing } is interpreted as a reference to the value of the property. For property aName, the string "${aName}" will be replaced with the value held by the aName property.

        Default values for variables
        Under certain circumstances, it may be desirable for a variable to have a default value if it is not declared or its value is null.
        As in the Bash shell, default values can be specified using the ":-" operator.
        For example, assuming the variable named aName is not defined, "${aName:-golden}" will be interpreted as "golden".

        Locate log file according by system property: ${catalina.home} or ${java.io.tmpdir} if $catalina.home} not defined.
    -->
    <property name="LOG_FILE" value="${catalina.home:-${java.io.tmpdir:-/tmp}}/logs/spring-jdbc.log}"/>

    <!--
        Below is a configuration file illustrating coloring.
        Note the %cyan conversion specifier enclosing "%logger{39}". This will output the logger name abbreviated to 39 characters in cyan.
        The %highlight conversion specifier prints its sub-pattern in bold-red for events of level ERROR, in red for WARN, in BLUE for INFO, and in the default color for other levels.
    -->
    <property name="CONSOLE_LOG_PATTERN"
              value="%d{yyyy-MM-dd HH:mm:ss.SSS} %highlight(-%5p) --- [%15.15thread] %cyan(%-40.40logger{39} [%5line] :) %m%n%ex"/>

    <property name="FILE_LOG_PATTERN"
              value="%d{yyyy-MM-dd HH:mm:ss.SSS} -%5p --- [%15.15thread] %-40.40logger{39} [%5line] : %m%n%ex"/>

    <!--
        What is an Appender? - http://logback.qos.ch/manual/appenders.html

        Logback delegates the task of writing a logging event to components called appenders.
        Appenders must implement the ch.qos.logback.core.Appender interface.
    -->
    <appender name="CONSOLE" class="ch.qos.logback.core.ConsoleAppender">

        <!--
            What is an encoder - http://logback.qos.ch/manual/encoders.html

            Encoders are responsible for transforming an event into a byte array as well as writing out that byte array into an OutputStream.
            Encoders were introduced in logback version 0.9.19. In previous versions, most appenders relied on a layout to transform an event into a string and write it out using a java.io.Writer. In previous versions of logback, users would nest a PatternLayout within FileAppender.

            Since logback 0.9.19, FileAppender and sub-classes expect an encoder and no longer take a layout.
        -->
        <encoder>

            <!--
                What is a layout? - http://logback.qos.ch/manual/layouts.html

                Layouts are logback components responsible for transforming an incoming event into a String.

                PatternLayout/XMLLayout/HTMLLayout
            -->
            <pattern>${CONSOLE_LOG_PATTERN}</pattern>
            <charset>utf8</charset>
        </encoder>
    </appender>

    <!--
        RollingFileAppender extends FileAppender to backup the log files depending on RollingPolicy and TriggeringPolicy.
    -->
    <appender name="FILE" class="ch.qos.logback.core.rolling.RollingFileAppender">
        <encoder>
            <pattern>${FILE_LOG_PATTERN}</pattern>
        </encoder>
        <file>${LOG_FILE}</file>
        <rollingPolicy class="ch.qos.logback.core.rolling.FixedWindowRollingPolicy">
            <fileNamePattern>${LOG_FILE}.%i</fileNamePattern>
        </rollingPolicy>

        <!--
            SizeBasedTriggeringPolicy looks at size of the file being currently written to.
            If it grows bigger than the specified size, the FileAppender using the SizeBasedTriggeringPolicy rolls the file and creates a new one.
        -->
        <triggeringPolicy class="ch.qos.logback.core.rolling.SizeBasedTriggeringPolicy">
            <MaxFileSize>10MB</MaxFileSize>
        </triggeringPolicy>
    </appender>

    <!--
        To ensure that all loggers can eventually inherit a level, the root logger always has an assigned level.

        By default, this level is DEBUG.
    -->
    <root level="DEBUG">
        <appender-ref ref="CONSOLE"/>
        <appender-ref ref="FILE"/>
    </root>
</configuration>
{% endhighlight %}

References
-----

1. [Chapter 3: Logback configuration](http://logback.qos.ch/manual/configuration.html)
2. [Chapter 6: Layouts](http://logback.qos.ch/manual/layouts.html)
3. [Spring Boot Reference Guide](http://docs.spring.io/spring-boot/docs/current/reference/htmlsingle/)
