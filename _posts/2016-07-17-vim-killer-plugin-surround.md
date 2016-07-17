---
layout: post
title: "vim killer plugin surround.vim"
date: Sun, 17 Jul 2016 12:36:33 +0800
categories: vim
---

What's surround.vim
-----

这个插件主要可以用来处理一些标点和标签配对相关的删除(Delete)、修改(Change)和复制(Yank)操作，如`""`，`''`，`<>`，`()`，`{}`，`[]`，`<p></p>`和`<div></div>`，所以在HTML、XML的配对标签编辑处理时尤其方便。

Install surround.vim
-----

建议使用[pathogen.vim](https://github.com/tpope/vim-pathogen)或者是[vundle.vim](https://github.com/VundleVim/Vundle.vim)来管理vim插件：

{% highlight bash %}
$> cd ~/.vim/bundle
$> git clone git://github.com/tpope/vim-surround.git
{% endhighlight %}

Help manual
-----

可以通过`:h surround`命令查看帮助手册，其中有在普通模式中命令操作举例说明：

| Old text               | Command     | New text                     |
|:-----------------------|:------------|:-----------------------------|
|  "Hello *world!"       |    ds"      |   Hello world!               |
|  [123+4*56]/2          |    cs])     |   (123+456)/2                |
|  "Look ma, I'm *HTML!" |    cs"<q>   |   <q>Look ma, I'm HTML!</q>  |
|  if *x>3 {             |    ysW(     |   if ( x>3 ) {               |
|  my $str = *whee!;     |    vllllS'  |   my $str = 'whee!';         |


vim普通模式中的命令
-----

当光标位于`Hello world!`的Hello这个单词字母上时，按组合命令`ysiw]`或者是`ysiw[`，结果分别如下，用`[`生成的配对标点带有空格：

{% highlight text %}
[Hello] world!

[ Hello ] world!
{% endhighlight %}

在如上结果上执行命令`cs]}`或者是`cs]{`，结果如下，用`{`生成的配对标点带有空格：

{% highlight text %}
{Hello} world!

{ Hello } world!
{% endhighlight %}

使用`yssb`或者是`yss)`命令将整行记录用小括号包起来：

{% highlight text %}
({ Hello } world!)
{% endhighlight %}

上面命令`yssb`中的`b`是指右半边小括号`)`，另外还有字母`B`，`r`，`a`分别是指符号`}`，`]`，`>`。

使用命令`ds{ds)`还原为原来的文本内容`Hello world!`。

{% highlight text %}
Hello world!
{% endhighlight %}

光标在单词`Hello`的任意一个字母上时，用命令`ysiw<em>`：

{% highlight html %}
<em>Hello</em> world!
{% endhighlight %}

光标在`<em>Hello</em>`任意字母上时，执行命令`dst`还原这个单词，`t`代表`tag`的意思：

普通模式中多行操作
=====

当光标位于`line1`这行时，按下命令`5ySS<ul>`，

{% highlight text %}
line1
line2
line3
line4
line5
{% endhighlight %}

产生如下输出，会生成一个配对的`ul`标签包住这5行：

{% highlight html %}
<ul>
line1
line2
line3
line4
line5
</ul>
{% endhighlight %}

再将光标移到`line1`所在行上，执行命令`:.,+4norm yss<li>`后回车：

{% highlight html %}
<ul>
<li>line1</li>
<li>line2</li>
<li>line3</li>
<li>line4</li>
<li>line5</li>
</ul>
{% endhighlight %}

Visual mode
-----

按`Shift+V`进入`linewise visual model`，光标从第一行`line6`上移到第五行`line10`，选中这5行记录，然后按命令`gS<ul>`。

{% highlight text %}
line6
line7
line8
line9
line10
{% endhighlight %}

得到的结果如下，与`5ySS<ul>`效果相同：

{% highlight html %}
<ul>
line6
line7
line8
line9
line10
</ul>
{% endhighlight %}

按`v`进入`visual model`，选中`line6`到`line10`所有行，然后按命令`gS<li>`，输出结果：

{% highlight html %}
<ul>
<li>
line6
line7
line8
line9
line10
</li></ul>
{% endhighlight %}

Insert mode
-----

在vim的`insert mode`时，按一次`<CTRL-s>`组合键，然后输入`<div>`，得到结果为一行div配对标签：

{% highlight html %}
<div></div>
{% endhighlight %}

如果是按`<CTRL-s><CTRL-s>`组合键，即二次`<CTRL-s>`组合键，然后输入`<div>`，输出3行代码，一个div配对标签，和一个光标定位的空行：

{% highlight html %}
<div>

</div>
{% endhighlight %}

Action repeat
-----

如果想让`.`操作对以上提到的`ds`，`cs`，`yss`，`ySS`命令生效，需要安装另一个插件：[repeat.vim](https://github.com/tpope/vim-repeat).

Plaintext Text Objects
-----

vim中提供三种类型的`text-object`：`words`，`sentences`和`paragraphs`。

| Type     | Command | Operation                                                |
|:--------:|:-------:|:--------------------------------------------------------:|
|Words     |aw       |a word (includes surrounding white space)                 |
|Words     |iw       |inner word (does not include surrounding white space)     |
|Sentences |as       |a sentence                                                |
|Sentences |is       |inner sentence                                            |
|Paragraphs|ap       |a paragraph                                               |
|Paragraphs|ip       |inner paragraph                                           |

命令说明
-----

{% highlight text %}
cit = Change Inner Tag
cip = Change Inner Paragraph
daw = Delete A Word
yaw = Yank A Word
{% endhighlight %}

基于text-object的常用命令
-----

| 操作 | 结果                                                      |
|:----:|:----------------------------------------------------------|
|ci[   | 删除一对[]中的所有字符并进入插入模式                      |
|ci(   | 删除一对()中的所有字符并进入插入模式                      |
|ci<   | 删除一对<>中的所有字符并进入插入模式                      |
|ci{   | 删除一对{}中的所有字符并进入插入模式                      |
|ci"   | 删除一对引号字符 " 中所有字符并进入插入模式               |
|ci'   | 删除一对引号字符 ' 中所有字符并进入插入模式               |
|cit   | 删除一对 HTML/XML 的标签内部的所有字符并进入插入模式      |
|ci    | 修改配对标点或者标签之间的文本                            |
|di    | 剪切配对标点或者标签之间的文本                            |
|yi    | 复制配对标点或者标签之间的文本                            |
|ca    | 同ci，但修改内容包括配对符号本身                          |
|da    | 同di，但剪切内容包括配对符号本身                          |
|ya    | 同yi，但复制内容包括配对符号本身                          |

References
-----

1. [surround.vim](https://github.com/tpope/vim-surround)
2. [Vim Text Objects: The Definitive Guide](http://blog.carbonfive.com/2011/10/17/vim-text-objects-the-definitive-guide/)
