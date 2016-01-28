---
layout: post
title: "ssh-copy-id命令详解"
date: "Wed, 20 Jan 2016 12:36:58 +0800"
categories: linux
---

之前服务器公钥配置，都是用`ssh-keygen`生成公钥私钥之后，是通过`scp`命令复制公钥到远程服务器后，再到远程服务器上将公钥追加到`~/.ssh/authorized_keys`文件中的。
从别人的博客中得知有个`ssh-copy-id`命令，可以直接将公钥传到远程服务器的对应文件中，操作如下：

{% highlight bash %}
$> ssh-keygen -q -N "" -t rsa -f ~/.ssh/id_rsa
$> ssh-copy-id user@server

The authenticity of host 'server (192.168.10.221)' can't be established.
ECDSA key fingerprint is ff:53:68:f0:42:d1:39:4d:fe:29:42:66:3b:ad:3a:0d.
Are you sure you want to continue connecting (yes/no)? yes
/usr/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
/usr/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
user@server's password: YOURPASSWORD

Number of key(s) added: 1

Now try logging into the machine, with:   "ssh 'server'"
and check to make sure that only the key(s) you wanted were added.
{% endhighlight %}

## ssh-copy-id

ssh-copy-id - install your public key in a remote machine's authorized_keys

### 语法

{% highlight bash %}
ssh-copy-id [-i [identity_file]] [user@]machine
{% endhighlight %}

### 说明

> `ssh-copy-id` is a script that uses ssh to log into a remote machine (presumably using a login password, so password authentication should be enabled, unless you've done some clever use of multiple identities)
>
> It also changes the permissions of the remote user's home, `~/.ssh`, and `~/.ssh/authorized_keys` to remove group writability (which would otherwise prevent you from logging in, if the remote sshd has StrictModes set in its configuration).
>
> If the -i option is given then the identity file (defaults to `~/.ssh/id_rsa.pub`) is used, regardless of whether there are any keys in your ssh-agent. Otherwise, if this:
>
> `ssh-add -L`
>
> provides any output, it uses that in preference to the identity file.
>
> If the -i option is used, or the ssh-add produced no output, then it uses the contents of the identity file. Once it has one or more fingerprints (by whatever means) it uses ssh to append them to `~/.ssh/authorized_keys` on the remote machine (creating the file, and directory, if necessary)

References
-----

1. [SSH-COPY-ID](http://www.4e00.com/manpages/man1/ssh-copy-id.1.html)
2. [SSH keys (简体中文)](https://wiki.archlinux.org/index.php/SSH_keys_(%E7%AE%80%E4%BD%93%E4%B8%AD%E6%96%87))
3. [SSH原理与运用（一）：远程登录](http://www.ruanyifeng.com/blog/2011/12/ssh_remote_login.html)
