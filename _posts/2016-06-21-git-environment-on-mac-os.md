---
layout: post
title: "git environment on mac os x"
date: Tue, 21 Jun 2016 21:52:09 +0800
categories: linux
---

个人苹果电脑上的`.bash_profile`设置，主要是关于git的命令行提示和颜色配置。

{% highlight bash %}
export GREP_OPTIONS='--color=auto'
export GREP_COLOR='1;35;40'
export CLICOLOR=1
export LSCOLORS=GxFxCxDxBxegedabagaced

alias ll='ls -la'

# export JAVA_TOOL_OPTIONS=-Dfile.encoding=UTF-8
# export JAVA_HOME=$(/usr/libexec/java_home -v 1.8)
# export NVM_DIR="/Users/david/.nvm"
# [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"  # This loads nvm

# Xcode installed firstly.
source /Applications/Xcode.app/Contents/Developer/usr/share/git-core/git-completion.bash
source /Applications/Xcode.app/Contents/Developer/usr/share/git-core/git-prompt.sh

GIT_PS1_SHOWDIRTYSTATE=true

# MAGENTA="\[\033[0;35m\]"
# YELLOW="\[\033[0;33m\]"
# BLUE="\[\033[34m\]"
# LIGHT_GRAY="\[\033[0;37m\]"
# CYAN="\[\033[0;36m\]"
# GREEN="\[\033[0;32m\]"
export PS1="\u@\W\[\033[32m\]\$(git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ (\1)/')\[\033[00m\] $ "
{% endhighlight %}

PS1中的命令部分不要提到`function`里，否则切换`screen`命令会报错提示方法找不到。

如果不安装Xcode，可以通过`brew`命令安装，也可以达到相同的效果，按照安装完成的提示相应设置即可：

{% highlight bash %}
$> brew install git bash-completion
{% endhighlight %}

个人的git全局配置文件`~/.gitconfig`的内容：

{% highlight bash %}
[push]
    default = simple
[user]
    name = yu
    email = test@gmail.com
[alias]
    co = checkout
    st = status
    last = log -n 20 --graph --decorate --pretty=oneline --abbrev-commit --all
[color "branch"]
    current = yellow reverse
    local = yellow
    remote = green
[color "diff"]
    meta = yellow bold
    frag = magenta bold
    old = red bold
    new = green bold
[color "status"]
    added = yellow
    changed = green
    untracked = cyan
{% endhighlight %}

References
-----

1. [Creating a Happy Git Environment on OS X](https://gist.github.com/trey/2722934)
2. [Add Git Branch Name to Terminal Prompt (Mac)](http://mfitzp.io/article/add-git-branch-name-to-terminal-prompt-mac/)
3. [How To Setup Git Completion And Repo State On Osx](http://www.4e00.com/blog/linux/2016/06/19/bash-scripting-manipulating-variables.html)
4. [git-prompt.sh](https://raw.githubusercontent.com/git/git/master/contrib/completion/git-prompt.sh)
