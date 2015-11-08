---
layout: post
title: "addevent of javascript"
date: "Sun Jul 29 2007 00:16:00 GMT+0800 (CST)"
categories: javascript
---

此方法是[Dean Edwards](http://dean.edwards.name/weblog/2005/10/add-event/)为浏览器事件绑定兼容性问题设计的。

{% highlight javascript %}
// written by Dean Edwards, 2005
// with input from Tino Zijdel, Matthias Miller, Diego Perini
function addEvent(element, type, handler) {
    if (element.addEventListener) {
        element.addEventListener(type, handler, false); // copy handler function.
    } else {
        // assign each event handler a unique ID
        if (!handler.$$guid) handler.$$guid = addEvent.guid++;
        // create a hash table of event types for the element
        if (!element.events) element.events = {};
        // create a hash table of event handlers for each element/event pair
        var handlers = element.events[type];
        if (!handlers) {
            handlers = element.events[type] = {};
            // store the existing event handler (if there is one, else this value is 'undefined')
            if (element["on" + type]) {
                //alert('exist on' + type + ' handler! copy existing event handler to handlers[0]');
                handlers[0] = element["on" + type];
            }
        }
        // store the event handler in the hash table
        handlers[handler.$$guid] = handler;
        // assign a global event handler to do all the work
        element["on" + type] = handleEvent;
    }
};
// a counter used to create unique IDs, Static variable: addEvent.guid
addEvent.guid = 1;

function removeEvent(element, type, handler) {
    if (element.removeEventListener) {
        element.removeEventListener(type, handler, false);
    } else {
        // delete the event handler from the hash table
        if (element.events && element.events[type]) {
            delete element.events[type][handler.$$guid];
        }
    }
};

function handleEvent(event) {
    var returnValue = true;
    // grab the event object (IE uses a global event object)
    event = event || fixEvent(((this.ownerDocument || this.document || this).parentWindow || window).event);
    // get a reference to the hash table of event handlers
    var handlers = this.events[event.type];
    // execute each event handler
    for (var i in handlers) {
        this.$$handleEvent = handlers[i];
        if (this.$$handleEvent(event) === false) {
            returnValue = false;
        }
    }
    return returnValue;
};

function fixEvent(event) {
    // add W3C standard event methods, IE has not those methods
    event.preventDefault = fixEvent.preventDefault;
    event.stopPropagation = fixEvent.stopPropagation;
    return event;
};
fixEvent.preventDefault = function() {
    this.returnValue = false;
};
fixEvent.stopPropagation = function() {
    this.cancelBubble = true;
};

// This little snippet fixes the problem that the onload attribute on the body-element will overwrite
// previous attached events on the window object for the onload event
if (!window.addEventListener) {
    document.onreadystatechange = function() {
        if (window.onload && window.onload != handleEvent) {
            addEvent(window, 'load', window.onload);
            window.onload = handleEvent;
        }
    }
}
{% endhighlight %}
