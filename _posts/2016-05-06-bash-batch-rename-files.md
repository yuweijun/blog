---
layout: post
title: "Batch rename files using bash script"
date: "Wed May 06 2015 15:23:57 GMT+0800 (CST)"
categories: bash
---

这个主要是为了方便批量重名命图片文件而写的，并且只是我简单针对小写文件名为jpg/png的图片做了处理，其他形式需要传后缀名进来，在第二个参数中指定文件扩展名：

{% highlight bash %}
#! /bin/sh

function batchrename {
    if [ -d $1 ]
    then
        cd $1
        i=0
        for file in $(ls)
        do
            if [[ $file =~ .*\.$2 ]]
            then
                target=$i.$2
                if [ $i -lt 10 ]
                then
                    target=00$i.$2
                elif [ $i -lt 100 ]
                then
                    target=0$i.$2
                fi
                if [ $target != $file ]
                then
                    echo mv $1/$file $1/$target
                    mv $file $target
                fi

                (( i=i+1 ))
            fi
        done
    fi
}


if [ $# -eq 0 ]
then
    echo "Usage: batchrename foldername"
    echo "or: batchrename foldername extname"
elif [ $# -eq 2 ]
then
    batchrename $1 $2
else
    if [ -d $1 ]
    then
        echo batchrename images for $1
        batchrename $1 jpg
        batchrename $1 png
    fi
fi

{% endhighlight %}

