---
layout: post
title: "install xen-4.0 from source"
date: "Fri Jul 02 2010 13:12:00 GMT+0800 (CST)"
categories: linux
---

以下指令使用root用户执行。

{% highlight bash %}
$> sudo su -

$> apt-get install bcc bin86 gawk bridge-utils iproute libcurl3 libcurl4-openssl-dev bzip2 module-init-tools transfig tgif texinfo texlive-latex-base texlive-latex-recommended texlive-fonts-extra texlive-fonts-recommended pciutils-dev mercurial build-essential make gcc libc6-dev zlib1g-dev python python-dev python-twisted libncurses5-dev patch libvncserver-dev libsdl-dev libjpeg62-dev iasl libbz2-dev e2fslibs-dev git-core uuid-dev

$> hg clone http://xenbits.xen.org/xen-4.0-testing.hg
$> cd xen-4.0-testing.hg
{% endhighlight %}

这个指令会下载linux-2.6-pvops.git，需要较长时间
{% highlight bash %}
$> make world
{% endhighlight %}

这个指令会安装xen.gz和vmlinuz-2.6.31到系统/boot目录下，vmlinuz-2.6.31这个linux kernel文件在下面会重新设置参数并生成，可查看后面linux kernel编译步骤详细说明。

{% highlight bash %}
$> make install
{% endhighlight %}

不修改.config文件，可以直接重新生成xen，kernels，tools，docs到dist目录中

{% highlight bash %}
$> make dist
{% endhighlight %}

修改了.config文件，可以用以下指令重新生成kernel

{% highlight bash %}
$> make linux-2.6-xen-config CONFIGMODE=menuconfig # (or xconfig)
$> make linux-2.6-xen-build
$> make linux-2.6-xen-install
{% endhighlight %}

linux kernel编译步骤
-----

{% highlight bash %}
$> git clone git://git.kernel.org/pub/scm/linux/kernel/git/jeremy/xen.git linux-2.6-xen
{% endhighlight %}

这个指令会从xen.git仓库中下载props dom0 kernel 2.6.31.x，大概是1.6G左右，需要不少时间

{% highlight bash %}
$> cd linux-2.6-xen
{% endhighlight %}

这个指令的作用是检查源码是否有.o文件和依赖问题，从全新的源码编译时不需要这一步。make mrproper主要清除环境变量及配置文件

{% highlight bash %}
$> make mrproper
{% endhighlight %}

设置linux kernel编译参数，在图形界面中输入斜杠"/"可以查询"XEN_DOM0"、"PAE"、"HIGHPTE"等参数设置

{% highlight bash %}
$> make menuconfig
{% endhighlight %}

退出并保存配置文件之后，手工调整.config文件，并且在内核参数设置中需要注意：

1. 如果编译32位的内核，XEN需要有PAE支持，(Processor type and features -> High Memory Support (64GB) -> PAE (Physical Address Extension) Support)，对于64位内核PAE不需要，32位的操作系统最大内存支持不到4G，安装了PAE之后可以支持64G。
2. 编译32位的内核，必须设置CONFIG_HIGHPTE=n。
3. 要为DOM0开启ACPI功能。
4. 在.config文件中添加以下选项，并重新运行make menuconfig，并检查XEN_DOM0等参数设置状态

{% highlight text %}
     CONFIG_ACPI_PROCFS=y
     CONFIG_XEN=y
     CONFIG_XEN_MAX_DOMAIN_MEMORY=32
     CONFIG_XEN_SAVE_RESTORE=y
     CONFIG_XEN_DOM0=y
     CONFIG_XEN_PRIVILEGED_GUEST=y
     CONFIG_XEN_PCI=y
     CONFIG_PCI_XEN=y
     CONFIG_XEN_BLKDEV_FRONTEND=m
     CONFIG_NETXEN_NIC=m
     CONFIG_XEN_NETDEV_FRONTEND=m
     CONFIG_XEN_KBDDEV_FRONTEND=m
     CONFIG_HVC_XEN=y
     CONFIG_XEN_FBDEV_FRONTEND=m
     CONFIG_XEN_BALLOON=y
     CONFIG_XEN_SCRUB_PAGES=y
     CONFIG_XEN_DEV_EVTCHN=y
     CONFIG_XEN_BACKEND=y
     CONFIG_XEN_BLKDEV_BACKEND=y
     CONFIG_XEN_NETDEV_BACKEND=y
     CONFIG_XENFS=y
     CONFIG_XEN_COMPAT_XENFS=y
     CONFIG_XEN_XENBUS_FRONTEND=m
     CONFIG_XEN_PCIDEV_FRONTEND=y
{% endhighlight %}

在rhel5和centos5中，需要在`.config`文件中加入以下2个参数：

{% highlight text %}
     CONFIG_SYSFS_DEPRECATED=y
     CONFIG_SYSFS_DEPRECATED_V2=y
{% endhighlight %}

要使用initrd，编译内核时必须选择以下两项：

{% highlight text %}
     CONFIG_BLK_DEV_RAM=y
     CONFIG_BLK_DEV_INITRD=y
{% endhighlight %}

重新运行menuconfig，检查配置，最后保存配置文件。

{% highlight bash %}
$> make menuconfig
$> make install
{% endhighlight %}

这个指令会将内核映象和相应的System.map拷贝到/boot目录下。

可以查看指令运行完成后，在/boot目录下新增加的文件列表。

{% highlight bash %}
$> ll --sort=time /boot/
total 214594
-rw-r--r--  1 root root  109K 2010-07-02 11:51 config-2.6.31.13
-rw-r--r--  1 root root  1.6M 2010-07-02 11:51 System.map-2.6.31.13
-rw-r--r--  1 root root  3.9M 2010-07-02 11:51 vmlinuz-2.6.31.13
...
{% endhighlight %}

生成初始化镜像文件(initialed ramdisk)

{% highlight bash %}
$> cd /boot
$> depmod 2.6.31.13
# ubuntu中生成初如化镜像文件的指令
$> mkinitramfs -o initrd-2.6.31-xen.img 2.6.31.13
# 或者是用这个指令(CentOS5)
$> mkinitrd -v -f --with=aacraid --with=sd_mod --with=scsi_mod initrd-2.6.31-xen.img 2.6.31.13

$> vi /boot/grub/menu.lst

title Xen 4.0, dom0 Linux kernel 2.6.31.13
kernel /boot/xen.gz dom0_mem=512M
module /boot/vmlinuz-2.6.31.13 root=/dev/sda7 ro nomodeset
module /boot/initrd-2.6.31-xen.img
{% endhighlight %}

注：一般内核编译中会有以下一些步骤
-----

{% highlight bash %}
$> make menuconfig
$> make
$> make bzImage
$> make modules
$> make modules_install
$> make install
{% endhighlight %}

References
-----

1. [http://wiki.xensource.com/xenwiki/Xen4.0](http://wiki.xensource.com/xenwiki/Xen4.0)
2. [http://wiki.xensource.com/xenwiki/XenParavirtOps](http://wiki.xensource.com/xenwiki/XenParavirtOps)
