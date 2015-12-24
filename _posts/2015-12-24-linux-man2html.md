---
layout: post
title: "linux man2html script"
date: "Thu, 24 Dec 2015 13:54:43 +0800"
categories: linux
---

在linux中，可以利用系统提供的`man2html`命令，将linux命令的man手册转成html代码，将下面的源码保存为`man2html.sh`，这个脚本可以将man批量转化成html代码。

{% highlight bash %}
#!/bin/bash

if [ $# -eq 0 ]
then
    echo "Usage: ./man2html.sh /path/to/man/dirctory /path/to/html/output/dirctory"
    echo "Exampe: ./man2html.sh /usr/share/man/man1 /tmp/html1"
fi

dir=/usr/share/man/man1
output=/tmp/html1

if [ $# -eq 1 ]
then
    dir=$1
elif [ $# -eq 2 ]
then
    dir=$1
    output=$2
fi

echo "./man2html.sh ${dir} ${output}"

if [ ! -d $output ]
then
    mkdir -p $output
fi

if [ -d $dir ]
then
    mkdir -p /tmp/unzipped
    cd $dir
    for f in *.gz; do
        if [ -f $f ]
        then
            filename=$(basename "${f}" .gz)
            unzipfile="/tmp/unzipped/${filename}"
            outputfilename="${output}/${filename}.html"

            gunzip -c "${f}" > ${unzipfile}
            man2html ${unzipfile} > ${outputfilename}

            sed -i 's/^Content-type: text\/html$/<!DOCTYPE html>/' ${outputfilename}
            sed -i 's#</HEAD>#<meta charset="utf-8"></head>#' ${outputfilename}
            sed -i 's#<BODY>#<body>\n <header class="site-header">\n <div class="wrap"> <div class="site-title"><a href="/man/index.html">linux man pages</a></div>\n <div class="site-description">{"type":"programming"}</div>\n </div>\n </header>\n <div class="page-content">#' ${outputfilename}
            sed -i 's#</BODY>#</div></body>#' ${outputfilename}
            sed -i 's#http://localhost/cgi-bin/man/man2html#/man/index.html#' ${outputfilename}
            sed -i 's#</head>#\n<link rel="stylesheet" href="/man/css/man.css" type="text/css">\n</head>\n#' ${outputfilename}
        fi
    done
fi

echo "done!"
{% endhighlight %}

### man后面的数字说明

    1. User Commands
    2. System Calls
    3. C Library Functions
    4. Devices and Special Files
    5. File Formats and Conventions
    6. Games et. Al.
    7. Miscellanea
    8. System Administration tools and Deamons

### man使用

{% highlight bash %}
$> man -k '^printf'

$> man 1 printf
$> man 3 printf
$> man -a printf
{% endhighlight %}

References
------

1. [What do the numbers in a man page mean](http://unix.stackexchange.com/questions/3586/what-do-the-numbers-in-a-man-page-mean)
