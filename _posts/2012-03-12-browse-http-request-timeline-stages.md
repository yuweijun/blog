---
layout: post
title: "chrome浏览器http request timeline各阶段说明"
date: "Mon, 12 Mar 2012 11:18:19 +0800"
categories: web
---

Rough Definitions
-----

1. DNS Lookup: Translating the web address into a destination IP address by using a DNS server Connecting: Establishing a connection with the web server
2. Blocking: Previously known as 'queueing', this is explained in more detail here
3. Sending: Sending your HTTP Request to the server
4. Waiting: Waiting for a response from the server - this is where it's probably doing all the work
5. Receiving: Getting the HTTP response back from the server

References
-----

1. [Resource network timing](https://developers.google.com/web/tools/chrome-devtools/profile/network-performance/resource-loading#resource-network-timing)
