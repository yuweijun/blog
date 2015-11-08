---
layout: post
title: "rails activerecord observer usage"
date: "Sun Aug 26 2007 20:30:00 GMT+0800 (CST)"
categories: ruby
---

要使Observer工作，第一种做法是在controller里声明， 这样unit test就无法加载此observer到console中

{% highlight ruby %}
# app/models/flower_observer.rb
class FlowerObserver < ActiveRecord::Observer
  observe Flower

  def after_create(model)
    # model.do_something!
  end
end

# controller(s)
class FlowerController < ApplicationController
  observer :flower_observer
end
{% endhighlight %}

第二种做法可以加载observer到console中：

{% highlight ruby %}
# app/models/foo_bar.rb
class FooBar < ActiveRecord::Base
end

FooBarObserver.instance
{% endhighlight %}

最后一个方法是在config/environment.rb中加载此observer:

{% highlight ruby %}
config.active_record.observers = :flower_observer
{% endhighlight %}
