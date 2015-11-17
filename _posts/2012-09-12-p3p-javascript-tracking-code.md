---
layout: post
title: "javascript p3p tracking code"
date: "Wed, 12 Sep 2012 16:46:19 +0800"
categories: javascript
---

下面代码中的参数说明
-----

1. id：3000是指定的某个跟踪项目的id，跟踪数据产生的visits/leads都会与此id关联上。
2. step：0代表visits数据，1代表leads数据。
3. code：代表跟踪相关的某个unique的key。
4. items：电商订单完成之后，与商品相关的一些信息。

visits
-----

{% highlight javascript %}
(function(){
    var _ecomm_code = 0, url = window.location;
    if (/_ecomm_code=(.*?)(?:&|$)/.test(url.href)) {
        _ecomm_code = RegExp.$1;
    } else if (/(google|baidu|soso|sogou|yahoo|live|bing|youdao)\.[cnom]+/i.test(document.referrer)) {
        // TODO _ecomm_code of seo keyword by client
        _ecomm_code = 1;
    }
    if (_ecomm_code) {
        document.cookie = ['_ecomm_code=', encodeURIComponent(_ecomm_code), '; expires=', new Date(new Date().getTime() + 30*24*60*60*1000).toGMTString(), '; path=/', '; domain=', url.hostname].join('');
        // 需要设置clientId和trackStep，php文件后缀名改为gif，利用浏览器缓存减少重复请求，PHP服务器端同时要做REFERRER验证，直接的请求视为无效数据
        // 利用"//"可以忽略判断是http还是https请求
        // TODO id: client_id
        new Image().src = ['//tracking.server-host.com/v.php?id=3000&step=0&code=', encodeURIComponent(_ecomm_code), '&t=', new Date().getTime(), '&rf=', document.referrer].join('');
    }
})();
{% endhighlight %}

leads
-----

{% highlight javascript %}
(function(){
    if (/_ecomm_code=(.*?)(?:;|$)/.test(document.cookie)) {
        // Arguments for this method are matched by position, so be sure to supply all parameters, even if some of them have an empty value.
        // var trans = ["orderId", "affiliation", "total", "tax", "shipping", "city", "state", "country", "currency"];
        // TODO parameters:
        // String   orderId Required. Internal unique order id number for this transaction.
        // String   affiliation Optional. Partner or store affiliation (undefined if absent).
        // String   total Required. Total dollar amount of the transaction.
        // String   tax Optional. Tax amount of the transaction.
        // String   shipping Optional. Shipping charge for the transaction.
        // String   city Optional. City to associate with transaction.
        // String   state Optional. State to associate with transaction.
        // String   country Optional. Country to associate with transaction.
        // String   currency Optional. Currency to associate with transaction, default is "RMB".
        var trans = ["orderid1", "SE", "218.00", "0", "0", "上海", "上海", "中国", "RMB"];
        //  associates the item to the parent transaction object via the orderId argument
        // var items =  ["orderId", "skuCode", "name", "category", "price", "quantity"];
        var items = [];
        // TODO parameters:
        // String   orderId Optional Order ID of the transaction to associate with item.
        // String   skuCode Required. Item's SKU code.
        // String   name Required. Product name. Required to see data in the product detail report.
        // String   category Optional. Product category.
        // String   price Required. Product price.
        // String   quantity Required. Purchase quantity.
        items.push(["orderid1","233243","物品名1","1","740","2"].join('||'));
        items.push(["orderid1","212328","物品名2","1","740","3"].join('||'));
        new Image().src = ['//tracking.server-host.com/s.php?id=3000&step=1&code=', RegExp.$1, '&items=', encodeURIComponent(items.join('::')), '&t=', new Date().getTime(), '&ts=', encodeURIComponent(trans.join('::')), '&rf=', document.referrer].join('');
    }
})();
{% endhighlight %}

References
-----

1. [Tracking Code: Ecommerce](https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiEcommerce?hl=zh-CN#_gat.GA_Tracker_._addTrans)
