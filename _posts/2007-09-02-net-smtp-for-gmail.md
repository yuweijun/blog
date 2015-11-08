---
layout: post
title: "net::smtp for gmail"
date: "Sun Sep 02 2007 17:09:00 GMT+0800 (CST)"
categories: ruby
---

先安装gem包tlsmail。

{% highlight bash %}
$> gem install tlsmail
{% endhighlight %}

测试代码
-----

{% highlight ruby %}
require "rubygems"
require "tlsmail"
require "time"

content = << EOF
From: test@gmail.com
To: testx@gmail.com
Subject: TEST
Date: #{Time.now.rfc2822}

TEST CONTENT
EOF

Net::SMTP.enable_tls(OpenSSL::SSL::VERIFY_NONE)
Net::SMTP.start("smtp.gmail.com", "587", "localhost", "test@gmail.com", "********", :plain) do |smtp|
smtp.send_message content, "test@gmail.com", "testx@gmail.com"
end
{% endhighlight %}
