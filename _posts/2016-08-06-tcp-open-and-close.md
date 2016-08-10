---
layout: post
title: "tcp connection open and close"
date: Sat, 06 Aug 2016 23:36:55 +0800
categories: linux
---

wireshark install and usage
-----

下面示例中会用到`wireshark`抓包工具，安装`wireshark`并且通过一个http访问来查看TCP连接打开关闭请求的过程。

{% highlight bash %}
$> brew install wireshark --with-qt
$> sudo wireshark-qt
{% endhighlight %}

`wireshark`过滤规则简单示例，更多使用说明可查阅[参考链接](https://www.wireshark.org/docs/man-pages/wireshark-filter.html)：

{% highlight bash %}
host 10.1.2.3
ip src host 10.1.1.1
ip.addr == 10.1.1.1
ip.addr eq 10.1.1.1
ip.src != 10.1.2.3 and ip.dst != 10.4.5.6
src host 10.7.2.12 and not dst net 10.200.0.0/16
tcp.port == 25
tcp.dstport == 25
tcp dst port 3128
http.request.method == "POST"
http.host == "www.google.com"
{% endhighlight %}

在`wireshark`中打开有数据流量的那个网络设备，并打开新的`Terminal`输入以下命令，发起一个http请求：

{% highlight bash %}
$> curl -o index.html http://www.baidu.com
{% endhighlight %}

请求完成将`wireshark`监控暂停，并保存结果到文件，方便重新分析使用。

TCP连接建立
-----

TCP用三次握手`three-way handshake`过程建立一个连接，三次握手过程示意图如下：

![tcp-states-connect]({{ site.baseurl }}/img/linux/tcp/tcp-states-connect.jpg)

TCP连接建立一般是由服务器端打开一个套接字`socket`，然后监听来自客户端的连接，所以服务器端表示为被动打开`passive open`，客户端表示为主动打开`active open`，建立连接过程：

1. SYN: 客户端通过向服务器端发送一个`SYN`来建立一个主动打开，作为三次握手的一部分。客户端把这段连接的初始序号设定为随机数`A`。
2. SYN-ACK: 服务器端应当为一个合法的`SYN`回送一个`SYN/ACK`，`ACK`确认号在接收的序号上加`1`，也就是`A+1`，`SYN/ACK`包本身又有一个随机序号`B`。
3. ACK: 最后，客户端再发送一个`ACK`，当服务端受到这个`ACK`的时候，就完成了三次握手，并进入了连接建立状态，此时包序号被设定为收到的确认号`A+1`，而响应则为`B+1`。

wireshark抓包截图如下：

![tcp-open-in-wireshark]({{ site.baseurl }}/img/linux/tcp/tcp-open-in-wireshark.png)

如上图所示，前面3条记录是TCP建立连接的3次握手，图中的显示的初始序号为`0`，是为了方便查看而显示的序号相对值。

TCP连接关闭
-----

TCP连接关闭使用了四次挥手过程`four-way handshake`，四次挥手过程示意图如下：

![tcp-states-terminate]({{ site.baseurl }}/img/linux/tcp/tcp-states-terminate.jpg)

1. 主动关闭方发送一个`FIN`并进入`FIN_WAIT_1`状态，并包括一个序号`X`；
2. 被动关闭方接收到主动关闭方发送的`FIN`，然后回复`ACK`确认号`X+1`和已方的序号`Y`给主动关闭方，此时被动关闭方进入`CLOSE_WAIT`状态；主动关闭方收到被动关闭方的`ACK`后，进入`FIN_WAIT_2`状态；
3. 被动关闭方发送一个`FIN`给主动关闭方，包括`ACK`确认号`X+1`和已方的一个序号`Y`，并进入`LAST_ACK`状态；
4. 主动关闭方收到被动关闭方发送的`FIN`，然后回复`ACK`确认号`Y+1`和已方的序号`X+1`给被动关闭方，此时主动关闭方进入`TIME_WAIT`状态，经过`2*MSL`时间后关闭连接；被动关闭方收到主动关闭方的`ACK`后，关闭连接。

wireshark抓包截图如下：

![tcp-close-in-wireshark]({{ site.baseurl }}/img/linux/tcp/tcp-close-in-wireshark.png)

如上图所示，最后面4条是关闭连接的4次挥手，红字那条的`Ack`确认号不正确，所以忽略掉，最后本机又向服务器重发了一条正确的`Ack`确认号。

数据传输
-----

在TCP的数据传输过程中，很多重要的机制保证了TCP的可靠性和强壮性，它们包括：

1. 使用序号，对收到的TCP报文段进行排序以及检测重复的数据。
2. 使用校验和来检测报文段的错误。
3. 使用确认号和重传计时器来检测和纠正丢包或延时。

序号Seq和确认号Ack
-----

1. 在TCP的连接建立状态，两个主机的TCP层间要交换初始序号ISN: `initial sequence number`。
2. 这些序号用于标识字节流中的数据，并且还是对应用层的数据字节进行记数的整数。
3. 通常在每个TCP报文段中都有一对序号和确认号。
4. TCP报文发送者将自己的字节编号做为序号，而将接收者的字节编号做为确认号。
5. TCP报文的接收者为了确保可靠性，在接收到一定数量的连续字节流后才发送确认。
6. `Seq`和`Ack`是以字节数为单位，所以`Ack`的时候，不能跳着确认，只能确认连续收到的`Seq`最大的包。
7. 这是对TCP的一种扩展，通常称为选择确认`Selective Acknowledgement`。
8. 选择确认使得TCP接收者可以对乱序到达的数据块进行确认，每一组字节传输过后，`ISN`号都会递增`1`。

通过使用序号和确认号，TCP层可以把收到的报文段中的字节按正确的顺序交付给应用层。序号是32位的无符号数，在它增大到`2^32-1`时，便会回绕到0。对于ISN的选择是TCP中关键的一个操作，它可以确保强壮性和安全性。

序号和确认号及TCP报文中数据长度的关系如下：

{% highlight txt %}
Sequence Number In + Bytes of Data Received = Acknowledgment Number Out
{% endhighlight %}

当前发送的报文序号和下一个报文序号之间的关系：

{% highlight txt %}
Sequence Number Out + Bytes of Data Sent = Next Sequence Number Out
{% endhighlight %}

举例客户端发出的TCP报文的序号`Sequence Number`为1，报文总长度`Total Length`为`1480bytes`，减去IP头长度`20bytes`，再减去TCP头长度`20bytes`，实际数据的长度为`1440bytes`，服务器正确收到报文后应该返回一个数据长度为0的`ACK`报文，其中`Acknowledgment Number`应该为`1441`，并且客户端下一个TCP报文的序号也是`1441`，计算公式如下:

> 1 + 1440 = 1441

TCP数据传输过程举例
-----

![tcp-seq-ack-number]({{ site.baseurl }}/img/linux/tcp/tcp-seq-ack-number.png)

1. 上图所示的最上面三条报文说明了TCP连接建立的3次握手过程。
2. 上图所示的第32条记录，服务器发送第1个包含序号为1(相对值)和1440字节数据的TCP报文段给客户端。
3. 上图所示的第33条记录，服务器发送第2个包含序号为1441(1+1440)和1440字节数据的TCP报文段给客户端。
4. 上图所示的第34条记录，服务器发送第3个包含序号为2881(1441+1440)和1440字节数据的TCP报文段给客户端。
5. 上图所示的第35条记录，服务器发送第4个包含序号为4321(2881+1440)和930字节数据的TCP报文段给客户端。
6. 上图所示的第36条记录，客户端返回第1个包含序号为87，确认号为2881(1441+1440)和0字节数据的TCP报文段给服务器，通知服务器已经正确收到服务器发过来的前2个报文段。说明：数据包都是连续的情况下，接收方没有必要每一次都回应，比如，他收到第1到2条TCP报文段，只需回应第2条就行了。
7. 上图所示的第37条记录，客户端返回第2个包含序号为87，确认号为4321(2881+1440)和0字节数据的TCP报文段给服务器，通知服务器已经正确收到服务器发过来的前3个报文段。
8. 上图所示的第38条记录，客户端返回第3个包含序号为87，确认号为5251(4321+930)和0字节数据的TCP报文段给服务器，通知服务器已经正确收到服务器发过来的全部4个报文段。
9. 第39条记录中客户端发起TCP连接关闭`FIN`请求，第40条服务器返回一个`FIN`请求，在`FIN`消息前缺少一条服务器给客户端的`ACK`报文，最后客户端回复服务器一个`ACK`报文，TCP连接关闭。
10. 假如在上述例子中服务器发出的第2条TCP报文段被丢失了，所以尽管客户端收到了后面的第3条和第4条，然而他只能回应第1条`ACK`报文，确认号为`1441`。
11. 服务器在发送了第2条以后，没能收到回应，会在重传计时器超时后，重发第2条。当第2条被成功接收，接收方可以直接确认第4条，因为第3条和第4条已收到。

决定报文是否有必要重传的主要机制是重传计时器`Retransmission Timer`，它的主要功能是维护重传超时`RTO`值。当报文使用TCP传输时，重传计时器启动，收到`ACK`时计时器停止。

报文发送至接收到`ACK`的时间称为往返时间`Round Trip Time - RTT`。

对若干次时间取平均值，该值用于确定最终`RTO`值。在最终`RTO`值确定之前，确定每一次报文传输是否有丢包发生。当报文发送之后，但接收方尚未发送`ACK`报文，发送方假设源报文丢失并将其重传。重传之后，`RTO`值加倍；如果在2倍`RTO`值到达之前还是没有收到`ACK`报文，就再次重传。如果仍然没有收到`ACK`，那么`RTO`值再次加倍。如此持续下去，每次重传`RTO`都翻倍，直到收到`ACK`报文或发送方达到配置的最大重传次数。

最大重传次数取决于发送操作系统的配置值，默认情况下，Windows主机默认重传`5`次，大多数Linux系统默认最大`15`次，两种操作系统都可配置。

TCP状态机
-----

下表为`TCP`状态码列表，以`S`指代服务器，`C`指代客户端，`S&C`表示两者，`S/C`表示两者之一：

1. LISTEN `S`: 服务启动后首先处于侦听LISTENING状态。
2. SYN_SENT `C`: 在发送连接请求后等待匹配的连接请求。通过connect()函数向服务器发出一个同步`SYNC`信号后进入此状态。
3. SYN_RECEIVED `S`: 已经收到并发送同步`SYNC`信号之后等待确认`ACK`请求。
4. ESTABLISHED `S&C`: 连接已经建立，表示2台机器可以相互通信，此时连接两端是平等的。
5. FIN_WAIT_1 `S&C`: 主动关闭端调用`close()`函数发出FIN请求包，表示本方的数据发送全部结束，等待TCP连接另一端的确认包或FIN请求包。
6. FIN_WAIT_2 `S&C`: 主动关闭端在`FIN_WAIT_1`状态下收到确认包，进入等待远程TCP的连接终止请求的半关闭状态，这时可以接收数据，但不再发送数据。
7. CLOSE_WAIT `S&C`: 被动关闭端接到`FIN`后，就发出`ACK`以回应`FIN`请求，并进入等待本地用户的连接终止请求的半关闭状态，这时可以发送数据，但不再接收数据。
8. CLOSING `S&C`: 在发出`FIN`后，又收到对方发来的`FIN`后，进入等待对方对连接终止`FIN`的确认`ACK`的状态，少见。
9. LAST_ACK `S&C`: 被动关闭端全部数据发送完成之后，向主动关闭端发送`FIN`，进入等待确认包的状态。
10. TIME_WAIT `S/C`: 主动关闭端接收到`FIN`后，就发送`ACK`包，等待足够时间(2倍`MSL`时间)以确保被动关闭端收到了终止请求的确认包。
11. CLOSED `S&C`: 连接关闭，代表双方无任何连接状态。

![tcp-state-diagram]({{ site.baseurl }}/img/linux/tcp/tcp-state-diagram.png)

TIME_WAIT and MSL
-----

1. `MSL`就是最大分节生命期`maximum segment lifetime`，这是一个IP数据包能在互联网上生存的最长时间，超过这个时间IP数据包将在网络中消失。
2. `MSL`在RFC 1122上建议是`2分钟`，而源自berkeley的TCP实现传统上使用`30秒`。
3. `TIME_WAIT`状态维持时间是两个`MSL`时间长度，也就是在`1-4分钟`，`Windows`操作系统就是4分钟。
4. 进入`TIME_WAIT`状态的一般情况下是客户端，如果并发20个线程，每个线程打开并关闭`socket`超过3000次，就会产生太多`TIME_WAIT`状态的连接，并产生连接异常。
5. 服务器端更多应该是`CLOSE_WAIT`状态。

统计TCP连接状态
----

{% highlight bash %}
$> netstat -n | awk '/^tcp/ {++S[$NF]} END {for(a in S) print a, S[a]}'
{% endhighlight %}

上述命令输出如下：

> TIME_WAIT 25
>
> ESTABLISHED 100
>
> LAST_ACK 12

服务器上的监听服务列表
-----

{% highlight bash %}
# The word tulpen means tulips(郁金香) in german
$> netstat -tulpen
{% endhighlight %}

上述命令输出如下：

> Active Internet connections (only servers)
>
> Proto Recv-Q Send-Q Local Address           Foreign Address         State       User       Inode       PID/Program name
>
> tcp        0      0 0.0.0.0:3000            0.0.0.0:*               LISTEN      0          11481       1497/node
>
> tcp        0      0 0.0.0.0:1723            0.0.0.0:*               LISTEN      0          9773        1010/pptpd
>
> tcp        0      0 0.0.0.0:8000            0.0.0.0:*               LISTEN      0          10994       1376/node
>
> tcp        0      0 203.88.168.146:8388     0.0.0.0:*               LISTEN      0          9811        1018/ss-server
>
> tcp        0      0 127.0.0.1:5000          0.0.0.0:*               LISTEN      0          4732265     30298/python
>
> tcp        0      0 0.0.0.0:80              0.0.0.0:*               LISTEN      0          9225        862/nginx
>
> tcp        0      0 0.0.0.0:22              0.0.0.0:*               LISTEN      0          8997        777/sshd
>
> tcp6       0      0 :::80                   :::*                    LISTEN      0          9226        862/nginx
>
> udp        0      0 127.0.0.1:123           0.0.0.0:*                           0          10618       1330/ntpd
>
> udp        0      0 203.88.168.146:8388     0.0.0.0:*                           0          9816        1018/ss-server
>
> udp6       0      0 :::123                  :::*                                0          10612       1330/ntpd

TCP/IP headers format
-----

![tcp-header-format]({{ site.baseurl }}/img/linux/tcp/tcp-header-format.png)

![ip4-header-format]({{ site.baseurl }}/img/linux/tcp/ip4-header-format.png)

常见网络协议的头格式
------

1. [tcp-header-format.png]({{ site.baseurl }}/img/linux/tcp/tcp-header.png)
2. [ip-header-format.png]({{ site.baseurl }}/img/linux/tcp/ip-header.png)
3. [udp-header-format.png]({{ site.baseurl }}/img/linux/tcp/udp-header.png)
4. [icmp-header-format.png]({{ site.baseurl }}/img/linux/tcp/icmp-header.png)

TCP三次握手和四次挥手的示意图
-----

1. [tcp-open.png]({{ site.baseurl }}/img/linux/tcp/tcp-open.png)
2. [tcp-close.png]({{ site.baseurl }}/img/linux/tcp/tcp-close.png)
3. [tcp-simultaneous-open.png]({{ site.baseurl }}/img/linux/tcp/tcp-simultaneous-open.png)
4. [tcp-simultaneous-close.png]({{ site.baseurl }}/img/linux/tcp/tcp-simultaneous-close.png)
5. [tcp-open-close.jpg]({{ site.baseurl }}/img/linux/tcp/tcp-open-close.jpg)

文中配图主要来源于以下参考链接。

References
-----

1. [Transmission Control Protocol](https://en.wikipedia.org/wiki/Transmission_Control_Protocol)
2. [传输控制协议](https://zh.wikipedia.org/wiki/%E4%BC%A0%E8%BE%93%E6%8E%A7%E5%88%B6%E5%8D%8F%E8%AE%AE)
3. [TCP/IP Reference](https://nmap.org/book/tcpip-ref.html)
4. [TCP connection states](https://blog.confirm.ch/tcp-connection-states/)
5. [wireshark-filter - Wireshark filter syntax and reference](https://www.wireshark.org/docs/man-pages/wireshark-filter.html)

