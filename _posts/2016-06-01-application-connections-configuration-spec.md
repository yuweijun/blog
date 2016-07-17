---
layout: post
title: "应用的连接配置说明"
date: "Wed, 01 Jun 2016 10:08:34 +0800"
categories: linux
---

应用程序需要连接到其他服务器时，程序中就需要配置相应的连接信息，比如是mysql服务器，如果直接使用ip的话，发生变动就需要更新重启所有相关的应用，所以建议使用域名或者是主机别名来代替ip，并且使用相同的配置文件，就可以同时适用开发环境和生产环境。

1. 应用程序中的所有涉及连接的配置不使用IP地址，无论是开发环境还是生产环境。
2. 开发环境和生产环境尽量使用相同的端口。
3. 修改服务器和本地机器的`/etc/hosts`文件，使用开发相关的域名配置，如主域名为: `example.com`。
4. 线上生产环境配置举例，格式为（ip domain hostname-alias）：

    > 10.20.30.36 mysql1.example.com mysql1
    >
    > 10.20.30.40 mysql2.example.com mysql2
    >
    > 10.20.30.41 mongo.example.com mongo
    >
    > 10.20.30.42 memcached.example.com memcached
    >
    > 10.20.30.43 redis.example.com redis

5. 开发环境配置举例：

    > 192.168.1.36 mysql1.example.com mysql1
    >
    > 192.168.1.40 mysql2.example.com mysql2
    >
    > 192.168.1.41 mongo.example.com mongo
    >
    > 192.168.1.42 memcached.example.com memcached
    >
    > 192.168.1.43 redis.example.com redis

6. DNS服务器解析使用生产环境的设置，以防服务器设置`/etc/hosts`有遗漏，本地局域网由IT同事将DNS配置写到本地DNS服务器中，开发的本机可以由开发人员自行设置`/etc/hosts`。
7. 有连接配置变化时，线上由运维或者是leader统一更新，本地开发环境需要工程师自行维护并更新。局域网DNS服务器需要IT帮助更新。
8. 线上生产环境每台服务器的hostname需要对应修改成`/etc/hosts`里相应的主机名。
9. 如果配置的服务器多了，可以有一个配置中心的git仓库，只需要在一个机器上配置并提交到仓库，其他节点机可以使用脚本远程同步git仓库，将更新的内容merge到`/etc/hosts`中，有点像`gitolite`的方式，借助一个git仓库来管理配置。
10. windows的hosts配置文件位置：`C:\windows\system32\drivers\etc\hosts`。

