---
layout: post
title: "rails helpers using outside of views"
date: "Mon Sep 07 2009 09:21:00 GMT+0800 (CST)"
categories: ruby
---

How to access helper methods outside of the view layer?

{% highlight ruby %}
# models/category.rb
def description
  "This category has #{helpers.pluralize(products.count, 'product')}."
end

def helpers
  ActionController::Base.helpers
end

# products_controller.rb
def create
  @product = Product.new(params[:product])
  if @product.save
    flash[:notice] = "Successfully created #{@template.link_to('product', @product)}."
    redirect_to products_url
  else
    render :action => 'new'
  end
end
{% endhighlight %}


References
-----

1. [http://railscasts.com/episodes/132-helpers-outside-views](http://railscasts.com/episodes/132-helpers-outside-views)
