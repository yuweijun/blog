---
layout: post
title: "ansible simple tutorial"
date: Sun, 24 Jul 2016 00:01:46 +0800
categories: linux
---

ANSIBLE
-----

> Simple, agentless and powerful open source IT automation.

ansible是一个轻量级的自动化运维工具，学习门槛低，安装简单，执行方便，它基于ssh，远程服务器上不需要安装客户端agent，利用推送方式对客户系统加以配置，这样所有工作都可在主服务器端完成，不需要维护远程服务器上的客户端agent。

ansible中的概念
-----

1. 任务task：多个`task`顺序执行，在每个`task`执行结束可以通知`hanlder`触发新操作。
2. 变量variable：用户定义的变量。
3. 环境facts：`facts`从每台服务器上收集得到，可以用作变量。
4. 模块module：比如`shell`，`ping`，`apt`，`git`，`copy`，默认的`command`等等。
5. 操作hanlder：任务执行后的回调任务，通过`task`中的`notify`配置关联`handler`，不管有多少个通知，`handler`在所有任务运行后，按照配置的先后顺序只运行一次。

使用前提
-----

ansible基于ssh操作远程服务器，所以要求远程服务器提供：

1. `remote_user`能够通过ssh公钥无密码登录到所管理的远程服务器。
2. `remote_user`执行`sudo`的时候不需要密码，配置如下说明。

{% highlight bash %}
$> sudo chmod +w /etc/sudoers
$> visudo
{% endhighlight %}

或者是用`vim`直接编辑此文件：

{% highlight bash %}
$> sudo vim /etc/sudoers
{% endhighlight %}

找到`root ALL=(ALL:ALL) ALL`，在下面添加一行，`username`为ssh远程登陆用户的用户名：

{% highlight text %}
username    ALL=(ALL:ALL) NOPASSWD:ALL
{% endhighlight %}

保存退出，然后恢复为只读。

{% highlight bash %}
$> sudo chmod -w /etc/sudoers
{% endhighlight %}

Mac OS上安装ansible
-----

使用`homebrew`安装最方便：

{% highlight bash %}
$> brew install ansible
{% endhighlight %}

或者是使用`pip`安装：

{% highlight bash %}
$> sudo easy_install pip
$> sudo pip install ansible
{% endhighlight %}

配置
-----

ansible配置文件设置`.ansible.cfg`，ansible执行的时候会按照以下顺序查找配置文件:

* ANSIBLE_CONFIG (环境变量)
* ansible.cfg (当前目录)
* ~/.ansible.cfg (用户目录)
* /etc/ansible/ansible.cfg (默认位置)

在当前用户目录新建一份空文件：

{% highlight bash %}
$> touch ~/.ansible.cfg
{% endhighlight %}

Ping command test
-----

编辑`/etc/ansible/hosts`文件，加入以下远程服务地址，并且在这些远程服务器的`~/.ssh/authorized_keys`文件中加入了登录用户的ssh公钥，使得用户可以通过ssh无密码登录：

{% highlight text %}
192.168.1.50

[local]
127.0.0.1

[mail]
mail.example.com ansible_ssh_user=username

[webservers]
www.example.com

[dbservers]
db[a:f].example.com
{% endhighlight %}

