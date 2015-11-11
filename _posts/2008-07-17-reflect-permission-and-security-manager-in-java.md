---
layout: post
title: "reflect permission and security manager in java"
date: "Thu Jul 17 2008 13:51:00 GMT+0800 (CST)"
categories: java
---

This main method had set a security manage to this java application, in order to prevent java reflect get and set private fields.

{% highlight java %}
import java.lang.reflect.Field;
import java.lang.reflect.ReflectPermission;

public class ReflectField {

    private String test = "test string in class ReflectField!";

    public static void main(String[] args) throws Exception {
        Class ref = ReflectField.class;

        SecurityManager nsm = new SecurityManager();
        System.setSecurityManager(nsm);
        ReflectPermission rp = new ReflectPermission("suppressAccessChecks", null);
        System.out.println(rp);
        // java.security.AccessControlException:
        // access denied (java.lang.reflect.ReflectPermission suppressAccessChecks
        // nsm.checkPermission(rp);
        SecurityManager sm = System.getSecurityManager();
        System.out.println(sm);

        Field[] fields = ref.getDeclaredFields();
        System.out.println(fields.length);
        for (Field field : fields) {
            // java.security.AccessControlException:
            // access denied (java.lang.reflect.ReflectPermission suppressAccessChecks
            // field.setAccessible(true);

            // java.lang.IllegalArgumentException
            // at sun.reflect.UnsafeFieldAccessorImpl.ensureObj(UnsafeFieldAccessorImpl.java)
            // System.out.println(field.get(ref));

            System.out.println(field.getName());
        }

    }

}
{% endhighlight %}
