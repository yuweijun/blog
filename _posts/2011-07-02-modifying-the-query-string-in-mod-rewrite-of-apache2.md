---
layout: post
title: "modifying the query string in mod_rewrite of apache2"
date: "Sat Jul 02 2011 15:06:00 GMT+0800 (CST)"
categories: linux
---

RewriteRule backreferences
-----

These are backreferences of the form `$N` (0 <= N <= 9), which provide access to the grouped parts (in parentheses) of the pattern, from the RewriteRule which is subject to the current set of RewriteCond conditions..

RewriteCond backreferences
-----

These are backreferences of the form `%N` (1 <= N <= 9), which provide access to the grouped parts (again, in parentheses) of the pattern, from the last matched RewriteCond in the current set of conditions.

By default, the query string is passed through unchanged. You can, however, create URLs in the substitution string containing a query string part. Simply use a question mark inside the substitution string to indicate that the following text should be re-injected into the query string. When you want to erase an existing query string, end the substitution string with just a question mark. To combine new and old query strings, use the [QSA] flag.

{% highlight text %}
# rewrite without old query string
RewriteCond %{QUERY_STRING} ^page_id=(.*)$
RewriteRule ^index.php$ /index/%1.php? [R=301,L]

# rewrite with new query string
RewriteCond %{QUERY_STRING} ^page_id=(.*)$
RewriteRule ^index.php$ /index/%1.php\?test=1 [R=301,L]

# rewrite with combined old query string
RewriteCond %{QUERY_STRING} ^page_id=(.*)$
RewriteRule ^index.php$ /index/%1.php\?test=1 [QSA,R=301,L]
{% endhighlight %}
