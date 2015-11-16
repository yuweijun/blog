---
layout: post
title: "web performance optimization (wpo) and reports"
date: "Wed, 12 Oct 2011 11:45:32 +0800"
categories: web
---

Google准备将网站载入时间纳入到搜索排名因素之中[refer](http://www.google.com/intl/en/corporate/tenthings.html)，网站载入速度对业务的影响，举例如下：

{% highlight text %}
Amazon: 增加100ms延迟将导致收入下降1%；
Google: 400ms延迟将导致每用户搜索请求下降0.59%；
Yahoo!: 400ms延迟会导致流量下降5-9%；
Bing: 2秒的延迟将导致收入降低4.3%/用户(请问，首页用个那么大的背景图干啥?);
Mozilla 将下载页时间缩短2.2秒之后下载量增加15.4%;
Google Maps将文件大小减少30%后请求增加了30%；
Netflix在服务器端启用gzip，页面快了13-25%，节省了50%的网络流量；
Shopzilla将页面载入时间从7秒缩减到2秒，转化率提升了7-12%，页面请求增加25%，只用一半服务器就够了
要注意，这些只是数据，实际上，我们没有办法验证这些数据的真实性。但是可以肯定的是，网站访问速度过慢，一定对用户有负面影响。
{% endhighlight %}

References
-----

1. [http://www.stevesouders.com/blog/2010/05/07/wpo-web-performance-optimization/](http://www.stevesouders.com/blog/2010/05/07/wpo-web-performance-optimization/)
1. [http://www.dbanotes.net/web/web_performance_optimization.html](http://www.dbanotes.net/web/web_performance_optimization.html)
1. [http://continuum.apache.org/](http://continuum.apache.org/)
1. [http://imxpan.com/2011/02/10-golden-principles-of-successful-web-apps/](http://imxpan.com/2011/02/10-golden-principles-of-successful-web-apps/)
1. [http://thinkvitamin.com/web-apps/fred-wilsons-10-golden-principles-of-successful-web-apps/](http://thinkvitamin.com/web-apps/fred-wilsons-10-golden-principles-of-successful-web-apps/)
1. [http://pagespeed.googlelabs.com/](http://pagespeed.googlelabs.com/)
1. [http://www.webpagetest.org/](http://www.webpagetest.org/)
1. [http://www.dbanotes.net/web-performance.html](http://www.dbanotes.net/web-performance.html)
