---
layout: post
title: "java静态成员类与非静态成员类的区别"
date: "Tue Nov 04 2008 13:52:00 GMT+0800 (CST)"
categories: java
---

java静态成员类与非静态成员类的区别
-----

{% highlight java %}
/**
 * 静态成员类与非静态成员类的区别
 */
public class StaticMemberType {

    // Interfaces, enumerated types, 和annotation types 无论是否声明static，它们都是static的。
    static class StaticInnerClass {
        public void test() {
            System.out.println("Static Nested Class Method.");

        }
    }

    public void test() {
        new NonStaticInnerClass().test();
    }

    public class NonStaticInnerClass {
        // 非静态成员类不可以包含任何static字段、methods或者类型，除非同时使用了static和final的常量字段之外。
        // static String CONST1 = "TEST"; // error
        final static String CONST2 = "TEST";
        public void test() {
            System.out.println("Non Static Inner Class.");
        }
    }

    public static void main(String[] args) {
        new StaticMemberType.StaticInnerClass().test();

        StaticMemberType staticMemberType = new StaticMemberType();
        staticMemberType.test();
        // new StaticMemberType.NonStaticInnerClass().test(); // error
    }
}
{% endhighlight %}
