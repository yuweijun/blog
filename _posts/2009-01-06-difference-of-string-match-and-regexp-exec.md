---
layout: post
title: "difference of string.match and regexp.exec"
date: "Tue Jan 06 2009 21:46:00 GMT+0800 (CST)"
categories: javascript
---

The `match()` method is the most general of the String regular-expression methods. It takes a regular expression as its only argument (or converts its argument to a regular expression by passing it to the `RegExp()` constructor) and returns an array that contains the results of the match. If the regular expression has the g flag set, the method returns an array of all matches that appear in the string. For example:

{% highlight javascript %}
"1 plus 2 equals 3".match(/\d+/g)  // returns ["1", "2", "3"]
{% endhighlight %}

If the regular expression does not have the g flag set, `match()` does not do a global search; it simply searches for the first match. However, `match()` returns an array even when it does not perform a global search. In this case, the first element of the array is the matching string, and any remaining elements are the parenthesized subexpressions of the regular expression. Thus, if `match()` returns an array a, `a[0]` contains the complete match, `a[1]` contains the substring that matched the first parenthesized expression, and so on. To draw a parallel with the `replace()` method, `a[n]` holds the contents of `$n`.

For example, consider parsing a `URL` with the following code:

{% highlight javascript %}
var url = /(\w+):\/\/([\w.]+)\/(\S*)/;
var text = "Visit my blog at http://www.example.com/~david";
var result = text.match(url);
if (result != null) {
    var fullurl = result[0];   // Contains "http://www.example.com/~david"
    var protocol = result[1];  // Contains "http"
    var host = result[2];      // Contains "www.example.com"
    var path = result[3];      // Contains "~david"
}
{% endhighlight %}

Finally, you should know about one more feature of the `match()` method. The array it returns has a length property, as all arrays do. When `match()` is invoked on a nonglobal regular expression, however, the returned array also has two other properties: the index property, which contains the character position within the string at which the match begins, and the input property, which is a copy of the target string. So in the previous code, the value of the result.index property would be 17 because the matched URL begins at character position 17 in the text. The result.input property holds the same string as the text variable. For a regular expression r and string s that does not have the g flag set, calling `s.match(r)` returns the same value as `r.exec(s)`. The RegExp.`exec()` method is discussed a little later in this chapter.

{% highlight javascript %}
var pattern = /\bJava\w*\b/g;
var text = "JavaScript is more fun than Java or JavaBeans!";
var result;
while((result = pattern.exec(text)) != null) {
    alert("Matched '" + result[0] +
          "' at position " + result.index +
          " next search begins at position " + pattern.lastIndex);
}
{% endhighlight %}

When `exec()` is invoked on a nonglobal pattern, it performs the search and returns the result described earlier. When regexp is a global regular expression, however, `exec()` behaves in a slightly more complex way. It begins searching string at the character position specified by the lastIndex property of regexp. When it finds a match, it sets lastIndex to the position of the first character after the match. This means that you can invoke `exec()` repeatedly in order to loop through all matches in a string. When `exec()` cannot find any more matches, it returns null and resets lastIndex to zero. If you begin searching a new string immediately after successfully finding a match in another string, you must be careful to manually reset lastIndex to zero.

Note that `exec()` always includes full details of every match in the array it returns, whether or not regexp is a global pattern. This is where `exec()` differs from `String.match()`, which returns much less information when used with global patterns. Calling the `exec()` method repeatedly in a loop is the only way to obtain complete pattern-matching information for a global pattern.

This article copy from `OReilly's《JavaScript The Definitive Guide》5th Edition`.
