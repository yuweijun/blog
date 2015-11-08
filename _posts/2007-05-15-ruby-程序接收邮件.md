---
layout: post
title: "用ruby程序接收邮件"
date: "Tue May 15 2007 23:21:00 GMT+0800 (CST)"
categories: ruby
---

使用Ruby接收126邮箱邮件简单示例：

{% highlight ruby %}
require 'net/pop'

pop = Net::POP3.new('pop3.126.com')
pop.start('test', 'password')
if pop.mails.empty?
    puts 'No mail.'
else
    i = 0
    # pop.each_mail do |m|   # or "pop.mails.each do |m|"
    #   File.open("#{i}", 'w') do |f|
    #     f.write m.pop
    #   end
    #   m.delete
    #   i += 1
    # end
    pop.mails.each do |m|
      File.open("mail", 'a') do |f|
        f.write m.pop
      end
    end
    puts "#{pop.mails.size} mails popped."
end
pop.finish
{% endhighlight %}
