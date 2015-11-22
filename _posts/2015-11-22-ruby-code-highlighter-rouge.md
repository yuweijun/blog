---
layout: post
title: "rouge - syntax highlighter"
date: "Sun, 22 Nov 2015 12:30:56 +0800"
categories: ruby
---

[rouge](http://rouge.jneen.net/)是用ruby实现，兼容python [pygments](http://pygments.org/)的语法高亮工具。

安装
-----

{% highlight bash %}
$> sudo gem install rouge
{% endhighlight %}

主要有以下几种用法：

生成代码高亮的html片段
-----

{% highlight ruby %}
require 'rouge'

# make some nice lexed html
source = File.read('/etc/bashrc')
formatter = Rouge::Formatters::HTML.new(css_class: 'highlight')
lexer = Rouge::Lexers::Shell.new
html = formatter.format(lexer.lex(source))
puts html
{% endhighlight %}

运行以上ruby脚本后，会输出如下类似html片段：

{% highlight html %}
<pre class="highlight"><code><span class="c"># System-wide .bashrc file for interactive bash(1) shells.</span>
<span class="k">if</span> <span class="o">[</span> -z <span class="s2">"</span><span class="nv">$PS1</span><span class="s2">"</span> <span class="o">]</span>; <span class="k">then
   return
fi
</span><span class="nv">PS1</span><span class="o">=</span><span class="s1">'\h:\W \u\$ '</span>
<span class="c"># Make bash check its window size after a process completes</span>
<span class="nb">shopt</span> -s checkwinsize
<span class="c"># Tell the terminal about the working directory at each prompt.</span>
<span class="k">if</span> <span class="o">[</span> <span class="s2">"</span><span class="nv">$TERM_PROGRAM</span><span class="s2">"</span> <span class="o">==</span> <span class="s2">"Apple_Terminal"</span> <span class="o">]</span> <span class="o">&amp;&amp;</span> <span class="o">[</span> -z <span class="s2">"</span><span class="nv">$INSIDE_EMACS</span><span class="s2">"</span> <span class="o">]</span>; <span class="k">then
    </span>update_terminal_cwd<span class="o">()</span> <span class="o">{</span>
        <span class="c"># Identify the directory using a "file:" scheme URL,</span>
        <span class="c"># including the host name to disambiguate local vs.</span>
        <span class="c"># remote connections. Percent-escape spaces.</span>
	<span class="nb">local </span><span class="nv">SEARCH</span><span class="o">=</span><span class="s1">' '</span>
	<span class="nb">local </span><span class="nv">REPLACE</span><span class="o">=</span><span class="s1">'%20'</span>
	<span class="nb">local </span><span class="nv">PWD_URL</span><span class="o">=</span><span class="s2">"file://</span><span class="nv">$HOSTNAME</span><span class="k">${</span><span class="nv">PWD</span><span class="p">//</span><span class="nv">$SEARCH</span><span class="p">/</span><span class="nv">$REPLACE</span><span class="k">}</span><span class="s2">"</span>
	<span class="nb">printf</span> <span class="s1">'\e]7;%s\a'</span> <span class="s2">"</span><span class="nv">$PWD_URL</span><span class="s2">"</span>
    <span class="o">}</span>
    <span class="nv">PROMPT_COMMAND</span><span class="o">=</span><span class="s2">"update_terminal_cwd; </span><span class="nv">$PROMPT_COMMAND</span><span class="s2">"</span>
<span class="k">fi</span>
</code></pre>
{% endhighlight %}

生成css样式文件
-----

{% highlight ruby %}
require 'rouge'

puts Rouge::Themes::Base16.mode(:light).render(scope: '.highlight')
{% endhighlight %}

运行以上ruby脚本，得到如下css样式输出内容：

{% highlight css %}
.highlight table td { padding: 5px; }
.highlight table pre { margin: 0; }
.highlight, .highlight .w {
  color: #303030;
}
.highlight .err {
  color: #151515;
  background-color: #ac4142;
}
.highlight .c, .highlight .cd, .highlight .cm, .highlight .c1, .highlight .cs {
  color: #505050;
}
.highlight .cp {
  color: #f4bf75;
}
.highlight .nt {
  color: #f4bf75;
}
.highlight .o, .highlight .ow {
  color: #d0d0d0;
}
.highlight .p, .highlight .pi {
  color: #d0d0d0;
}
.highlight .gi {
  color: #90a959;
}
.highlight .gd {
  color: #ac4142;
}
.highlight .gh {
  color: #6a9fb5;
  background-color: #151515;
  font-weight: bold;
}
.highlight .k, .highlight .kn, .highlight .kp, .highlight .kr, .highlight .kv {
  color: #aa759f;
}
.highlight .kc {
  color: #d28445;
}
.highlight .kt {
  color: #d28445;
}
.highlight .kd {
  color: #d28445;
}
.highlight .s, .highlight .sb, .highlight .sc, .highlight .sd, .highlight .s2, .highlight .sh, .highlight .sx, .highlight .s1 {
  color: #90a959;
}
.highlight .sr {
  color: #75b5aa;
}
.highlight .si {
  color: #8f5536;
}
.highlight .se {
  color: #8f5536;
}
.highlight .nn {
  color: #f4bf75;
}
.highlight .nc {
  color: #f4bf75;
}
.highlight .no {
  color: #f4bf75;
}
.highlight .na {
  color: #6a9fb5;
}
.highlight .m, .highlight .mf, .highlight .mh, .highlight .mi, .highlight .il, .highlight .mo, .highlight .mb, .highlight .mx {
  color: #90a959;
}
.highlight .ss {
  color: #90a959;
}
{% endhighlight %}

rougify命令在控制台高亮输出脚本文件内容
-----

{% highlight bash %}
$> rougify jquery.js
{% endhighlight %}

这个命令会在命令行里高亮输出文件内容，但不支持less/more分页查看。

将内置配色主题导出css样式文件
-----

{% highlight bash %}
$> rougify style monokai.sublime > sublime.css
$> rougify style monokai > monokai.css
$> rougify style github > github.css
{% endhighlight %}

将内置的配色[主题](https://github.com/jneen/rouge/tree/master/lib/rouge/themes)导出为css文件。

References
-----

1. [https://github.com/jneen/rouge](https://github.com/jneen/rouge)
