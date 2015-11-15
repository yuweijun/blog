---
layout: post
title: "java unicode字符串转换"
date: "Fri Feb 13 2009 13:58:00 GMT+0800 (CST)"
categories: java
---

java转换unicode字符串为其对应的实际字符串。

{% highlight javascript %}
/**
 * 转换unicode字符串为其对应的实际字符串
 * UnicodeToString("测试\\u4E2D\\u6587") 输出为: "测试中文"
 *
 * @param str
 * @return
 */
public static String UnicodeToString(String str) {
    Pattern pattern = Pattern.compile("(\\\\u(\\p{XDigit}{4}))");
    Matcher matcher = pattern.matcher(str);
    char ch;

    while (matcher.find()) {
        ch = (char) Integer.parseInt(matcher.group(2), 16);
        str = str.replace(matcher.group(1), ch + "");
    }
    return str;
}
{% endhighlight %}

References
-----

1. [http://yuweijun.blogspot.com/2008/06/unicode.html](http://yuweijun.blogspot.com/2008/06/unicode.html)
2. [http://yuweijun.blogspot.com/2008/08/unicode-and-html-entities-in-javascript.html](http://yuweijun.blogspot.com/2008/08/unicode-and-html-entities-in-javascript.html)
3. [http://yuweijun.blogspot.com/2008/12/rubyunicodeutf8.html](http://yuweijun.blogspot.com/2008/12/rubyunicodeutf8.html)
