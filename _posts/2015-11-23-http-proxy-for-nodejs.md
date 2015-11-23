---
layout: post
title: "node-http-proxy usage example"
date: "Sun, 22 Nov 2015 23:25:15 +0800"
categories: nodejs
---

`node-http-proxy`是用node.js实现的可编程的代理服务器类库，支持`websockets`，可作代理集群和负载均衡管理，可以根据自定义的逻辑，代理不同的请求到不同的服务器上去，如下例子。

http servers
-----

{% highlight javascript %}
#! /usr/bin/env node

// We simulate the 3 target applications
var http = require("http");

http.createServer(function(req, res) {
  res.writeHead(200, { 'Content-Type': 'text/plain' });
  res.write('8081 request successfully proxied!' + '\n' + JSON.stringify(req.headers, true, 2));
  res.end();
}).listen(8081);

http.createServer(function(req, res) {
  res.writeHead(200, { 'Content-Type': 'text/plain' });
  res.write('8082 request successfully proxied!' + '\n' + JSON.stringify(req.headers, true, 2));
  res.end();
}).listen(8082);

http.createServer(function(req, res) {
  res.writeHead(200, { 'Content-Type': 'text/plain' });
  res.write('8083 request successfully proxied!' + '\n' + JSON.stringify(req.headers, true, 2));
  res.end();
}).listen(8083);
{% endhighlight %}

保存文件为`servers.js`，然后用pm2启动此脚本：

{% highlight bash %}
$> pm2 start servers.js
{% endhighlight %}

proxy server
-----

{% highlight javascript %}
#! /usr/bin/env node

var http = require('http');
var url = require('url');
var httpProxy = require('http-proxy');

var proxy = httpProxy.createProxyServer();
var port = 8080;

var proxyServer = http.createServer(function(req, res) {
    var u = url.parse(req.url, true);
    var target = u.pathname.replace('/', '');
	console.info(target);
    console.log(JSON.stringify(req.headers, true, 2));

    // dynamic proxying with custom logic
    if (target === 'forbidden') {
        return res.end('forbidden request.');
    }

    proxy.web(req, res, {
        target: 'http://localhost:' + target
    });
}).listen(port);

console.log("proxy server listen on " + port);
{% endhighlight %}

保存文件为`proxy-server.js`，运行命令：

{% highlight bash %}
$> pm2 start proxy-server.js
{% endhighlight %}

分别访问以下链接，查看结果。

1. http://localhost:8080/forbidden
2. http://localhost:8080
3. http://localhost:8080/8081
4. http://localhost:8080/8082
5. http://localhost:8080/8083

References
-----

1. [https://github.com/nodejitsu/node-http-proxy](https://github.com/nodejitsu/node-http-proxy)
