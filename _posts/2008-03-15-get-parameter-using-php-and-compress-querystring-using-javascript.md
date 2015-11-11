---
layout: post
title: "get parameter using php and compress querystring using javascript"
date: "Sat Mar 15 2008 19:05:00 GMT+0800 (CST)"
categories: javascript
---

在php中如果从一个form提交一个数组给php页面的时候，一般在form中就将对应的input属性名写为: `name="arr[]"`, 这样就可以在接收页面直接用php取到`$arr ＝ $_POST["arr"]`取到这个数组，如果用get方法传送的query string是这样子的话: `?arr=1&arr=2&arr=3`时，php则在用`$_GET["arr"]`获得arr时则会只得到最后赋值的3，这是php处理query string的方法，不同的语言在处理这个query string是不一样的，这不是http协议的规定。

对于server一端还是会正常收到这个请求的query string: `?arr=1&arr=2&arr=3`，仍可以用server端的脚本语言重新对参数进行分析，也可以用javascript在client端分析query string。

javascript compress query string
-----

{% highlight javascript %}
function compress(data){
    var q = {}, ret = "";
    data.replace(/([^=&]+)=([^&]*)/g, function(m, key, value){
        q[key] = (q[key] ? q[key] + "," : "") + value;
    });
    for ( var key in q )
        ret = (ret ? ret + "&" : "") + key + "=" + q[key];
    return ret;
}
{% endhighlight %}

or
-----

{% highlight javascript %}
function compress(data) {
  data = data.replace(/([^&=]+=)([^&]*)(.*?)&\1([^&]*)/g, "$1$2,$4$3");
  return /([^&=]+=).*?&\1/.test(data) ? compress(data) : data;
}
{% endhighlight %}

References
-----

1. [Jone Resig's blog](http://ejohn.org/blog/search-and-dont-replace/)
