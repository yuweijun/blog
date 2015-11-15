---
layout: post
title: "linux环境变量显示和设置"
date: "Fri Jul 31 2009 12:04:00 GMT+0800 (CST)"
categories: linux
---

`export`命令不带参数可以将系统的环境变量全部列出来，如果需要设置环境变量，可以在`/etc/profile`，`~/.bash_profile`，`~/.bashrc`文件中设置。

{% highlight bash %}
$> export
{% endhighlight %}

一些标准的环境变量
-----

{% highlight text %}
SHELL 默认shell
LANG 默认语言
PATH linux寻找命令的默认路径，一般包括/bin，/usr/bin，/sbin，/usr/sbin，
/usr/X11R6/bin， /opt/bin，/usr/local/bin等。用户可以自行添加，
MANPATH man手册的默认路径
INPUTRC 默认键盘映象，详见/etc/inputrc
BASH_ENV bash shell的环境变量，通常在~/.bashrc中
DISPLAY X窗口适用的控制台，DISPLAY=：0对应于控制台F7，DISPLAY=：1对应于
控制台F8，DISPLAY=server：0向远程计算机发送 GUI应用程序。
COLORTERM GUI中的默认终端，通常是gnome-terminal.
USER 自动设置当前登陆用户的用户名。
LONGNAME 通常设置为$USER
MAIL 设置特定$USR的标准邮件目录
HOSTNAME 设置为/bin/hostname的命令输出
HISTSIZE 设置为history命令记住的命令数
{% endhighlight %}

Mac OSX下使用set查看
-----

{% highlight bash %}
$> set
Apple_PubSub_Socket_Render=/tmp/launch-yJC1WH/Render
BASH=/bin/bash
BASH_ARGC=()
BASH_ARGV=()
BASH_LINENO=()
BASH_SOURCE=()
BASH_VERSINFO=([0]="3" [1]="2" [2]="53" [3]="1" [4]="release" [5]="x86_64-apple-darwin13")
BASH_VERSION='3.2.53(1)-release'
CLICOLOR=1
COLUMNS=120
DIRSTACK=()
EUID=501
GREP_COLOR='1;35;40'
GREP_OPTIONS=--color=auto
GROUPS=()
HISTFILE=/Users/yu/.bash_history
HISTFILESIZE=500
HISTSIZE=500
HOME=/Users/yu
HOSTNAME=MacBookPro.local
HOSTTYPE=x86_64
IFS=$' \t\n'
JAVA_TOOL_OPTIONS=-Dfile.encoding=UTF-8
LANG=en_US.UTF-8
LINES=36
LOGNAME=yu
LSCOLORS=GxFxCxDxBxegedabagaced
MACHTYPE=x86_64-apple-darwin13
MAILCHECK=60
OLDPWD=/Users/yu/jekyll
OPTERR=1
OPTIND=1
OSTYPE=darwin13
PATH=/Users/yu/bin:/usr/local/sbin:/usr/bin:/bin:/usr/sbin:/sbin:/usr/local/bin
PIPESTATUS=([0]="0")
PPID=6439
PROMPT_COMMAND='update_terminal_cwd; '
PS1='\h:\W \u\$ '
PS2='> '
PS4='+ '
PWD=/Users/yu/jekyll/_posts
SHELL=/bin/bash
SHELLOPTS=braceexpand:emacs:hashall:histextpand:history:interactive-comments:monitor
SHLVL=1
SSH_AUTH_SOCK=/tmp/launch-wn7RDs/Listeners
TERM=xterm-256color
TERM_PROGRAM=Apple_Terminal
TERM_PROGRAM_VERSION=326
TERM_SESSION_ID=C8724B01-C3DF-4AC2-BEC4-49A1D767AE8A
TMPDIR=/var/folders/_f/ty2vbxy160z0f43h3n07cglc0000gn/T/
UID=501
USER=yu
_=../_site/
__CF_USER_TEXT_ENCODING=0x1F5:0:0
__CHECKFIX1436934=1
update_terminal_cwd ()
{
    local SEARCH=' ';
    local REPLACE='%20';
    local PWD_URL="file://$HOSTNAME${PWD//$SEARCH/$REPLACE}";
    printf '\e]7;%s\a' "$PWD_URL"
}
{% endhighlight %}
