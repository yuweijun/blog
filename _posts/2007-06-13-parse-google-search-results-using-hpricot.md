---
layout: post
title:  "parse google search results using hpricot"
date: "Sun Jun 13 2007 11:38:00 GMT+0800 (CST)"
categories: ruby
---

使用hpricot抓取google搜索结果页内容：

{% highlight ruby %}
require 'rubygems'
require 'cgi'
require 'open-uri'
require 'hpricot'

q = %w{david cain pc}.map { |w| CGI.escape(w) }.join("+")
url = "http://www.google.com/search?q=#{q}"
doc = Hpricot(open(url).read)
urls = (doc/"div[@class='g'] a")
urls.each {|url| puts url['href']}
{% endhighlight %}
