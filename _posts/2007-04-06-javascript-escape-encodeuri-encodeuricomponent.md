---
layout: post
title: "JavaScript escape encodeURI encodeURIComponent"
date: "Fri Apr 06 2007 17:13:00 GMT+0800 (CST)"
categories: javascript
---

{% highlight javascript %}
var str = 'a=2&test=1&c=' + '<?php echo urlencode('中文');?>';
console.log(str);
console.log(decodeURI(str));
console.log(decodeURIComponent(str));
console.log(escape('a=2&test=1&c=中文'));
console.log(unescape(escape('a=2&test=1&c=中文')));
// 结果不是“a=2&test=1&c=中文”
{% endhighlight %}

Another interesting consideration for Global methods is the escaping of strings
provided by escape() and unescape(). Primarily, we see this done on the Web
in order to create URL safe strings. You probably have seen this when working with forms.
While these methods would be extremely useful, the ECMAScript specification suggests
that escape() and unescape() are deprecated in favor of the more aptly named encodeURI(),
encodeURIComponent(), decodeURI(), and decodeURIComponent().

{% highlight javascript %}
var aURL = encodeURI("http://www.pint.com/cgi-bin/search?term=O''Neill & Sons");
console.log("encodedURI: " + aURL);
console.log("decodedURI: " + decodeURI(aURL));
var aURL = encodeURIComponent("http://www.pint.com/cgi-bin/search?term=O''Neill & Sons");
console.log("encodeURIComponent: " + aURL);
console.log("decodeURIComponent: " + decodeURIComponent(aURL));
{% endhighlight %}

如果URL里字符串已经被encode过（一般包含'%'符号）的话，再调decodeURI, decodeURIComponent会报错误misformed error。

decodeURIComponent和PHP中的rawurlencode，如果不包括!~*'()这些字符那产生的结果是一致的，二者对-_.这三个字符都不会处理。而PHP中的urlencode则在空格处理上稍有不同，将其替换成了+，如下说明：

Returns a string in which all non-alphanumeric characters except -_. have been replaced with a percent (%) sign followed by two hex digits and spaces encoded as plus (+) signs. It is encoded the same way that the posted data from a WWW form is encoded, that is the same way as in application/x-www-form-urlencoded media type. This differs from the » RFC 1738 encoding (see rawurlencode()) in that for historical reasons, spaces are encoded as plus (+) signs.
