---
layout: post
title: "nodejs实现google网页抓取"
date: "Thu Nov 19 2015 22:49:54 GMT+0800 (CST)"
categories: javascript
---

使用nodejs的`request`类库实现谷歌网页爬虫。

pc版本网页抓取
-----

{% highlight javascript %}
var request = require('request');
var iconv = require('iconv-lite');

request({url:'https://www.google.com.hk/search?num=30&safe=active&q=nodejs',encoding:null}, function(err, res, body) {
    var html = iconv.decode(body, 'Big5');
    console.log(html);
});
{% endhighlight %}

或者
-----

使用`charset-parser`来获取`chartset`网页编码。

{% highlight javascript %}
var request = require('request');
var iconv = require('iconv-lite');
var charsetParser = require('charset-parser');

iconv.extendNodeEncodings();

request('https://www.google.com.hk/search?num=30&safe=active&q=nodejs', {
    encoding: null
}, function(err, res, binary) {
    var charset = charsetParser(res.headers['content-type'], binary, 'utf-8');
    console.log(charset);
    var html = iconv.decode(binary, charset);
    console.log(html);
});
{% endhighlight %}

或者抓取google手机版本
-----

使用mobile版本，可避过`Big5`编码问题，网页返回的内容默认就是`utf-8`编码的，更加简洁。

{% highlight javascript %}
var request = require("request");

var headers = {
    "user-agent": "Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4"
};

var options = {
    url: "https://www.google.com.hk/search?num=30&safe=active&q=nodejs",
    headers: headers
};

var handler = function(err, res, body) {
    console.log(body);
}

request(options, handler);
{% endhighlight %}

附package.json
-----

{% highlight json %}
{
    "name": "google-spider",
    "version": "0.1.0",
    "private": true,
    "scripts": {
        "start": "node ./google-spider.js"
    },
    "dependencies": {
        "request": "~2.67.0",
        "iconv-lite": "~0.4.13"
    }
}
{% endhighlight %}
