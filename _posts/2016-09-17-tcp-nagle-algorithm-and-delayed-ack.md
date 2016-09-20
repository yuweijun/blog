---
layout: post
title: "tcp nagle's algorithm and delayed ack"
date: Sat, 17 Sep 2016 21:09:09 +0800
categories: linux
---

本文内容主要是为了帮助理解`socket`对象中的一个方法：`socket.setTcpNoDelay(boolean)`。

其中相关的主要内容如下：

1. 糊涂窗口综合症- silly window syndrome。
2. nagle算法 - nagle's algorithm。
3. 延迟ACK - tcp delayed acknowledgment。

糊涂窗口综合症
-----

当tcp连接建立之后，在一些情况下，网络传输的tcp报文段中数据长度只有1个字节，而传输开销有40字节(20字节的IP头 + 20字节的TCP头)，结果有很多41字节的IP数据报就在互连网中传来传去，造成网络拥塞，这种现象就叫糊涂窗口综合症。

如果要避免糊涂窗口综合症，可以从发送端和接收端分别进行优化设置：

1. 发送端使用`nagle算法`。
2. 接收端设置`延迟ACK`。

Nagle's algorithm
-----

为了避免糊涂窗口综合症，纳格算法会尽可能发送大块数据，减少大量小数据包的发送，避免网络中充斥着许多小数据块，从而提高网络利用率。

纳格算法伪代码如下，实现可参考`tcp_output.c`文件里`tcp_nagle_check`函数注释：

{% highlight text %}
if there is new data to send
  if the window size >= MSS and available data is >= MSS
    send complete MSS segment now
  else
    if there is unconfirmed data still in the pipe
      enqueue data in the buffer until an acknowledge is received
    else
      send data immediately
    end if
  end if
end if
{% endhighlight %}

纳格算法在发送端为了避免发送很小的tcp segment，规定只有在下面情况下才会发送tcp segment:

1. 发送端的数据累计达到了MSS（maximun segment size）。
2. 如果该包含有FIN，则允许发送。
3. 设置了TCP_NODELAY选项，则允许发送。
4. 未设置TCP_CORK选项时，若所有发出去的小数据包（包长度小于MSS）均收到ACK确认，则允许发送。
5. 上述条件都未满足，但发生了超时（一般为200ms），则立即发送。

纳格算法维基百科原文有段说明如下：

> A solution recommended by Nagle is to avoid the algorithm sending premature packets by buffering up application writes and then flushing the buffer:
>
> The user-level solution is to avoid write-write-read sequences on sockets. write-read-write-read is fine. write-write-write is fine. But write-write-read is a killer. So, if you can, buffer up your little writes to TCP and send them all at once. Using the standard UNIX I/O package and flushing write before each read usually works.


也就是说纳格算法对于`write-read-write-read`和`write-write-write`模式的应用能有效的优化网络，但对于使用`write-write-read`模式的应用，在启用纳格算法时，却反而可能会带来程序运行性能的问题，纳格算法的维基百科页面上提到了`尽量编写好的代码而不要依赖TCP内置的所谓的算法`来优化TCP的行为。

使用`TCP_NODELAY`选项可以禁止纳格算法。

延迟确认机制 - TCP delayed acknowledgment
-----

