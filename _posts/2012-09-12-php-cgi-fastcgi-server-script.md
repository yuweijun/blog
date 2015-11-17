---
layout: post
title: "php-cgi启动php内置的fastcgi server"
date: "Wed, 12 Sep 2012 16:21:56 +0800"
categories: linux
---

首先设置下面二个环境变量，保证php能够派生出子进程来负责处理请求，而不是由主进程来做，否则在运行一段时间后nginx就会出现502 Bad Gateway错误，因为php-cgi进程处理的请求数达到最大（默认500）自动退出了。

1. PHP_FCGI_CHILDREN – 派生子进程的数量
2. PHP_FCGI_MAX_REQUESTS – 每个子进程所能处理的最大请求数

设置好上面二个环境变量后启动php-cgi，监听默认的9000端口：

{% highlight bash %}
$> php-cgi -q -b 127.0.0.1:9000 &
{% endhighlight %}

一个简单的脚本
-----

{% highlight bash %}
#! /bin/sh

PHP_FCGI_CHILDREN=4
export PHP_FCGI_CHILDREN
PHP_FCGI_MAX_REQUESTS=5000
export PHP_FCGI_MAX_REQUESTS
nohup /usr/bin/php-cgi -q -b 127.0.0.1:9000 2>&1 > /dev/null &
{% endhighlight %}

另一个启动脚本
-----

{% highlight bash %}
#!/bin/bash

## ABSOLUTE path to the PHP binary
PHPFCGI=`which php-cgi`

## tcp-port to bind on
FCGIPORT="9000"

## IP to bind on
FCGIADDR="127.0.0.1"

## number of PHP children to spawn
PHP_FCGI_CHILDREN=5

## number of request before php-process will be restarted
PHP_FCGI_MAX_REQUESTS=1000

# allowed environment variables sperated by spaces
ALLOWED_ENV="ORACLE_HOME PATH USER"

## if this script is run as root switch to the following user
USERID=www-data

################## no config below this line

if test x$PHP_FCGI_CHILDREN = x; then
  PHP_FCGI_CHILDREN=5
fi

ALLOWED_ENV="$ALLOWED_ENV PHP_FCGI_CHILDREN"
ALLOWED_ENV="$ALLOWED_ENV PHP_FCGI_MAX_REQUESTS"
ALLOWED_ENV="$ALLOWED_ENV FCGI_WEB_SERVER_ADDRS"

if test x$UID = x0; then
  EX="/bin/su -m -c \"$PHPFCGI -q -b $FCGIADDR:$FCGIPORT\" $USERID"
else
  EX="$PHPFCGI -b $FCGIADDR:$FCGIPORT"
fi

echo $EX

# copy the allowed environment variables
E=

for i in $ALLOWED_ENV; do
  E="$E $i=${!i}"
done

# clean environment and set up a new one
nohup env - $E sh -c "$EX" &> /dev/null &
{% endhighlight %}

附nginx关于php-cgi配置
-----

{% highlight text %}
# pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
location ~ .php$ {
    fastcgi_pass   127.0.0.1:9000;
    fastcgi_index  index.php;

    fastcgi_param  SCRIPT_FILENAME  /usr/local/nginx/html$fastcgi_script_name;
    fastcgi_param  QUERY_STRING     $query_string;
    fastcgi_param  REQUEST_METHOD   $request_method;
    fastcgi_param  CONTENT_TYPE     $content_type;
    fastcgi_param  CONTENT_LENGTH   $content_length;
}
{% endhighlight %}

References
-----

1. [Nginx With PHP As FastCGI Howto](http://kovyrin.net/2006/05/30/nginx-php-fastcgi-howto/)
2. [Shell脚本实现启动PHP内置FastCGI Server](http://www.jb51.net/article/63401.htm)
