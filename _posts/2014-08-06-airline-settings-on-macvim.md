---
layout: post
title: "在macvim中为vim-airline配色"
date: "Wed Aug 06 2014 21:37:32 GMT+0800 (CST)"
categories: vim
---

使用brew安装macvim，并下载powerline-fonts安装：

{% highlight bash %}
$ brew install macvim
$ git clone https://github.com/powerline/fonts.git
{% endhighlight %}

运行`powerline-fonts`目录的`install.sh`即可，或者是双击下载后Meslo文件夹中的这个字体: `Meslo LG S Regular for Powerline.otf`，然后在 `~/.vimrc` 中配置如下内容，此处使用bundle管理vim插件:

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

### airline theme设置

可以使用命令`:AirlineTheme {theme-name}`设置状态条的配色主题，可以参考此[网页](https://github.com/bling/vim-airline/wiki/Screenshots)。`airline`支持的主题如下所示：

1. badwolf
1. badwolf
1. base16
1. base16
1. behelit
1. behelit
1. bubblegum
1. bubblegum
1. dark
1. dark
1. durant
1. durant
1. hybridline
1. hybridline
1. hybrid
1. hybrid
1. jellybeans
1. jellybeans
1. kalisi
1. kalisi
1. kolor
1. kolor
1. laederon
1. laederon
1. light
1. light
1. lucius
1. lucius
1. luna
1. luna
1. molokai
1. molokai
1. monochrome
1. monochrome
1. murmur
1. murmur
1. papercolor
1. papercolor
1. powerlineish
1. powerlineish
1. raven
1. raven
1. serene
1. serene
1. silver
1. silver
1. simple
1. simple
1. solarized
1. solarized
1. sol
1. sol
1. term
1. term
1. tomorrow
1. tomorrow
1. ubaryd
1. ubaryd
1. understated
1. understated
1. wombat
1. wombat
1. zenburn

References:
-----

1. [vim-airline](https://github.com/bling/vim-airline)
2. [powerline-doc](https://powerline.readthedocs.org/en/latest/)
3. [powerline-fonts](https://github.com/Lokaltog/powerline-fonts)
4. [powerline](https://github.com/Lokaltog/powerline)
5. [Screenshots of AirlineThemes](https://github.com/bling/vim-airline/wiki/Screenshots)

