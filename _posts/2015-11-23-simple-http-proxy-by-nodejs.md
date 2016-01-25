---
layout: post
title: "simple http proxy server written in nodejs"
date: "Mon, 23 Nov 2015 21:46:10 +0800"
categories: nodejs
---

用nodejs的`http`模块实现的http代理服务器，不支持https代理。

http-proxy.js
-----

{% highlight javascript %}
var http = require('http');

http.createServer(function(request, response) {
    var ip = request.connection.remoteAddress;
    console.log(ip + ": " + request.method + " " + request.url);
    console.log(JSON.stringify(request.headers, null, 4));

    var proxy = http.request(request.url);
    proxy.on('response', function(res) {
        res.on('data', function(chunk) {
            response.write(chunk, 'binary');
        });
        res.on('error', function(err) {
            console.log('proxy error: ' + err.message);
            response.end();
        });
        res.on('end', function() {
            response.end();
        });
        response.writeHead(res.statusCode, res.headers);
    });

    request.on('data', function(chunk) {
        proxy.write(chunk, 'binary');
    });

    request.on('error', function(err) {
        console.log('request error' + err.message);
        proxy.end();
    });

    request.on('end', function() {
        proxy.end();
    });
}).listen(8080);
console.log("proxy server listen on 8080.");
{% endhighlight %}

node运行此脚本，代理服务器在`8080`端口开始监听。写个http请求测试如下：

http-proxy-test.js
-----

{% highlight javascript %}
var request = require('request');

process.argv.forEach(function(value, index, array) {
    console.log(index + ': ' + value);
});

var url = process.argv.pop();
if (!/^http/i.test(url)) {
    url = 'http://www.4e00.com';
}

request({url: url, proxy: 'http://localhost:8080'}, function(err, response, body) {
    if (err) {
        console.log('request err: ' + err.message);
    } else if (response.statusCode == 200) {
        console.log(body.length);
    } else {
        console.log(JSON.stringify(response, null, 4));
    }
});
{% endhighlight %}

在命令行里输入：

{% highlight bash %}
$> node http-proxy-test.js http://www.4e00.com
{% endhighlight %}

