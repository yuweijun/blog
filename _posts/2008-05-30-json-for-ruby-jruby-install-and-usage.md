---
layout: post
title: "json for ruby install and usage"
date: "Fri May 30 2008 12:01:00 GMT+0800 (CST)"
categories: ruby
---

ruby和jruby中安装json
-----

{% highlight bash %}
$> sudo gem install json
$> sudo gem install json_pure
{% endhighlight %}

测试
-----

{% highlight ruby %}
require 'rubygems'
require 'json'

class Range
  def to_json(*a)
    {
      'json_class'   => self.class.name,
      'data'         => [ first, last, exclude_end? ]
    }.to_json(*a)
  end

  def self.json_create(o)
    new(*o['data'])
  end
end

puts (1..10).to_json
p JSON.parse((1..10).to_json)
puts JSON.parse((1..10).to_json) == (1..10)

json =<<-"JSON"
{
  "hasItems": true,
  "totalItems": 5,
  "orderId": "xxx-xxxxx-xxx",
  "hasDetails": true,
  "details": [{"item": "2323-2323", "number": 2, "price": 2.00}, {"item": "2323-2324", "number": 3, "price": 3.00}]
}
JSON

p JSON.parse(json)["hasItems"] # => true
p JSON.parse(json)["totalItems"] # => 5
{% endhighlight %}
