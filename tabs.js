/**
 * tabs.js
 * tab link it's content div using a.title of tab.
 * or, using a.href.hash to link it's content div.
 * version 0.2
 * 
 * example: http://localhost/test_js/test_tabs.html
 * 
 */

var Tabs = Class.create();
Tabs.prototype = {
	initialize: function(element /*, Object options */) {
		this.element = $(element);
		var self = this;
		var options = {
			tabClassName: "tab",
			hoverTabClassName: "hoverTab",
			removableClassName: "removable",
			activedTabClassName: "activedTab",
			tabContentClassName: "tabContent", // tab content default hidden.
			activedTabContentClassName: "activedTabContent" // show which tab content.
		};
		this.options = Object.extend(options, arguments[1] || {});
		this.tabs = $A(document.getElementsByTagName("LI")).select(function(li){
			// TODO: prototype in IE7 has bug, li.hasClassName == "undefined"
			return li.hasClassName(self.options.tabClassName) && li.ancestors().include(self.element);
		});
		var activedTabs = this.tabs.select(function(tab){
			return tab.hasClassName(self.options.activedTabClassName);
		});
		// find the first actived tab.
		this.activedTab = activedTabs.length > 0 ? activedTabs[0] : this.tabs[0];
		this.activedTab.addClassName(this.options.activedTabClassName);
		// TODO: first descendant may not anchor link!
		this.activedLink = this.activedTab.firstDescendant(); // li:first-child => a
		// console.log(this.activedLink);
		$(this.activedLink.title).addClassName(this.options.activedTabContentClassName);
		Event.observe(this.element, "click", this.activate.bindAsEventListener(this));
	},
	activate: function(event) {
		// for remove select tab.
		var self = this;
		var ul = Event.findElement(event, "ul");
		var li = Event.findElement(event, "li"); // maybe null
		if (li) {
			var clickedSpan = Event.findElement(event, "span");
			var clickedLink = Event.findElement(event, "a");
			if (!clickedLink) {
				clickedLink = li.firstDescendant();
			}
			if (clickedSpan && clickedSpan.hasClassName(this.options.removableClassName)) {
				if (clickedLink === this.activedLink) {
					$(this.activedLink.title).hide();
					// remove actived tab, then activate tabs.first().
					this.activedTab = this.tabs.without(this.activedTab).first();
					this.activedLink = this.activedTab.firstDescendant();
					this.activedTab.addClassName(this.options.activedTabClassName);
					$(this.activedLink.title).addClassName(this.options.activedTabContentClassName);
				}
				ul.removeChild(li);
				// remove clicked tab from this.tabs.
				this.tabs = this.tabs.reject(function(tab){
					return tab === li;
				});
				return;
			}
			// for activate new tab.
			if (clickedLink) {
				this.activedTab.removeClassName(this.options.activedTabClassName);
				$(this.activedLink.title).removeClassName(this.options.activedTabContentClassName);
				this.activedTab = li;
				this.activedLink = clickedLink;
				this.activedTab.addClassName(this.options.activedTabClassName);
				$(this.activedLink.title).addClassName(this.options.activedTabContentClassName);
			}
		}
	},
	add: function(tabName, tabContentId /*, String tabContent, Boolean removable*/) {
		// arguments[2] accepts tabContent
		var link = document.createElement("A");
		link.setAttribute("title", tabContentId);
		link.setAttribute("href", "#");
		link.appendChild(document.createTextNode(tabName));
		var list = document.createElement("LI");
		list.setAttribute("class", this.options.tabClassName);
		list.appendChild(link);
		if (arguments[3]) {
			var span = document.createElement("SPAN");
			span.setAttribute("class", this.options.removableClassName);
			list.appendChild(span);
		} else {
			link.setAttribute("style", "width: 100%;");
		}
		new Insertion.Bottom(this.activedTab.parentNode, list);
		if (!$(tabContentId)) {
			var tabContent = document.createElement("DIV");
			tabContent.setAttribute("id", tabContentId);
			tabContent.setAttribute("class", this.options.tabContentClassName);
		 	// new tab content div body
			tabContent.innerHTML = arguments[2] || "";
			new Insertion.After($(this.activedLink.title), tabContent);
		}
		this.tabs.push(link);
	}
}

