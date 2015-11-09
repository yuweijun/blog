---
layout: post
title: "usage of cut or awk with xargs"
date: "Sun Jan 20 2008 22:27:00 GMT+0800 (CST)"
categories: linux
---

用这个命令alias用以杀掉ruby rails server进程。

{% highlight bash %}
alias die_rails='ps -a|grep "/usr/local/bin/ruby script/server"|grep -v "grep /usr"|cut -d " " -f1|xargs -n 1 kill -KILL $1'
alias reset_rails='ps -a|grep "/usr/local/bin/ruby script/server"|grep -v "grep /usr"|cut -d " " -f1|xargs -n 1 kill -HUP $1'
{% endhighlight %}
