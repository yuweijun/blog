---
layout: post
title:  "在macvim中为vim-airline配色"
date: "Wed Aug 06 2014 21:37:32 GMT+0800 (CST)"
categories: howto
---

使用brew安装macvim，并下载powerline-fonts安装：

{% highlight bash %}
$ brew install macvim
$ git clone git@github.com:Lokaltog/powerline-fonts.git
{% endhighlight %}

双击下载后Meslo文件夹中的这个字体: `Meslo LG S Regular for Powerline.otf`，然后在 `~/.vimrc` 中配置如下内容，此处使用bundle管理vim插件:

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

可以在vim下查看帮助: `:h airline`

![Screenshot](https://github.com/bling/vim-airline/wiki/screenshots/demo.gif)

资源列表:
----------

1. [vim-airline](https://github.com/bling/vim-airline)
2. [powerline-doc](https://powerline.readthedocs.org/en/latest/)
3. [powerline-fonts](https://github.com/Lokaltog/powerline-fonts)
4. [powerline](https://github.com/Lokaltog/powerline)