这是一份需要维护的远程服务器的`inventory`目录文件，更详细的说明可查看[Inventory](http://docs.ansible.com/ansible/intro_inventory.html)官方文档。

使用`ping`模块检查以上配置中的所有服务器状态：

{% highlight bash %}
$> ansible all -m ping
{% endhighlight %}

前面公钥设置正确的情况下，会得到如下类似的输出：

> 127.0.0.1 \| success >> {
>
> "changed": false,
>
> "ping": "pong"
>
> }
>
> mail.example.com \| success >> {
>
> "changed": false,
>
> "ping": "pong"
>
> }

Echo command test
-----

{% highlight bash %}
$> ansible all -a "/bin/echo hello"
{% endhighlight %}

> 127.0.0.1 \| success \| rc=0 >>
>
> hello
>
> mail.example.com \| success \| rc=0 >>
>
> hello

如果要执行远程服务器操作的用户与当前操作用户不同时，可以通过`/etc/ansible/hosts`文件配置`ansible_ssh_user`，或者是在运行命令时加上`-u`参数覆盖配置文件中的设置。

{% highlight bash %}
$> ansible all -a "/bin/echo hello" -u bruce
{% endhighlight %}

这是一条`ansible ad-hoc`命令，`ad-hoc`命令是指临时的，在ansible中是指需要快速执行，并且不需要保存的命令。ansible提供两种方式去完成任务，一是`ad-hoc`命令，一是写`playbook`，前者可以解决一些简单的任务，后者解决较复杂的任务.

指定ansible hosts配置文件运行ad-hoc命令
-----

{% highlight bash %}
$> ansible -i ~/hosts all -a 'who'
{% endhighlight %}

以上命令参数说明：

1. `-i`表示`hosts`文件的位置，默认是`/etc/ansible/hosts`。
2. `-a`后面是`module`的参数,这边没有指定`module`，即默认的`module`，是`command module`。
3. `all`代表`host`所有服务器。

这条命令就是对用户指定配置文件`~/hosts`中所有的`host`执行`who`命令。

修改ansible默认hosts文件位置
-----

修改默认`hosts`文件位置，编辑用户目录下的`~/.ansible.cnf`文件：

{% highlight bash %}
$> mkdir ~/ansible
$> cp /etc/ansible/hosts ~/ansible/hosts
$> vim .ansible.cfg
{% endhighlight %}

加入以下内容：

{% highlight conf %}
[defaults]
hostfile=~/ansible/hosts
{% endhighlight %}

playbook.yml配置
-----

前面已经提到`playbook`是ansible保存的一份配置文件，用于执行复杂的任务和需要重复执行的任务。

以下是官方[示例](http://docs.ansible.com/ansible/playbooks_intro.html)中的一份`playbook`配置文件，这份配置只有一个`play`，一份配置可以添加多个`play`。在这个配置中定义了3个`task`和1个`handler`，使用yum安装apache，添加apache配置文件并通知`hanlder`执行apache重启，检查当前是否正在运行apache服务，最后由`handler`重启apache服务。

{% highlight yml %}
---
- hosts: webservers
  vars:
    http_port: 80
    max_clients: 200
  remote_user: root
  tasks:
  - name: ensure apache is at the latest version
    yum:
      sudo: yes
      name: httpd
      state: latest
  - name: write the apache config file
    template:
      src: /srv/httpd.j2
      dest: /etc/httpd.conf
    notify:
    - restart apache
  - name: ensure apache is running
    service:
      name: httpd
      state: started
  handlers:
    - name: restart apache
      service:
        name: httpd
        state: restarted
{% endhighlight %}

playbook简单示例
-----

设置一个简单的任务，登陆远程服务器将登录用户名写入`whoami.rst`文件中，保存以下内容到`~/ansible/playbook.yml`。

{% highlight yml %}
# playbook.yml
---
- hosts: webservers # hosts中指定
  remote_user: username # 如果和当前用户一样，则无需指定
  tasks:
    - name: whoami
      shell: 'whoami > whoami.rst'
{% endhighlight %}

playbook的结构
-----

一个playbook文件由一个或多个play组成，在play中首先需要定义在哪个主机上执行，即

{% highlight yml %}
hosts: webservers
{% endhighlight %}

每个play定义了在一个或多个远程主机上执行的一系列的task，下面就是一个task：

{% highlight yml %}
  - name: whoami
    shell: 'whoami > whoami.rst'
{% endhighlight %}

其中每个`task`一般就是调用一个ansible的模块，如调用`copy`模块复制文件到远程主机或调用`shell`模块执行命令。`tasks`中的各任务按次序逐个在`hosts`中指定的所有主机上执行，即在所有主机上完成第一个任务后再开始第二个，如果中途发生错误所有已执行任务都将回滚因此在更正`playbook`后重新执行一次即可。

运行以上配置文件：

{% highlight bash %}
$> ansible-playbook -i ~/ansible/hosts ~/ansible/playbook.yml
{% endhighlight %}

输出内容如下：

> PLAY [webservers] \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> GATHERING FACTS \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> ok: [www.example.com]
>
> TASK: [whoami] \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> changed: [www.example.com]
>
> PLAY RECAP \*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*
>
> www.example.com                   : ok=2    changed=1    unreachable=0    failed=0

关于`playbook`，还可以查看官方提供的`playbook`示例项目：

{% highlight bash %}
$> git clone git@github.com:ansible/ansible-examples.git
{% endhighlight %}

References
-----

1. [Getting Started](http://docs.ansible.com/ansible/intro_getting_started.html)
2. [Ansible入门](http://blog.leanote.com/post/linkfluenceasia/ansible%E5%85%A5%E9%97%A8)
3. [Ansible 快速入门](http://cn.soulmachine.me/blog/20140127/)
4. [轻量级自动化部署工具 Ansible](https://blog.eood.cn/the-ansible)
5. [Ansible first book download](https://www.gitbook.com/book/ansible-book/ansible-first-book/details)

