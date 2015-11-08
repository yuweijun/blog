---
layout: post
title: "Rails Routes 详解"
date: "Sun Aug 26 2007 23:34:00 GMT+0800 (CST)"
categories: ruby
---

Rails Routes控制台测试
-----

{% highlight bash %}
$> cd RubyOnRails/app/path
$> cd script/console
>> rs = ActionController::Routing::Routes
>> puts rs.routes
ANY /:controller/service.wsdl/ {:action=>"wsdl"}
ANY /:controller/:action/:id.:format/ {}
ANY /:controller/:action/:id/ {}
=>nil
{% endhighlight %}

修改config/routes.rb文件，添加一行

{% highlight ruby %}
map.connect 'modules/controller/:action/*other', :controller => 'modules/controller'
{% endhighlight %}

回到console

{% highlight bash %}
>> rs.reload
>> puts rs.routes
ANY /:controller/service.wsdl/ {:action=>"wsdl"}
ANY /:controller/:action/:id.:format/ {}
ANY /:controller/:action/:id/ {}
ANY /modules/controller/:action/:other/ {:controller=>"modules/controller"}
=>nil
>> rs.recognize_path '/modules/controller/test/2?x=1'
=> {:action=>"test", :other=>["2?x=1"], :controller=>"modules/controller"}
>> rs.recognize_path '/test/action/1'
=> {:action=>"action", :id=>"1", :controller=>"test"}
>> rs.add_route('/x/y/z', {:controller => 'test', :action => 'index', :id => 0})=> #>>puts rs.routes
ANY /:controller/service.wsdl/ {:action=>"wsdl"}
ANY /:controller/:action/:id.:format/ {}
ANY /:controller/:action/:id/ {}
ANY /modules/controller/:action/:other/ {:controller=>"modules/controller"}
ANY /x/y/z/ {:action=>"index", :id=>0, :controller=>"test"}
=> nil
>> rs.draw do |map|
?> map.connect '/blog/:id', :controller => 'blog', :action => 'list'
>> end
=> [ActionController::Base, ActionView::Base]
>> puts rs.routes
ANY /blog/:id/ {:action=>"list", :controller=>"blog"}
=> nil
>> rs.recognize_path '/blog/1'
=> {:action=>"list", :id=>"1", :controller=>"blog"}
{% endhighlight %}

这里发现原来的routes全部不见了，只有这个新建的routes了，因为draw方法内部实现为：

{% highlight ruby %}
def draw
    clear!
    yield Mapper.new(self)
    named_routes.install
end
{% endhighlight %}

draw方法块里的map对象里method_missing方法就是用来实现具名路由named routes的。先看下源码如下：

{% highlight ruby %}
def method_missing(route_name, *args, &proc)
  super unless args.length >= 1 && proc.nil?
  @set.add_named_route(route_name, *args)
end
{% endhighlight %}

上面对实例变量@set就是前面的rs对象，那么就看一下rs.add_named_route方法吧

{% highlight bash %}
>> rs.clear!
>> rs.add_named_route('index', 'user/index', :controller => 'user', :action => 'index')
puts rs.named_routes
#
=> nil
>> puts rs.routes
ANY /user/index/ {:action=>"index", :controller=>"user"}
=> nil
>> puts rs.named_routes.routes
indexANY /user/index/ {:action=>"index", :controller=>"user"}
=> nil
{% endhighlight %}

定义了具名路后就可以在controller/view中像url_for一样调用，只要在route name后面加上_url就可以，url_for在console里不能调试，可以用rs.generate(request)方法测试。

{% highlight javascript %}
# In addition to providing url_for, named routes are also accessible after including UrlWriter.
url_for(:controller => 'signup', action => 'index', :token => token)
index_url(:id => 1)
#=> http://localhost/user/index?id=1
>> rs.generate(:controller => 'test', :action => 'index', :id => 3)
=> "/test/index/3"
{% endhighlight %}

另外在map这个对象里有个方法root可以给根目录定义路径，用法如下：

{% highlight bash %}
>> rs.draw do |map|
?>   map.root :controller => 'test', :action => 'list'
>> end
ArgumentError: Illegal route: the :controller must be specified!
        from /usr/local/lib/ruby/gems/1.8/gems/actionpack-1.13.3/lib/action_controller/routing.rb:965:in `build'
        from /usr/local/lib/ruby/gems/1.8/gems/actionpack-1.13.3/lib/action_controller/routing.rb:1172:in `add_route'
        from /usr/local/lib/ruby/gems/1.8/gems/actionpack-1.13.3/lib/action_controller/routing.rb:1178:in `add_named_route'
        from /usr/local/lib/ruby/gems/1.8/gems/actionpack-1.13.3/lib/action_controller/routing.rb:997:in `root_without_deprecation'
        from /usr/local/lib/ruby/gems/1.8/gems/activesupport-1.4.2/lib/active_support/deprecation.rb:94:in `root'
        from (irb):60
        from /usr/local/lib/ruby/gems/1.8/gems/actionpack-1.13.3/lib/action_controller/routing.rb:1139:in `draw'
        from (irb):59


# Added deprecation notice for anyone who already added a named route called "root".
# It'll be used as a shortcut for map.connect '' in Rails 2.0.
# def root(*args, &proc)
#   super unless args.length >= 1 && proc.nil?
#   @set.add_named_route("root", *args)
# end
# deprecate :root => "(as the the label for a named route) will become a shortcut for map.connect '', so find another name"
{% endhighlight %}

Rails2.0才会引进此方法，所以Rails建议用户不要使用此具名路由，如下：

{% highlight bash %}
>> rs.draw do |map|
?> map.root '', :controller => 'test', :action => 'list'
>> end
=> [ActionController::Base, ActionView::Base]
>> puts rs.routes
ANY / {:action=>"list", :controller=>"test"}
=> nil
>> puts rs.named_routes.routes
rootANY / {:action=>"list", :controller=>"test"}
=> nil
{% endhighlight %}

这个写法其实就是做了个具名路由，而不是调用map.root方法，调用的是method_missing方法。
