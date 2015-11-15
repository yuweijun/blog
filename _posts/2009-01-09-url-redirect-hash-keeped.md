---
layout: post
title: "页面重定向与url中的hash在不同的浏览器中的表现"
date: "Fri Jan 09 2009 12:11:00 GMT+0800 (CST)"
categories: web
---

访问时url上带有hash(如`http://localhost/a.php#test`)，重定向到`b.php`页面时，当前`a.php`页上`hash`会被带到`b.php`页面上，在firefox/opera上测试的效果是如此，但ie6上则直接到`b.php`页，不会将`a.php`页上`hash`值带过来。示例代码：

a.php
-----

{% highlight php %}
<php
header("Location: b.php");
?>
{% endhighlight %}

b.php
-----

{% highlight php %}
<php
var_dump($_SERVER);
?>
{% endhighlight %}

访问`http://localhost/a.php#test`，跳转完成后url为`http://localhost/b.php#test`。
