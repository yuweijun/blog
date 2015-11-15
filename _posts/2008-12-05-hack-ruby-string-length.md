---
layout: post
title: "hack ruby string#length"
date: "Fri Dec 05 2008 11:57:00 GMT+0800 (CST)"
categories: ruby
---

ruby
-----

{% highlight ruby %}
en = 'test'
cn = '一'
p en # >> "test"
p cn # >> "\344\270\200"
puts cn.inspect # >> "\344\270\200"
p "344".oct.to_s(16)
# >> "e4"
p "270".oct.to_s(16)
# >> "b8"
p "200".oct.to_s(16)
# >> "80"
p "344".oct
# >> 228
p "270".oct
# >> 184
p "200".oct
# >> 128
cn.scan(/./).each do |ch|
p ch, ch[0]
end

# hack ruby String#length
puts en.length # >> 4
puts cn.length # >> 3
puts en.scan(/./).length # >> 4
puts cn.scan(/./).length # >> 3
puts en.scan(/./u).length # >> 4
puts cn.scan(/./u).length # >> 1
{% endhighlight %}
