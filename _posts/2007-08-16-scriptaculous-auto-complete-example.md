---
layout: post
title: "scriptaculous auto complete example"
date: "Thu Aug 16 2007 11:53:00 GMT+0800 (CST)"
categories: javascript
---

scriptaculous.js自动完成功能测试。

CSS源码
-----

{% highlight css %}
div.autocomplete {
    position:absolute;
    width:250px;
    background-color:white;
    border:1px solid #888;
    margin:0px;
    padding:0px;
}
div.autocomplete ul {
    list-style-type:none;
    margin:0px;
    padding:0px;
}
div.autocomplete ul li.selected {
    background-color: #ffb;
}
div.autocomplete ul li {
    list-style-type:none;
    display:block;
    margin:0;
    padding:2px;
    height:32px;
    cursor:pointer;
}
{% endhighlight %}

javascript源码
-----

{% highlight javascript %}
function updateElemTest(selectedElement) {
    console.log(selectedElement.id);
    $('autocomplete').value = selectedElement.innerHTML;
}

function getSelectionId(input, li) {
    console.log(input.value + ' + ' + li.id);
}

function calledOnHide(input, update) {
    new Effect.Fade(update, {
        duration: 1
    })
}

//  new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ajax_response.html", {
//  paramName: "value",
//  minChars: 2,
//  updateElement: updateElemTest, //取代默认方法（更新input），用户自定义更新
//  indicator: 'indicator1'
//  });

//  new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ajax_response.html", {
//  paramName: "value", //本来默认传过去的参数名是input name，可以重定义覆盖此名字
//  minChars: 2,
//  afterUpdateElement : getSelectionId, //只发生在默认更新动作发生之后
//  indicator: 'indicator1'
//  });

var my_completer = new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ajax_response.html", {
    onHide: calledOnHide
});
{% endhighlight %}

html源码
-----

{% highlight html %}
<script src="prototype.js" type="text/javascript"></script>
<script src="effects.js" type="text/javascript"></script>
<script src="controls.js" type="text/javascript"></script>

<input type="text" id="autocomplete" name="autocomplete_parameter"/>
<span id="indicator1" style="display: none"><img src="indicator.gif" alt="Working..." /></span>
<div id="autocomplete_choices" class="autocomplete"></div>
{% endhighlight %}

ajax调用的ajax_response.html源码
-----

{% highlight html %}
<ul>
    <li id="1">yes</li>
    <li id="2">no</li>
    <li id="3">thanks</li>
</ul>
{% endhighlight %}
