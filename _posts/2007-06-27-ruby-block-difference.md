---
layout: post
title: "ruby block二种写法之间的区别"
date: "Sun Jun 13 2007 11:38:00 GMT+0800 (CST)"
categories: ruby
---

Ruby的block有二种写法，一种是花括号，一种是do...end，这二种写法略有不同，花括号{}方式与前面对象是紧密结合的：

{% highlight ruby %}
class Array
  def find
    for i in 0 ... self.length
      return self[i] if yield self[i]
    end
    return nil
  end
end

a = [1, 2, 3, 4, 5]
puts a.find { |i|
  i == 4
}

# error syntax
puts a.find do |i|
  i == 4
end

# {...}比do...end块的结合能力强。例如：
#
# foobar a, b do .. end   # foobar 是带块的方法
# foobar a, b { .. }      # b    成了带块的方法
{% endhighlight %}
