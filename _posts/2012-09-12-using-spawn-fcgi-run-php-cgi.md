---
layout: post
title: "使用spawn-fcgi运行php-cgi"
date: "Wed, 12 Sep 2012 16:27:24 +0800"
categories: linux
---

install
-----

{% highlight bash %}
sudo apt-get install spawn-fcgi
{% endhighlight %}

php启动脚本
-----

一种是使用端口号。

{% highlight bash %}
$> /usr/local/bin/spawn-fcgi -a 127.0.0.1 -p 9000 -u ${user} -g ${group} -f /usr/bin/php5-cgi -C 128
{% endhighlight %}

一种是使用sock文件。

{% highlight bash %}
$> /usr/local/bin/spawn-fcgi -u ${user} -g ${group} -s /tmp/php.sock -f /usr/bin/php5-cgi -P ${pidpath} 1>/dev/null
{% endhighlight %}


附nginx关于php-cgi配置
-----

当spawn-fcgi开启两个端口，利用nginx的upstream负载均衡php程序到不同的fcgi端口上。

{% highlight text %}
upstream  spawn {
    # 负载策略: ip_hash;
    server 127.0.0.1:9000 max_fails=0 fail_timeout=30s;
    server 127.0.0.1:9001 max_fails=0 fail_timeout=30s;
}

location ~ .php$ {
    fastcgi_pass   spawn;
    fastcgi_index  index.php;

    fastcgi_param  SCRIPT_FILENAME  /usr/local/nginx/html$fastcgi_script_name;
    fastcgi_param  QUERY_STRING     $query_string;
    fastcgi_param  REQUEST_METHOD   $request_method;
    fastcgi_param  CONTENT_TYPE     $content_type;
    fastcgi_param  CONTENT_LENGTH   $content_length;
}
{% endhighlight %}

附运行spawn-fcgi脚本二种
-----

{% highlight bash %}
#!/bin/bash
PHP_SCRIPT="/usr/local/bin/spawn-fcgi -a 127.0.0.1 -p 2222 -u www-data -g www-data -f /usr/bin/php5-cgi -C 128"
FASTCGI_USER=www-data
RETVAL=0
case "$1" in
    start)
        su - $FASTCGI_USER -c "$PHP_SCRIPT"
        RETVAL=$?
        ;;
    stop)
        killall -9 php5-cgi
        RETVAL=$?
        ;;
    restart)
        killall -9 php5-cgi
        su - $FASTCGI_USER -c "$PHP_SCRIPT"
        RETVAL=$?
        ;;
    *)
        echo "Usage: php {start|stop|restart}"
        exit 1
        ;;
esac
exit $RETVAL
{% endhighlight %}

{% highlight bash %}
#! /bin/bash
pidpath="/tmp/spawn_php.pid"
user="daemon"
group="daemon"
phpcgi="/usr/local/php/bin/php-cgi"
PHP_FCGI_CHILDREN=50
PHP_FCGI_MAX_REQUESTS=50000

echo_ok ()
{
    echo -ne "/033[33C ["
    echo -ne "/033[32m"
    echo -ne "/033[1C OK"
    echo -ne "/033[39m"
    echo -ne "/033[1C ]/n"
}

start_spawn()
{
    env - PHP_FCGI_CHILDREN=${PHP_FCGI_CHILDREN} PHP_FCGI_MAX_REQUESTS=${PHP_FCGI_MAX_REQUESTS} /usr/local/bin/spawn-fcgi -u ${user} -g ${group} -s /tmp/php.sock -f ${phpcgi} -P ${pidpath} 1>/dev/null
    echo -ne "php-cgi start successfull"
    echo_ok
}

case "$1" in
    start)
        if [ ! -f $pidpath ]
        then
            start_spawn
        else
            pidcount=`ps -ef |grep ${phpcgi}|wc -l`
            if [ "$pidcount" -gt "1" ]
            then
                echo -ne "php  already  running  "
                echo_ok
            else
                rm -f $pidpath
                rm -f /tmp/php.sock
                start_spawn
            fi
        fi
        ;;
    stop)
        pid=`cat ${pidpath} 2>/dev/null`
        kill ${pid} 2>/dev/null
        rm -f ${pidpath} 2>/dev/null
        rm -f /tmp/php.sock 2>/dev/null
        echo -ne "php-cgi  stop successfull"
        echo_ok
        ;;
    restart)
        pid=`cat ${pidpath} 2>/dev/null`
        kill ${pid} 2>/dev/null
        rm -f ${pidpath} 2>/dev/null
        rm -f /tmp/php.sock 2>/dev/null
        echo -ne "php-cgi  stop successfull"
        echo_ok

        if [ ! -f $pidpath ]
        then
            start_spawn
        else
            pidcount=`ps -ef |grep ${phpcgi}|wc -l`
            if [ "$pidcount" -gt "1" ]
            then
                echo -ne "php  already  running  "
                echo_ok
            else
                rm -f $pidpath
                rm -f /tmp/php.sock
                start_spawn
            fi
        fi
        ;;
    *)
        echo "Usage: $0 {start|stop|restart}"
        exit 1
esac
exit
{% endhighlight %}

References
-----

1. [使用spawn-fcgi运行php-cgi](http://blog.csdn.net/xiaomu_fireant/article/details/6343176)
