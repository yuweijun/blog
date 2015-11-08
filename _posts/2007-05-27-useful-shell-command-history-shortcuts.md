---
layout: post
title: "useful shell command history shortcuts"
date: "Thu May 27 2007 13:34:00 GMT+0800 (CST)"
categories: linux
---

Reuse previous arguments
-----

The `!` operator gives you a quick way to refer to parts of the previous command.

`!!` (Full contents of previous command)

{% highlight bash %}
$> emacs /etc/init.d/mongrel_cluster
=> Permission Denied
$> sudo !!
=> Now opens the file as root
{% endhighlight %}

`!$` (Last arg of previous command)

{% highlight bash %}
$> wget http://weather.yahooapis.com/forecastrss?p=98117
=> wget: No Match
$> wget '!$'
=> Now it works
{% endhighlight %}

`!^` (First argument of previous command)

{% highlight bash %}
$> echo fish and chips
$> cho !^
=> fish # First argument
{% endhighlight %}

`!:1` (Argument by number)

`!:1` is the same as `!^`. The difference is that you can reference any element of the previous command.

{% highlight bash %}
$> cho fish and chips
=> fish and chips
$> cho !:1
=> fish # Same thing as !^

$> echo fish and chips
$> echo !:2
=> and # Word 2 in previous command, zero-indexed

$> echo fish and chips
$> echo !:0
=> echo # The very first word in the previous command

$> echo fish and chips
$> echo !:1-3
=> fish and chips # A range
{% endhighlight %}

`!pattern` (Repeat last command in history with pattern)

The bang is useful for re-running a command that you’ve run before. Spell out the first few letters and hit ENTER (or TAB to show the completion in tcsh). The shell will search backwards in your history until it finds a command that starts with the same letters.


{% highlight bash %}
$> rake test:recent
...

$> !rak
=> Runs 'rake test:recent' or last command starting with 'rak'
{% endhighlight %}

Sets:
`{a,b}` (A set)

How often to you rename just part of a file? The {} syntax is convenient.

{% highlight bash %}
$> mv file.{txt,xml}
=> Expands to 'mv file.txt file.xml'

$> mv file{,.orig}
=> Expands to 'mv file file.orig'

$> mkdir foo{1,2,3}
=> Expands to 'mkdir foo1 foo2 foo3'
{% endhighlight %}

Mac OS X-specific
-----

{% highlight bash %}
$> pbcopy and pbpaste
{% endhighlight %}

In Mac OS X, you can copy things to the clipboard and read them back out. This is nice because you can reuse it in the shell or back in the OS with command-C or command-V.

{% highlight bash %}
$> ./generate_random_password | pbcopy

$> pbpaste > file.txt
{% endhighlight %}
