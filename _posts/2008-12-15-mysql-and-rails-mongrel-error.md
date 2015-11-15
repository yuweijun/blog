---
layout: post
title: "mongrel and rails 2.2.2 环境下报 mysql lib 的错误及解决方法"
date: "Mon Dec 15 2008 17:25:00 GMT+0800 (CST)"
categories: mysql
---

error
-----

{% highlight text %}
Processing Rails::InfoController#properties (for 127.0.0.1 at 2008-12-15 17:10:53) [GET]
LoadError (dlopen(/Library/Ruby/Site/1.8/universal-darwin9.0/mysql.bundle, 9): Library not loaded: /usr/local/mysql/lib/libmysqlclient.15.dylib
Referenced from: /Library/Ruby/Site/1.8/universal-darwin9.0/mysql.bundle
Reason: image not found - /Library/Ruby/Site/1.8/universal-darwin9.0/mysql.bundle):
/Library/Ruby/Site/1.8/universal-darwin9.0/mysql.bundle
/Library/Ruby/Site/1.8/rubygems/custom_require.rb:31:in `require'
/Library/Ruby/Gems/1.8/gems/activesupport-2.2.2/lib/active_support/dependencies.rb:153:in `require'
{% endhighlight %}

在MAC OSX 10.5 和 mysql 5.1.23-rc 上，rails 2.2.2 用 mongrel server 启动时访问首页mysql连接属性时抛出以上错误，在对应的/usr/local/mysql/lib下根本没有/usr/local/mysql/lib/libmysqlclient.15.dylib这个文件，当然会加载失败，之前在 rails 2.0.2 上倒没有碰到过这个问题。

从mysql官网重新下载了一个 mysql 5.0.67，从其下面拷了一个libmysqlclient.15.dylib 到 /usr/local/mysql/lib/libmysqlclient.15.dylib，就可以解决此问题。
{% highlight bash %}
$> sudo cp /usr/local/mysql5/lib/libmysqlclient.15.dylib /usr/local/mysql/lib/libmysqlclient.15.dylib
{% endhighlight %}
