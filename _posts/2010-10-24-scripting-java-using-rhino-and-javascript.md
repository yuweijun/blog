---
layout: post
title: "scripting java using rhino and javascript"
date: "Sun Oct 24 2010 19:21:00 GMT+0800 (CST)"
categories: java
---

通过rhino可以使javascript调用java的标准类库，这使得javascript的编程能力得以强化，在服务器端已经有javascript的mvc框架，一般也是基于rhino的。

首先在ubuntu上可以通过命令安装rhino:

{% highlight bash %}
$> sudo apt-get install rhino
{% endhighlight %}

安装完成后，在命令行中输入js或者rhino:

{% highlight bash %}
$> js
$> rhino
Rhino 1.7 release 2 2010 09 15
js> new Date()
Sun Oct 24 2010 17:24:27 GMT+0800 (CST)
js> /^\d+$/.test("369");
true
{% endhighlight %}

就可以进入javascript的控制台。

另外一种方式就是从网上下载rhino 1.5r4.1版本，在命令行中用java运行，下面的代码在命令行的rhino1.7版本中不能运行，因为无法导入`java.awt.*`的包:

{% highlight bash %}
$> java -jar rhino-1.5R4.1.jar
Rhino 1.5 release 4.1 2003 04 21
js> importPackage(java.awt);
js> frame = new Frame("JavaScript")
js> frame.show()
js> button = new Button("OK")
js> frame.add(button)
js> frame.show()
js> function printDate() { print(new Date()) }
js> o = { actionPerformed: printDate }
js> buttonListener = java.awt.event.ActionListener(o)
js> button.addActionListener(buttonListener)
{% endhighlight %}

javascript访问java的包和class
-----

java中所有的代码都是class包装了的，而class又是在package下的，rhino为此封装了一个全局对象Packages，通过Packages可以引入所有的java类，如`Package.java.lang`，`Packages.java.io`等:

{% highlight bash %}
js> Packages.java.io.File
[JavaClass java.io.File]
js> importPackage(java.io)
js> File
[JavaClass java.io.File]
{% endhighlight %}

`importPackage(java.io)`的效果类似java代码中的`import java.io.*`。

java中的第三方类库也可以通过`importClass`和`importPackage`引入，如:

{% highlight bash %}
js> importPackage(Packages.org.mozilla.javascript);
js> Context.currentContext
org.mozilla.javascript.Context@1bc887b
js> importClass(java.awt.List)
js> List
[JavaClass java.awt.List]
{% endhighlight %}

在引入了java的类库之后，就可以在javascript中使用这些类库了，如:

{% highlight bash %}
js> new java.util.Date()
Sun Oct 24 17:46:50 CST 2010
js> new Date()
Sun Oct 24 2010 17:46:54 GMT+0800 (CST)
js> var f = new java.io.File("/etc/hosts");
js> f.exists();
true
js> f.getName();
hosts
js> java.lang.Math.PI
3.141592653589793
js> java.lang.Math.cos(0)
1
js> for (i in f) { print(i) }
getAbsoluteFile
setReadOnly
listFiles
setReadable
writable
hashCode
wait
setExecutable
usableSpace
file
canonicalPath
getUsableSpace
notifyAll
equals
getParent
mkdirs
parent
class
compareTo
freeSpace
getTotalSpace
createNewFile
toString
toURI
toURL
getCanonicalPath
getCanonicalFile
canonicalFile
renameTo
getParentFile
executable
getFreeSpace
absolute
deleteOnExit
canWrite
name
notify
path
canRead
getPath
delete
length
getClass
readable
totalSpace
absoluteFile
lastModified
absolutePath
isAbsolute
list
mkdir
setWritable
isHidden
readOnly
canExecute
isDirectory
hidden
directory
isFile
getName
getAbsolutePath
exists
parentFile
setLastModified
{% endhighlight %}

在上面列出的File的方法中，还包括了其从`java.lang.Object`中继承的所有方法。对于java的重载方法，javascript调用需要用特别的方式。

用javascript实现java的接口
-----

如要实现`Runnable`接口，按以下方式操作:

{% highlight bash %}
js> var obj = { run: function () { print("\nrunning"); } }
js> obj.run()

running
js> var r = new java.lang.Runnable(obj);
js> r.getClass()
class adapter1
js> var t = new java.lang.Thread(r)
Thread[Thread-1,5,main]
js> t.start();
js>
running
{% endhighlight %}

用javascript创建java的数组对象
-----

一般直接用javascript创建数组就可以，转为java对象时，rhino会处理类型转换，也可以用以下方法直接创建java数组:

{% highlight bash %}
js> var arr = java.lang.reflect.Array.newInstance(java.lang.String, 5); arr[0] = arr[1] = arr[2] = arr[3] = arr[4] = 'create java array using javascript.'
create java array using javascript.
js> arr
[Ljava.lang.String;@1e97f9f
js> arr[1]
create java array using javascript.
js>
{% endhighlight %}

References
-----

1. [http://www.mozilla.org/rhino/scriptjava.html](http://www.mozilla.org/rhino/scriptjava.html)
