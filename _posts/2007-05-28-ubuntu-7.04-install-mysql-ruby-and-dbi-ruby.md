---
layout: post
title: "ubuntu-7.04下安装mysql-ruby及dbi-ruby"
date: "Thu May 28 2007 18:06:00 GMT+0800 (CST)"
categories: ruby
---

在ubuntu中使用apt-get安装ruby的mysql包：

{% highlight bash %}
$> sudo apt-get install libdbi-ruby1.8 libdbi-ruby libdbd-mysql-ruby1.8 libdbd-mysql-ruby

$> irb
irb(main):001:0> require 'dbi'
=> true
irb(main):002:0> exit

$> sudo apt-get install libmysqlclient15-dev zlib1g-dev
$> sudo apt-get install libdbm-ruby1.8 libfcgi-ruby1.8 libfcgi0 libgdbm-ruby1.8 libopenssl-ruby1.8 libruby1.8-dbg
$> sudo gem install mysql

$> irb
irb(main):001:0> require 'mysql'
=> true
irb(main):002:0> require 'rubygems'
=> true
irb(main):004:0> require_gem 'mysql'
=> true
irb(main):005:0> exit
{% endhighlight %}
