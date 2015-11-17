---
layout: post
title: "introduction to xmlhttprequest level 2"
date: "Fri, 21 Sep 2012 16:47:25 +0800"
categories: javascript
---

老版本的XMLHttpRequest对象有以下几个缺点
-----

* 只支持文本数据的传送，无法用来读取和上传二进制文件。
* 传送和接收数据时，没有进度信息，只能提示有没有完成。
* 受到"同域限制"（Same Origin Policy），只能向同一域名的服务器请求数据。

新版本的XMLHttpRequest对象，针对老版本的缺点，做出了大幅改进
-----

* 可以设置HTTP请求的时限。
* 可以使用FormData对象管理表单数据。
* 可以上传文件。
* 可以请求不同域名下的数据（跨域请求）。
* 可以获取服务器端的二进制数据。
* 可以获得数据传输的进度信息。

References
-----

1. [http://caniuse.com/#feat=xhr2](http://caniuse.com/#feat=xhr2)
1. [https://dev.opera.com/articles/xhr2/](https://dev.opera.com/articles/xhr2/)
1. [http://www.ruanyifeng.com/blog/2012/09/xmlhttprequest_level_2.html](http://www.ruanyifeng.com/blog/2012/09/xmlhttprequest_level_2.html)
1. [http://www.html5rocks.com/zh/tutorials/file/xhr2/](http://www.html5rocks.com/zh/tutorials/file/xhr2/)
1. [http://www.html5rocks.com/en/tutorials/file/xhr2/](http://www.html5rocks.com/en/tutorials/file/xhr2/)
1. [https://developer.mozilla.org/en-US/docs/DOM/XMLHttpRequest/Using_XMLHttpRequest](https://developer.mozilla.org/en-US/docs/DOM/XMLHttpRequest/Using_XMLHttpRequest)
1. [https://developer.mozilla.org/en-US/docs/HTTP_access_control](https://developer.mozilla.org/en-US/docs/HTTP_access_control)
1. [http://dev.opera.com/articles/view/dom-access-control-using-cross-origin-resource-sharing/](http://dev.opera.com/articles/view/dom-access-control-using-cross-origin-resource-sharing/)
1. [http://en.wikipedia.org/wiki/Cross-Origin_Resource_Sharing](http://en.wikipedia.org/wiki/Cross-Origin_Resource_Sharing)
1. [https://developer.mozilla.org/en-US/docs/Server-Side_Access_Control](https://developer.mozilla.org/en-US/docs/Server-Side_Access_Control)
1. [http://dvcs.w3.org/hg/xhr/raw-file/tip/Overview.html](http://dvcs.w3.org/hg/xhr/raw-file/tip/Overview.html)
1. [http://www.w3.org/TR/XMLHttpRequest2/](http://www.w3.org/TR/XMLHttpRequest2/)
1. [https://developer.mozilla.org/en-US/docs/DOM/XMLHttpRequest/Sending_and_Receiving_Binary_Data](https://developer.mozilla.org/en-US/docs/DOM/XMLHttpRequest/Sending_and_Receiving_Binary_Data)
