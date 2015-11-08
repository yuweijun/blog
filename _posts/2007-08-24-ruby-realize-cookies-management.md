---
layout: post
title: "Ruby realize cookies management"
date: "Thu Aug 23 2007 11:51:00 GMT+0800 (CST)"
categories: ruby
---

用ruby实现HTTP Cookies管理。

{% highlight ruby %}
#!/usr/bin/env ruby
require 'pp'
require 'erb'
require 'uri'
require 'hpricot'
require 'net/http'
require 'net/https'

module CookieMechanism
  def cookies_hashed(set_cookie, path, domain)
    expires = 'Mon, 01-Jan-2010 00:00:00 GMT'
    arr_hashed = Array.new
    if set_cookie
      arr = set_cookie.split(/, /)
      for i in 0 ... arr.size - 1
        if arr[i] =~ /Expires=/ && arr[i + 1] =~ /.*GMT/
          arr[i] << ', ' << arr[i + 1]
          arr[i + 1] = ''
        end
      end
      arr.delete_if {|del| del == ''}
      arr.each do |cookie|
        fields = cookie.split(';')
        hashed = Hash.new
        hashed['Path'] = path
        hashed['Domain'] = domain
        hashed['Secure'] = 0
        fields.each do |f|
          h = f.strip.split('=', 2)
          h[0].capitalize! if h[0].capitalize =~ /Expires|Path|Domain|Secure/i
          h[1] = 1 if h[0] == 'Secure'
          hashed.merge!({h[0] => h[1]})
        end
        if hashed.has_key?('Expires')
          hashed['Expires'] = 0 if Time.httpdate(hashed['Expires']) <= Time.now()
        else
          hashed['Expires'] = expires
        end
        arr_hashed << hashed
      end
    end
    return arr_hashed
  end

  def cookies_process(arr_hashed_new, arr_hashed_old = Array.new)
    arr_hashed_new.each do |new|
      same_key = 0
      new.each do |new_key, new_value|
        if new_key !~ /Expires|Path|Domain|Secure/i
          arr_hashed_old.each do |old|
            old.each do |old_key, old_value|
              if old_key !~ /Expires|Path|Domain|Secure/i && same_key == 0
                if old_key == new_key
                  same_key = 1
                end
              end
              break if same_key == 1
            end
          end
          if same_key == 1
            arr_hashed_old.each do |old|
              old.each do |old_key, old_value|
                if old_key == new_key
                  if new['Path'] == old['Path'] && new['Domain'] == old['Domain']
                    if new['Expires'] != 0
                      old[old_key] = new_value
                      old['Expires'] = new['Expires']
                      old['Secure'] = new['Secure']
                    else
                      arr_hashed_old.delete(old)
                    end
                  else
                    arr_hashed_old << new if new['Expires'] != 0
                  end
                end
              end
            end
          else
            arr_hashed_old << new if new['Expires'] != 0
          end
        end
      end
    end
    return arr_hashed_old
  end

  def cookies_request(arr_hashed_old, path, domain, secure = 0)
    cookie = String.new
    cookies = Array.new
    arr_hashed_old.each do |old|
      reg_str = Regexp.escape(old['Path'])
      reg_path = Regexp.new('^' + reg_str)
      reg_str = Regexp.escape(old['Domain'])
      reg_domain = Regexp.new(reg_str + '$')
      if path =~ reg_path && domain =~ reg_domain
        if secure == 0
          cookies << old if old['Secure'] == 0
        else
          cookies << old
        end
      end
    end
    cookies.sort!{|a, b| a['Path'] <=> b['Path']}.reverse!
    cookies.each do |c|
      c.each do |key, value|
        if key !~ /Expires|Path|Domain|Secure/i
          cookie == '' ? cookie += key + '=' + value : cookie += '; ' + key + '=' + value
        end
      end
    end
    return cookie
  end

  def add_cookie(response, path, domain, arr_hashed_old)
    set_cookie = response['set-cookie']
    # response: HTTPResponse object
    arr_hashed_new = cookies_hashed(set_cookie, path, domain)
    arr_hashed_old = cookies_process(arr_hashed_new, arr_hashed_old)
  end

  def get_http_headers(path, domain, arr_hashed_old)
    return {
     'Cookie' => cookies_request(arr_hashed_old, path, domain, 0),
     'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
     'Connection' => 'Keep-Alive',
     'Accept-Language' => 'zh-cn'
    }
  end

  def post_http_headers(path, domain, arr_hashed_old)
    return {
     'Cookie' => cookies_request(arr_hashed_old, path, domain, 0),
     'Content-Type' => 'application/x-www-form-urlencoded',
     'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
     'Connection' => 'Keep-Alive',
     'Accept-Language' => 'zh-cn'
    }
  end

  def get_https_headers(path, domain, arr_hashed_old)
    return {
     'Cookie' => cookies_request(arr_hashed_old, path, domain, 1),
     'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
     'Connection' => 'Keep-Alive',
     'Accept-Language' => 'zh-cn'
    }
  end

  def post_https_headers(path, domain, arr_hashed_old)
    return {
     'Cookie' => cookies_request(arr_hashed_old, path, domain, 1),
     'Content-Type' => 'application/x-www-form-urlencoded',
     'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
     'Connection' => 'Keep-Alive',
     'Accept-Language' => 'zh-cn'
    }
  end
end
{% endhighlight %}
