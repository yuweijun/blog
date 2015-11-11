---
layout: post
title: "how to protect private field from reflect get and set in java"
date: "Thu Jul 17 2008 01:58:00 GMT+0800 (CST)"
categories: java
---

It is just a hack, and usally protect private field from access using security manager.

{% highlight java %}
public class ReflectField {

    protected String test = "test string in class ReflectField!";

}


/**
 * subclass of ReflectField
 *
 * 通过子类把父类的test字段隐藏掉，不让反射获取到其值
 * 正确的应该是用java的security manager来管理
 */
import java.lang.reflect.Field;

public class ReflectExtendsField extends ReflectField {

    // protected String test = "test string in class ReflectExtendsField!";
    private String test = "test string in class ReflectExtendsField!";

    public static void main(String[] args) throws Exception {
        // Class ref = Class.forName("ReflectExtendsField");
        Class ref = ReflectExtendsField.class;

        Field[] fields = ref.getFields();
        System.out.println(fields.length);
        for (Field field : fields) {
            field.setAccessible(true);
            System.out.println(field.get(ref));
        }

        Field testField = ref.getDeclaredField("test");
        testField.setAccessible(true);
        System.out.println(testField);
        System.out.println(testField.get(ref));
    }
}
{% endhighlight %}
