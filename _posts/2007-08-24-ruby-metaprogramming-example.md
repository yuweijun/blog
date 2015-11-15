---
layout: post
title: "ruby metaprogramming example"
date: "Fri Aug 24 2007 11:41:00 GMT+0800 (CST)"
categories: ruby
---

ruby metaprogramming需要熟悉Ruby的一些方法和类：

{% highlight text %}
eval
class_eval
module_eval
const_get
instance_variable_get
instance_variable_set
define_method
const_missing
undef
remove_method
undef_method
remove_const
ancestors
constants
class_variables
instance_variables
instance_methods
public_instance_methods
protected_instance_methods
included_modules
private_methods
public_methods
caller
set_trace_func
ObjectSpace.each_object
method_missing
alias_method
singleton_method_added
inherited
included
extend_object
define_finalizer
block_given?
yield
{% endhighlight %}

举例可以为类添加属性property
-----

{% highlight ruby %}
module Properties
 def property(sym)
   # 实际上，使用Ruby中已有的attr_accessor :property可以起到同样的效果。
   define_method(sym) do
     instance_variable_get("@#{sym}")
   end

    define_method("#{sym}=") do |value|
      instance_variable_set("@#{sym}", value)
      puts sym
    end

    define_method("add_#{sym}_listener") do |z|
      puts sym
      z.call('yy') if z.kind_of?Proc
      puts z if z.kind_of?String
    end
 end
end

class CruiseShip
  self.extend Properties
  property :direction
  property :speed

  def initialize
    @listener = []
  end

end

h = CruiseShip.new
h.add_direction_listener('xxxx')
h.add_speed_listener lambda {|x| puts "Oy... someone changed the property to #{x}"}
h.speed = 10

puts h.speed
{% endhighlight %}
