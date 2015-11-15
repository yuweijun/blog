---
layout: post
title: "debugging rails with ruby-debug"
date: "Mon Mar 01 2010 14:06:00 GMT+0800 (CST)"
categories: ruby
---

ruby-debug setup
-----

{% highlight bash %}
$> sudo gem install ruby-debug
{% endhighlight %}

start rails webrick/mongrel web server
-----

{% highlight bash %}
$> script/server --debugger
{% endhighlight %}

using debugger in controller
-----

{% highlight ruby %}
class PeopleController < ApplicationController
    def new
        debugger # 当应用程序访问到此行时，在console中会出现debugger控制台
        @person = Person.new
    end
end
{% endhighlight %}

debugger shell 命令帮助
-----

{% highlight text %}
(rdb:40) help
ruby-debug help v0.10.3
Type 'help ' for help on a specific command

Available commands:
backtrace  delete   enable  help    next  quit     show
break      disable  eval    info    p     reload   source
catch      display  exit    irb     pp    restart  step
condition  down     finish  list    ps    save     thread
continue   edit     frame   method  putl  set      trace
{% endhighlight %}

其中比较常用的是next/list/continue/method/backtrace命令。

References
-----

1. [http://articles.sitepoint.com/article/debug-rails-app-ruby-debug](http://articles.sitepoint.com/article/debug-rails-app-ruby-debug)
2. [http://guides.rubyonrails.org/debugging_rails_applications.html](http://guides.rubyonrails.org/debugging_rails_applications.html)
