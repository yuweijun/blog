---
layout: post
title: "html canonical link element"
date: "Sun Nov 22 2015 09:07:20 GMT+0800 (CST)"
categories: web
---

相同一篇文章内容出现在不同的网址上，就产生了内容重复，这就给搜索引擎网页排名算法造成了麻烦。

网页内容重复常见原因
-----

1. 同一网站下因为url请求参数不一样，却显示了相同内容。
2. 由于CMS系统内容分发导致不同网址输出了相同的一篇文章。
3. 相同的一个数据源，在不同的hosts/protocols中显示了同一份内容。
4. 互联网转载。

搜索引擎为了优化网页排名，在`link`标签的属性`rel`中加入了一个`canonical`值，为当前网页内容指定了`canonical`版本，更多可参考[官方文档](https://tools.ietf.org/html/rfc6596)。

搜索引擎如何处理rel=canonical
-----

自2009年2月起，Google、Yahoo和Microsoft三个搜索引擎公司开始支持此标签属性。

搜索引擎会利用此属性来对抓取的结果进行过滤，并根据此属性值来确定内容的原始来源页面，对于搜索结果的排名算法有一定影响。

实现rel=canonical的二种方法
-----

1. 在html的head元素中添加：`<link ref="canonical" src="http://example.com/page.html" />`。
2. 在http响应头里添加：`Link: <http://example.com/page.html>; rel="canonical"`。

错误用法
-----

1. `rel=canonical`指向的url不存在。
2. `rel=cononical`指向网站的`robots.txt`禁止了搜索引擎爬虫。
3. 在网页里出现多次`rel=cononical`。
4. `rel=cononical`出现在`body`标签中。
5. 分页时，多个页面指向了一个相同的`rel=cononical`。
6. 使用CMS或者博客插件，导致`rel=cononical`指向了插件作者的网站。
7. `rel=cononical`使用了相对地址，最好是使用url绝对地址。

References
-----

1. [Canonical link element](https://en.wikipedia.org/wiki/Canonical_link_element)
2. [使用规范网址](https://support.google.com/webmasters/answer/139066?hl=zh-Hans)
3. [5 common mistakes with rel=canonical](http://googlewebmastercentral.blogspot.com/2013/04/5-common-mistakes-with-relcanonical.html)
4. [Matt Cutts: Gadgets, Google, and SEO](https://www.mattcutts.com/blog/canonical-link-tag/)
