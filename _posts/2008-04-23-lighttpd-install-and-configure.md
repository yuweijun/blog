---
layout: post
title: "lighttpd install and configure"
date: "Wed Apr 23 2008 22:12:00 GMT+0800 (CST)"
categories: linux
---

# install libevent

{% highlight bash %}
$> curl -O http://www.monkey.org/~provos/libevent-1.4.3-stable.tar.gz
$> tar xzvf libevent-1.4.3-stable.tar.gz
$> cd libevent-1.4.3-stable
$> ./configure --prefix=/usr/local/libevent143
$> make
$> sudo make install
{% endhighlight %}

# install memcached

{% highlight bash %}
$> curl -O http://www.danga.com/memcached/dist/memcached-1.2.5.tar.gz
$> tar xzvf memcached-1.2.5.tar.gz
$> cd memcached-1.2.5
$> ./configure --prefix=/usr/local/memcached125 --with-libevent=/usr/local/libevent143
$> make
$> sudo make install
{% endhighlight %}

#install pcre in order to install lighttpd

{% highlight bash %}
$> curl -O ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/pcre-6.6.tar.gz
$> tar zxvf pcre-6.6.tar.gz
$> cd pcre-6.6
$> ./configure --prefix=/usr/local/pcre66
$> make
$> sudo make install
{% endhighlight %}

modify system variable PATH including `/usr/local/pcre66/bin`, and add below command to `.bashrc` or `.profile` or `.bash_profile` etc.

{% highlight bash %}
$> export PATH="/usr/local/pcre66/bin:$PATH"
{% endhighlight %}

# install lighttpd

{% highlight bash %}
$> curl -O http://www.lighttpd.net/download/lighttpd-1.4.19.tar.gz
$> tar zxvf lighttpd-1.4.19.tar.gz
$> cd lighttpd-1.4.19
$> ./configure --prefix=/usr/local/lighttpd1419 --with-zlib --with-pcre --with-openssl
$> make
$> sudo make install
{% endhighlight %}

# lighttpd.conf example

{% highlight bash %}
server.modules = (
    "mod_rewrite",
    "mod_redirect",
    "mod_fastcgi",
    "mod_proxy",
    "mod_userdir",
    "mod_cgi",
    "mod_usertrack",
    "mod_accesslog"
)

server.name = "localhost"
server.document-root = "/Users/test/Sites/Public"
server.errorlog = "/Users/test/Sites/lighttpd/logs/lighttpd.error.log"
accesslog.filename = "/Users/test/Sites/lighttpd/logs/access.log"

server.port = 80

server.username = "test"
server.groupname = "admin"

mimetype.assign = (
    ".html" => "text/html",
    ".txt" => "text/plain",
    ".jpg" => "image/jpeg",
    ".png" => "image/png"
)

static-file.exclude-extensions = ( ".fcgi", ".php", ".rb", "~", ".inc" )
index-file.names = ( "index.html" )

$HTTP["host"] == "www.test.com" {
    server.document-root = "/Users/test/Sites/CakePHP/"
}

### for PHP don't forget to set cgi.fix_pathinfo = 1 in the php.ini
fastcgi.server = ( ".php" =>
   ( "localhost" =>
     (
       "socket" => "/tmp/php-fastcgi.socket",
       "bin-path" => "/usr/local/php5/bin/php-cgi"
     )
   )
)

### CGI module
cgi.assign = ( ".pl"  => "/usr/bin/perl",
    ".cgi" => "/usr/bin/perl",
    ".py" => "/usr/bin/python",
    ".rb" => "/usr/local/ruby186/bin/ruby"
)
{% endhighlight %}

# lighttpd server start

{% highlight bash %}
$> /usr/local/lighttpd1419/sbin/lighttpd -f lighttpd.conf
{% endhighlight %}
