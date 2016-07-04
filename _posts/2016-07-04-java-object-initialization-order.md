---
layout: post
title: "java object initialization order"
date: Mon, 04 Jul 2016 22:15:18 +0800
categories: java
---

存在继承关系的java对象初始化顺序
-----

1. 执行父类静态域和静态初始化块。
2. 执行子类静态域和静态初始化块。
3. 执行父类非静态的实例域和实例初始化块。
4. 执行父类的构造器方法。
5. 执行子类非静态的实例域和实例初始化块。
6. 执行子类的构造器方法。

示例
-----

{% highlight java %}
public class ObjectInitializeOrder {

    public static void main(String[] args) throws Exception {
        new SonClass();
    }

}

class FatherClass {

    private static final Logger LOGGER = LoggerFactory.getLogger(FatherClass.class.getSimpleName());

    // 实例属性fatherInstanceVariable和instance block中的instanceBlockInitial()是相同优先级的，看代码顺序
    public int fatherInstanceVariable = fatherInstanceVariableInitial();

    public static int fatherStaticVariable = fatherStaticVariableInitial();

    {
        instanceBlockInitial();
    }

    // 这里static的fatherStaticVariable和staticBlockInitial()要看代码顺序，哪个在前就先招待哪个，优先级相同
    static {
        staticBlockInitial();
    }

    public FatherClass() {
        LOGGER.info("4. FatherClass Constructor invoked by : {}", this.getClass().getSimpleName());
    }

    public int fatherInstanceVariableInitial() {
        LOGGER.info("3. father public instance variable initialized in {} and by : {}", FatherClass.class.getSimpleName(), this.getClass().getSimpleName());
        return 0;
    }

    public static int fatherStaticVariableInitial() {
        LOGGER.info("1. father public static variable initialized in : {}", FatherClass.class.getSimpleName());
        return 1;
    }

    public void instanceBlockInitial() {
        LOGGER.info("3. father instance block initialized in {} and by : {}", FatherClass.class.getSimpleName(), this.getClass().getSimpleName());
    }

    public static void staticBlockInitial() {
        LOGGER.info("1. father static block initialized in : {}", FatherClass.class.getSimpleName());
    }

}

class SonClass extends FatherClass {

    private static final Logger LOGGER = LoggerFactory.getLogger(SonClass.class.getSimpleName());

    public int sonInstanceVariable = sonInstanceVariableInitial();

    public static int sonStaticVariable = sonStaticVariableInitial();

    {
        sonInstanceBlockInitial();
    }

    static {
        sonStaticBlockInitial();
    }

    public SonClass() {
        LOGGER.info("6. SonClass Constructor invoked by : {}", this.getClass().getSimpleName());
    }

    public int sonInstanceVariableInitial() {
        LOGGER.info("5. son public instance variable initialized in : {}", this.getClass().getSimpleName());
        return 0;
    }

    public static int sonStaticVariableInitial() {
        LOGGER.info("2. son public static variable initialized in : {}", SonClass.class.getSimpleName());
        return 1;
    }

    public void sonInstanceBlockInitial() {
        LOGGER.info("5. son instance block initialized in : {}", this.getClass().getSimpleName());
    }

    public static void sonStaticBlockInitial() {
        LOGGER.info("2. son static block initialized in : {}", SonClass.class.getSimpleName());
    }

}
{% endhighlight %}

运行结果
-----

> 1\. father public static int {{fatherStaticVariable}} initialized in : FatherClass
>
> 1\. father static block initialized in : FatherClass
>
> 2\. son public static int {{sonStaticVariable}} initialized in : SonClass
>
> 2\. son static block initialized in : SonClass
>
> 3\. father public int {{fatherInstanceVariable}} initialized in FatherClass and by : SonClass
>
> 3\. father instance block initialized in FatherClass and by : SonClass
>
> 4\. FatherClass Constructor invoked by : SonClass
>
> 5\. son public int {{sonInstanceVariable}} initialized in : SonClass
>
> 5\. son instance block initialized in : SonClass
>
> 6\. SonClass Constructor invoked by : SonClass

对象初始化的几点说明
-----

1. 类的每个基本类型数据成员（包括static成员）都会有一个默认初始值，对象的话为特殊值null。
2. 无论创建多少个对象，相同类的静态数据都只占用一份存储区域。
3. 静态域和静态块初始化只在Class对象首次加载的时候进行一次。
4. 虽然没有显示地使用static关键字，构造器实际上也是静态方法。
5. 实例初始化块是在构造器之前执行的。
6. 创建类的第一个对象，或者是类的static域和方法被访问时，会触发类加载。
7. 如果加载的类有基类，则会加载基类。
8. 基类的构造器会被自动调用。

References
-----

1. [Object initialization in Java](http://www.javaworld.com/article/2076614/core-java/object-initialization-in-java.html)
2. [Creation of New Class Instances](http://docs.oracle.com/javase/specs/jls/se8/html/jls-12.html#jls-12.5)
