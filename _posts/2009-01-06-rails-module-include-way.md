---
layout: post
title: "rails module include way"
date: "Tue Jan 06 2009 13:51:00 GMT+0800 (CST)"
categories: ruby
---

以下方式的代码在rails中源码中的相当多见，其中的`self.included(base)`方法是一个回调方法，当此`module`被其他名为`base`的`module` (或者`class`) included的时候触发此方法。通过`class_eval`，`include`，`extend`加入了实例方法和类方法到`base`中，代码划分得很干净。

{% highlight ruby %}
module ActionController
  module Components
    def self.included(base)
      base.class_eval do
        include InstanceMethods
        extend ClassMethods
        helper HelperMethods
      end
    end

    module ClassMethods
    end

    module HelperMethods
    end

    module InstanceMethods
    end
  end
end
{% endhighlight %}
