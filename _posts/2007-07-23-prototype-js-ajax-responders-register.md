---
layout: post
title: "prototype.js ajax.responders.register"
date: "Mon Jul 23 2007 10:45:00 GMT+0800 (CST)"
categories: javascript
---

`Ajax.Responders.register(obj)`是将obj注册到`Ajax.Responders.responders`中，原代码`this.responders.push(responderToAdd)`上面是注册了一个对象，其中包含了二个方法(onCreate和onComplete)。这个obj注册了之后，就成了responders数组中的一个值，在后面`Ajax.Responders.dispatch`里调用对象responder时会遍历到此obj，callback即为obj中的方法(onCreate和onComplete)。 dispatch里会对所有responders数组中对象执行此callback方法。

{% highlight javascript %}
Ajax.Responders.register({
    onCreate: function() {
        Ajax.activeRequestCount++;
    },

    onComplete: function() {
        Ajax.activeRequestCount--;
    }
});
{% endhighlight %}
