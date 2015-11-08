---
layout: post
title: "hierarchy of javascript and java"
date: "Sat Mar 17 2007 18:26:00 GMT+0800 (CST)"
categories: javascript
---

简单举例说明java和javascript在对象继承上的差别，javascript是原型继承，java甪是类继承：

|Java               |JavaScript     |
|:----------------- |:------------- |
|Strongly-typed     |Loosely-typed  |
|Static             |Dynamic        |
|Classical          |Prototypal     |
|Classes            |Functions      |
|Constructors       |Functions      |
|Methods            |Functions      |

JavaScript
-----

{% highlight javascript %}
function Manager () {
    this.reports = [];
}
Manager.prototype = new Employee;
function WorkerBee () {
    this.projects = [];
}
WorkerBee.prototype = new Employee;
{% endhighlight %}

Java
-----

{% highlight java %}
public class Manager extends Employee {
   public Employee[] reports;
   public Manager () {
      this.reports = new Employee[0];
   }
}
public class WorkerBee extends Employee {
   public String[] projects;
   public WorkerBee () {
      this.projects = new String[0];
   }
}
{% endhighlight %}
