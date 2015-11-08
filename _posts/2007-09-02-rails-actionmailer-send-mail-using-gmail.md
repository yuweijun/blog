---
layout: post
title: "Rails ActionMailer send mail using GMail"
date: "Sun Sep 02 2007 21:20:00 GMT+0800 (CST)"
categories: ruby
---

在config/environment.rb文件末添加ActionMailer配置，在插件目录里加入Net::SMTP补丁。

{% highlight ruby %}
require 'smtp_tls'
ActionMailer::Base.delivery_method = :smtp
ActionMailer::Base.server_settings = {
  :address => "smtp.gmail.com",
  :port => 587,
  :domain => "localhost",
  :authentication => :plain,
  :user_name => "test@gmail.com",
  :password => "********"
}
{% endhighlight %}

vendor/plugins/action_mailer_tls/init.rb

{% highlight ruby %}
require_dependency 'smtp_tls'
{% endhighlight %}

vendor/plugins/action_mailer_tls/lib/smtp_tls.rb

{% highlight ruby %}
require "openssl"
require "net/smtp"
Net::SMTP.class_eval do
  private
  def do_start(helodomain, user, secret, authtype)
    raise IOError, 'SMTP session already started' if @started
    check_auth_args user, secret, authtype if user or secret

    sock = timeout(@open_timeout) { TCPSocket.open(@address, @port) }
    @socket = Net::InternetMessageIO.new(sock)
    @socket.read_timeout = 60 #@read_timeout

    check_response(critical { recv_response() })
    do_helo(helodomain)

    raise 'openssl library not installed' unless defined?(OpenSSL)
    starttls
    ssl = OpenSSL::SSL::SSLSocket.new(sock)
    ssl.sync_close = true
    ssl.connect
    @socket = Net::InternetMessageIO.new(ssl)
    @socket.read_timeout = 60 #@read_timeout
    do_helo(helodomain)

    authenticate user, secret, authtype if user
    @started = true
  ensure
    unless @started
      # authentication failed, cancel connection.
      @socket.close if not @started and @socket and not @socket.closed?
      @socket = nil
    end
  end

  def do_helo(helodomain)
    begin
      if @esmtp
        ehlo helodomain
      else
        helo helodomain
      end
    rescue Net::ProtocolError
      if @esmtp
        @esmtp = false
        @error_occured = false
        retry
      end
      raise
    end
  end

  def starttls
    getok('STARTTLS')
  end

  def quit
    begin
      getok('QUIT')
    rescue EOFError
    end
  end
end
{% endhighlight %}

reference: http://blog.pomozov.info/posts/how-to-send-actionmailer-mails-to-gmailcom.html
