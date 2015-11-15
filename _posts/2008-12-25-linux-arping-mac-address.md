---
layout: post
title: "使用arping查询得到局域网里其他机器的mac地址"
date: "Thu Dec 25 2008 15:28:00 GMT+0800 (CST)"
categories: linux
---

arping usage
-----

{% highlight bash %}
#!/bin/bash
# 使用arping查询得到mac地址

for i in `seq 254` ; do
    sudo /sbin/arping -c2 192.168.1.$i | awk '/Unicast reply from/{print $4,$5}' | sed 's/\[//' | sed 's/\]//'
done

#!/bin/bash
for ((i = 1; i < 254; i++))
do
    sudo /sbin/arping -i eth0 192.168.1.$i -c 1
done

/sbin/arp -a > mac_table
{% endhighlight %}
