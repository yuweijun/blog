---
layout: post
title: "在centos5中开启apache2的mod_ssl模块"
date: "Mon May 24 2010 10:51:00 GMT+0800 (CST)"
categories: linux
---

在配置完`/etc/httpd/conf.d/ssl.conf`文件之后，进行配置文件语法正确性测试时，报语法错误如下：

{% highlight bash %}
$> httpd -t
Syntax error on line 5 of /etc/httpd/conf.d/ssl.conf:
Invalid command 'SSLPassPhraseDialog', perhaps misspelled or defined by a module not included in the server configuration
{% endhighlight %}

移除`ssl.conf`文件之后，语法检查正确，列出`httpd`的模块检查：

{% highlight bash %}
$> httpd -M
Loaded Modules:
core_module (static)
mpm_prefork_module (static)
http_module (static)
so_module (static)
auth_basic_module (shared)
auth_digest_module (shared)
authn_file_module (shared)
authn_alias_module (shared)
authn_anon_module (shared)
authn_dbm_module (shared)
authn_default_module (shared)
authz_host_module (shared)
authz_user_module (shared)
authz_owner_module (shared)
authz_groupfile_module (shared)
authz_dbm_module (shared)
authz_default_module (shared)
ldap_module (shared)
authnz_ldap_module (shared)
include_module (shared)
log_config_module (shared)
logio_module (shared)
env_module (shared)
ext_filter_module (shared)
mime_magic_module (shared)
expires_module (shared)
deflate_module (shared)
headers_module (shared)
usertrack_module (shared)
setenvif_module (shared)
mime_module (shared)
dav_module (shared)
status_module (shared)
autoindex_module (shared)
info_module (shared)
dav_fs_module (shared)
vhost_alias_module (shared)
negotiation_module (shared)
dir_module (shared)
actions_module (shared)
speling_module (shared)
userdir_module (shared)
alias_module (shared)
rewrite_module (shared)
proxy_module (shared)
proxy_balancer_module (shared)
proxy_ftp_module (shared)
proxy_http_module (shared)
proxy_connect_module (shared)
cache_module (shared)
suexec_module (shared)
disk_cache_module (shared)
file_cache_module (shared)
mem_cache_module (shared)
cgi_module (shared)
version_module (shared)
proxy_ajp_module (shared)
Syntax OK
{% endhighlight %}

发现apache2没有加载`ssl_module`，并且`/etc/httpd/modules`目录中无`mod_ssl.so`文件，需要在线安装：

{% highlight bash %}
$> yum search mod_ssl
mod_ssl.x86_64 : SSL/TLS module for the Apache HTTP server
{% endhighlight %}

{% highlight bash %}
$> yum install mod_ssl
Installed: mod_ssl.x86_64 1:2.2.3-43.el5.centos
Dependency Installed: distcache.x86_64 0:1.4.5-14.1
Updated: httpd.x86_64 0:2.2.3-43.el5.centos
Complete!
{% endhighlight %}

安装`mod_ssl`会增加一个用户类型，所以会修改`/etc/passwd`文件。

安装完成之后，修改`ssl.conf`文件，在文件顶部添加以下一行代码，加载`ssl_module`:

{% highlight html %}
LoadModule ssl_module modules/mod_ssl.so
Listen 443
AddType application/x-x509-ca-cert .crt
AddType application/x-pkcs7-crl    .crl
SSLPassPhraseDialog  builtin
SSLSessionCache         shmcb:/var/cache/mod_ssl/scache(512000)
SSLSessionCacheTimeout  300
SSLMutex  default

<VirtualHost _default_:443>
    DocumentRoot "/path/to/wwwroot"
    ServerName www.test.com:443
    ServerAdmin test@gmail.com

    SSLEngine on
    SSLCipherSuite ALL:!ADH:!EXPORT:!SSLv2:RC4+RSA:+HIGH:+MEDIUM:+LOW
    SSLCertificateFile /usr/local/apache2/conf/ssl.crt/server.crt
    SSLCertificateKeyFile /usr/local/apache2/conf/ssl.key/server.key
    SSLCACertificateFile /usr/local/apache2/conf/ssl.crt/cacertificate.crt

    <FilesMatch "\.(cgi|shtml|phtml|php)$">
        SSLOptions +StdEnvVars
    </FilesMatch>

    <Directory "/usr/local/apache2/cgi-bin">
        SSLOptions +StdEnvVars
    </Directory>

    SetEnvIf User-Agent ".*MSIE.*" \
    nokeepalive ssl-unclean-shutdown \
    downgrade-1.0 force-response-1.0
</VirtualHost>
{% endhighlight %}

再测试httpd配置文件：

{% highlight bash %}
$> httpd -t
Syntax OK
{% endhighlight %}
