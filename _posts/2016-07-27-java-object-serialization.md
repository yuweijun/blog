---
layout: post
title: "java object serialization"
date: Wed, 27 Jul 2016 21:22:50 +0800
categories: java
---

目录
-----

* [Java 序列化简介](#java-)
* [相关接口及类](#section)
* [如何对Java对象进行序列化与反序列化](#java)
* [当对象没有实现java.io.Serializable接口时](#javaioserializable)
* [SerializableObject实现java.io.Serializable接口](#serializableobjectjavaioserializable)
* [为SerializableObject声明serialVersionUID](#serializableobjectserialversionuid)
* [当SerializableObject中有其他对象引用时](#serializableobject)
* [手动将UnSerializableReference对象序列化和反序列化](#unserializablereference)
* [如果父类是可序列化的，那么子类都是可序列化的](#section-1)
* [如果父类是不可序列化的，而子类实现了java.io.Serializable接口](#javaioserializable-1)
* [不能序列化static属性](#static)
* [序列化对单例的破坏](#section-2)
* [java.io.Externalizable](#javaioexternalizable)
* [序列化的数据可以被签名和密封](#section-5)

Java 序列化简介
-----

`Java对象序列化`是`JDK 1.1`中引入的一组开创性特性之一，是Java语言内建的一种对象持久化方式，用于作为一种将Java对象的状态转换为字节数组，以便存储或传输的机制，以后仍可以将字节数组转换回Java对象原有的状态。

实际上，序列化的思想是`冻结`对象状态，`传输`对象状态，如写到磁盘或者通过网络使用RMI远程方法调用等，然后`解冻`对象状态，重新获得可用的Java对象。

相关接口及类
-----

Java为了方便开发人员将Java对象进行序列化及反序列化提供了一套方便的API来支持，其中包括以下接口和类：

1. java.io.Serializable
2. java.io.Externalizable
3. java.io.ObjectOutput
4. java.io.ObjectInput
5. java.io.ObjectOutputStream
6. java.io.ObjectInputStream
7. javax.crypto.SealedObject
8. java.security.SignedObject

如何对Java对象进行序列化与反序列化
-----

在Java中，只要一个类实现了`java.io.Serializable`接口，那么它就可以被序列化，这个接口属于标记接口，源码如下：

{% highlight java %}
/*
 * @see java.io.ObjectOutputStream
 * @see java.io.ObjectInputStream
 * @see java.io.ObjectOutput
 * @see java.io.ObjectInput
 * @see java.io.Externalizable
 * @since   JDK1.1
 */
public interface Serializable {
}
{% endhighlight %}

一、当对象没有实现java.io.Serializable接口时
-----

如下所示的`SerializableObject`类在没在实现`java.io.Serializable`接口时，运行下面`SerializeDeserializeExample.main`方法：

{% highlight java %}
public class SerializableObject {

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                '}';
    }
}
{% endhighlight %}

SerializeDeserializeExample.java
=====

{% highlight java %}
public class SerializeDeserializeExample {

    private static final Logger LOGGER = LoggerFactory.getLogger(SerializeDeserializeExample.class);

    public static void serialize(SerializableObject serializableObject) throws FileNotFoundException, IOException {
        File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
        ObjectOutputStream objectOutputStream = null;
        try {
            objectOutputStream = new ObjectOutputStream(new FileOutputStream(file));
            objectOutputStream.writeObject(serializableObject);
            objectOutputStream.flush();
        } finally {
            if (objectOutputStream != null) {
                objectOutputStream.close();
            }
        }
    }

    public static SerializableObject deSerialize() throws FileNotFoundException, IOException, ClassNotFoundException {
        ObjectInputStream objectInputStream = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
            objectInputStream = new ObjectInputStream(new FileInputStream(file));
            Object object = objectInputStream.readObject();
            SerializableObject deserializedObject = (SerializableObject) object;
            LOGGER.info(deserializedObject .getName());
            return deserializedObject;
        } finally {
            if (objectInputStream != null) {
                objectInputStream.close();
            }
        }
    }

    public static void main(String[] args) throws FileNotFoundException, ClassNotFoundException, IOException {
        SerializableObject serializableObject = new SerializableObject();
        serializableObject.setName("test java.io.Serializable");
        serialize(serializableObject);

        SerializableObject deSerializedObject = deSerialize();
        LOGGER.info("{}", deSerializedObject);
    }

}
{% endhighlight %}

程序会抛出如下异常`NotSerializableException`：

> Exception in thread "main" java.io.NotSerializableException: com.example.test.io.SerializableObject
>
>   at java.io.ObjectOutputStream.writeObject0(ObjectOutputStream.java:1184)
>
>   at java.io.ObjectOutputStream.writeObject(ObjectOutputStream.java:348)
>
>   at com.example.test.io.SerializeDeserializeExample.serialize(SerializeDeserializeExample.java:40)
>
>   at com.example.test.io.SerializeDeserializeExample.main(SerializeDeserializeExample.java:67)

`SerializeDeserializeExample`这个类中使用了对象序列化和反序列化操作时最重要的2个类：`ObjectInputStream`和`ObjectOutputStream`，其中`ObjectInputStream`类中最重要的方法为：

{% highlight java %}
public final void writeObject(Object obj) throws IOException {}
{% endhighlight %}

`ObjectInputStream`对应的方法为：

{% highlight java %}
public final Object readObject() throws IOException, ClassNotFoundException {}
{% endhighlight %}

二、SerializableObject实现java.io.Serializable接口
-----

{% highlight java %}
import java.io.Serializable;

public class SerializableObject implements Serializable {

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                '}';
    }
}
{% endhighlight %}

再次运行`SerializeDeserializeExample.main`方法后输出如下内容：

> 2016-07-27 21:41:12.266 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   56] : test java.io.Serializable
>
> 2016-07-27 21:41:12.267 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   73] : SerializableObject{name='test java.io.Serializable'}

`SerializableObject`对象被正确的序列化保存到文件，并从文件中重新加载为一个`SerializableObject`实例。

三、为SerializableObject声明serialVersionUID
-----

上面`SerializableObject`虽然实现了`java.io.Serializable`接口，但并没有设置`serialVersionUID`，jvm会为当前类自动添加一个`serialVersionUID`。

Eclipse中会出现`The serializable class SerializableObject does not declare a static final serialVersionUID field of type long`这样的警告信息，java doc中建议实现`java.io.Serializable`接口的类添加一个固定的`serialVersionUID`，避免不同的JDK实现因为`serialVersionUID`算法不同导致反序列化出现问题。

`serialVersionUID`必须使用`static`和`final`修饰，并且为`long`型的任意一个数字，并且java doc中也明确提到尽可能的将它声明为`private`。

如果在上述`SerializeDeserializeExample.main`运行成功后，再为`SerializableObject`设置一个`serialVersionUID`，如下所示：

{% highlight java %}
import java.io.Serializable;

public class SerializableObject implements Serializable {

    private static final long serialVersionUID = 1L;

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                '}';
    }
}
{% endhighlight %}

前面`SerializeDeserializeExample.main`方法运行生成的`serializableObject.ser`文件还在，执行下面的代码进行反序列化：

{% highlight java %}
public class OnlyDeserializeExample {

    private static final Logger LOGGER = LoggerFactory.getLogger(OnlyDeserializeExample.class);

    public static SerializableObject deSerialize() throws FileNotFoundException, IOException, ClassNotFoundException {
        ObjectInputStream objectInputStream = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
            objectInputStream = new ObjectInputStream(new FileInputStream(file));
            Object object = objectInputStream.readObject();
            SerializableObject deserializedObject = (SerializableObject) object;
            LOGGER.info(deserializedObject .getName());
            return deserializedObject;
        } finally {
            if (objectInputStream != null) {
                objectInputStream.close();
            }
        }
    }

    public static void main(String[] args) throws FileNotFoundException, ClassNotFoundException, IOException {
        SerializableObject deSerializedObject = deSerialize();
        LOGGER.info("{}", deSerializedObject);
    }

}
{% endhighlight %}

运行后会抛出`InvalidClassException`异常如下：

> Exception in thread "main" java.io.InvalidClassException: com.example.test.io.SerializableObject; local class incompatible: stream classdesc serialVersionUID = 8024319097192047636, local class serialVersionUID = 1
>
>   at java.io.ObjectStreamClass.initNonProxy(ObjectStreamClass.java:616)
>
>   at java.io.ObjectInputStream.readNonProxyDesc(ObjectInputStream.java:1623)
>
>   at java.io.ObjectInputStream.readClassDesc(ObjectInputStream.java:1518)
>
>   at java.io.ObjectInputStream.readOrdinaryObject(ObjectInputStream.java:1774)
>
>   at java.io.ObjectInputStream.readObject0(ObjectInputStream.java:1351)
>
>   at java.io.ObjectInputStream.readObject(ObjectInputStream.java:371)
>
>   at com.example.test.io.OnlyDeserializeExample.deSerialize(OnlyDeserializeExample.java:20)
>
>   at com.example.test.io.OnlyDeserializeExample.main(OnlyDeserializeExample.java:32)

`serialVersionUID`就是用来保证对象序列化和反序列化时使用的是相同的class文件。

四、当SerializableObject中有其他对象引用时
-----

以下`UnSerializableReference`类没有实现`java.io.Serializable`接口：

{% highlight java %}
public class UnSerializableReference {

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public String toString() {
        return "UnSerializableReference{" +
                "name='" + name + '\'' +
                '}';
    }
}
{% endhighlight %}

而`SerializableObject`引用了此对象，代码如下：

{% highlight java %}
import java.io.Serializable;

public class SerializableObject implements Serializable {

    private static final long serialVersionUID = 1L;

    private String name;

    private UnSerializableReference unSerializableReference;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public UnSerializableReference getUnSerializableReference() {
        return unSerializableReference;
    }

    public void setUnSerializableReference(UnSerializableReference unSerializableReference) {
        this.unSerializableReference = unSerializableReference;
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                ", unSerializableReference=" + unSerializableReference +
                '}';
    }
}
{% endhighlight %}

运行以下`SerializeDeserializeExample.main`方法：

{% highlight java %}
public class SerializeDeserializeExample {

    private static final Logger LOGGER = LoggerFactory.getLogger(SerializeDeserializeExample.class);

    public static void serialize(SerializableObject serializableObject) throws FileNotFoundException, IOException {
        File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
        ObjectOutputStream objectOutputStream = null;
        try {
            objectOutputStream = new ObjectOutputStream(new FileOutputStream(file));
            objectOutputStream.writeObject(serializableObject);
            objectOutputStream.flush();
        } finally {
            if (objectOutputStream != null) {
                objectOutputStream.close();
            }
        }
    }

    public static SerializableObject deSerialize() throws FileNotFoundException, IOException, ClassNotFoundException {
        ObjectInputStream objectInputStream = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
            objectInputStream = new ObjectInputStream(new FileInputStream(file));
            Object object = objectInputStream.readObject();
            SerializableObject deserializedObject = (SerializableObject) object;
            LOGGER.info(deserializedObject .getName());
            return deserializedObject;
        } finally {
            if (objectInputStream != null) {
                objectInputStream.close();
            }
        }
    }

    public static void main(String[] args) throws FileNotFoundException, ClassNotFoundException, IOException {
        SerializableObject serializableObject = new SerializableObject();
        serializableObject.setName("test java.io.Serializable");
        UnSerializableReference unSerializableReference = new UnSerializableReference();
        unSerializableReference.setName("unSerializableReference");
        serializableObject.setUnSerializableReference(unSerializableReference);
        serialize(serializableObject);

        SerializableObject deSerializedObject = deSerialize();
        LOGGER.info("{}", deSerializedObject);
    }

}
{% endhighlight %}

运行抛出`NotSerializableException`异常如下：

> Exception in thread "main" java.io.NotSerializableException: com.example.test.io.UnSerializableReference
>
>   at java.io.ObjectOutputStream.writeObject0(ObjectOutputStream.java:1184)
>
>   at java.io.ObjectOutputStream.defaultWriteFields(ObjectOutputStream.java:1548)
>
>   at java.io.ObjectOutputStream.writeSerialData(ObjectOutputStream.java:1509)
>
>   at java.io.ObjectOutputStream.writeOrdinaryObject(ObjectOutputStream.java:1432)
>
>   at java.io.ObjectOutputStream.writeObject0(ObjectOutputStream.java:1178)
>
>   at java.io.ObjectOutputStream.writeObject(ObjectOutputStream.java:348)
>
>   at com.example.test.io.SerializeDeserializeExample.serialize(SerializeDeserializeExample.java:40)
>
>   at com.example.test.io.SerializeDeserializeExample.main(SerializeDeserializeExample.java:71)

也就是说`SerializableObject`要正确的序列化，则要求其属性字段都是可序列化的，除非用`transient`修饰符明确指出当前属性不需要被一起序列化，将`SerializableObject`修改一下，在`UnSerializableReference`属性前加上`transient`修饰符：

{% highlight java %}
private transient UnSerializableReference unSerializableReference;
{% endhighlight %}

再运行以上`SerializeDeserializeExample.main`方法，输出结果如下：

> 2016-07-27 23:14:23.078 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   56] : test java.io.Serializable
>
> 2016-07-27 23:14:23.078 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   76] : SerializableObject{name='test java.io.Serializable', unSerializableReference=null}

五、手动将UnSerializableReference对象序列化和反序列化
-----

上面通过`transient`修饰符指定`UnSerializableReference`属性不需要序列化，从而避免`SerializableObject`序列化失败，但如果一定也要求将`UnSerializableReference`对象序列化，但又不能改源码的情况下，要用到以下2个重要方法，这2个方法在对象序列化和反序列化时会通过java反射来执行：

{% highlight java %}
private void writeObject(ObjectOutputStream os) throws IOException, ClassNotFoundException {}
{% endhighlight %}

{% highlight java %}
private void readObject(ObjectInputStream is) throws IOException, ClassNotFoundException {}
{% endhighlight %}

修改`SerializableObject`实现上述2个方法，注意2个方法写入和读取属性数据时保持顺序一致：

{% highlight java %}
public class SerializableObject implements Serializable {

    private static final long serialVersionUID = 1L;

    private String name;

    private transient UnSerializableReference unSerializableReference;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public UnSerializableReference getUnSerializableReference() {
        return unSerializableReference;
    }

    public void setUnSerializableReference(UnSerializableReference unSerializableReference) {
        this.unSerializableReference = unSerializableReference;
    }

    private void writeObject(ObjectOutputStream os) throws IOException, ClassNotFoundException {
        try {
            os.defaultWriteObject();
            os.writeObject(unSerializableReference.getName());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void readObject(ObjectInputStream is) throws IOException, ClassNotFoundException {
        try {
            is.defaultReadObject();
            String unSerializableReferenceName = (String) is.readObject();
            unSerializableReference = new UnSerializableReference();
            unSerializableReference.setName(unSerializableReferenceName);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                ", unSerializableReference=" + unSerializableReference +
                '}';
    }
}
{% endhighlight %}

再运行以上`SerializeDeserializeExample.main`方法，输出结果如下，`UnSerializableReference`已经正确序列化和反序列化：

> 2016-07-27 23:33:15.543 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   56] : test java.io.Serializable
>
> 2016-07-27 23:33:15.547 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   74] : SerializableObject{name='test java.io.Serializable', unSerializableReference=UnSerializableReference{name='unSerializableReference'}}

对象序列化时，writeObject方法的调用栈：

1. ObjectOutputStream.writeObject
2. ObjectOutputStream.writeObject0
3. ObjectOutputStream.writeOrdinaryObject
4. ObjectOutputStream.writeSerialData
5. ObjectStreamClass.invokeWriteObject: Invokes the writeObject method of the represented serializable class.
6. Method.invoke: private void com.example.test.io.SerializableObject.writeObject(java.io.ObjectOutputStream) throws java.io.IOException,java.lang.ClassNotFoundException

对象反序列化时，readObject方法的调用栈：

1. ObjectInputStream.readObject
2. ObjectInputStream.readObject0
3. ObjectInputStream.readOrdinaryObject
4. ObjectInputStream.readSerialData
5. ObjectStreamClass.invokeReadObject: Invokes the readObject method of the represented serializable class.
6. Method.invoke: private void com.example.test.io.SerializableObject.readObject(java.io.ObjectInputStream) throws java.io.IOException,java.lang.ClassNotFoundException

六、如果父类是可序列化的，那么子类都是可序列化的
-----

但如果父类是可序列化的，却不想子类是可序列化的，则需要重写`writeObject`和`readObject`这2个方法，在方法体内抛出`NotSerializableException`异常即可，参数名为当前`class`的名字：

{% highlight java %}
throw new NotSerializableException(getClass().getCanonicalName());
{% endhighlight %}

七、如果父类是不可序列化的，而子类实现了java.io.Serializable接口
-----

这种情况下，子类只能序列化和反序列化自己定义的属性，而父类中的属性不能被序列化和反序列化。

如下父类不能序列化：

{% highlight java %}
public class UnSerializableParent {

    private String parent;

    public String getParent() {
        return parent;
    }

    public void setParent(String parent) {
        this.parent = parent;
    }

    @Override
    public String toString() {
        return "UnSerializableParent{" +
                "parent='" + parent + '\'' +
                '}';
    }

}
{% endhighlight %}

而`SerializableObject`继承了上面的`UnSerializableParent`类：

{% highlight java %}
public class SerializableObject extends UnSerializableParent implements Serializable {

    private static final long serialVersionUID = 1L;

    private String name;

    private transient UnSerializableReference unSerializableReference;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public UnSerializableReference getUnSerializableReference() {
        return unSerializableReference;
    }

    public void setUnSerializableReference(UnSerializableReference unSerializableReference) {
        this.unSerializableReference = unSerializableReference;
    }

    private void writeObject(ObjectOutputStream os) throws IOException, ClassNotFoundException {
        try {
            os.defaultWriteObject();
            os.writeObject(unSerializableReference.getName());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void readObject(ObjectInputStream is) throws IOException, ClassNotFoundException {
        try {
            is.defaultReadObject();
            String unSerializableReferenceName = (String) is.readObject();
            unSerializableReference = new UnSerializableReference();
            unSerializableReference.setName(unSerializableReferenceName);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                ", parent='" + getParent() + '\'' +
                ", unSerializableReference=" + unSerializableReference +
                '}';
    }
}
{% endhighlight %}

修改`SerializeDeserializeExample`类如下，并运行main方法：

{% highlight java %}
public class SerializeDeserializeExample {

    private static final Logger LOGGER = LoggerFactory.getLogger(SerializeDeserializeExample.class);

    public static void serialize(SerializableObject serializableObject) throws FileNotFoundException, IOException {
        File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
        ObjectOutputStream objectOutputStream = null;
        try {
            objectOutputStream = new ObjectOutputStream(new FileOutputStream(file));
            objectOutputStream.writeObject(serializableObject);
            objectOutputStream.flush();
        } finally {
            if (objectOutputStream != null) {
                objectOutputStream.close();
            }
        }
    }

    public static SerializableObject deSerialize() throws FileNotFoundException, IOException, ClassNotFoundException {
        ObjectInputStream objectInputStream = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "serializableObject.ser");
            objectInputStream = new ObjectInputStream(new FileInputStream(file));
            Object object = objectInputStream.readObject();
            SerializableObject deserializedObject = (SerializableObject) object;
            LOGGER.info(deserializedObject .getName());
            return deserializedObject;
        } finally {
            if (objectInputStream != null) {
                objectInputStream.close();
            }
        }
    }

    public static void main(String[] args) throws FileNotFoundException, ClassNotFoundException, IOException {
        SerializableObject serializableObject = new SerializableObject();
        serializableObject.setName("test java.io.Serializable");
        serializableObject.setParent("parent is not serializable");
        UnSerializableReference unSerializableReference = new UnSerializableReference();
        unSerializableReference.setName("unSerializableReference");
        serializableObject.setUnSerializableReference(unSerializableReference);
        serialize(serializableObject);

        SerializableObject deSerializedObject = deSerialize();
        LOGGER.info("{}", deSerializedObject);
    }

}
{% endhighlight %}

结果如下，父类属性`parent`并不能被子类一起序列化和反序列化，输出结果中为`null`。

> 2016-07-28 00:12:56.640 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   56] : test java.io.Serializable
>
> 2016-07-28 00:12:56.643 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   75] : SerializableObject{name='test java.io.Serializable', parent='null', unSerializableReference=UnSerializableReference{name='unSerializableReference'}}

如果需要将父类这个属性序列化，那还是要借助`writeObject`和`readObject`这2个方法，修改`SerializableObject`类如下：

{% highlight java %}
public class SerializableObject extends UnSerializableParent implements Serializable {

    private static final long serialVersionUID = 1L;

    private String name;

    private transient UnSerializableReference unSerializableReference;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public UnSerializableReference getUnSerializableReference() {
        return unSerializableReference;
    }

    public void setUnSerializableReference(UnSerializableReference unSerializableReference) {
        this.unSerializableReference = unSerializableReference;
    }

    private void writeObject(ObjectOutputStream os) throws IOException, ClassNotFoundException {
        try {
            os.defaultWriteObject();
            os.writeObject(unSerializableReference.getName());
            os.writeObject(getParent());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void readObject(ObjectInputStream is) throws IOException, ClassNotFoundException {
        try {
            is.defaultReadObject();
            String unSerializableReferenceName = (String) is.readObject();
            unSerializableReference = new UnSerializableReference();
            unSerializableReference.setName(unSerializableReferenceName);
            String parent = (String) is.readObject();
            setParent(parent);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public String toString() {
        return "SerializableObject{" +
                "name='" + name + '\'' +
                ", parent='" + getParent() + '\'' +
                ", unSerializableReference=" + unSerializableReference +
                '}';
    }
}
{% endhighlight %}

运行结果如下，`parent`属性被序列化和反序列化了：

> 2016-07-28 00:15:25.678 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   56] : test java.io.Serializable
>
> 2016-07-28 00:15:25.682 - INFO --- [           main] c.e.test.io.SerializeDeserializeExample  [   75] : SerializableObject{name='test java.io.Serializable', parent='parent is not serializable', unSerializableReference=UnSerializableReference{name='unSerializableReference'}}

八、不能序列化static属性
-----

这个属于`class`级别，而不是对象级别的，因此不在对象序列化管理范围内。

九、序列化对单例的破坏
-----

运行以下`SerializableSingleton.main`方法：

{% highlight java %}
public class SerializableSingleton implements Serializable {

    private static final Logger LOGGER = LoggerFactory.getLogger(SerializableSingleton.class);

    private volatile static SerializableSingleton singleton;

    private SerializableSingleton() {
    }

    public static SerializableSingleton getSingleton() {
        if (singleton == null) {
            synchronized (SerializableSingleton.class) {
                if (singleton == null) {
                    singleton = new SerializableSingleton();
                }
            }
        }
        return singleton;
    }

    public static void main(String[] args) throws IOException, ClassNotFoundException {
        ObjectOutputStream oos = null;
        ObjectInputStream ois = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "serializableSingleton.ser");
            oos = new ObjectOutputStream(new FileOutputStream(file));
            //Write Obj to file
            oos.writeObject(SerializableSingleton.getSingleton());

            //Read Obj from file
            ois = new ObjectInputStream(new FileInputStream(file));
            SerializableSingleton newInstance = (SerializableSingleton) ois.readObject();

            //判断是否是同一个对象
            LOGGER.info("is the same object : {}", newInstance == SerializableSingleton.getSingleton());
        } finally {
            if (oos != null) {
                oos.close();
            }
            if (ois != null) {
                ois.close();
            }
        }
    }

}
{% endhighlight %}

结果输出为：

> 2016-07-28 00:46:29.377 - INFO --- [           main] c.example.test.io.SerializableSingleton  [   47] : is the same object : false

也就是通过对`SerializableSingleton`的序列化与反序列化操作得到的对象是一个新的对象，这就破坏了`SerializableSingleton`的单例性。

`ObjectInputStream.readOrdinaryObject()`方法如下：

{% highlight java %}
private Object readOrdinaryObject(boolean unshared) throws IOException {
    //此处省略部分代码

    Object obj;
    try {
        obj = desc.isInstantiable() ? desc.newInstance() : null;
    } catch (Exception ex) {
        throw (IOException) new InvalidClassException(
            desc.forClass().getName(),
            "unable to create instance").initCause(ex);
    }

    //此处省略部分代码

    if (obj != null && handles.lookupException(passHandle) == null && desc.hasReadResolveMethod()) {
        Object rep = desc.invokeReadResolve(obj);
        if (unshared && rep.getClass().isArray()) {
            rep = cloneArray(rep);
        }
        if (rep != obj) {
            handles.setObject(passHandle, obj = rep);
        }
    }

    return obj;
}
{% endhighlight %}

防止序列化破坏单例模式
=====

只要在`SerializableSingleton`类中定义`readResolve`方法就可以解决该问题：

{% highlight java %}
public class SerializableSingleton implements Serializable {

    private static final Logger LOGGER = LoggerFactory.getLogger(SerializableSingleton.class);

    private volatile static SerializableSingleton singleton;

    private SerializableSingleton() {
    }

    public static SerializableSingleton getSingleton() {
        if (singleton == null) {
            synchronized (SerializableSingleton.class) {
                if (singleton == null) {
                    singleton = new SerializableSingleton();
                }
            }
        }
        return singleton;
    }

    private Object readResolve() {
        return singleton;
    }

    public static void main(String[] args) throws IOException, ClassNotFoundException {
        ObjectOutputStream oos = null;
        ObjectInputStream ois = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "serializableSingleton.ser");
            oos = new ObjectOutputStream(new FileOutputStream(file));
            //Write Obj to file
            oos.writeObject(SerializableSingleton.getSingleton());

            //Read Obj from file
            ois = new ObjectInputStream(new FileInputStream(file));
            SerializableSingleton newInstance = (SerializableSingleton) ois.readObject();

            //判断是否是同一个对象
            LOGGER.info("is the same object : {}", newInstance == SerializableSingleton.getSingleton());
        } finally {
            if (oos != null) {
                oos.close();
            }
            if (ois != null) {
                ois.close();
            }
        }
    }

}
{% endhighlight %}

`readResolve`方法调用栈：

1. ObjectInputStream.readObject
2. ObjectInputStream.readObject0
3. ObjectInputStream.readOrdinaryObject
4. ObjectStreamClass.invokeReadResolve
5. Method.invoke: private java.lang.Object com.example.test.io.SerializableSingleton.readResolve()

以上代码运行结果：

> 2016-07-28 01:01:52.198 - INFO --- [           main] c.example.test.io.SerializableSingleton  [   51] : is the same object : true

十、java.io.Externalizable
-----

`java.io.Externalizable`继承自`java.io.Serializable`接口，源码：

{% highlight java %}
public interface Externalizable extends java.io.Serializable {
    void writeExternal(ObjectOutput out) throws IOException;
    void readExternal(ObjectInput in) throws IOException, ClassNotFoundException;
}
{% endhighlight %}

正如接口名字所提示的，对象序列化和反序列化由外部代码指定才会执行，`ExternalizableObject`如下代码：

{% highlight java %}
public class ExternalizableObject implements Externalizable {

    private static final Logger LOGGER = LoggerFactory.getLogger(ExternalizableObject.class);

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public void writeExternal(ObjectOutput out) throws IOException {
        LOGGER.info("begin serialize object.");
    }

    @Override
    public void readExternal(ObjectInput in) throws IOException, ClassNotFoundException {
        LOGGER.info("begin deserialize object.");
    }

    @Override
    public String toString() {
        return "ExternalizableObject{" +
                "name='" + name + '\'' +
                '}';
    }

}
{% endhighlight %}

运行`ExternalizableSerializeExample.main`方法如下：

{% highlight java %}
public class ExternalizableSerializeExample {

    private static final Logger LOGGER = LoggerFactory.getLogger(ExternalizableSerializeExample.class);

    public static void serialize(ExternalizableObject externalizableObject) throws FileNotFoundException, IOException {
        File file = new File(System.getProperty("java.io.tmpdir") + "externalizableObject.ser");
        ObjectOutputStream objectOutputStream = null;
        try {
            objectOutputStream = new ObjectOutputStream(new FileOutputStream(file));
            objectOutputStream.writeObject(externalizableObject);
            objectOutputStream.flush();
        } finally {
            if (objectOutputStream != null) {
                objectOutputStream.close();
            }
        }
    }

    public static ExternalizableObject deSerialize() throws FileNotFoundException, IOException, ClassNotFoundException {
        ObjectInputStream objectInputStream = null;
        try {
            File file = new File(System.getProperty("java.io.tmpdir") + "externalizableObject.ser");
            objectInputStream = new ObjectInputStream(new FileInputStream(file));
            Object object = objectInputStream.readObject();
            ExternalizableObject deserializedObject = (ExternalizableObject) object;
            LOGGER.info(deserializedObject.getName());
            return deserializedObject;
        } finally {
            if (objectInputStream != null) {
                objectInputStream.close();
            }
        }
    }

    public static void main(String[] args) throws IOException, ClassNotFoundException {
        ExternalizableObject externalizableObject = new ExternalizableObject();
        externalizableObject.setName("externalizableObject");

        serialize(externalizableObject);

        ExternalizableObject deSerializedObject = deSerialize();
        LOGGER.info("{}", deSerializedObject);
    }

}
{% endhighlight %}

运行结果如下，发现对象并没有被序列化：

> 2016-07-28 22:14:53.932 - INFO --- [           main] c.example.test.io.ExternalizableObject   [   30] : begin serialize object.
>
> 2016-07-28 22:14:53.937 - INFO --- [           main] c.example.test.io.ExternalizableObject   [   35] : begin deserialize object.
>
> 2016-07-28 22:14:53.938 - INFO --- [           main] c.e.t.io.ExternalizableSerializeExample  [   36] : null
>
> 2016-07-28 22:14:53.939 - INFO --- [           main] c.e.t.io.ExternalizableSerializeExample  [   51] : ExternalizableObject{name='null'}

JVM在序列化对象时，发现类实现了接口`Externalizable`，那么它会调用此对象的`writeExternal`方法，在反序列化时，则会调用readExternal方法，并不会像前面实现`Serializable`接口那样通过`ObjectOutputStream`和`ObjectInputStream`那样进行对象序列化和反序列化。

还有一点值得注意：在使用`Externalizable`进行序列化的时候，在读取对象时，会调用被序列化类的无参构造器去创建一个新的对象，然后再将被保存对象的字段的值分别填充到新对象中。所以，实现`Externalizable`接口的类必须要提供一个`public`的无参的构造器，否则会有类似以下错误提示：

> Exception in thread "main" java.io.InvalidClassException: com.example.test.io.ExternalizableObject; no valid constructor

修改`ExternalizableObject`代码如下：

{% highlight java %}
public class ExternalizableObject implements Externalizable {

    private static final Logger LOGGER = LoggerFactory.getLogger(ExternalizableObject.class);

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public void writeExternal(ObjectOutput out) throws IOException {
        LOGGER.info("begin serialize object.");
        out.writeObject(name);
    }

    /**
     * The readExternal method must read the values in the same sequence
     * and with the same types as were written by writeExternal.
     */
    @Override
    public void readExternal(ObjectInput in) throws IOException, ClassNotFoundException {
        LOGGER.info("begin deserialize object.");
        name = (String) in.readObject();
    }

    @Override
    public String toString() {
        return "ExternalizableObject{" +
                "name='" + name + '\'' +
                '}';
    }

}
{% endhighlight %}

再次运行`ExternalizableSerializeExample.main`方法后，输出如下：

> 2016-07-28 22:31:56.044 - INFO --- [           main] c.example.test.io.ExternalizableObject   [   30] : begin serialize object.
>
> 2016-07-28 22:31:56.055 - INFO --- [           main] c.example.test.io.ExternalizableObject   [   36] : begin deserialize object.
>
> 2016-07-28 22:31:56.057 - INFO --- [           main] c.e.t.io.ExternalizableSerializeExample  [   36] : externalizableObject
>
> 2016-07-28 22:31:56.058 - INFO --- [           main] c.e.t.io.ExternalizableSerializeExample  [   52] : ExternalizableObject{name='externalizableObject'}

之所以提供`Externalizable`这个接口，主要是为了定制对象的序列化和反序列化，另外也有性能方面的提升，毕竟`Serializable`会通过java反射来执行序列化和反序列化，并且会将所有引用对象都进行序列化操作。

十一、父类没有实现Externalizable

只要在`writeExternal`和`readExternal`方法里以相同的顺序写入和读取父类属性即可：

{% highlight java %}
public class ExternalizableObject extends UnSerializableParent implements Externalizable {

    private static final Logger LOGGER = LoggerFactory.getLogger(ExternalizableObject.class);

    private String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    @Override
    public void writeExternal(ObjectOutput out) throws IOException {
        LOGGER.info("begin serialize object.");
        out.writeObject(name);
        out.writeObject(getParent());
    }

    @Override
    public void readExternal(ObjectInput in) throws IOException, ClassNotFoundException {
        LOGGER.info("begin deserialize object.");
        name = (String) in.readObject();
        String parent = (String) in.readObject();
        setParent(parent);
    }

    @Override
    public String toString() {
        return "ExternalizableObject{" +
                "name='" + name + '\'' +
                ", parent='" + getParent() + '\'' +
                '}';
    }

}
{% endhighlight %}

如果父类也实现了`Externalizable`接口，则子类的2个实现方法中通过`super.writeExternal(out)`和`super.readExternal(in)`调用父类的方法实现即可。

十一、序列化的数据可以被签名和密封
-----

如果需要对整个对象进行加密和签名，可以通过使用`writeObject`和`readObject`可以实现密码加密和签名管理，但最简单的是将它放在一个`javax.crypto.SealedObject`和`java.security.SignedObject`包装器中。两者都是可序列化的，所以将对象包装在`SealedObject`中，可以围绕原对象创建一个`Wrapper`，必须有对称密钥才能解密，而且密钥必须单独管理。同样，也可以将`SignedObject`用于数据验证，并且对称密钥也必须单独管理。

References
-----

1. [Serialization in java](http://www.java2blog.com/2013/03/serialization-in-java.html)
2. [单例与序列化的那些事儿](http://www.hollischuang.com/archives/1144)
3. [Java对象的序列化与反序列化](http://www.hollischuang.com/archives/1150)

