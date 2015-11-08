---
layout: post
title: "Ruby spreadsheet/excel生成Excel"
date: "Thu Aug 16 2007 12:21:00 GMT+0800 (CST)"
categories: ruby
---

Ruby生成excel文件。

{% highlight ruby %}
require "rubygems"
require "spreadsheet/excel"

include Spreadsheet

workbook = Excel.new("test.xls")

# format实际上没有生效
format = Format.new
format.color = "green"
format.bold = true

worksheet = workbook.add_worksheet
worksheet.write(0, 0, "Hello", format)
worksheet.write(1, 1, ["Matz","Larry","Guido"])

workbook.close
{% endhighlight %}

这是doc里的例子，但是format并不能应用到"Hello"这个格子上，需要用`format = workbook.add_format(:color => "green", :bold => true)`才能真正生效。

PS:　这个lib只能生成，读取用parseexcel这个lib。
