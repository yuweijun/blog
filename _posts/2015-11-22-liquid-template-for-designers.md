---
layout: post
title: "liquid模板使用简介"
date: "Sun, 22 Nov 2015 16:30:03 +0800"
categories: ruby
---

`liqid`中有二种标签类型：

{% raw %}
1. 一种是输出，格式为`{{ }}`。
2. 另一种主要是控制输出的标签`{% %}`。
{% endraw %}

普通输出文本或者变量
-----

{% highlight text %}
{% raw %}
Hello {{name}}
Hello {{user.name}}
Hello {{ 'tobi' }}
{% endraw %}
{% endhighlight %}

filter的使用
-----

管道符`|`左侧的内容输出，做为右边filter方法的第一个参数输入，这与linux和管道符设计比较相似。

{% highlight text %}
{% raw %}
Hello {{ 'tobi' | upcase }}
Hello tobi has {{ 'tobi' | size }} letters!
Hello {{ '*tobi*' | textilize | upcase }}
Hello {{ 'now' | date: "%Y %h" }}
{% endraw %}
{% endhighlight %}

Standard Filters
-----

{% highlight text %}
{% raw %}
date - reformat a date (syntax reference)
capitalize - capitalize words in the input sentence
downcase - convert an input string to lowercase
upcase - convert an input string to uppercase
first - get the first element of the passed in array
last - get the last element of the passed in array
join - join elements of the array with certain character between them
sort - sort elements of the array
map - map/collect an array on a given property
size - return the size of an array or string
escape - escape a string
escape_once - returns an escaped version of html without affecting existing escaped entities
strip_html - strip html from string
strip_newlines - strip all newlines (\n) from string
newline_to_br - replace each newline (\n) with html break
replace - replace each occurrence e.g. {{ 'foofoo' | replace:'foo','bar' }} #=> 'barbar'
replace_first - replace the first occurrence e.g. {{ 'barbar' | replace_first:'bar','foo' }} #=> 'foobar'
remove - remove each occurrence e.g. {{ 'foobarfoobar' | remove:'foo' }} #=> 'barbar'
remove_first - remove the first occurrence e.g. {{ 'barbar' | remove_first:'bar' }} #=> 'bar'
truncate - truncate a string down to x characters. It also accepts a second parameter that will append to the string e.g. {{ 'foobarfoobar' | truncate: 5, '.' }} #=> 'foob.'
truncatewords - truncate a string down to x words
prepend - prepend a string e.g. {{ 'bar' | prepend:'foo' }} #=> 'foobar'
append - append a string e.g. {{ 'foo' | append:'bar' }} #=> 'foobar'
slice - slice a string. Takes an offset and length, e.g. {{ "hello" | slice: -3, 3 }} #=> llo
minus - subtraction e.g. {{ 4 | minus:2 }} #=> 2
plus - addition e.g. {{ '1' | plus:'1' }} #=> 2, {{ 1 | plus:1 }} #=> 2
times - multiplication e.g {{ 5 | times:4 }} #=> 20
divided_by - integer division e.g. {{ 10 | divided_by:3 }} #=> 3
round - rounds input to the nearest integer or specified number of decimals
split - split a string on a matching pattern e.g. {{ "a~b" | split:"~" }} #=> ['a','b']
modulo - remainder, e.g. {{ 3 | modulo:2 }} #=> 1
{% endraw %}
{% endhighlight %}

逻辑控制
-----

{% highlight text %}
assign - Assigns some value to a variable
capture - Block tag that captures text into a variable
case - Block tag, its the standard case...when block
comment - Block tag, comments out the text in the block
cycle - Cycle is usually used within a loop to alternate between values, like colors or DOM classes.
for - For loop
break - Exits a for loop
continue Skips the remaining code in the current for loop and continues with the next loop
if - Standard if/else block
include - Includes another template; useful for partials
raw - temporarily disable tag processing to avoid syntax conflicts.
unless - Mirror of if statement
{% endhighlight %}

jekyll filters
-----

{% highlight text %}
{% raw %}
{{ site.time | date_to_xmlschema }}
{{ site.time | date_to_rfc822 }}
{{ site.time | date_to_string }}
{{ site.time | date_to_long_string }}
{{ site.members | where:"graduation_year","2014" }}
{{ site.members | group_by:"graduation_year" }}
{{ page.content | xml_escape }}
{{ "foo,bar;baz?" | cgi_escape }}
{{ "foo, bar \baz?" | uri_escape }}
{{ page.content | number_of_words }}
{{ page.tags | array_to_sentence_string }}
{{ page.excerpt | markdownify }}
{{ some_scss | scssify }} {{ some_sass | sassify }}
{{ "The _config.yml file" | slugify }}
{{ "The _config.yml file" | slugify: 'pretty' }}
{{ site.data.projects | jsonify }}
{{ page.tags | sort }}
{{ site.posts | sort: 'author' }}
{{ site.pages | sort: 'title', 'last' }}
{% endraw %}
{% endhighlight %}

jekyll tags
-----

{% highlight text %}
{% raw %}
{% include footer.html %}
{% include footer.html param="value" variable-param=page.variable %}
{{ include.param }}
{% include_relative somedir/footer.html %}
{% highlight ruby %}
{% highlight ruby linenos %}
{% endraw %}
{% endhighlight %}

References
-----

1. [Liquid for Designers](https://github.com/Shopify/liquid/wiki/Liquid-for-Designers)
