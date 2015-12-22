---
layout: post
title: "WADL简介"
date: "Tue, 22 Dec 2015 15:31:35 +0800"
categories: web
---

## 什么是WADL

`WADL`(Web Application Description Language)是为不同的`web应用`之间提供数据交互的一种协议描述格式，通过`WADL`协议说明，使得基于HTTP的`web应用`之间可以进行数据交互。

### Web Applications

`web应用`被定义为一种基于HTTP的应用，它们的交互应该能够被计算机处理，然而大多数已经存在的web站点都需要人来识别它们的功能。典型的`web应用`有以下特点：

1. 基于已经存在的web架构和基础结构
1. 不依赖于特定的平台和编程语言
1. 促进了应用的重用（不仅限于浏览器）
1. 能够和其他的Web应用或桌面应用集成
1. 使用它们的过程中交换的内容（表象）有明确的语义

最后一个必须遵守的要求是使用自描述的数据格式，比如`XML`或`JSON`。`XML`尤其适合，因为它允许在特定的应用领域定义特定的模式（complete custom schema）或者利用扩展点把特定的格式片段（custom micro-format）嵌入到一个已经存在的模式。

鉴于上面`web应用`的定义，我们能够看出一个应用的下面几个方面能够被机器可处理的格式有效地描述：

1. 资源的集合：类似在网站站点上提供的资源
1. 资源之间的关系：描述资源之间的联系，即引用和因果（链接）
1. 适用于每个资源的方法（Unique Interface）：适用于所有资源的HTTP方法，期望的输入输出以及支持的格式
1. 资源表象的格式：所支持的MIME类型和数据模式（XMLSchema）的使用

### 何时使用WADL

`WADL`的目的是在web应用之间建立一个`契约`，此`契约`说明了应用之间如何来调用对方的数据，有点像是基于XML或JSON的`RPC`，也在某种程度上实现了类似linux命令行中管道符`|`的作用。

1. 如果需要开发的web应用不需要与已有的基于HTTP的应用进行数据交互，那么就用不到`WADL`。
2. 如果应用可以很容易集成到已有系统中，并且和开发团队可以密切沟通合作的，也不需要`WADL`。
3. 如果需要和很多复杂的，不同部门，甚至不同公司的第三方web应用整合，或者数据交互时，那么此时就需要一份严谨的`WADL`标准说明。
4. 如果需要整合一些不再维护的遗留项目，需要找到能与其数据进行交互的合适方式，并提供一份`WADL`说明供新应用调用。

### WADL和WSDL的区别

`WSDL 1.1`(Web Services Description Language 1.1)是一个`W3C`的推荐标准，一般通过`SOAP`协议提供服务，也可通过`SMTP`和`HTTP`协议提供服务，但是除了`GET`和`POST`之外，不支持其他HTTP动词。而`REST`服务提供的请求中需要使用到其他HTTP动词，如`PUT`，`DELETE`，所以`WSDL 1.1`用于提供`REST`服务，并不是很好的选择。

`WSDL 2.0`之后，开始支持全部HTTP动词，可以用之于提供`REST`服务。

`WADL`是`Sun Microsystems`提议的一个标准，在提供`REST`服务时，相比`WSDL`而言，其更轻量级，更易于理解和编写。

### WADL示例

以下为一个`WADL`文档示例，描述了Amazon的`Item Search`服务：

{% highlight xml %}
<application xmlns="http://wadl.dev.java.net/2009/02"
   xmlns:aws="http://webservices.amazon.com/AWSECommerceService/2005-07-26"
   xmlns:xsd="http://www.w3.org/2001/XMLSchema"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://wadl.dev.java.net/2009/02 wadl.xsd">

   <grammars>
     <include href="AWSECommerceService.xsd"/>
   </grammars>

   <resources base="http://webservices.amazon.com/onca/">
     <resource path="xml">
       <method href="#ItemSearch"/>
     </resource>
   </resources>

   <method name="GET" id="ItemSearch">
     <request>
       <param name="Service" style="query" fixed="AWSECommerceService"/>
       <param name="Version" style="query" fixed="2005-07-26"/>
       <param name="Operation" style="query" fixed="ItemSearch"/>
       <param name="SubscriptionId" style="query" type="xsd:string" required="true"/>
       <param name="SearchIndex" style="query" type="aws:SearchIndexType" required="true">
          <option value="Books"/>
          <option value="DVD"/>
          <option value="Music"/>
       </param>
       <param name="Keywords" style="query" type="aws:KeywordList" required="true"/>
       <param name="ResponseGroup" style="query" type="aws:ResponseGroupType" repeating="true">
          <option value="Small"/>
          <option value="Medium"/>
          <option value="Large"/>
          <option value="Images"/>
       </param>
     </request>
     <response>
       <representation mediaType="text/xml" element="aws:ItemSearchResponse"/>
     </response>
   </method>
 </application>
{% endhighlight %}

如上例所示，这份自描述`WADL`文档很清楚的说明了功能，调用方式，请求参数及其类型，返回结果的说明，很容易理解和调用。

References
-----

1. [Web Application Description Language](http://www.w3.org/Submission/wadl/)
2. [Learn REST: A Tutorial](http://rest.elkstein.org/2008/02/documenting-rest-services-wsdl-and-wadl.html)
3. [WADL 简介](http://blog.chinaunix.net/uid-9789791-id-1997442.html)
4. [What is the reason for using WADL](http://stackoverflow.com/questions/1312087/what-is-the-reason-for-using-wadl)
5. [Web Services Description Language (WSDL) 1.1](http://www.w3.org/TR/wsdl)
