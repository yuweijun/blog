---
layout: post
title: "java keytool命令和httpclient-4.5 https请求示例"
date: "Tue, 31 May 2016 12:51:40 +0800"
categories: java
---

以下http client 4.5版本实现https请求的[例子](https://hc.apache.org/httpcomponents-client-ga/httpclient/examples/org/apache/http/examples/client/ClientCustomSSL.java)，实际运行过程中会碰到一些问题。

{% highlight java %}
package org.apache.http.examples.client;

import java.io.File;

import javax.net.ssl.SSLContext;

import org.apache.http.HttpEntity;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.conn.ssl.SSLConnectionSocketFactory;
import org.apache.http.conn.ssl.TrustSelfSignedStrategy;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.ssl.SSLContexts;
import org.apache.http.util.EntityUtils;

/**
 * This example demonstrates how to create secure connections with a custom SSL context.
 */
public class ClientCustomSSL {

    public final static void main(String[] args) throws Exception {
        // Trust own CA and all self-signed certs
        SSLContext sslcontext = SSLContexts.custom().loadTrustMaterial(new File("my.keystore"), "nopassword".toCharArray(), new TrustSelfSignedStrategy()).build();
        // Allow TLSv1 protocol only
        SSLConnectionSocketFactory sslsf = new SSLConnectionSocketFactory(sslcontext, new String[] { "TLSv1" }, null, SSLConnectionSocketFactory.getDefaultHostnameVerifier());
        CloseableHttpClient httpclient = HttpClients.custom().setSSLSocketFactory(sslsf).build();
        try {

            HttpGet httpget = new HttpGet("https://httpbin.org/");
            System.out.println("Executing request " + httpget.getRequestLine());
            CloseableHttpResponse response = httpclient.execute(httpget);
            try {
                HttpEntity entity = response.getEntity();

                System.out.println("----------------------------------------");
                System.out.println(response.getStatusLine());
                EntityUtils.consume(entity);
            } finally {
                response.close();
            }
        } finally {
            httpclient.close();
        }
    }

}
{% endhighlight %}

以上代码中的`my.keystore`文件是使用`keytool`生成的。`keytool`是密钥和证书管理工具，`keystore`中包含二种数据，它使用户能够管理自己的公钥/私钥对`key`及相关证书`certificates`，通过数字签名自我认证或者是用户向别的用户/服务认证自己，它还允许用户储存他们的通信对等者的公钥（以证书形式）。

用keytool创建keystore文件
-----

创建keystore和密钥对(密码为nopassword)

{% highlight bash %}
$> keytool -genkey -alias com.example -keyalg RSA -keystore my.keystore -keysize 2048
{% endhighlight %}

CSR文件生成
-----

为存在的keystore生成证书请求文件`CSR(certificate signing request)`，正常会提交此文件给第三方权威的CA认证机构进行认证，并返回一份认证有效的`CRT`证书文件，然后加证书加入到`tomcat`或者是`apache`服务器配置中。

{% highlight bash %}
$> keytool -certreq -alias com.example -keystore my.keystore -file com.example.csr
{% endhighlight %}

CA证书导入
-----

导入SSL服务器证书到keystore

{% highlight bash %}
$> keytool -import -trustcacerts -alias com.example -file com.example.crt -keystore my.keystore -storepass nopassword
{% endhighlight %}

以上httpclient-4.5 https request例子运行时，访问`https://httpbin.org/`后抛出如下错误：

> javax.net.ssl.SSLHandshakeException: sun.security.validator.ValidatorException: PKIX path building failed: sun.security.provider.certpath.SunCertPathBuilderException: unable to find valid certification path to requested target

则需要在浏览器中将对应的`https://httpbin.org/`请求域名的CA证书从浏览器里导出另存为`httpbin.org.cer`，然后用keytool命令导入到java keystore文件中。

{% highlight bash %}
$> keytool -import -trustcacerts -alias httpbin.org -file httpbin.org.cer -keystore my.keystore -storepass nopassword
{% endhighlight %}

CRT证书的导出
-----

{% highlight bash %}
$> keytool -export -alias com.example -keystore my.keystore -file com.example.crt -storepass nopassword
{% endhighlight %}

为存在的keystore生成自签名证书
-----

Generate a keystore and self-signed certificate

{% highlight bash %}
$> keytool -genkey -keyalg RSA -alias selfsigned -keystore my.keystore -storepass nopassword -validity 360 -keysize 2048
{% endhighlight %}

keytool查看命令
-----

查看单个证书 Check a stand-alone certificate
=====

{% highlight bash %}
$> keytool -printcert -v -file com.example.crt
{% endhighlight %}

列出keystore存在的所有证书
=====

{% highlight bash %}
$> keytool -list -v -keystore my.keystore -storepass nopassword
{% endhighlight %}

使用别名查看keystore特定条目
=====

{% highlight bash %}
$> keytool -list -v -keystore my.keystore -alias com.example -storepass nopassword
{% endhighlight %}

删除keystore里面指定证书
-----

{% highlight bash %}
$> keytool -delete -alias com.example -keystore my.keystore -storepass nopassword
{% endhighlight %}

更改keysore密码
-----

{% highlight bash %}
$> keytool -storepasswd -new new_storepass -keystore my.keystore
{% endhighlight %}

列出信任的CA证书
-----

{% highlight bash %}
$> keytool -list -v -keystore $JAVA_HOME/jre/lib/security/cacerts
{% endhighlight %}

导入新的CA到信任证书
{% highlight bash %}
$> keytool -import -trustcacerts -file /path/to/ca/ca.pem -alias CA_ALIAS -keystore $JAVA_HOME/jre/lib/security/cacerts
{% endhighlight %}

References
-----

1. [httpclient-4.5 custom ssl request](https://hc.apache.org/httpcomponents-client-ga/httpclient/examples/org/apache/http/examples/client/ClientCustomSSL.java)
2. [Tomcat SSL Installation Instructions](https://www.sslshopper.com/tomcat-ssl-installation-instructions.html)
3. [The Most Common Java Keytool Keystore Commands](https://www.sslshopper.com/article-most-common-java-keytool-keystore-commands.html)
4. [常用的Java Keytool Keystore命令](https://www.chinassl.net/ssltools/keytool-commands.html)
5. [那些证书相关的玩意儿(SSL,X.509,PEM,DER,CRT,CER,KEY,CSR,P12等)](http://www.cnblogs.com/guogangj/p/4118605.html)
