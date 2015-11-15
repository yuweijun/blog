---
layout: post
title: "phpunit error in ubuntu-11.04"
date: "Sat Jul 02 2011 15:12:00 GMT+0800 (CST)"
categories: linux
---

Got below warning when run phpunit in ubuntu-11.04:

{% highlight text %}
PHP Warning: require_once(PHP/CodeCoverage/Filter.php): failed to open stream: No such file or directory in /usr/bin/phpunit on line 38
PHP Stack trace:
PHP 1. {main}() /usr/bin/phpunit:0
PHP Fatal error: require_once(): Failed opening required 'PHP/CodeCoverage/Filter.php' (include_path='.:/usr/share/php:/usr/share/pear') in /usr/bin/phpunit on line 38
PHP Stack trace:
PHP 1. {main}() /usr/bin/phpunit:0
{% endhighlight %}

repair commands:

{% highlight bash %}
$> sudo apt-get remove phpunit
$> sudo pear channel-discover pear.phpunit.de
$> sudo pear channel-discover pear.symfony-project.com
$> sudo pear channel-discover components.ez.no
$> sudo pear update-channels
$> sudo pear upgrade-all
$> sudo pear install --alldeps phpunit/PHPUnit
$> sudo apt-get install phpunit
{% endhighlight %}
