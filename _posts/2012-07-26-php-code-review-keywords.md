---
layout: post
title: "php代码审计部分关键词"
date: "Thu, 26 Jul 2012 12:19:11 +0800"
categories: php
---

Find user input/output for possible XSS
-----

{% highlight bash %}
$> grep -i -r "echo" *
$> grep -i -r "\$_GET" *
$> grep -i -r "\$_" * | grep "echo"
$> grep -i -r "\$_GET" * | grep "echo"
$> grep -i -r "\$_POST" * | grep "echo"
$> grep -i -r "\$_REQUEST" * | grep "echo"
{% endhighlight %}

Find potential command execution
-----

{% highlight bash %}
$> grep -i -r “shell_exec(” *
$> grep -i -r “system(” *
$> grep -i -r “exec(” *
$> grep -i -r “popen(” *
$> grep -i -r “passthru(” *
$> grep -i -r “proc_open(” *
$> grep -i -r “pcntl_exec(” *
{% endhighlight %}

Find potential code execution
-----

{% highlight bash %}
$> grep -i -r “eval(” *
$> grep -i -r “assert(” *
$> grep -i -r “preg_replace” * | grep “/e”
$> grep -i -r “create_function(” *
{% endhighlight %}

Find potential SQL injection
-----

{% highlight bash %}
$> grep -i -r “\$sql” *
$> grep -i -r “\$sql” * | grep “\$_”
{% endhighlight %}

Find potential information disclosure
-----

{% highlight bash %}
$> grep -i -r “phpinfo” *
{% endhighlight %}

Find potential development functionality
-----

{% highlight bash %}
$> grep -i -r “debug” *
$> grep -i -r “\$_GET['debug']” *
$> grep -i -r “\$_GET['test']” *
{% endhighlight %}

Find potential file inclusion
-----

{% highlight bash %}
$> grep -i -r “file_include” *
$> grep -i -r “include(” *
$> grep -i -r “require(” *
$> grep -i -r “require(\$file)” *
$> grep -i -r “include_once(” *
$> grep -i -r “require_once(” *
$> grep -i -r “require_once(” * | grep “\$_”
{% endhighlight %}

Other
-----

{% highlight bash %}
$> grep -i -r "header(" * | grep "\$_"
{% endhighlight %}
