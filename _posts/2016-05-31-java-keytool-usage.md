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
        SSLContext sslcontext = SSLContexts.custom().loadTrustMaterial(new File("my.keystore"), "changeit".toCharArray(), new TrustSelfSignedStrategy()).build();
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

以上代码中的`my.keystore`文件是使用java自带的工具`keytool`生成的，下面介绍一下这个工具其及使用方法。

keytool
-----

`keytool`是java密钥和证书管理工具，可以用来创建包含公钥和密钥的的`keystore`(database)文件，并且利用`keystore`文件来创建只包含公钥的`truststore`文件，并发布这个`truststore`给客户端使用，`keystore`里包含两种数据：

1. 密钥实体`key entity`: 密钥`secret key`又或者是私钥和配对公钥，采用非对称加密。
2. 可信任的证书实体`trusted certificate entries`: 只包含公钥。
3. 别名`alias`: 每个`keystore`都关联这一个独一无二的`alias`，这个`alias`通常不区分大小写。

用keytool创建keystore文件
-----

创建`keystore.jks`和密钥对，密码为`changeit`，`keytool`工具的参数详细说明，参考[官方文档](http://docs.oracle.com/javase/6/docs/technotes/tools/solaris/keytool.html)：

{% highlight bash %}
$> keytool -genkey -alias server-alias -keyalg RSA -keypass changeit -storepass changeit -keystore keystore.jks
{% endhighlight %}

生成了`keystore.jks`文件之后，根据需求不同分二种证书做法，一种是CA认证的证书，一种是自签名认证的证书，先说CA证书制作：

1. 为上面的`keystore.jks`生成证书请求文件(certificate signing request)，即`CSR`文件。
2. 提交`CSR`给第三方权威`CA`机构认证，并获得有效的`CRT`证书文件。
3. 将`CRT`证书配置到tomcat，或者是nginx和apache服务器中。

CSR文件生成
-----

{% highlight bash %}
$> keytool -certreq -alias server-alias -keystore keystore.jks -keypass changeit -storepass changeit -file server.csr
{% endhighlight %}

提交此`server.csr`给第三方CA机构认证，并得到`server.crt`文件，使用下面的指令导入`truststore.jks`文件中，或者是直接配置到web服务器中。

CA证书导入
-----

导入`CRT`证书到客户端使用的`truststore.jks`公钥文件中：

{% highlight bash %}
$> keytool -import -v -trustcacerts -alias server-alias -file server.crt -keystore truststore.jks -keypass changeit -storepass changeit
{% endhighlight %}

以上httpclient-4.5 https request例子运行时，访问`https://httpbin.org/`后抛出如下错误：

> javax.net.ssl.SSLHandshakeException: sun.security.validator.ValidatorException: PKIX path building failed: sun.security.provider.certpath.SunCertPathBuilderException: unable to find valid certification path to requested target

则需要在浏览器中将对应的`https://httpbin.org/`请求域名的CA证书从浏览器里导出另存为`httpbin.org.cer`，然后用`keytool`命令导入到上述java代码指定的`my.keystore`文件中，`my.keystore`文件是一个包含公钥的`truststore`文件。

{% highlight bash %}
$> keytool -import -trustcacerts -alias httpbin.org -file httpbin.org.cer -keystore my.keystore -storepass changeit
{% endhighlight %}

自签名证书的导出
-----

有些开发环境中使用的SSL证书不需要第三方CA权威机构认证，或者不是提供给浏览器使用的，只是提供给java内部进行服务器和客户端之间相互验证，那么可以使用自签名认证的方式生成`server.crt`文件：

{% highlight bash %}
$> keytool -export -alias server-alias -storepass changeit -keystore keystore.jks -file server.crt
{% endhighlight %}

自签名证书导入到truststore文件
-----

将上面生成的`server.crt`文件导入到`cacerts.jks`文件中，这是提供给客户端使用的`truststore`文件：

{% highlight bash %}
$> keytool -import -v -trustcacerts -alias server-alias -file server.crt -keystore cacerts.jks -keypass changeit -storepass changeit
{% endhighlight %}

keytool其他命令示例
-----

#### 查看单个证书 Check a stand-alone certificate

{% highlight bash %}
$> keytool -printcert -v -file server.crt
{% endhighlight %}

#### 列出keystore存在的所有证书

{% highlight bash %}
$> keytool -list -v -keystore keystore.jks -storepass changeit
{% endhighlight %}

#### 使用别名查看keystore特定条目

{% highlight bash %}
$> keytool -list -v -keystore keystore.jks -alias server-alias -storepass changeit
{% endhighlight %}

#### 删除keystore里面指定证书

{% highlight bash %}
$> keytool -delete -alias server-alias -keystore keystore.jks -storepass changeit
{% endhighlight %}

#### 更改keysore密码

{% highlight bash %}
$> keytool -storepasswd -new new_storepassword -keystore keystore.jks
{% endhighlight %}

#### 列出信任的CA证书

{% highlight bash %}
# for Mac OS
$> export JAVA_HOME=$(/usr/libexec/java_home -v 1.8)
$> keytool -list -v -keystore $JAVA_HOME/jre/lib/security/cacerts
{% endhighlight %}

#### 导入新的CA证书到信任证书

{% highlight bash %}
$> keytool -import -trustcacerts -file /path/to/ca.crt -alias CA_ALIAS -keystore $JAVA_HOME/jre/lib/security/cacerts
{% endhighlight %}

References
-----

1. [keytool - Key and Certificate Management Tool](http://docs.oracle.com/javase/6/docs/technotes/tools/solaris/keytool.html)
2. [To Use keytool to Create a Server Certificate](https://docs.oracle.com/cd/E19798-01/821-1841/gjrgy/index.html)
3. [httpclient-4.5 custom ssl request](https://hc.apache.org/httpcomponents-client-ga/httpclient/examples/org/apache/http/examples/client/ClientCustomSSL.java)
