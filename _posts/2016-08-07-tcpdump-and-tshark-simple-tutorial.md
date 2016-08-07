---
layout: post
title: "tcpdump and tshark simple tutorial"
date: Sun, 07 Aug 2016 13:48:05 +0800
categories: linux
---

`tcpdump`和`tshark`是网络抓包的工具，解包可以使用`wireshark`来查看抓包结果，以下命令需要管理员权限。

tcpdump命令使用举例
-----

以下命令中部分参数说明：

1. `-D`: 显示网络接口列表。
2. `-i`: 设置抓包的网络接口，不设置则默认为第一个非自环接口。
3. `-w`: 写入文件，再使用`wireshark`打开文件，进行解包分析。
4. `-r`: 读取之前的使用`-w`写入的文件。
5. `-c`: 抓取的packet数，在处理一定数量的packet后，停止抓取并退出程序。
6. `-n`: 禁止所有地址名字解析。
7. `-s`: 设置每个抓包的大小，默认为65535，多于这个大小的数据将不会被程序记入内存、写入文件。
8. `-nn`: 第1个`n`表示以IP地址的方式显示主机名，第2个`n`是以端口数字的形式代替服务名。
9. `-XX`: 使用`HEX`和`ASCII`显示封包的内容。
10. `-vv`: 比较详细的输出封包信息。

{% highlight bash %}
$> tcpdump -D
$> tcpdump -nS
$> tcpdump -nnvvS
$> tcpdump -nnvvXS
$> tcpdump host 1.2.3.4
$> tcpdump src 2.3.4.5
$> tcpdump dst 3.4.5.6
$> tcpdump net 1.2.3.0/24
$> tcpdump port 3306
$> tcpdump src port 1025
$> tcpdump dst port 389
$> tcpdump src port 1025 and tcp
$> tcpdump udp and src port 53
$> tcpdump -nvX src net 192.168.0.0/16 and dst net 10.0.0.0/8 or 172.16.0.0/16
$> tcpdump -nvvXSs 1514 dst 192.168.31.231 and src net and not icmp
$> tcpdump -i eth0 dst 192.168.31.231 and port 80
$> tcpdump -XX -i eth0
$> tcpdump -nn -vv -i eth0 tcp src or dst 192.168.31.231 and port 80
$> tcpdump -nn -i eth0 tcp and host 192.168.1.163 and port 80
$> tcpdump -nn -vv -i eth0 src and dst 192.168.31.231 and port 80
$> tcpdump -s 1514 port 80 -w file.pcap
$> tcpdump -nn -i eth0 -w ~/packets.dump tcp and dst 192.168.31.231 and port 22
$> tcpdump 'src 10.0.2.4 and (dst port 3389 or 22)'
{% endhighlight %}

linux下使用`tcpdump`监控`MySQL`的网络请求：

{% highlight bash %}
$> tcpdump -i eth0 -s 0 -l -w - dst port 3306 | strings | perl -e '
while(<>) { chomp; next if /^[^ ]+[ ]*$/;
    if(/^(SELECT|UPDATE|DELETE|INSERT|SET|COMMIT|ROLLBACK|CREATE|DROP|ALTER)/i) {
        if (defined $q) { print "$q\n"; }
        $q=$_;
    } else {
        $_ =~ s/^[ \t]+//; $q.=" $_";
    }
}'
{% endhighlight %}

tcpflow监控MySQL的网络请求
-----

{% highlight bash %}
$> brew install tcpflow
$> tcpflow -c -p -i lo0 dst port 3306 | grep -i -E "select|insert|update|delete|replace"

# below command is not work on Mac OS
$> tcpflow -c -p -i any dst port 3306 | grep -i -E "select|insert|update|delete|replace" | sed 's/\(.*\)\([.]\{4\}\)\(.*\)/\3/'
{% endhighlight %}

tshark命令使用举例
-----

`tshark`的参数很多与`tcpdump`一样，其他部分参数说明：

