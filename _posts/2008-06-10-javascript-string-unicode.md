---
layout: post
title: "字符串与unicode编码的相互转换"
date: "Tue Jun 10 2008 13:30:00 GMT+0800 (CST)"
categories: javascript
---

unicode编码简而言之就是将每一个字符用16位2进制数标识。但是通常都用4位的16进制数标识。例如：

1. 中文字符串"你好"的unicode码为：\u4f60\u597d;
2. 英文字符串"ab"的unicode码为：\u0061\u0062；

其中`\u`是标识unicode码用的，后面的4位16进制数则是对应字符的unicode码。

unicode编码规则
-----

unicode码对每一个字符用4位16进制数表示。具体规则是：将一个字符(char)的高8位与低8位分别取出，转化为16进制数，
如果转化的16进制数的长度不足2位，则在其后补0，然后将高、低8位转成的16进制字符串拼接起来并在前面补上`\u`即可。

用java代码说明unicode的编码规则
-----

java的unicode解码编码的代码详见javaeye的帖子

{% highlight java %}
public class Unicode {

    public static void main(String[] args) {
        char c = '一'; // 一(4e00)是unicode中文字符集首字，龥(9fa5)是unicode中文字符集尾字
        int i, j;
        i = c & 0xFF;
        j = c >>> 8;
        System.out.println("Original character is: " + c);
        System.out.println("low 8 bit is: " + i);
        System.out.println("high 8 bit is: " + j);
    }

}
{% endhighlight %}

{% highlight bash %}
$> javac Unicode.java
$> java Unicode
Original character is: 一
low 8 bit is: 0
high 8 bit is: 78
{% endhighlight %}

高位`78`转为16进制为`4e`，将高、低8位转成的16进制字符串拼接起来即为`\u4e00`，这就是中文字`一`的unicode编码。

在ajax请求返回的responseText或者json数据可以先在服务器端编码为unicode格式传给客户端浏览器，这样客户端的页面无论是什么编码，js都可以很好的处理返回的内容。
`4e00`的十进制值为`4 * 16 * 16 * 16 + 14 * 16 * 16 + 0 * 16 + 0 = 19968`

网页中则可以用`&#19968`；来表示中文字`一`，这样不论网页以何种编码，页面始终都可以正常显示，而不会出现乱码。这种方法以前在做wap项目常常将页面上所有中文字都用unicode编码后发布，就是为了避免乱码问题。

javascript代码，验证字符串中是否包括中文字
-----

{% highlight javascript %}
/[\u4e00-\u9fa5]/.test(str)
{% endhighlight %}

ruby中验证字符串中是否包括中文字
-----

{% highlight ruby %}
/[一-龥]/ =~ str
{% endhighlight %}

References
-----

1. [http://haoi77.javaeye.com/blog/198840](http://haoi77.javaeye.com/blog/198840)