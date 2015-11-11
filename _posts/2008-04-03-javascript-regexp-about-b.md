---
layout: post
title: "difference of \\b and [\\b] in javascript"
date: "Thu Apr 03 2008 16:14:00 GMT+0800 (CST)"
categories: javascript
---

`\b`Match a word boundary. That is, match the position between a `\w` character and a `\W` character or between a `\w` character and the beginning or end of a string. (Note, however, that `[\b]` matches `backspace`.)

{% highlight tex %}
\B Match a position that is not a word boundary.
[\b] A literal backspace (special case).
{% endhighlight %}

Note that the special character-class escapes can be used within square brackets. `\s` matches any whitespace character, and `\d` matches any digit, so `/[\s\d]/` matches any one whitespace character or digit.

Note that there is one special case. As you'll see later, the `\b` escape has a special meaning. When used within a character class, however, it represents the backspace character. Thus, to represent a backspace character literally in a regular expression, use the character class with one element: `/[\b]/`.

References
-----

1. Javascript the Definitive Guide 5th.
