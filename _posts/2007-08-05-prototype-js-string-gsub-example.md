---
layout: post
title: "prototype.js string.gsub example"
date: "Sun Aug 05 2007 19:34:00 GMT+0800 (CST)"
categories: javascript
---

prototype.js中`String.gsub`源码学习。

{% highlight javascript %}
 // Ruby String.gsub
 // In the block form, the current match is passed in as a parameter,
 // and variables such as $1, $2, $`, $&, and $' will be set appropriately.
 // The value returned by the block will be substituted for the match on each call.
 Object.extend(String.prototype, {
     gsub: function(pattern, replacement) {
         var result = '',
             source = this,
             match;
         replacement = arguments.callee.prepareReplacement(replacement);
         console.log(replacement);
         while (source.length > 0) {
             if (match = source.match(pattern)) {
                 result += source.slice(0, match.index);
                 result += String.interpret(replacement(match));
                 // pass match (object) to replacement(function),
                 // return value replace match[0]...
                 // origin Template.evaluate function recursive invoke String.gsub function
                 source = source.slice(match.index + match[0].length);
             } else {
                 result += source, source = '';
             }
         }
         return result;
     },

     sub: function(pattern, replacement, count) {
         replacement = this.gsub.prepareReplacement(replacement);
         count = count === undefined ? 1 : count;

         return this.gsub(pattern, function(match) {
             //alert(match.index);
             //alert(match[0]);
             if (--count < 0) return match[0];
             return replacement(match);
         });
     }
 });

 String.prototype.gsub.prepareReplacement = function(replacement) {
     if (typeof replacement == 'function') return replacement;
     var template = new Template(replacement);
     return function(match) {
         return template.evaluate(match)
     };
 }
{% endhighlight %}

example
-----

{% highlight javascript %}
 var Template = Class.create();
 Template.prototype = {
     initialize: function(template, pattern) {
         alert(template.toString());
         this.template = template.toString();
     },

     evaluate: function(match_object) {
         return '<font color="red">' + this.template + '</font>';
     }
 }

 var str = 'ThereAreTest1AndTest2AndTest3.';
 var txt = str.gsub(/test/i, 'Text');
 console.log(txt);

 var txt2 = str.sub(/test/i, 'Text', 2);
 console.log(txt2);
{% endhighlight %}
