---
layout: post
title: "hpricot css selector speed test"
date: "Mon Aug 13 2007 23:11:00 GMT+0800 (CST)"
categories: ruby
---

使用hpricot测试不同的css选择器的解析速度。

{% highlight ruby %}
require 'rubygems'
require 'scrubyt' # mechanize hpricot open-uri rubyinline parse-tree ...

doc = Hpricot(open('http://extjs.com/playpen/slickspeed/system/template.php?include=prototype.js&function=$$&modifier=&nocache=1187009411'))


def step(doc, selector)
  print selector + "\t\t"
  start_time = Time.now.to_f
  rs = doc/selector
  end_time = Time.now.to_f
  print rs.length.to_s + "\t\t"
  puts  ((end_time - start_time) * 1000).round # !> (...) interpreted as grouped expression
end

step(doc, "*")
step(doc, "div:only-child")
step(doc, "div:contains(CELIA)")
step(doc, "div:nth-child(even)")
step(doc, "div:nth-child(2n)")
step(doc, "div:nth-child(odd)")
step(doc, "div:nth-child(2n+1)")
step(doc, "div:nth-child(n)")
step(doc, "div:last-child")
step(doc, "div:first-child")
step(doc, "div:not(:first-child)")
step(doc, "div:not(.dialog)")
step(doc, "div > div")
step(doc, "div + div")
step(doc, "div ~ div")
step(doc, "body")
step(doc, "body div")
step(doc, "div")
step(doc, "div div")
step(doc, "div div div")
step(doc, "div, div, div")
step(doc, "div, a, span")
step(doc, ".dialog")
step(doc, "div.dialog")
step(doc, "div.dialog.emphatic")
step(doc, "div .dialog")
step(doc, "div.character, div.dialog")
step(doc, "#speech5")
step(doc, "div#speech5")
step(doc, "div #speech5")
step(doc, "div.scene div.dialog")
step(doc, "div#scene1 div.dialog div")
step(doc, "#scene1 #speech1")
step(doc, "div[@class]")
step(doc, "div[@class='dialog']")
step(doc, "div[@class^='dia']")
step(doc, "div[@class$='log']")
step(doc, "div[@class*='sce']")
step(doc, "div[@class|='dialog']")
step(doc, "div[@class!='madeup']")
step(doc, "div[@class~='dialog']")

# >> selector founded time
# >> * 755 24
# >> div:only-child 22 223
# >> div:contains(CELIA) 26 130
# >> div:nth-child(even) 106 70
# >> div:nth-child(2n) 14 65
# >> div:nth-child(odd) 137 116
# >> div:nth-child(2n+1) 14 191
# >> div:nth-child(n) 31 65
# >> div:last-child 53 101
# >> div:first-child 51 89
# >> div:not(:first-child) 192 100
# >> div:not(.dialog) 192 49
# >> div > div 242 171
# >> div + div 0 35
# >> div ~ div 240 6882
# >> body 1 20
# >> body div 243 37
# >> div 243 26
# >> div div 242 287
# >> div div div 241 525
# >> div, div, div 729 72
# >> div, a, span 243 184
# >> .dialog 51 41
# >> div.dialog 51 48
# >> div.dialog.emphatic 5 52
# >> div .dialog 51 318
# >> div.character, div.dialog 99 104
# >> #speech5 1 3
# >> div#speech5 1 165
# >> div #speech5 1 26
# >> div.scene div.dialog 49 101
# >> div#scene1 div.dialog div 142 240
# >> #scene1 #speech1 1 3
# >> div[@class] 103 40
# >> div[@class='dialog'] 45 50
# >> div[@class^='dia'] 51 47
# >> div[@class$='log'] 45 57
# >> div[@class*='sce'] 1 201
# >> div[@class|='dialog'] 45 62
# >> div[@class!='madeup'] 243 46
# >> div[@class~='dialog'] 51 51
{% endhighlight %}
