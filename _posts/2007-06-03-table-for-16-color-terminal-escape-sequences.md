---
layout: post
title: "table for 16-color terminal escape sequences"
date: "Sun Jun 03 2007 23:55:00 GMT+0800 (CST)"
categories: linux
---

linux terminal中可控制文字的显示颜色，包括8个前景色，8个背景色和字体粗细：

{% highlight bash %}
#!/bin/bash

# prints a color table of 8bg * 8fg * 2 states (regular/bold)
echo
echo Table for 16-color terminal escape sequences.
echo Replace ESC with \\033 in bash.
echo
echo "Background | Foreground colors"
echo "---------------------------------------------------------------------"
for((bg=40;bg<=47;bg++)); do
 for((bold=0;bold<=1;bold++)) do
  echo -en "\033[0m"" ESC[${bg}m   | "
  for((fg=30;fg<=37;fg++)); do
   if [ $bold == "0" ]; then
    echo -en "\033[${bg}m\033[${fg}m [${fg}m  "
   else
    echo -en "\033[${bg}m\033[1;${fg}m [1;${fg}m"
   fi
  done
  echo -e "\033[0m"
 done
 echo "--------------------------------------------------------------------- "
done

echo
echo
{% endhighlight %}

以上脚本运行后会输出以下结果：

{% highlight tex %}
Table for 16-color terminal escape sequences.
Replace ESC with \033 in bash.

Background | Foreground colors
---------------------------------------------------------------------
 ESC[40m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[40m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[41m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[41m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[42m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[42m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[43m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[43m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[44m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[44m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[45m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[45m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[46m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[46m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------
 ESC[47m   |  [30m   [31m   [32m   [33m   [34m   [35m   [36m   [37m
 ESC[47m   |  [1;30m [1;31m [1;32m [1;33m [1;34m [1;35m [1;36m [1;37m
---------------------------------------------------------------------

{% endhighlight %}

根据这个color table，就可以用命令来控制显示terminal中的文字颜色，也正是利用这个原理，可以控制应用日志中不同类型的内容，用不同的颜色来显示，可以对日志内容一目了然：

{% highlight bash %}
$> ruby -e 'print "\e[33m"'
$> ruby -e 'print "\e[0m"'
$> ruby -e 'print "\e[1m"'
$> printf "\e[0m"
$> echo -e "\e[1m"
$> echo -e "\033[47m\033[1;31mBright red on white.\033[0m"
{% endhighlight %}
