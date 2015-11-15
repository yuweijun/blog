---
layout: post
title: "rails error about action controller invalid authenticity token"
date: "Tue Dec 16 2008 15:33:00 GMT+0800 (CST)"
categories: ruby
---

升级到rails 2.2.2后，在用户登录时抛出以上错误，因为rails新版本对安全控制做了一些加强措施，只要在form中添加`<%= token_tag %>`即可，rails会添加一个`token`(在action中的`form_authenticity_token`方法生成这个token)在form中，随表单一起提交，可以适当的防止csrf攻击。

{% highlight html %}
<input name="authenticity_token" type="hidden" value="d688e6bf60f43bd171504e059de1ba03f876d129" />
{% endhighlight %}

具体可参考`ActionController::RequestForgeryProtection`和`config/environment.rb`中的配置说明：

{% highlight ruby %}
# Your secret key for verifying cookie session data integrity.
# If you change this key, all old sessions will become invalid!
# Make sure the secret is at least 30 characters and all random,
# no regular words or you'll be exposed to dictionary attacks.
config.action_controller.session = {
    :session_key => '_rails_session_key',
    :secret => '_rails_secret'
}
{% endhighlight %}

