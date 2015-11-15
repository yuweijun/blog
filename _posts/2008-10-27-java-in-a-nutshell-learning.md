---
layout: post
title: "java in a nutshell 泛型学习笔记"
date: "Mon Oct 27 2008 22:49:00 GMT+0800 (CST)"
categories: java
---

泛型的类型变量只可以作用于该类中的实例成员，而不可以用于静态成员。但是，跟实例方法一样，静态方法也可以使用通配符，不过无法使用所处泛型类的泛型实例变量，但可以声明自己的类型变量。

当一个方法（不管是实例方法还是静态方法）只要明它自己的类型变量，这个方法就是一个`泛型方法`(generic method)。

{% highlight java %}
import java.util.ArrayList;
import java.util.List;

public class Tree<V> {

    V value;

    List<Tree<? extends V>> branches = new ArrayList<Tree<? extends V>> ();

    public Tree(V value) {
        this.value = value;
    }

    V getValue() {
        return this.value;
    }

    void setValue(V value) {
        this.value = value;
    }

    int getNumBranches() {
        return this.branches.size();
    }

    Tree <? extends V> getBranch(int n) {
        return branches.get(n);
    }

    void addBranch(Tree<V> branch) {
        branches.add(branch);
    }

    // public static <N extends V> V sum(Tree<N> t) {}
    // can not make a static reference to the non-static type V
    // 静态方法无法使用它们所在类的类型变量，但它们可以声明自己的类型变量
    // 静态成员不能使用泛型的类型变量
    // 这个不是 generic method
    public static double sum(Tree <? extends Number> t) {
        double total = t.value.doubleValue();
        for (Tree <? extends Number > b: t.branches)
            total += sum(b);
        return total;
    }

    // 当类型变量是用来表示两个参数或一个参数与一个返回值之间的关系时，就需要使用到generic method
    // 在方法返回值前面先声明自己的类型变量，如下的N。
    public static <N extends Number> Tree <N> max(Tree<N> t, Tree<N> u) {
        double ts = sum(t);
        double us = sum(u);
        if (ts > us)
            return t;
        else
            return u;
    }

    public static void main(String[] args) {
        Tree<String> s1 = new Tree<String>("Tree");
        Tree<String> s2 = new Tree<String>("Tree");
        Tree<? extends Number> i1 = new Tree<Integer>(3);
        Tree<? extends Number> i2 = new Tree<Integer>(3);
        s1.addBranch(s2);

        // i1.addBranch(i2); // compile error.
        System.out.println(i1.getValue());
        System.out.println(s1.getValue());
        System.out.println(i1.getNumBranches());
        System.out.println(s1.getNumBranches());
    }
}
{% endhighlight %}
