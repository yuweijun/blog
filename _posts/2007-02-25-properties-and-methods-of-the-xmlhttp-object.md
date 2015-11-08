---
layout: post
title: "properties and methods of the xmlhttp object"
date: "Sun Feb 25 2007 13:54:00 GMT+0800 (CST)"
categories: javascript
---

The techniques we’ve covered so far use standard browser features for purposes other than that for which they were intended. As such, they lack many features you might want out of RPC-over-HTTP, such as the ability to check HTTP return codes and to specify username/password authentication information for requests. Modern browsers let you do JavaScript RPCs in a much cleaner, more elegant fashion with a flexible interface supporting the needed features missing from the previously discussed hacks.
Internet Explorer 5 and later support the XMLHTTP object and Mozilla-based browsers provide an XMLHTTPRequest object. These objects allow you to create arbitrary HTTP requests (including POSTs), send them to a server, and read the full response, including headers. Table 19-1 shows the properties and methods of the XMLHTTP object.

Properties and Methods of the XMLHTTP Object
-----

`readyState` Integer indicating the state of the request, either ` 0 (uninitialized), 1 (loading), 2 (response headers received), 3 (some response body received), or 4 (request complete).

`Onreadystatechange` Function to call whenever the readyState changes.

`status` HTTP status code returned by the server (e.g., “200”).

`statusText` Full status HTTP status line returned by the server (e.g., “200 OK”).

`responseText` Full response from the server as a string.

`responseXML` A Document object representing the server's response parsed as an XML document.

`abort()` Cancels an asynchronous HTTP request.

`getAllResponseHeaders()` Returns a string containing all the HTTP headers the server sent in its response. Each header is a name/value pair separated by a colon, and header lines are separated by a carriage return/linefeed pair.

`getResponseHeader(headerName)` Returns a string corresponding to the value of the headerName header returned by the server (e.g., request.getResponseHeader("Set-cookie")).

`open(method, url [, asynchronous[, user, password]])` Initializes the request in preparation for sending to the server. The method parameter is the HTTP method to use, for example, GET or POST. The url is the URL the request will be sent to. The optional asynchronous parameter indicates whether send() returns immediately or after the request is complete (default is true, meaning it returns immediately). The optional user and password arguments are to be used if the URL requires HTTP authentication. If no parameters are specified by the URL requiring authentication, the user will be prompted to enter it.

`setRequestHeader(name, value)` Adds the HTTP header given by the name (without the colon) and value parameters.

`send(body)` Initiates the request to the server. The body parameter should contain the body of the request, i.e., a string containing fieldname=value&fieldname2=value2… for POSTs or the empty string ("") for GETs.

Creating and Sending Requests
-----

XMLHTTP requests can be either synchronous or asynchronous, as specified by the optional third parameter to open(). The send() method of a synchronous request will return only once the request is complete, that is, the request completes “while you wait.” The send() method of an asynchronous request returns immediately, and the download happens in the background. In order to see if an asynchronous request has completed, you need to check its readyState. The advantage of an asynchronous request is that your script can go on to other things while it is made and the response received, for example, you could download a bunch of requests in parallel.

To create an XMLHTTP object in Mozilla-based browsers, you use the XMLHttpRequest constructor:

{% highlight javascript %}
var xmlhttp = new XMLHttpRequest();
// In IE, you instantiate a new MSXML XHMLHTTP ActiveX object:
var xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
{% endhighlight %}

Once you have an XMLHTTP object, the basic usage for synchronous requests is
Parameterize the request with open().
Set any custom headers you wish to send with setRequestHeader().
Send the request with send().
Read the response from one of the response-related properties.
The following example illustrates the concept:

{% highlight javascript %}
if (document.all)
    var xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
else
    var xmlhttp = new XMLHttpRequest();

xmlhttp.open("GET", "http://www.example.com/somefile.html", false);
xmlhttp.send("");
alert("Response code was: " + xmlhttp.status)
{% endhighlight %}

The sequence of steps for an asynchronous request is similar:
Parameterize the request with open().
Set any custom headers you wish to send with setRequestHeader().
Set the onreadystatechange property to a function to be called when the request is complete.
Send the request with send().
The following example illustrates an asynchronous request:

{% highlight javascript %}
if (document.all)
    var xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
else
    var xmlhttp = new XMLHttpRequest();

xmlhttp.open("GET", window.location);
xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4)
    alert("The text of this page is: " + xmlhttp.responseText);
};

xmlhttp.setRequestHeader("Cookie", "FakeValue=yes");
xmlhttp.send("");
{% endhighlight %}

When working with asynchronous requests, you don’t have to use the onreadystatechange handler. Instead, you could periodically check the request’s readyState for completion.

POSTs
-----

You can POST form data to a server in much the same way as issuing a GET. The only differences are using the POST method and setting the content type of the request appropriately (i.e., to “application/x-www-form-urlencoded”).

{% highlight javascript %}
var formData = "username=billybob&password=angelina5";
var xmlhttp = null;
if (document.all)
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
else if (XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();

if (xmlhttp) {
    xmlhttp.open("POST", "http://demos.javascriptref.com/xmlecho.php", false);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send(formData);
    document.write("<hr>" + xmlhttp.responseText + "<hr>");
}
{% endhighlight %}
