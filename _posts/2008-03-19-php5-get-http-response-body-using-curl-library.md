---
layout: post
title: "php5 get http response body using curl library"
date: "Wed Mar 19 2008 11:36:00 GMT+0800 (CST)"
categories: php
---

get response body of specified web page using php5, such as google homepage.

{% highlight php %}
<?php
    ob_start();

    $ch = curl_init("http://www.google.com/");
    curl_exec($ch);
    curl_close($ch);
    $retrievedhtml = ob_get_contents();
    ob_end_clean();
    // ob_end_flush();

    echo $retrievedhtml;
?>
{% endhighlight %}
