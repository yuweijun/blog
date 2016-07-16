---
layout: post
title: "howto tcp keep-alive"
date: Sun, 19 Jun 2016 20:02:35 +0800
categories: linux
---

Fabio Busatto <fabio.busatto@sikurezza.org>
=====

This document describes the TCP keepalive implementation in the linux kernel, introduces the overall concept and points to both system configuration and software development.

Table of Contents
-----

* [1\. Introduction](#introduction)
* [2\. TCP keepalive overview](#tcp-keepalive-overview)
* [2.1. What is TCP keepalive?](#what-is-tcp-keepalive)
* [2.2. Why use TCP keepalive?](#why-use-tcp-keepalive)
* [2.3. Checking for dead peers](#checking-for-dead-peers)
* [2.4. Preventing disconnection due to network inactivity](#preventing-disconnection-due-to-network-inactivity)
* [3\. Using TCP keepalive under Linux](#using-tcp-keepalive-under-linux)
* [3.1. Configuring the kernel](#configuring-the-kernel)
* [3.2. Making changes persistent to reboot](#making-changes-persistent-to-reboot)
* [4.1. tcp keep alive packets dump example](#tcp-keep-alive-packets-dump-example)

1\. Introduction
-----

Understanding TCP keepalive is not necessary in most cases, but it's a subject that can be very useful under particular circumstances. You will need to know basic TCP/IP networking concepts, and the C programming language to understand all sections of this document.

The main purpose of this HOWTO is to describe TCP keepalive in detail and demonstrate various application situations. After some initial theory, the discussion focuses on the Linux implementation of TCP keepalive routines in the modern Linux kernel releases (2.4.x, 2.6.x), and how system administrators can take advantage of these routines, with specific configuration examples and tricks.

2\. TCP keepalive overview
-----

In order to understand what TCP keepalive (which we will just call keepalive) does, you need do nothing more than read the name: keep TCP alive. This means that you will be able to check your connected socket (also known as TCP sockets), and determine whether the connection is still up and running or if it has broken.

2.1. What is TCP keepalive?
-----

The keepalive concept is very simple: when you set up a TCP connection, you associate a set of timers. Some of these timers deal with the keepalive procedure. When the keepalive timer reaches zero, you send your peer a keepalive probe packet with no data in it and the ACK flag turned on. You can do this because of the TCP/IP specifications, as a sort of duplicate ACK, and the remote endpoint will have no arguments, as TCP is a stream-oriented protocol. On the other hand, you will receive a reply from the remote host (which doesn't need to support keepalive at all, just TCP/IP), with no data and the ACK set.

If you receive a reply to your keepalive probe, you can assert that the connection is still up and running without worrying about the user-level implementation. In fact, TCP permits you to handle a stream, not packets, and so a zero-length data packet is not dangerous for the user program.

This procedure is useful because if the other peers lose their connection (for example by rebooting) you will notice that the connection is broken, even if you don't have traffic on it. If the keepalive probes are not replied to by your peer, you can assert that the connection cannot be considered valid and then take the correct action.

2.2. Why use TCP keepalive?
-----


You can live quite happily without keepalive, so if you're reading this, you may be trying to understand if keepalive is a possible solution for your problems. Either that or you've really got nothing more interesting to do instead, and that's okay too. :)

Keepalive is non-invasive, and in most cases, if you're in doubt, you can turn it on without the risk of doing something wrong. But do remember that it generates extra network traffic, which can have an impact on routers and firewalls.

In short, use your brain and be careful.

In the next section we will distinguish between the two target tasks for keepalive: Checking for dead peers

Preventing disconnection due to network inactivity

2.3. Checking for dead peers
-----


Keepalive can be used to advise you when your peer dies before it is able to notify you. This could happen for several reasons, like kernel panic or a brutal termination of the process handling that peer. Another scenario that illustrates when you need keepalive to detect peer death is when the peer is still alive but the network channel between it and you has gone down. In this scenario, if the network doesn't become operational again, you have the equivalent of peer death. This is one of those situations where normal TCP operations aren't useful to check the connection status.


Think of a simple TCP connection between Peer A and Peer B: there is the initial three-way handshake, with one SYN segment from A to B, the SYN/ACK back from B to A, and the final ACK from A to B. At this time, we're in a stable status: connection is established, and now we would normally wait for someone to send data over the channel. And here comes the problem: unplug the power supply from B and instantaneously it will go down, without sending anything over the network to notify A that the connection is going to be broken. A, from its side, is ready to receive data, and has no idea that B has crashed. Now restore the power supply to B and wait for the system to restart. A and B are now back again, but while A knows about a connection still active with B, B has no idea. The situation resolves itself when A tries to send data to B over the dead connection, and B replies with an RST packet, causing A to finally to close the connection.


Keepalive can tell you when another peer becomes unreachable without the risk of false-positives. In fact, if the problem is in the network between two peers, the keepalive action is to wait some time and then retry, sending the keepalive packet before marking the connection as broken.


{% highlight html %}
   _____                                                     _____
  |     |                                                   |     |
  |  A  |                                                   |  B  |
  |_____|                                                   |_____|
     ^                                                         ^
     |--->--->--->-------------- SYN -------------->--->--->---|
     |---<---<---<------------ SYN/ACK ------------<---<---<---|
     |--->--->--->-------------- ACK -------------->--->--->---|
     |                                                         |
     |                                       system crash ---> X
     |
     |                                     system restart ---> ^
     |                                                         |
     |--->--->--->-------------- PSH -------------->--->--->---|
     |---<---<---<-------------- RST --------------<---<---<---|
     |                                                         |
{% endhighlight %}

2.4. Preventing disconnection due to network inactivity
-----


The other useful goal of keepalive is to prevent inactivity from disconnecting the channel. It's a very common issue, when you are behind a NAT proxy or a firewall, to be disconnected without a reason. This behavior is caused by the connection tracking procedures implemented in proxies and firewalls, which keep track of all connections that pass through them. Because of the physical limits of these machines, they can only keep a finite number of connections in their memory. The most common and logical policy is to keep newest connections and to discard old and inactive connections first.


Returning to Peers A and B, reconnect them. Once the channel is open, wait until an event occurs and then communicate this to the other peer. What if the event verifies after a long period of time? Our connection has its scope, but it's unknown to the proxy. So when we finally send data, the proxy isn't able to correctly handle it, and the connection breaks up.


Because the normal implementation puts the connection at the top of the list when one of its packets arrives and selects the last connection in the queue when it needs to eliminate an entry, periodically sending packets over the network is a good way to always be in a polar position with a minor risk of deletion.


{% highlight html %}
   _____           _____                                     _____
  |     |         |     |                                   |     |
  |  A  |         | NAT |                                   |  B  |
  |_____|         |_____|                                   |_____|
     ^               ^                                         ^
     |--->--->--->---|----------- SYN ------------->--->--->---|
     |---<---<---<---|--------- SYN/ACK -----------<---<---<---|
     |--->--->--->---|----------- ACK ------------->--->--->---|
     |               |                                         |
     |               | <--- connection deleted from table      |
     |               |                                         |
     |--->- PSH ->---| <--- invalid connection                 |
     |               |                                         |
{% endhighlight %}

3\. Using TCP keepalive under Linux
-----


Linux has built-in support for keepalive. You need to enable TCP/IP networking in order to use it. You also need procfs support and `sysctl` support to be able to configure the kernel parameters at runtime.


The procedures involving keepalive use three user-driven variables:

tcp_keepalive_time
=====

the interval between the last data packet sent (simple ACKs are not considered data) and the first keepalive probe; after the connection is marked to need keepalive, this counter is not used any further

tcp_keepalive_intvl
=====

the interval between subsequential keepalive probes, regardless of what the connection has exchanged in the meantime

tcp_keepalive_probes
=====

the number of unacknowledged probes to send before considering the connection dead and notifying the application layer


Remember that keepalive support, even if configured in the kernel, is not the default behavior in Linux. Programs must request keepalive control for their sockets using the setsockopt interface. There are relatively few programs implementing keepalive, but you can easily add keepalive support for most of them following the instructions explained later in this document.

3.1. Configuring the kernel
-----


There are two ways to configure keepalive parameters inside the kernel via userspace commands:

{% highlight bash %}
# procfs interface
# sysctl interface

$> sysctl net.inet.tcp | grep -E "keepidle|keepintvl|keepcnt"
{% endhighlight %}

We mainly discuss how this is accomplished on the procfs interface because it's the most used, recommended and the easiest to understand. The `sysctl` interface, particularly regarding the
`sysctl(2)` syscall and not the

`sysctl(8)` tool, is only here for the purpose of background knowledge.

3.1.1. The procfs interface
-----

This interface requires both `sysctl` and `procfs` to be built into the kernel, and `procfs` mounted somewhere in the filesystem (usually on `/proc`, as in the examples below). You can read the values for the actual parameters by "catting" files in `/proc/sys/net/ipv4/` directory:

{% highlight bash %}
$> cat /proc/sys/net/ipv4/tcp_keepalive_time
# 7200

$> cat /proc/sys/net/ipv4/tcp_keepalive_intvl
# 75

$> cat /proc/sys/net/ipv4/tcp_keepalive_probes
# 9
{% endhighlight %}


The first two parameters are expressed in seconds, and the last is the pure number. This means that the keepalive routines wait for two hours (7200 secs) before sending the first keepalive probe, and then resend it every 75 seconds. If no ACK response is received for nine consecutive times, the connection is marked as broken.


Modifying this value is straightforward: you need to write new values into the files. Suppose you decide to configure the host so that keepalive starts after ten minutes of channel inactivity, and then send probes in intervals of one minute. Because of the high instability of our network trunk and the low value of the interval, suppose you also want to increase the number of probes to 20.


Here's how we would change the settings:

{% highlight bash %}
$> echo 600 > /proc/sys/net/ipv4/tcp_keepalive_time

$> echo 60 > /proc/sys/net/ipv4/tcp_keepalive_intvl

$> echo 20 > /proc/sys/net/ipv4/tcp_keepalive_probes
{% endhighlight %}


To be sure that all succeeds, recheck the files and confirm these new values are showing in place of the old ones.


Remember that procfs handles special files, and you cannot perform any sort of operation on them because they're just an interface within the kernel space, not real files, so try your scripts before using them, and try to use simple access methods as in the examples shown earlier.


You can access the interface through the `sysctl(8)` tool, specifying what you want to read or write.


{% highlight bash %}
$> sysctl net.ipv4.tcp_keepalive_time net.ipv4.tcp_keepalive_intvl net.ipv4.tcp_keepalive_probes

net.ipv4.tcp_keepalive_time = 7200
net.ipv4.tcp_keepalive_intvl = 75
net.ipv4.tcp_keepalive_probes = 9
{% endhighlight %}


Note that `sysctl` names are very close to
procfs paths. Write is performed using the -w switch of `sysctl(8)`:


{% highlight bash %}
$> sysctl -w net.ipv4.tcp_keepalive_time=600 net.ipv4.tcp_keepalive_intvl=60 net.ipv4.tcp_keepalive_probes=20

net.ipv4.tcp_keepalive_time = 600
net.ipv4.tcp_keepalive_intvl = 60
net.ipv4.tcp_keepalive_probes = 20
{% endhighlight %}


Note that `sysctl(8)` doesn't use `sysctl(2)` syscall, but reads and writes directly in the procfs subtree, so you will need procfs enabled in the kernel and mounted in the filesystem, just as you would if you directly accessed the files within the procfs interface.

`sysctl(8)` is just a different way to do the same thing.

3.1.2. The `sysctl` interface
-----


There is another way to access kernel variables: `sysctl(2)` syscall. It can be useful when you don't have procfs available because the communication with the kernel is performed directly via syscall and not through the procfs subtree. There is currently no program that wraps this syscall (remember that `sysctl(8)` doesn't use it).


For more details about using `sysctl(2)` refer to the manpage.

3.2. Making changes persistent to reboot
-----

There are several ways to reconfigure your system every time it boots up. First, remember that every Linux distribution has its own set of init scripts called by init (8). The most common configurations include the /etc/rc.d/ directory, or the alternative, /etc/init.d/. In any case, you can set the parameters in any of the startup scripts, because keepalive rereads the values every time its procedures need them. So if you change the value of tcp_keepalive_intvl when the connection is still up, the kernel will use the new value going forward.

There are three spots where the initialization commands should logically be placed: the first is where your network is configured, the second is the rc.local script, usually included in all distributions, which is known as the place where user configuration setups are done. The third place may already exist in your system. Referring back to the `sysctl` (8) tool, you can see that the -p switch loads settings from the `/etc/sysctl`.conf configuration file. In many cases your init script already performs the `sysctl -p` (you can "grep" it in the configuration directory for confirmation), and so you just have to add the lines in `/etc/sysctl`.conf to make them load at every boot. For more information about the syntax of `sysctl.conf`(5), refer to the manpage.

For more information, visit the libkeepalive project homepage: [http://libkeepalive.sourceforge.net/](http://libkeepalive.sourceforge.net/)

4.1. tcp keep-alive packets dump example
-----

![tcp-keep-alive-dump]({{ site.baseurl }}/img/linux/tcp-keep-alive-packets.png)

References
-----

1. [TCP Keepalive HOWTO](http://www.tldp.org/HOWTO/html_single/TCP-Keepalive-HOWTO/)
2. [a library that can be pre-loaded and that sets the TCP KEEP-ALIVE flag whenever socket(2) is called](https://github.com/flonatel/libdontdie)
3. [The Internet Protocol Stack](https://www.w3.org/People/Frystyk/thesis/TcpIp.html)
4. [TCP - How it works](http://www.potaroo.net/ispcol/2004-07-isp.htm)
5. [2.6 TCP Connection Establishment and Termination](http://www.masterraghu.com/subjects/np/introduction/unix_network_programming_v1.3/ch02lev1sec6.html)
6. [随手记之TCP Keepalive笔记](http://www.blogjava.net/yongboy/archive/2015/04/14/424413.html)
