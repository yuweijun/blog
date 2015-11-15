---
layout: post
title: "vim正则表达式说明"
date: "Tue Oct 19 2010 20:05:00 GMT+0800 (CST)"
categories: vim
---

vim中查找替换使用的正则表达式与一般的编程语言如javascript/ruby/perl不一样，尤其是在进行非贪婪匹配时，下面主要是分析在vim中怎么进行非贪婪匹配的。

在vim手册中有这样的说明
-----

{% highlight text %}
If a character is taken literally or not depends on the 'magic' option and the items mentioned next.
It is recommended to always keep the 'magic' option at the default setting, which is 'magic'. This avoids portability problems.
{% endhighlight %}

就是说默认的搜索替换是按magic模式进行查找的，如果有字符用反斜杠`\`转义的话，是会在magic模式中检查其对应转义后含意。

并且在vim配置文件中尽量保持使用magic模式。

vim中有4种正则匹配模式，可以用`:h /magic`查看帮助文档的说明，简单说就是有`\m`，`\M`，`\v`，`\V`这四种，最常用的是默认的`\m`，其次`\v`在进行分组捕获时也非常有用，因为不需要像在`\m`模式下那样对小括号进行转义，写起来比较麻烦，另二个模式用得较少些。

在帮助手册中对`\v`的说明如下:

{% highlight text %}
Use of '\v' means that in the pattern after it all ASCII characters except '0-9', 'a-z', 'A-Z' and '_' have a special meaning. 'very magic'
{% endhighlight %}

也就是除了`0-9`，`a-z`，`A-Z`和下划线`_`之外的其他ASCII字符都有特殊含义，如`(`，`)`，`|`，`$`，`^`，`[`，`]`，`{`，`}`，`:`，`!`，`.`，`*`，`?`，`+`，`<`，`>`等，这与其他的如javascript的正则更接近一些，在这些字符用得比较多的时候，就考虑用`\v`模式，可以少打好多反斜杠，更像其他语言中的正则表达式，更容易看得明白，所以`\v`模式很好用。

关于`\m`，`\v`二种模式的比较说明，摘自帮助手册，移除了`\M`，`\V`部分，只要掌握好前面的二种模式就已经够用。

{% highlight text %}
\v             \m           matches
$              $            matches end-of-line
.              .            matches any character
*              *            any number of the previous atom
()             \(\)         grouping into an atom
|              \|           separating alternatives
\a             \a           alphabetic character
\\             \\           literal backslash
\.             \.           literal dot
\{             {            literal '{'
a              a            literal 'a'
{% endhighlight %}

对于这二种模式而言，`\a`都是代表字母，其中需要注意的是`\m`中的`|`(或分隔符)需要转义，这个与linux中的grep命令一样，另外其`(`，`)`，`{`这三个字符也需要转义，但是对于`\m`模式，`}`却可以无需转义(The } may optionally be preceded with a backslash: \{n，m\})，所以在`\m`模式中写出来的没有`\v`中的正则表达式更加整齐好明白，下面提到的非贪婪匹配正是与`{`，`}`这对花括号有关。
非贪婪匹配写法

vim中的匹配1个或者更多相同字符的`+`，在magic模式下需要转义，即用`\+`表示。如`\w\+`匹配一个或者一个以上的字母数字或者下划线。`\+`和`*`一样是贪婪匹配的。

可以通过`:h non-greedy`查看非贪婪匹配的写法。只能在`\m`模式下进行非贪婪匹配，使用`.\{-}`进行最小匹配，如果写整齐点也可以用`.\{-\}`来表示，如`pa.\{-\}n`可以匹配到`pattern`或者是`pan`。摘录帮助手册中关于贪婪/非贪婪匹配说明如下(magic模式):

{% highlight text %}
\{n,m}              Matches n to m of the preceding atom, as many as possible
\{n}                Matches n of the preceding atom
\{n,}               Matches at least n of the preceding atom, as many as possible
\{,m}               Matches 0 to m of the preceding atom, as many as possible
\{}                 Matches 0 or more of the preceding atom, as many as possible (like *)
\{-n,m}             Matches n to m of the preceding atom, as few as possible
\{-n}               Matches n of the preceding atom
\{-n,}              Matches at least n of the preceding atom, as few as possible
\{-,m}              Matches 0 to m of the preceding atom, as few as possible
\{-}                Matches 0 or more of the preceding atom, as few as possible
{% endhighlight %}

匹配包括换号符在内的任意字符

如果匹配内容有换行符，用通配符`.`不能匹配换行符，在其他语言如perl/php/javascript是用`/m`修饰符让`.`可以匹配字符串内的换行符，vim中是用一个转义的下划线`\_`加上`.`组成的`\_.`来表示包括换行符在内的任意字符，与此效果相似的还有`\_^`，`\_$`，`\_s`。

关于vim和perl的正则表达式区别可以通过`:h perl-patterns`查看更详细的说明。

字符类(Character classes)
-----

摘录部分常用的字符类如下，这部分多数与其他语言相似，其他很多字符类与别的语言中的字符类完全不一样，并且大小写的字符集不是取反的字符集，如`\i`，就不作记录说明。另外如果需要忽略大小写，可查看`:h /ignorecase`， 在任何位置加入`\c`标记开始忽略字母的大小写。

{% highlight text %}
\s          whitespace character: <Space> and <Tab>
\S          non-whitespace character; opposite of \s
\d          digit:    [0-9]
\D          non-digit:   [^0-9]
\w          word character:   [0-9A-Za-z_]
\W          non-word character:  [^0-9A-Za-z_]
\a          alphabetic character:  [A-Za-z]
\A          non-alphabetic character: [^A-Za-z]
\l          lowercase character:  [a-z]
\u          uppercase character:  [A-Z]
\t          matches <Tab>
\r          matches <CR>
\n          matches an end-of-line
\1          Matches the same string that was matched by the first sub-expression in \( and \). Example: '\([a-z]\).\1' matches 'ata', 'ehe', 'tot', etc.
{% endhighlight %}

关于vim中的`[]`(`:h /collection`)

在其他语言中perl/javascript/ruby中，方括号可以使用转义字符代替的字符集，如`\s`，`\w`等，在vim中只能在方括号中使用字符序列，也可以像其他编程语言一样放入`a-z`，`0-9`等，但不能用转义字符，这点在使用时比较不顺手。

以上是个人认为比较常用并且较为简单的部分内容，更多神奇的vim正则查找，以及结合一些vim内置方法进行正则替换，则需要仔细阅读帮助手册说明。要熟练掌握vim，就需要多查手册，多实践。
