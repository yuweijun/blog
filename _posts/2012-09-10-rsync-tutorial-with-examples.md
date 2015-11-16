---
layout: post
title: "rsync tutorial with examples"
date: "Mon, 10 Sep 2012 10:25:13 +0800"
categories: linux
---

rsync command options
-----

1. z - compress data (faster transfer)
1. v - verbose
1. r - recurse into directories

upload files
-----

{% highlight bash %}
$> rsync -zvr ./ username@192.168.0.1:/home/username/websites/
{% endhighlight %}

upload files and show the progress
-----

{% highlight bash %}
$> rsync -zvr --progress ./ username@192.168.0.1:/home/username/websites/
{% endhighlight %}

delete files that don't exist in the local copy
-----

{% highlight bash %}
$> rsync -zvr --delete ./ username@192.168.0.1:/home/username/websites/
{% endhighlight %}

upload files but exclude some files and directory
-----

we want to exclude all files starting with a dot `.`, and a directory named `node_modules`.

$> rsync -zvr --exclude=".*/" --exclude node_modules/ ./ username@192.168.0.1:/home/username/websites/

rsync options can be used together
-----

{% highlight bash %}
$> rsync -zvr --delete --progress --exclude=".*/" --exclude node_modules/ ./ username@192.168.0.1:/home/username/websites/
{% endhighlight %}

use a public key (.pem file) with rsync
-----

{% highlight bash %}
$> rsync -zvr -e "ssh -i server-key.pem" ./ username@192.168.0.1:/home/username/websites/
{% endhighlight %}

download files
-----

{% highlight bash %}
$> rsync -zvr --progress --exclude=".*/" username@192.168.0.1:/home/username/websites/ ./
{% endhighlight %}

rsync with progress on Mac OSX
-----

{% highlight bash %}
$> rsync --progress --size-only --delete --exclude=".DS_Store" -r -v -e ssh /Users/username/views/ username@192.168.0.1:/data/views/
{% endhighlight %}

note
-----

If you are using `scp` or `rcp`, it is high time you switched to `rsync` - because `rsync` was written specifically to replace them.

References
-----

1. [15 rsync Command Examples](http://www.thegeekstuff.com/2010/09/rsync-command-examples/)
2. [10 Practical Examples of Rsync Command in Linux](http://www.tecmint.com/rsync-local-remote-file-synchronization-commands/)
3. [RSYNC Tutorial with Examples](http://www.hacksparrow.com/rsync-tutorial-with-examples.html)