[RFC 1122](https://en.wikipedia.org/wiki/TCP_delayed_acknowledgment)定义，全名`Delayed Acknowledgment`，简称`延迟ACK`，翻译为`延迟确认`。

接收端不启用`延迟ACK`时，在接收到每一个数据包后，都会发送一个ACK报文给发送方，这样就增加了网络中传输的小报文数。

与Nagle算法一样，`延迟ACK`的目的也是为了减少网络中传输大量的小报文数，但是此设置是针对接收端的ACK报文的。

一个来自发送端的报文到达接收端，TCP会延迟ACK的发送，根据实际情况来回复`ACK确认`给发送端：

1. 将ACK确认推迟到下一个TCP segment到来，即每收到两个TCP segment，发送一个ACK确认。
2. `ACK定时器`超时，此时一个TCP segment对应一个ACK确认。
3. 应用程序会对刚刚收到的数据进行应答，这样就可以用新数据将ACK捎带过去。

`延迟ACK`最终目标是通过`捎带技术`或者`多个segment共用一个ACK确认`等技术来减少用于`ACK确认`的TCP segment的数量，这样可以减少通信量，提高吞吐率。

#### 关于ACK定时器超时说明

TCP标准推荐最多延迟500ms，微软指定的延迟为200ms，Linux上延迟40ms。

[Reducing the TCP delayed ack timeout](https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_MRG/1.3/html/Realtime_Tuning_Guide/sect-Realtime_Tuning_Guide-General_System_Tuning-Reducing_the_TCP_delayed_ack_timeout.html)中说明如下：

> Some applications that send small network packets can experience latencies due to the TCP delayed acknowledgement timeout. This value defaults to 40ms. To avoid this problem, try reducing the tcp_delack_min timeout value. This changes the minimum time to delay before sending an acknowledgement systemwide.
>
> Write the desired minimum value, in microseconds, to `/proc/sys/net/ipv4/tcp_delack_min`

当纳格算法遇到延迟确认
-----

在`write-write-read`模式的应用程序中，发送端启用纳格算法，接收端启用`延迟ACK`时，就会对程序产生性能影响，简单说明如下：

#### 发送端伪代码示例

{% highlight java %}
write(head); // write
write(body); // write
read(response); // read
{% endhighlight %}

#### 接收端伪代码示例

{% highlight java %}
read(request);
process(request);
write(response);
{% endhighlight %}

假设这里head和body都比较小，并默认启用纳格算法，并且是第一次发送的时候：

1. 根据nagle算法，第一个段head可以立即发送，因为没有等待确认的段；
2. 接收端收到head，但是包不完整，继续等待body达到并`延迟ACK`；
3. 发送端继续写入body，这时候nagle算法起作用了，因为head还没有被ACK，所以body要延迟发送，这就造成了发送端和接收端都在等待对方发送数据的现象：
4. 发送端等待接收端对head进行ACK确认，以便继续发送body；
5. 接收端在等待发送方发送body并`延迟ACK`。

这种时候只有等待一端超时并发送数据，应用程序才能继续往下执行，一般接收端的`延迟ACK`40ms超时先触发，此而在程序中就产生了40ms的响应延时。

java代码示例
-----

#### 接收端server

{% highlight java %}
public class SocketTcpNoDelayServer {

    private static final Logger LOGGER = LoggerFactory.getLogger(SocketTcpNoDelayServer.class);

    private static final int PORT = 8888;

    public static void main(String[] args) throws IOException {
        ServerSocket serverSocket = new ServerSocket();
        serverSocket.bind(new InetSocketAddress(PORT));

        LOGGER.debug("Server startup at {}", PORT);

        while (true) {
            Socket socket = serverSocket.accept();
            InputStream in = socket.getInputStream();
            OutputStream out = socket.getOutputStream();
            int i = 1;

            while (true) {
                try {
                    BufferedReader reader = new BufferedReader(new InputStreamReader(in));
                    String line = reader.readLine();
                    LOGGER.debug("{} : {}", i++, line);
                    out.write((line + "\r\n").getBytes());
                } catch (Exception e) {
                    break;
                }
            }
        }
    }

}
{% endhighlight %}

运行main方法启动服务端应用程序。

#### 发送端client

{% highlight java %}
public class SocketTcpNoDelayClient {

    private static final Logger LOGGER = LoggerFactory.getLogger(SocketTcpNoDelayClient.class);

    @Test
    public void disableNagle() throws IOException {
        noDelay(true);
    }

    @Test
    public void enableNagle() throws IOException {
        // socket默认就是打开纳格算法的
        noDelay(false);
    }

    private void noDelay(boolean enable) throws IOException {
        Socket socket = new Socket();
        socket.setTcpNoDelay(enable);
        socket.connect(new InetSocketAddress("localhost", 8888));

        InputStream in = socket.getInputStream();
        OutputStream out = socket.getOutputStream();
        BufferedReader reader = new BufferedReader(new InputStreamReader(in));

        String head = "hello ";
        String body = "world\r\n";
        for (int i = 0; i < 1; i++) {
            long label = System.currentTimeMillis();
            // The user-level solution is to avoid write-write-read sequences on sockets.
            // write-read-write-read is fine.
            // write-write-write is fine.
            // But write-write-read is a killer.
            out.write(head.getBytes()); // write
            out.write(body.getBytes()); // write
            String line = reader.readLine(); // read

            // 注意如果时间大于40ms，说明服务器接收端tcp有设置Delayed Ack，
            // 是在等待后续数据包delayed超时之后，再向客户端发回ack消息，客户端再发第2个packet的
            LOGGER.debug("RTT: {}, receive: {}", (System.currentTimeMillis() - label), line);
        }
        in.close();
        out.close();
        socket.close();
    }

}
{% endhighlight %}

运行测试用例，并使用wireshark抓包截图如下：

![tcp-nagle-algorithm]({{ site.baseurl }}/img/linux/tcp/tcp-nagle-algorithm.png)

1. 上图标黑的前2条是在禁用纳格算法时，客户端连续发送head和body给服务端，图中序号为2057和2058这2条。
2. 启用纳格算法时（socket默认就是启用的），在发送head和body之间，有收到接收端的一个`ACK确认`消息，即2104到2016这三条，因为这个代码是在Mac OS上测试的，并没有`延迟ACK`，如果接收端有设置`延迟ACK`，则客户端程序中输出的RTT值应该大于40ms。

References
-----

1. [Nagle's algorithm](https://en.wikipedia.org/wiki/Nagle%27s_algorithm)
2. [当Nagle算法遇到Delayed ACK](https://my.oschina.net/xinxingegeya/blog/485643)
3. [java socket参数详解:TcpNoDelay](http://m.blog.csdn.net/article/details?id=7340241)
4. [神秘的40毫秒延迟与 TCP_NODELAY](http://jerrypeng.me/2013/08/mythical-40ms-delay-and-tcp-nodelay/)
5. [再次谈谈TCP的Nagle算法与TCP_CORK选项](http://blog.csdn.net/dog250/article/details/21303679)

