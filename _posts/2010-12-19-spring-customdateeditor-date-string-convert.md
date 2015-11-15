---
layout: post
title: "spring的属性编辑器customdateeditor及日期对象转化"
date: "Sun Dec 19 2010 15:36:00 GMT+0800 (CST)"
categories: java
---

在spring mvc的Controller中，属性在通过依赖注入(DI)时，普通数据类型都能够辨识。但诸如`Date`之类，就需要自定义`属性编辑器`解决。否则报如下错误：

{% highlight text %}
org.springframework.beans.TypeMismatchException:
Failed to convert property value of type [java.lang.String] to required type
[java.util.Date] for property 'date'; nested exception is java.lang.IllegalArgumentException: Cannot convert value of type [java.lang.String] to required type [java.util.Date] for property 'date': no matching editors or conversion strategy found
{% endhighlight %}

这表示spring无法找到合适的转换策略，需要自己写一个转换器，在spring中称之为`属性编辑器`。

spring中的`属性编辑器`可以将字符串转换为相应的对象，然后注入到其它对象中去。

编写自己的`属性编辑器`的步骤很简单，`属性编辑器`类需要从`java.beans.PropertyEditorSupport`类继承，在这个类中有一个`setAsText`方法，这个方法有一个`String`类型的参数，通过这个方法，可以将`String`类型的参数值转换成其他类型的属性。在这个方法中我们还需要使用一个`setValue`方法，就来指定转换后的对象实例。

spring中有个`CustomDateEditor`的类就是继承`PropertyEditorSupport`的一个属性编辑器，在Controller中添加一个`@InitBinder`的Annotation到某个方法上，在方法中指明日期字符串的格式，就可以将符合此格式的字符串转化为日期对象，代码如下：


{% highlight java %}
/**
 * <pre>
 * HTML forms work with string values only, when your Authority is a complex bean. You need to configure a PropertyEditor to perform conversion between Authority and String:
 *
 * @InitBinder
 * public void initBinder(WebDataBinder b) {
 *     b.registerCustomEditor(Authority.class, new AuthorityEditor());
 * }
 *
 * private class AuthorityEditor extends PropertyEditorSupport {
 *     @Override
 *     public void setAsText(String text) throws IllegalArgumentException {
 *         // 另外一个例子是根据字符串，从数据库中查找返回对象
 *         setValue(authorityService.findById(Long.valueOf(text)));
 *     }
 *
 *     @Override
 *     public String getAsText() {
 *         return ((Authority) getValue()).getId();
 *     }
 * }
 * </pre>
 *
 * 这个方法用来将页面表单上传的Date字符串转化成java的Date对象
 *
 * @param binder
 */
@InitBinder
public void initBinder(WebDataBinder binder) {
    DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
    dateFormat.setLenient(false);
    binder.registerCustomEditor(Date.class, new CustomDateEditor(dateFormat, false));
}
{% endhighlight %}

References
-----

1. [how to pass a date into bean property](http://www.mkyong.com/spring/spring-how-to-pass-a-date-into-bean-property-customdateeditor/)
