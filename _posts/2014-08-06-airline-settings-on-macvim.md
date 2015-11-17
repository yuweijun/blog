---
layout: post
title: "在macvim中为vim-airline配色"
date: "Wed Aug 06 2014 21:37:32 GMT+0800 (CST)"
categories: vim
---

使用brew安装macvim，并下载powerline-fonts安装：

{% highlight bash %}
$ brew install macvim
$ git clone git@github.com:Lokaltog/powerline-fonts.git
{% endhighlight %}

运行powerline-fonts目录的install.sh即可，或者是双击下载后Meslo文件夹中的这个字体: `Meslo LG S Regular for Powerline.otf`，然后在 `~/.vimrc` 中配置如下内容，此处使用bundle管理vim插件:

{% highlight vim %}
Bundle 'bling/vim-airline'

if has('gui_running')
    let g:airline_powerline_fonts = 1
    win 156 42
    " set fonts for gui vim
    set guifont=Meslo\ LG\ S\ for\ Powerline:h14
    " hide the gui menubar
    set guioptions=egmrt
endif
{% endhighlight %}

在Ubuntu 12.04中安装 `Ubuntu Mono derivative powerline` 字体后，修改vim配置文件:

{% highlight vim %}
Bundle 'bling/vim-airline'
if has('gui_running')
    let g:airline_powerline_fonts = 1
    win 150 43
    " set fonts for gui vim
    set guifont=Ubuntu\ Mono\ derivative\ Powerline\ 12
    " hide the gui menubar
    set guioptions=ie
endif
{% endhighlight %}

可以在vim下查看帮助: `:h airline`

![Screenshot]({{ site.baseurl }}/img/linux/vim/vim-airline-demo.gif)

资源列表:
----------

1. [vim-airline](https://github.com/bling/vim-airline)
2. [powerline-doc](https://powerline.readthedocs.org/en/latest/)
3. [powerline-fonts](https://github.com/Lokaltog/powerline-fonts)
4. [powerline](https://github.com/Lokaltog/powerline)

