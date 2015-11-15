---
layout: post
title: "regexp magic in vim"
date: "Mon May 24 2010 11:14:00 GMT+0800 (CST)"
categories: vim
---

Some characters in the pattern are taken literally. They match with the same character in the text. When preceded with a backslash however, these characters get a special meaning.

Other characters have a special meaning without a backslash. They need to be preceded with a backslash to match literally.

If a character is taken literally or not depends on the `magic` option and the items mentioned next.

{% highlight text %}
*/\m* */\M*
{% endhighlight %}

Use of `\m` makes the pattern after it be interpreted as if `magic` is set, ignoring the actual value of the `magic` option.

Use of `\M` makes the pattern after it be interpreted as if `nomagic` is used.

{% highlight text %}
*/\v* */\V*
{% endhighlight %}

Use of `\v` means that in the pattern after it all ASCII characters except `0-9`, `a-z`, `A-Z` and `_` have a special meaning. `very magic`

Use of `\V` means that in the pattern after it only the backslash has a special meaning. `very nomagic`

Examples
-----

{% highlight text %}
after:    \v           \m           \M           \V             matches ~
          $            $            $            \$             matches end-of-line
          .            .            \.           \.             matches any character
          *            *            \*           \*             any number of the previous atom
          ()           \(\)         \(\)         \(\)           grouping into an atom
          |            \|           \|           \|             separating alternatives
          \a           \a           \a           \a             alphabetic character
          \\           \\           \\           \\             literal backslash
          \.           \.           .            .              literal dot
          \{           {            {            {              literal `{`
          a            a            a            a              literal `a`

{only Vim supports \m, \M, \v and \V}
{% endhighlight %}

It is recommended to always keep the `magic` option at the default setting, which is `magic`. This avoids portability problems. To make a pattern immune
to the `magic` option being set or not, put `\m` or `\M` at the start of the pattern.

Without \v:
-----

{% highlight vim %}
:%s/^\%(foo\)\{1,3}\(.\+\)bar$/\1/
{% endhighlight %}

With \v:
-----

{% highlight vim %}
:%s/\v^%(foo){1,3}(.+)bar$/\1/
{% endhighlight %}

See also `:h /\v`
