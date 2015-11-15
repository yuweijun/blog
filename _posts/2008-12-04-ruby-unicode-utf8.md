---
layout: post
title: "在ruby中将unicode直接量转化成为utf-8字符"
date: "Thu Dec 04 2008 17:13:00 GMT+0800 (CST)"
categories: ruby
---

ruby
-----

{% highlight rub %}
require 'cgi'
def unicode_utf8(unicode_string)
  unicode_string.gsub(/\\u\w{4}/) do |s|
    str = s.sub(/\\u/, "").hex.to_s(2)
    if str.length < 8
      CGI.unescape(str.to_i(2).to_s(16).insert(0, "%"))
    else
      arr = str.reverse.scan(/\w{0,6}/).reverse.select{|a| a != ""}.map{|b| b.reverse}
      # ["100", "111000", "000000"]
      hex = lambda do |s|
        (arr.first == s ? "1" * arr.length + "0" * (8 - arr.length - s.length) + s : "10" + s).to_i(2).to_s(16).insert(0, "%")
      end
      CGI.unescape(arr.map(&hex).join)
    end
  end
end

puts unicode_utf8('test\u4E2Dtest\u6587test\u6D4Btest\u8BD5test')
{% endhighlight %}
