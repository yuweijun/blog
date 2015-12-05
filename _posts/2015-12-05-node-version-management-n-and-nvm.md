---
layout: post
title: "nodejs版本管理器n和nvm"
date: "Sat, 05 Dec 2015 16:31:27 +0800"
categories: nodejs
---

`nodejs`版本更新非常快，并且不能完全兼容之前的第3方开发者开发的类库，有很多类库需要在指定的`nodejs`版本环境中运行，如果系统中只有一个全局的`nodejs`环境就会产生非常多的错误，而且很难解决，这时可以使用`n`和`nvm`这样的`nodejs`版本管理器来处理。

n
-----

`n`是nodejs的一个模块，作者是`TJ Holowaychuk`（鼎鼎大名的`Express`框架作者），它的理念就是`简单`：`no subshells, no profile setup, no convoluted api, just simple`。

### 安装n

{% highlight bash %}
$> npm install -g n
{% endhighlight %}

### 用n安装不同版本的nodejs

{% highlight bash %}
$> n 0.8.14
$> n 0.8.17
$> n 0.9.6
$> n latest
$> n stable
{% endhighlight %}

### 指定nodejs版本

输入`n`命令后，用箭头选到对应的版本后，回车确认，或者是按`CTRL+C`取消。

{% highlight bash %}
$> n

#   0.8.14
# ο 0.8.17
#   0.9.6
{% endhighlight %}

### 删除nodejs版本

{% highlight bash %}
$> n rm 0.9.4 v0.10.0
# or
$> n - 0.9.4
{% endhighlight %}

### 指定版本nodejs运行

{% highlight bash %}
$> n bin 0.9.4
# /usr/local/n/versions/0.9.4/bin/node
$> n use 0.9.4 some.js
$> n as 0.9.4 --debug some.js
{% endhighlight %}

nvm
-----

`nvm`全称`node version manager`，它与`n`的实现方式不同，其是通过`shell`脚本实现的。

### 安装nvm

{% highlight bash %}
$> curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.29.0/install.sh | bash
{% endhighlight %}

### 使用nvm安装不同版本的nodejs

{% highlight bash %}
$> nvm install 5.0
$> nvm install 5.1.0
$> nvm install 0.12.2
$> nvm install 0.10.25
{% endhighlight %}

### 查看所有nvm管理的nodejs版本

{% highlight bash %}
$> nvm ls
#        v0.10.25
#         v0.12.2
# ->       system
# node -> stable (-> v0.12.2) (default)
# stable -> 0.12 (-> v0.12.2) (default)
# iojs -> N/A (default)
{% endhighlight %}

### 使用nvm指定nodejs版本

{% highlight bash %}
$> nvm use 5.0
{% endhighlight %}

### 使用指定的nodejs版本来运行命令

{% highlight bash %}
$> nvm run system --version

# Running node system (npm v3.3.12)
# v5.1.0
{% endhighlight %}

### 在一个subshell中运行指定版本的nodejs命令

{% highlight bash %}
$> nvm exec v0.12.2 node --version

# Running node v0.12.2 (npm v3.3.12)
# v0.12.2
{% endhighlight %}

References
-----

1. [https://github.com/visionmedia/n](https://github.com/visionmedia/n)
2. [https://github.com/creationix/nvm](https://github.com/creationix/nvm)
3. [利用n和nvm管理Node的版本](http://it.taocms.org/03/3079.htm)
