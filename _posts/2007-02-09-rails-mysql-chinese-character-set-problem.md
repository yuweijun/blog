---
layout: post
title: "rails mysql 中文乱码问题"
date: "Fri Feb 09 2007 15:46:00 GMT+0800 (CST)"
categories: mysql
---

一、MySQL设置：
-----

将MySQL的Character设置成为utf8(或GBK或GB2312，只要和ROR里的Character设置统一就行)。

修改方法有3种：

1. 用MySQL Server Instance Config Wizard设置character-set为utf8；
2. 修改MySQL的配置文件，改其中的两处default-character-set= utf8(推荐，改完后重启MySQL生效)；
3. 在建表时指定表的character-set为utf8

二、Ruby On Rails设置：
-----

修改application.rb告知MySql使用UTF8。
下面在@headers.[]=这个方法已经作废，直接用headers.[]=方法。

{% highlight ruby %}
class ApplicationController < ActionController::Base
    before_filter :configure_charsets
    def configure_charsets
        @headers["Content-Type"] = "text/html; charset=utf-8"

        # Set connection charset. MySQL 4.0 doesn’t support this so it
        # will throw an error, MySQL 4.1 needs this
        suppress(ActiveRecord::StatementInvalid) do
            ActiveRecord::Base.connection.execute 'SET NAMES utf8'
        end
    end
end
{% endhighlight %}

三、修改environment.rb
-----

{% highlight ruby %}
$KCODE = 'u'
require 'jcode'
{% endhighlight %}
