---
layout: post
title: "send mail through gmail smtp using javamail"
date: "Sun Nov 16 2008 14:39:00 GMT+0800 (CST)"
categories: java
---

利用gmail smtp和javamail发送邮件。

{% highlight java %}
import java.security.Security;
import java.util.Properties;

import javax.mail.Authenticator;
import javax.mail.Message;
import javax.mail.PasswordAuthentication;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeMessage;

import com.sun.net.ssl.internal.ssl.Provider;

public class GmailSend {
    private String mailhost = "smtp.gmail.com";

    public synchronized void sendMail(String subject, String body, String sender, String recipients) throws Exception {

        Security.addProvider(new Provider());

        Properties props = new Properties();
        props.setProperty("mail.transport.protocol", "smtp");
        props.setProperty("mail.host", "smtp.gmail.com");
        props.put("mail.smtp.auth", "true");
        props.put("mail.smtp.port", "465");
        props.put("mail.smtp.socketFactory.port", "465");
        props.put("mail.smtp.socketFactory.class", "javax.net.ssl.SSLSocketFactory");
        props.put("mail.smtp.socketFactory.fallback", "false");
        props.setProperty("mail.smtp.quitwait", "false");

        Session session = Session.getDefaultInstance(props, new Authenticator() {
            protected PasswordAuthentication getPasswordAuthentication() {
            return new PasswordAuthentication("test.yu@gmail.com", "****password****");
            }
            });

        MimeMessage message = new MimeMessage(session);
        message.setSender(new InternetAddress(sender));
        message.setSubject(subject);
        message.setContent(body, "text/plain");
        if (recipients.indexOf(',') > 0)
            message.setRecipients(Message.RecipientType.TO, InternetAddress.parse(recipients));
        else
            message.setRecipient(Message.RecipientType.TO, new InternetAddress(recipients));

        Transport.send(message);
        System.out.println("finished!");
    }

    public static void main(String args[]) throws Exception {
        GmailSend gmailSend = new GmailSend();
        gmailSend.sendMail("send from javamail", "send form javamail by java programmng.", "test@gmail.com", "test@gmail.com");
    }

}
{% endhighlight %}
