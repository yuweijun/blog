---
layout: post
title: "nodejs proxy server for google"
date: "Thu Nov 19 2015 22:49:54 GMT+0800 (CST)"
categories: javascript
---

以nodejs request为基础做一个google代理服务器。

package.json
-----

{% highlight json %}
{
    "name": "google-hk",
    "version": "0.2.1",
    "private": true,
    "scripts": {
        "start": "node ./google-hk"
    },
    "dependencies": {
        "pm2": "~0.12.16",
        "request": "~2.67.0",
        "iconv-lite": "~0.4.13"
    }
}
{% endhighlight %}

processes.json
-----

{% highlight json %}
{
    "apps": [{
        "name": "google-hk",
        "script": "google-hk",
        "log_date_format": "YYYY-MM-DD HH:mm Z",
        "ignore_watch": ["[\\/\\\\]\\./", "node_modules"],
        "watch": false,
        "vizion": true,
        "autorestart": true
    }]
}
{% endhighlight %}

proxy of www.google.com.hk
-----

{% highlight javascript %}
#!/usr/bin/env node

var http = require("http");
var proxy = require("request");

var server = http.createServer(function(request, response) {
    console.log(request.url);
    var url = "https://www.google.com.hk" + request.url;
    request.pipe(proxy(url)).pipe(response);
});

server.listen(8000);
console.log("google proxy server is listening");

{% endhighlight %}

运行server
-----

{% highlight bash %}
$> node_modules/pm2/bin/pm2 start processes.json
{% endhighlight %}

另外写了个手动抓取到内容，再返回给客户端的版本：

{% highlight javascript %}
#!/usr/bin/env node

var http = require("http");
var httpClient = require("request");
var url = require("url");

var server = http.createServer(function(request, response) {
    var google = "https://www.google.com.hk";
    var u = url.parse(request.url, true);
    var pathname = u.pathname;
    var headers = {
        "user-agent": request.headers["user-agent"]
    };

    if (/^\/url/.test(pathname)) {
        response.writeHead(302, {
            "location": u.query.q
        });
        response.end();
    } else {
        console.log(request.url);
        var options = {
            url: google + request.url,
            encoding: null,
            headers: headers
        };
        var handler = function(err, res, body) {
            if (err) {
                response.write("http client error: " + err.message);
            } else {
		response.writeHead(res.statusCode, res.headers);
                if (/html/i.test(request.headers.accept)) {
                    console.log(request.headers);
                    // response.writeHead(200, { "Content-Type": "text/html; charset=UTF-8" });
                }
                response.write(body);
            }
            response.end();
        }

        httpClient(options, handler);
    }
});

server.listen(8000);
console.log("google proxy server is listening");
{% endhighlight %}
