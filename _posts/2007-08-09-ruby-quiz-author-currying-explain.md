---
layout: post
title: "ruby quiz author's currying explain"
date: "Thu Aug 09 2007 00:27:00 GMT+0800 (CST)"
categories: ruby
---

Ruby lambda version
-----

{% highlight ruby %}
class Proc
  def curry(&block)
    lambda {|*args| call(*block[*args]) }
  end
end

murry = lambda {|i, j, *k| i * j}
# p murry[5, 2]

multiply = murry.curry {|*k| k + [2]}
# => Proc, lambda {|*args| call(*block[*args])}; block
# => Proc, lambda {|*k| k + [2]}; k.class is Array.

p multiply[5]
# proc[] is synonym for proc.call

p multiply[5, 3]
# output 5 * 3 = 15, not 5 * 2
# step explain double[5]

p lambda {|*k| k + [2]}[5]
p murry.call(*[5, 2])

p lambda {|*k| k + [2]}[5, 4, 3]
p murry.call(*[5, 4, 3, 2]) # 5 * 4
{% endhighlight %}

javascript version
-----

{% highlight javascript %}
console.log(function() {
    return arguments[0] * arguments[1];
}.apply(null, function() {
    return [arguments[0]].concat(2);
}(5)));
{% endhighlight %}

Ruby version
-----

{% highlight ruby %}
class Object
  def curry(new_name, old_name, &args_munger)
    ([Class, Module].include?(self.class) ? self : self.class).class_eval do
      define_method(new_name) { |*args| send(old_name, *args_munger[args]) }
    end
  end
end

class Value
  def initialize(value)
    @value = value
  end

  def *(other)
    @value * other
  end

  curry(:double, :*) { [2] }
  curry(:triple, :*) { |args| args.unshift(3) }
end

five, howdy = Value.new(5), Value.new("Howdy ")
puts five * 2 # => 10
puts five.double # => 10
puts five.triple # => 15
puts howdy.triple # => "Howdy Howdy Howdy "
{% endhighlight %}