1. `-f`: 设定抓包过滤表达式`capture filter expression`。
2. `-T`: fields\|pdml\|ps\|psml\|text，设置解码结果输出的格式，默认为text，设置为fields时，必须使用`-e field`指定输出字段，如`-e frame.number -e ip.addr -e udp`。
3. `-Y`: 设置显示的过滤表达式。
4. `-F`: 设置输出raw数据的格式，默认为libpcap。
5. `-x`: 设置在解码输出结果中，每个packet后面以HEX dump的方式显示具体数据。

{% highlight bash %}
$> tshark -v
$> tshark -D
$> tshark -i eth0 -d tcp.port==3306,mysql -Y 'mysql.query' -T fields -e mysql.query
$> tshark -i eth0 -n -d tcp.port==3306,mysql -Y 'mysql.query' -T fields -e mysql.query 'port 3306'
$> tshark -r tcpdump.out -d tcp.port==3306,mysql -T fields -e mysql.query > query_log.out
$> tshark -r login.tcpdump -T fields -e frame.number -e frame.time_relative -e ip.src -e ip.dst -e frame.protocols -e frame.len -E header=y -E quote=n -E occurrence=f
$> tshark -r ~/net.pcap -T fields -e ip.src | sort | sed '/^\s*$/d' | uniq -c | sort -rn | awk {'print $2 " " $1'} | head
$> tshark -nr ~/var/http.pcap -qz "io,phs"
$> tshark -s 512 -i eth0 -n -f 'tcp dst port 80' -R 'http.host and http.request.uri' -T fields -e http.host -e http.request.uri -l | tr -d '\t'
$> tshark 'tcp port 80 and (((ip[2:2] - ((ip[0]&0xf)<<2)) - ((tcp[12]&0xf0)>>2)) != 0)' -R 'http.request.method == "GET" || http.request.method == "HEAD"'
$> tshark -i eth0 -aduration:60 -d tcp.port==3306,mysql -T fields -e mysql.query 'port 3306'
$> tshark -i wlan0 -w capture-output.pcap
$> tshark -r capture-output.pcap
$> tshark -i wlan0 -Y http.request -T fields -e http.host -e http.user_agent
$> tshark -i wlan0 -f "src port 53" -n -T fields -e dns.qry.name -e dns.resp.addr
$> tshark -i wlan0 -f "src port 53" -n -T fields -e frame.time -e ip.src -e ip.dst -e dns.qry.name -e dns.resp.addr
$> tshark -i wlan0 -Y 'http.request.method == POST and tcp contains "password"' | grep password
{% endhighlight %}

监控本地`MySQL`服务器的网络请求中的SQL，客户端`mysql`命令需要指定`--host=127.0.0.1`或者`-h 127.0.0.1`，而不能指定`-h localhost`，使用`localhost`时，`mysql`客户端会使用`UNIX socket`连接本地的服务器，而没有通过网络设备`lo0`，因此不能监控到网络数据包，更详细的信息可查看[官方文档](http://dev.mysql.com/doc/refman/5.6/en/connecting.html)。

启动`tshark`监听`3306`端口：

{% highlight bash %}
$> sudo tshark -s 512 -i lo0 -n -f 'tcp dst port 3306' -Y 'mysql.query' -T fields -e mysql.query
{% endhighlight %}

在另外一个命令行中运行以下命令：

{% highlight bash %}
$> mysql -uroot -p test -h 127.0.0.1
{% endhighlight %}

在命令行中执行SQL语句如下：

{% highlight sql %}
select * from user;
select * from user where id = 1;
{% endhighlight %}

前面开启`tshark`监控的窗口中输出如下：

> Capturing on 'Loopback'
>
> show databases
>
> show tables
>
> select @@version_comment limit 1
>
> select * from user
>
> select * from user where id = 1

References
-----

1. [tshark man pages](https://www.wireshark.org/docs/man-pages/tshark.html)
2. [a tcpdump tutorial and primer with examples](https://danielmiessler.com/study/tcpdump/)
3. [tshark tutorial and filter examples](https://hackertarget.com/tshark-tutorial-and-filter-examples/)
4. [wireshark filter](https://www.wireshark.org/docs/man-pages/wireshark-filter.html)
5. [tcpdump commands – a network sniffer tool](http://www.tecmint.com/12-tcpdump-commands-a-network-sniffer-tool/)

