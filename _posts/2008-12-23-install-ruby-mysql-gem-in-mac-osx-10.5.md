---
layout: post
title: "install ruby-mysql gem in mac osx 10.5"
date: "Tue Dec 23 2008 15:21:00 GMT+0800 (CST)"
categories: ruby
---

首先用mysql的dmg包安装mysql，接下来安装`mysql-ruby`的gem包：

{% highlight bash %}
$> sudo gem install mysql -- --with-mysql-dir=/usr/local/mysql --with-mysql-lib=/usr/local/mysql/lib --with-mysql-include=/usr/local/mysql/include --with-mysql-config=/usr/local/mysql/bin/mysql_config
Building native extensions. This could take a while...
Successfully installed mysql-2.7
1 gem installed

$> sudo gem install dbd-mysql -- --with-mysql-dir=/usr/local/mysql --with-mysql-lib=/usr/local/mysql/lib --with-mysql-include=/usr/local/mysql/include --with-mysql-config=/usr/local/mysql/bin/mysql_config
Successfully installed dbd-mysql-0.4.2
1 gem installed
Installing ri documentation for dbd-mysql-0.4.2...
Installing RDoc documentation for dbd-mysql-0.4.2...
{% endhighlight %}

看上去是装成功了，但实际使用时却抛出了以下错误：

{% highlight bash %}
dyld: lazy symbol binding failed: Symbol not found: _mysql_init
Referenced from: /Library/Ruby/Gems/1.8/gems/mysql-2.7/lib/mysql.bundle
Expected in: dynamic lookup
{% endhighlight %}

这个主要是因为平台原因造成的，分别查看dmg包和二进制包的mysql_config可看到-arch的差异：

{% highlight bash %}
$> /usr/local/mysql/bin/mysql_config
Usage: /usr/local/mysql/bin/mysql_config [OPTIONS]
Options:
--cflags [-I/usr/local/mysql/include -Os -arch ppc -fno-common -D_P1003_1B_VISIBLE -DSIGNAL_WITH_VIO_CLOSE -DSIGNALS_DONT_BREAK_READ -DIGNORE_SIGHUP_SIGQUIT -DDONT_DECLARE_CXA_PURE_VIRTUAL]
--include [-I/usr/local/mysql/include]
--libs [-L/usr/local/mysql/lib -lmysqlclient -lz -lm]
--libs_r [-L/usr/local/mysql/lib -lmysqlclient_r -lz -lm]
--socket [/tmp/mysql.sock]
--port [0]
--version [5.1.23-rc]
--libmysqld-libs [-L/usr/local/mysql/lib -lmysqld -lz -lm]

$> /usr/local/mysql5/bin/mysql_config
Usage: /usr/local/mysql5/bin/mysql_config [OPTIONS]
Options:
--cflags [-I/usr/local/mysql5/include -g -Os -arch i386 -fno-common -D_P1003_1B_VISIBLE -DSIGNAL_WITH_VIO_CLOSE -DSIGNALS_DONT_BREAK_READ -DIGNORE_SIGHUP_SIGQUIT]
--include [-I/usr/local/mysql5/include]
--libs [-L/usr/local/mysql5/lib -lmysqlclient -lz -lm -lmygcc]
--libs_r [-L/usr/local/mysql5/lib -lmysqlclient_r -lz -lm -lmygcc]
--socket [/tmp/mysql.sock]
--port [0]
--version [5.0.67]
--libmysqld-libs [-L/usr/local/mysql5/lib -lmysqld -lz -lm -lmygcc]
{% endhighlight %}

而mac osx 10.5的macbook则是i386的：

{% highlight bash %}
$> uname -a
Darwin Macintosh.local 9.5.0 Darwin Kernel Version 9.5.0: Wed Sep 3 11:29:43 PDT 2008; root:xnu-1228.7.58~1/RELEASE_I386 i386
{% endhighlight %}

所以用i386平台编译的mysql5来编译安装mysql-ruby包：

{% highlight bash %}
$> sudo env ARCHFLAGS="-arch i386" gem install mysql -- --with-mysql-config=/usr/local/mysql5/bin/mysql_config
Building native extensions. This could take a while...
Successfully installed mysql-2.7
1 gem installed

$> sudo env ARCHFLAGS="-arch i386" gem install dbd-mysql -- --with-mysql-config=/usr/local/mysql5/bin/mysql_config
Successfully installed dbd-mysql-0.4.2
1 gem installed
Installing ri documentation for dbd-mysql-0.4.2...
Installing RDoc documentation for dbd-mysql-0.4.2...
{% endhighlight %}

这样才能正确安装上ruby-mysql和dbd-mysql。
