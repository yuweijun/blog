---
layout: post
title: "rsync install and configure"
date: "Thu Sep 18 2008 23:27:00 GMT+0800 (CST)"
categories: linux
---

rsync server configure
-----

{% highlight bash %}
motd file = /etc/rsyncd.motd
log file = /var/log/rsyncd.log
pid file = /var/run/rsyncd.pid
lock file = /var/run/rsync.lock

[MODULENAME]
   path = /data/backfrom
   comment = back data using rsync server
   uid = nobody
   gid = nobody
   read only = no
   list = yes
   auth users = test
   secrets file = /etc/rsyncd.scrt
{% endhighlight %}

{% highlight bash %}
$> rsync --daemon --port=873

$> vi /etc/rsyncd.scrt
test:test@gmail.com
$> chmod 400 /etc/rsyncd.scrt
{% endhighlight %}

client
-----

{% highlight bash %}
$> vi /home/users/passwordfile
test@gmail.com
$> chmod 400 /etc/rsyncd.scrt

$> rsync -vzrtopg --progress --delete test@192.168.0.1::MODULENAME /data/backto --password-file=/home/users/passwordfile
{% endhighlight %}

or connect rsync server using ssh
-----

{% highlight bash %}
#!/bin/sh
DEST="192.168.0.1"
USER="test"
BACKDIR="/data/backfrom/"
DESTDIR="/data/backto/"
OPTS="-vzrtopg --rsh=ssh --stats --progress"
VAR=`ping -s 1 -c 1 $DEST > /dev/null; echo $?`
if [ $VAR -eq 0 ]; then
    rsync $OPTS $BACKDIR $USER@$DEST:$DESTDIR
else
    echo "Cannot connect to $DEST."
fi
{% endhighlight %}
