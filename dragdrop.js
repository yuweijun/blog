// MyDragDrop 0.1 - based Prototype and imitate script.aculo.us dragdrop.js

DragController = {
	drags: [],
	dragging: false,
	currentDrag: null,
	register: function(drag) {
		if(this.drags.length == 0) {
			Event.observe(document, "mouseup", this.endDrag.bindAsEventListener(this));
			Event.observe(document, "mousemove", this.updateDrag.bindAsEventListener(this));
			Event.observe(document, "keypress", this.keyPress.bindAsEventListener(this));
		}
		this.drags.push(drag);
	},
	updateDrag: function(event) {
		this.currentDrag && this.currentDrag.updateDrag(event);
	},
	endDrag: function(event) {
		this.currentDrag && this.currentDrag.endDrag(event, true);
	},
	keyPress: function(event) {
		this.currentDrag && this.currentDrag.keyPress(event);
	}
};

Drag = Class.create({
	initialize: function(element) {
		var defaults = {
			handle: false,
			zindex: 1000,
			revert: false,
			ghosting: false,
			opacity: 0.0
		};
		this.element = $(element);
		this.options = Object.extend(defaults, arguments[1] || {});
		DragController.register(element);
		// fix IE position problem.
		if(Prototype.Browser.IE) {
			var position = this.element.style.position;
		    if (position == 'static' || !position) this.element.style.position = 'relative';
		}
		Event.observe(element, "mousedown", this.startDrag.bindAsEventListener(this));
	},
	startDrag: function(event) {
		if(Event.isLeftClick(event)) {	
			this.relativePosition = [this.element.style.left, this.element.style.top];
			this.clone = this.element.cloneNode(true);
			this.positionAbsolutize();
			this.element.parentNode.insertBefore(this.clone, this.element);
			this.originalOpacity = this.element.getOpacity();
			this.element.setOpacity(0.7);
			if(!this.options.ghosting) {
				this.clone.setOpacity(this.options.opacity);
			}
			this.element.style.zIndex = this.options.zindex;
			this.element.style.cursor = "move";
			// hack safari inline element shadow problem. 
			// this negative effect is block-level element display inline
			if (Prototype.Browser.WebKit && this.element.style.display != "block" && 
			["a", "b", "i", "q", "s", "em", "big", "sup", "sub", "abbr", "code", "cite", "span", "quote", "small", "strike", "strong"].include(this.element.tagName.toLowerCase()))
				this.element.style.display = "inline-block";
			this.startPointer = [Event.pointerX(event), Event.pointerY(event)];
			this.positionedOffset = [this.element.offsetLeft, this.element.offsetTop];
			DragController.dragging = true;
			DragController.currentDrag = this;
		}
		Event.stop(event);
	},
	updateDrag: function(event) {
		if (DragController.dragging) {
			var currentPointer = [Event.pointerX(event), Event.pointerY(event)];
			this.element.style.left = (currentPointer[0] - this.startPointer[0] + this.positionedOffset[0] - parseInt(this.element.style.marginLeft || 0)) + "px";
			this.element.style.top = (currentPointer[1] - this.startPointer[1] + this.positionedOffset[1] - parseInt(this.element.style.marginTop || 0)) + "px";
			Drop.show(currentPointer, this.element);
		}
		Event.stop(event);
	},
	endDrag: function(event, success) {
		if (DragController.dragging) {
			DragController.dragging = false;
			DragController.currentDrag = null;
			this.element.setOpacity(this.originalOpacity);
			var currentPointer = [Event.pointerX(event), Event.pointerY(event)];
			this.element.style.position = "relative";
			this.element.style.left = (currentPointer[0] - this.startPointer[0] + parseInt(this.relativePosition[0] || 0)) + "px";
			this.element.style.top = (currentPointer[1] - this.startPointer[1] + parseInt(this.relativePosition[1] || 0)) + "px";
			this.clone.remove(); // clone remove must before Drop event fire because clone will hold position
			this.clone = null;
			if(!success) {
				this.revertDrag(event);
			} else {
				var dropped;
				if (Drop.lastDrop) {
					Drop.removeHoverClass();
					dropped = Drop.fire(event, this.element, Drop.lastDrop);
				}
				if (!dropped && this.options.revert) 
					this.revertDrag(event);
			}
		}
		Event.stop(event);
	},
	keyPress: function(event) {
	    if(event.keyCode != Event.KEY_ESC) return;
	    this.endDrag(event, false);
	    Event.stop(event);
	},
	revertDrag: function(event) {
		this.element.style.left = this.relativePosition[0];
		this.element.style.top = this.relativePosition[1];
	},
	positionAbsolutize: function() {
		if (this.element.getStyle('position') == 'absolute') return;
		var offsets = [this.element.offsetLeft, this.element.offsetTop];
		// offsetLeft include this element's marginLeft value.
		var left = offsets[0] - parseInt(this.element.style.marginLeft || 0);
		var top  = offsets[1] - parseInt(this.element.style.marginTop || 0);
		this.element.style.position = 'absolute';
		this.element.style.top      = top + 'px';
		this.element.style.left     = left + 'px';
	}
});

var Drop = {
	drops: [],
	lastDrop: null,
	add: function(element) {
		var drop = Object.extend({
			hoverClass: null
		}, arguments[1] || {});
		drop.element = $(element);
		this.drops.push(drop);
	},
	isContained: function(drag, drop) {
		var container = drag.parentNode;
		return drop.containers.detect(function(c) {return $(c) == container });
	},
	dragHovered: function(pointer, drag, drop) {
		return ((drop.element != drag) && ((!drop.containers) || this.isContained(drag, drop)) &&
				Position.within(drop.element, pointer[0], pointer[1]));
	},
	removeHoverClass: function() {
		Element.removeClassName(Drop.lastDrop.element, Drop.lastDrop.hoverclass);
	},
	show: function(pointer, drag) {
		if(!this.drops.length) return;
		var hovered = false;
		this.drops.each(function(drop) {
			if(Drop.dragHovered(pointer, drag, drop)) {
				hovered = true;
				Drop.lastDrop = drop;
				Element.addClassName(drop.element, drop.hoverclass);
				if(drop.onHover) drop.onHover(drag, drop.element);
			}
		});
		if (Drop.lastDrop && !hovered)
			this.removeHoverClass();
	},
	fire: function(event, drag, drop) {
		if (drop && this.dragHovered([Event.pointerX(event), Event.pointerY(event)], drag, drop)) {
			if(drop.onDrop) drop.onDrop(drag, drop.element, event);
			return true;
		}
	}
}
