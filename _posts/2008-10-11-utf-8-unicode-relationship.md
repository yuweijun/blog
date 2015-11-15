---
layout: post
title: "utf-8和unicode之间相互转换"
date: "Sat Oct 11 2008 22:36:00 GMT+0800 (CST)"
categories: linux
---

utf-8是unicode的实现方式之一。

utf-8最大的一个特点，就是它是一种变长的编码方式。它可以使用1~4个字节表示一个符号，根据不同的符号而变化字节长度。

utf-8的编码规则很简单，只有二条：

1. 对于单字节的符号，字节的第一位设为0，后面7位为这个符号的unicode码。因此对于英语字母，utf-8编码和ascii码是相同的。
2. 对于n字节的符号（n>1），第一个字节的前n位都设为1，第n+1位设为0，后面字节的前两位一律设为10。剩下的没有提及的二进制位，全部为这个符号的unicode码。

下表总结了编码规则，字母x表示可用编码的位。

| unicode符号范围     | utf-8编码方式                       |
|:--------------------|:----------------------------------- |
| 十六进制            | 二进制                              |
| 0000 0000-0000 007F | 0xxxxxxx                            |
| 0000 0080-0000 07FF | 110xxxxx 10xxxxxx                   |
| 0000 0800-0000 FFFF | 1110xxxx 10xxxxxx 10xxxxxx          |
| 0001 0000-0010 FFFF | 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx |

以汉字`严`为例，演示如何实现utf-8编码。

已知`严`的unicode是`4e25`（100111000100101），根据上表，可以发现`4e25`处在第三行的范围内（0000 0800-0000 ffff），因此"严"的utf-8编码需要三个字节，即格式是"1110xxxx 10xxxxxx 10xxxxxx"。然后，从"严"的最后一个二进制位开始，依次从后向前填入格式中的`x`，多出的位补`0`。这样就得到了，"严"的utf-8编码是 "11100100 10111000 10100101"，转换成十六进制就是`e4b8a5`。

在firebug中测试中文"胡"字：

{% highlight javascript %}
console.log(encodeURI('胡'));
// "%E8%83%A1"
console.log('胡'.charCodeAt().toString(16));
// "80e1"
console.log('胡'.charCodeAt().toString(2));
// "1000000011100001"
console.log(parseInt('111010001000001110100001', 2));
// 15238049
console.log(parseInt('111010001000001110100001', 2).toString(16));
// "e883a1"
{% endhighlight %}

其中将`1000000011100001`对应位置补上`110`或者`10`，生成unicode二进制值`111010001000001110100001`，反过来从unicode转到utf-8只要移除对应位置上的`110`和`10`即可得到utf-8的二进制值。

References
-----

1. [http://yuweijun.blogspot.com/2008/06/unicode.html](http://yuweijun.blogspot.com/2008/06/unicode.html)
2. [http://yuweijun.blogspot.com/2008/08/unicode-and-html-entities-in-javascript.html](http://yuweijun.blogspot.com/2008/08/unicode-and-html-entities-in-javascript.html)
3. [http://dreamstone.iteye.com/blog/77939](http://dreamstone.javaeye.com/blog/77939)
