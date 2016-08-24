---
layout: post
title: "zsh config on mac os"
date: Sun, 10 Jul 2016 10:46:12 +0800
categories: linux
---

本文是使用`powerline`字体和一些`zsh`的插件，定制`Mac OS`上自带的命令行终端工具`Terminal`，而不是基于`iTerm2`。

首先查看一下系统是否已经安装了`zsh`，没有安装则使用`brew install zsh`安装。

{% highlight bash %}
$> cat /etc/shells
{% endhighlight %}

> \# List of acceptable shells for chpass(1).
>
> \# Ftpd will not allow users to connect who are not using
>
> \# one of these shells.
>
>
>
> /bin/bash
>
> /bin/csh
>
> /bin/ksh
>
> /bin/sh
>
> /bin/tcsh
>
> /bin/zsh

切换到`zsh`并退出终端。

{% highlight bash %}
$> chsh -s /bin/zsh
$> exist
{% endhighlight %}

退出后重新进入`Terminal`，再确认当前的`shell`：

{% highlight bash %}
$> echo $SHELL
{% endhighlight %}

> /bin/zsh

Oh My Zsh Installation
-----

{% highlight bash %}
# using curl install on Mac OS
$> sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
{% endhighlight %}

linux下还可以通过`wget`方式安装，`Mac OS`系统默认安装里没有安装`wget`命令。

{% highlight bash %}
$> sh -c "$(wget https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh -O -)"
{% endhighlight %}

安装完成之后，修改`~/.zshrc`配置文件，将主题改为`agnoster`。

> ZSH_THEME="agnoster"

Install a patched powerline font
-----

这里采用的`Menlo-for-Powerline`字体不是[https://github.com/powerline/fonts](https://github.com/powerline/fonts)里的那个`Meslo`字体，效果稍有不同，当然也可以用[Meslo LG L Regular for Powerline字体](https://github.com/powerline/fonts/blob/master/Meslo/Meslo%20LG%20L%20Regular%20for%20Powerline.otf)。

{% highlight bash %}
$> git clone git@github.com:abertsch/Menlo-for-Powerline.git
$> open Menlo-for-Powerline
{% endhighlight %}

双击打开的文件夹中的字体文件，并点击安装字体，然后再修改`Terminal`的字体和字体大小（个人17吋Mac上设置为16pt，27吋iMac使用20pt），最后退出重进终端查看效果。

Install zsh-syntax-highlighting
-----

{% highlight bash %}
$> brew install zsh-syntax-highlighting
{% endhighlight %}

将以下这句脚本添加到`~/.zshrc`文件中。

> source /usr/local/share/zsh-syntax-highlighting/zsh-syntax-highlighting.zsh

Install zsh-autosuggestions
-----

{% highlight bash %}
$> git clone git://github.com/zsh-users/zsh-autosuggestions $ZSH_CUSTOM/plugins/zsh-autosuggestions
{% endhighlight %}

绑定`ctrl + space`快捷键用于接受当前的自动完成提示，在`~/.zshrc`文件中加入下面的配置。

> bindkey '^ ' autosuggest-accept

Install autojump
-----

{% highlight bash %}
$> brew install autojump
{% endhighlight %}

安装`zsh-autosuggestions`和`autojump`完成后在`~/.zshrc`中修改`plugins=(git)`为如下内容：

> plugins=(git bundler osx rake ruby zsh-autosuggestions autojump)

然后继续在`~/.zshrc`文件中添加：

> [[ -s $(brew --prefix)/etc/profile.d/autojump.sh ]] && . $(brew --prefix)/etc/profile.d/autojump.sh

{% highlight bash %}
$> source ~/.zshrc
{% endhighlight %}

退出终端后重新进入。

最后确认一下`~/.zshrc`中有如下配置项：

{% highlight bash %}
export ZSH="~/.oh-my-zsh"

ZSH_THEME="agnoster"

plugins=(git bundler osx rake ruby zsh-autosuggestions autojump)

bindkey '^ ' autosuggest-accept

source $ZSH/oh-my-zsh.sh
source /usr/local/share/zsh-syntax-highlighting/zsh-syntax-highlighting.zsh
[[ -s $(brew --prefix)/etc/profile.d/autojump.sh ]] && source $(brew --prefix)/etc/profile.d/autojump.sh
{% endhighlight %}

终端截图
-----

![zsh-powerline-autojump-theme]({{ site.baseurl }}/img/linux/zsh-powerline-autojump-theme.jpg)

oh-my-zsh官方iTerm2配图
-----

![iterm2-powerline-theme]({{ site.baseurl }}/img/linux/iterm2-powerline-theme.png)

References
-----

1. [oh-my-zsh](https://github.com/robbyrussell/oh-my-zsh)
2. [Menlo for Powerline](https://github.com/abertsch/Menlo-for-Powerline)
3. [A cd command that learns](https://github.com/wting/autojump)
4. [Improve your shell using fish and oh my fish](http://jmolivas.com/improve-your-shell-using-fish-and-oh-my-fish)
5. [oh-my-zsh fork](https://github.com/yuweijun/oh-my-zsh)

