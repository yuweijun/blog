---
layout: post
title: "java.util.regex.pattern.canon_eq说明"
date: "Wed, 10 Oct 2012 17:34:20 +0800"
categories: java
---

以下为javadoc正文：

{% highlight text %}
int java.util.regex.Pattern.CANON_EQ = 128 [0x80]
CANON_EQ
public static final int CANON_EQ
Enables canonical equivalence.

When this flag is specified then two characters will be considered to match if, and only if, their full canonical decompositions match. The expression "a\u030A", for example, will match the string "\u00E5" when this flag is specified. By default, matching does not take canonical equivalence into account.
There is no embedded flag character for enabling canonical equivalence.
Specifying this flag may impose a performance penalty.
{% endhighlight %}

因为没有很明白的理解它的意思，写了一个测试代码如下：

{% highlight java %}
@Test
public void testPatternCanonEq() {
    String a = "a\u030A";
    String b = "\u00E5";

    // a = å, b = å
    System.out.printf("a = %s,\tb = %s\n", a, b);

    Matcher matcher = Pattern.compile(a).matcher(b);
    // false
    System.out.println(matcher.find());

    Matcher matcher2 = Pattern.compile(a, Pattern.CANON_EQ).matcher(b);
    // true
    System.out.println(matcher2.find());
}
{% endhighlight %}

在javascript中没有这种功能，所以无法达到这个效果：

{% highlight javascript %}
new RegExp("a\u030A").test("\u00E5")  => false
{% endhighlight %}
