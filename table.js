var Tooltip = Class.create();
Tooltip.prototype = {
  initialize: function(element, cid) {
    var options = Object.extend({
      defaultCss: true,
      margin: "0px",
	  padding: "5px",
	  backgroundColor: "#d6d6fc",
	  border: "#ccc 2px solid",
	  width: "300px",
	  deltaX: 5,
	  deltaY: 5,
      zindex: 1000
    }, arguments[2] || {});
	this.options = options;
	this.element = element;
	
	var tooltipContent = Table.cells[cid];
	new Insertion.Bottom(this.element, 
	  "<div id='" + cid + "' style='display:none;word-break:break-all;width:" + this.options.width + "'>" + tooltipContent + "</div>");
	this.tooltip = $(cid);
	
    this.displayEvent = this.showTooltip.bindAsEventListener(this);
    this.hideEvent = this.hideTooltip.bindAsEventListener(this);
    this.registerEvents();
  },

  registerEvents: function() {
    Event.observe(this.element, "mouseover", this.displayEvent);
    Event.observe(this.element, "mouseout", this.hideEvent);
  },

  showTooltip: function(event){
	Event.stop(event);
    var mouseX = Event.pointerX(event);
	var mouseY = Event.pointerY(event) + this.options.deltaY;
	var halfWidth = this.getBodyWidth() / 2;
	if (mouseX > halfWidth) {
		mouseX -= this.tooltip.getWidth() + this.options.deltaX;
	} else {
		mouseX += this.options.deltaX;
	}
	
	this.setStyles(mouseX, mouseY);
	new Element.show(this.tooltip);
  },
  
  setStyles: function(x, y) {
	Element.setStyle(this.tooltip, { 
			position: 'absolute',
			top: y + "px",
			left: x + "px",
			zindex: this.options.zindex
		});
	
	if (this.options.defaultCss){
	  	Element.setStyle(this.tooltip, { 
			margin: this.options.margin,
		    padding: this.options.padding,
            backgroundColor: this.options.backgroundColor,
 		    zindex: this.options.zindex,
			border: this.options.border,
			width: this.options.width
        });	
	}	
  },

  hideTooltip: function(event){
	new Element.hide(this.tooltip);
  },
 
  getBodyWidth: function(){
	return document.body.clientWidth;
  }
  
}


var Table = {
	rowEvenClass: 'roweven',
	rowOddClass: 'rowodd',
	rawCellContent: true,
	heads: {},
	rows: {},
	cells: {},
	getBodyRows: function(table) {
		if(typeof(table) == "string") table = $(table);
		var id = table.id || Math.random();
		if(!Table.rows[id] && table.tBodies[0]) {
			Table.rows[id] = (table.tHead && table.tHead.rows.length > 0) ? $A(table.tBodies[0].rows): $A(table.tBodies[0].rows);
		}
		return Table.rows[id] || [];
	},
	getHeadCells: function(table) {
		if(typeof(table) == "string") table = $(table);
		var id = table.id || Math.random();
		if(!Table.heads[id]) {
			Table.heads[id] = $A((table.tHead && table.tHead.rows.length > 0) ? table.tHead.rows[table.tHead.rows.length-1].cells: table.rows[0].cells);
		}
		return Table.heads[id];
	},
	getCellIndex: function(cell) {
		if(typeof(cell) == "string") cell = $(cell);
		return $A(cell.parentNode.cells).indexOf(cell);
	},
	getRowIndex: function(row) {
		if(typeof(row) == "string") row = $(row);
		return $A(row.parentNode.rows).indexOf(row);
	},
	getCellText: function(cell) {
		if(!cell) { return ""; }
		if(typeof(cell) == "string") cell = $(cell);
		textContent = cell.textContent ? cell.textContent: cell.innerText;
		return textContent;
	}
}

Table.Rows = {
	stripe: function(table) {
		var rows = Table.getBodyRows(table);
		rows.each(function(row, index) {
			Table.Rows.addStripeClass(table, row, index);
			Table.Rows.truncateCells(table, row, index);
		});
	},
	addStripeClass: function(table, row, index) {
		table = table || row.up('table');
		var css = ((index + 1) % 2 === 0 ? Table.rowEvenClass: Table.rowOddClass);
		var cn = row.className.split(/\s+/);
		var newCn = [];
		for(var x = 0, l = cn.length; x < l; x ++) {
			if(cn[x] !== Table.rowEvenClass && cn[x] !== Table.rowOddClass) { newCn.push(cn[x]); }
		}
		newCn.push(css);
		row.className = newCn.join(" ");
	},
	truncateCells: function(table, row, rowIndex) {
		table = table || row.up('table');
		$A(row.cells).each(function(cell, cellIndex) {
			var cid = "row" + rowIndex + "_col" + cellIndex;
			var cidTruncated = cid + "_truncated";
			if (Table.rawCellContent) {
				Table.cells[cid] = cell.innerHTML;
			} else {
				Table.cells[cid] = Table.getCellText(cell);	
			}
			Table.cells[cidTruncated] = false;
			try {
				Table.Cells.truncateTextNodes(cell, cidTruncated);
			} catch (e) {
				// ignore
			}
			if (Table.cells[cidTruncated]) new Tooltip(cell, cid);
		});
	}
};

Table.Cells = {
	truncate: function(node, cidTruncated) {
		var nodeData = node.data;
		if (/[\u4e00-\u9fa5]/.test(nodeData)) {
			if (nodeData.strip().length >= 10) {
				node.data = nodeData.truncate(10);
				if (!Table.cells[cidTruncated]) Table.cells[cidTruncated] = true;
			}
		} else {
			if (nodeData.length >= 30) {
				node.data = nodeData.truncate(30);
				if (!Table.cells[cidTruncated]) Table.cells[cidTruncated] = true;
			}
		}
	},
	truncateTextNodes: function(cell, cidTruncated) {
		$A(cell.childNodes).each(function(node){
			if (node.nodeType == 3) {
			    Table.Cells.truncate(node, cidTruncated);
			} else if (node.nodeType == 1 && ["select", "script", "embed", "object", "textarea", "table", "iframe"].include(node.tagName.toLowerCase())) {
				Table.cells[cidTruncated] = false;
				throw new SyntaxError("Do not truncate " + node.tagName);
			} else {
			    Table.Cells.truncateTextNodes(node, cidTruncated);
			}
		});
	}
}

Table.load = function() {
	$$("table[class~=t-data-grid]").each(function(table){
		Table.Rows.stripe(table);
	});
}

Event.observe(window, 'load', Table.load);

