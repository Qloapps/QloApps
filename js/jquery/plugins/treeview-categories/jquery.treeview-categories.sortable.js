/*
 * jQuery UI Sortable
 *
 * Copyright (c) 2008 Paul Bakaus
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * http://docs.jquery.com/UI/Sortables
 *
 * Depends:
 *   ui.base.js
 *
 * Revision: $Id: jquery.treeview.sortable.js 7776 2011-07-28 09:20:57Z rMalie $
 */
;(function($) {

	if (window.Node && Node.prototype && !Node.prototype.contains) {
		Node.prototype.contains = function (arg) {
			return !!(this.compareDocumentPosition(arg) & 16);
		};
	}


	$.widget("ui.sortableTree", $.extend($.ui.mouse, {
		init: function() {

			//Initialize needed constants
			var self = this, o = this.options;
			this.containerCache = {};
			this.element.addClass("ui-sortableTree");
			
			//Get the items
			this.refresh();
			
			//Let's determine the parent's offset
			if(!(/(relative|absolute|fixed)/).test(this.element.css('position'))) this.element.css('position', 'relative');
			this.offset = this.element.offset();
	
			//Initialize mouse events for interaction
			this.mouseInit();
			
			//Prepare cursorAt
			if(o.cursorAt && o.cursorAt.constructor == Array)
				o.cursorAt = { left: o.cursorAt[0], top: o.cursorAt[1] };

		},
		plugins: {},
		ui: function(inst) {
			return {
				helper: (inst || this)["helper"],
				position: (inst || this)["position"].current,
				absolutePosition: (inst || this)["position"].absolute,
				instance: this,
				options: this.options,
				element: this.element,
				item: (inst || this)["currentItem"],
				sender: inst ? inst.element : null
			};		
		},
		propagate: function(n,e,inst) {
			$.ui.plugin.call(this, n, [e, this.ui(inst)]);
			this.element.triggerHandler(n == "sort" ? n : "sort"+n, [e, this.ui(inst)], this.options[n]);
		},
		serialize: function(o) {
			
			var items = $(this.options.items, this.element).not('.ui-sortableTree-helper'); //Only the items of the sortable itself
			var str = []; o = o || {};
			
			items.each(function() {
				var res = ($(this).attr(o.attribute || 'id') || '').match(o.expression || (/(.+)[-=_](.+)/));
				if(res) str.push((o.key || res[1])+'[]='+(o.key ? res[1] : res[2]));
			});
			
			return str.join('&');
			
		},
		toArray: function(attr) {
			var items = $(this.options.items, this.element).not('.ui-sortableTree-helper'); //Only the items of the sortable itself
			var ret = [];

			items.each(function() { ret.push($(this).attr(attr || 'id')); });
			return ret;
		},
		enable: function() {
			this.element.removeClass("ui-sortableTree-disabled");
			this.options.disabled = false;
		},
		disable: function() {
			this.element.addClass("ui-sortableTree-disabled");
			this.options.disabled = true;
		},
		/* Be careful with the following core functions */
		intersectsWith: function(item) {
					
			var x1 = this.position.absolute.left - 10, x2 = x1 + 10,
			    y1 = this.position.absolute.top - 10, y2 = y1 + 10;
			var l = item.left, r = l + item.width, 
			    t = item.top,  b = t + item.height;
			
			return (   l < x1 + (this.helperProportions.width  / 2)    // Right Half
				&&     x2 - (this.helperProportions.width  / 2) < r    // Left Half
				&& t < y1 + (this.helperProportions.height / 2)        // Bottom Half
				&&     y2 - (this.helperProportions.height / 2) < b ); // Top Half
			
		},
		intersectsWithEdge: function(item) {	
			var y1 = this.position.absolute.top - 10, y2 = y1 + 10;
			var t = item.top,  b = t + item.height;

			if(!this.intersectsWith(item.item.parents(".ui-sortableTree").data("sortableTree").containerCache)) return false;

			if (!( t < y1 + (this.helperProportions.height / 2)        // Bottom Half
				&&     y2 - (this.helperProportions.height / 2) < b )) return false; // Top Half

			if(y2 > t && y1 < t) return 1; //Crosses top edge
			if(y1 < b && y2 > b) return 2; //Crosses bottom edge
			
			return false;
			
		},
		refresh: function() {
			this.refreshItems();
			this.refreshPositions();
		},
		refreshItems: function() {
			
			this.items = [];
			this.containers = [this];
			var items = this.items;
			var queries = [$(this.options.items, this.element)];
			
			if(this.options.connectWith) {
				for (var i = this.options.connectWith.length - 1; i >= 0; i--){
					var cur = $(this.options.connectWith[i]);
					for (var j = cur.length - 1; j >= 0; j--){
						var inst = $.data(cur[j], 'sortableTree');
						if(inst && !inst.options.disabled) {
							queries.push($(inst.options.items, inst.element));
							this.containers.push(inst);
						}
					};
				};
			}

			for (var i = queries.length - 1; i >= 0; i--){
				queries[i].each(function() {
					$.data(this, 'sortableTree-item', true); // Data for target checking (mouse manager)
					items.push({
						item: $(this),
						width: 0, height: 0,
						left: 0, top: 0
					});
				});
			};

		},
		refreshPositions: function(fast) {
			for (var i = this.items.length - 1; i >= 0; i--){
				if(!fast) this.items[i].height 			= this.items[i].item.outerHeight();
				this.items[i].top 						= this.items[i].item.offset().top;
			};
			for (var i = this.containers.length - 1; i >= 0; i--){
				var p =this.containers[i].element.offset();
				this.containers[i].containerCache.left 	= p.left;
				this.containers[i].containerCache.top 	= p.top;
				this.containers[i].containerCache.width	= this.containers[i].element.outerWidth();
				this.containers[i].containerCache.height= this.containers[i].element.outerHeight();
			};
		},
		destroy: function() {

			this.element
				.removeClass("ui-sortableTree ui-sortableTree-disabled")
				.removeData("sortableTree")
				.unbind(".sortableTree");
			this.mouseDestroy();
			
			for ( var i = this.items.length - 1; i >= 0; i-- )
				this.items[i].item.removeData("sortableTree-item");
				
		},
		contactContainers: function(e) {
			for (var i = this.containers.length - 1; i >= 0; i--){

				if(this.intersectsWith(this.containers[i].containerCache)) {
					if(!this.containers[i].containerCache.over) {
						
						if(this.currentContainer != this.containers[i]) {
							
							//When entering a new container, we will find the item with the least distance and append our item near it
							var dist = 10000; var itemWithLeastDistance = null; var base = this.position.absolute.top;
							for (var j = this.items.length - 1; j >= 0; j--) {
								if(!this.containers[i].element[0].contains(this.items[j].item[0])) continue;
								var cur = this.items[j].top;
								if(Math.abs(cur - base) < dist) {
									dist = Math.abs(cur - base); itemWithLeastDistance = this.items[j];
								}
							}
							
							itemWithLeastDistance ? this.rearrange(e, itemWithLeastDistance) : this.rearrange(e, null, this.containers[i].element);
							this.propagate("change", e); //Call plugins and callbacks
							this.containers[i].propagate("change", e, this); //Call plugins and callbacks
							this.currentContainer = this.containers[i];

						}
						
						this.containers[i].propagate("over", e, this);
						this.containers[i].containerCache.over = 1;
					}
				} else {
					if(this.containers[i].containerCache.over) {
						this.containers[i].propagate("out", e, this);
						this.containers[i].containerCache.over = 0;
					}
				}
				
			};			
		},
		mouseStart: function(e,el) {

			if(this.options.disabled || this.options.type == 'static') return false;

			//Find out if the clicked node (or one of its parents) is a actual item in this.items
			var currentItem = null, nodes = $(e.target).parents().each(function() {	
				if($.data(this, 'sortableTree-item')) {
					currentItem = $(this);
					return false;
				}
			});
			if($.data(e.target, 'sortableTree-item')) currentItem = $(e.target);
			
			if(!currentItem) return false;	
			if(this.options.handle) {
				var validHandle = false;
				$(this.options.handle, currentItem).each(function() { if(this == e.target) validHandle = true; });
				if(!validHandle) return false;
			}
				
			this.currentItem = currentItem;
			
			var o = this.options;
			this.currentContainer = this;
			this.refresh();

			//Create and append the visible helper
			this.helper = typeof o.helper == 'function' ? $(o.helper.apply(this.element[0], [e, this.currentItem])) : this.currentItem.clone();
			if(!this.helper.parents('body').length) this.helper.appendTo("body"); //Add the helper to the DOM if that didn't happen already
			this.helper.css({ position: 'absolute', clear: 'both' }).addClass('ui-sortableTree-helper'); //Position it absolutely and add a helper class
			
			//Prepare variables for position generation
			$.extend(this, {
				offsetParent: this.helper.offsetParent(),
				offsets: { absolute: this.currentItem.offset() }
			});

			//Save the first time position
			$.extend(this, {
				position: {
					current: { left: e.pageX, top: e.pageY },
					absolute: { left: e.pageX, top: e.pageY },
					dom: this.currentItem.prev()[0]
				},
				clickOffset: { left: -5, top: -5 }
			});

			this.propagate("start", e); //Call plugins and callbacks
			this.helperProportions = { width: this.helper.outerWidth(), height: this.helper.outerHeight() }; //Save and store the helper proportions

			for (var i = this.containers.length - 1; i >= 0; i--) {
				this.containers[i].propagate("activate", e, this);
			} //Post 'activate' events to possible containers
			
			//Prepare possible droppables
			if($.ui.ddmanager) $.ui.ddmanager.current = this;
			if ($.ui.ddmanager && !o.dropBehaviour) $.ui.ddmanager.prepareOffsets(this, e);

			this.dragging = true;
			return true;
			
		},
		mouseStop: function(e) {

			if(this.newPositionAt) this.options.sortIndication.remove.call(this.currentItem, this.newPositionAt); //remove sort indicator
			this.propagate("stop", e); //Call plugins and trigger callbacks

			//If we are using droppables, inform the manager about the drop
			var dropped = ($.ui.ddmanager && !this.options.dropBehaviour) ? $.ui.ddmanager.drop(this, e) : false;
			if(!dropped && this.newPositionAt) this.newPositionAt[this.direction == 'down' ? 'before' : 'after'](this.currentItem); //Append to element to its new position
			
			if(this.position.dom != this.currentItem.prev()[0]) this.propagate("update", e); //Trigger update callback if the DOM position has changed
			if(!this.element[0].contains(this.currentItem[0])) { //Node was moved out of the current element
				this.propagate("remove", e);
				for (var i = this.containers.length - 1; i >= 0; i--){
					if(this.containers[i].element[0].contains(this.currentItem[0])) {
						this.containers[i].propagate("update", e, this);
						this.containers[i].propagate("receive", e, this);
					}
				};
			};
			
			//Post events to containers
			for (var i = this.containers.length - 1; i >= 0; i--){
				this.containers[i].propagate("deactivate", e, this);
				if(this.containers[i].containerCache.over) {
					this.containers[i].propagate("out", e, this);
					this.containers[i].containerCache.over = 0;
				}
			}
			
			this.dragging = false;
			if(this.cancelHelperRemoval) return false;
			this.helper.remove();

			return false;
			
		},
		mouseDrag: function(e) {

			//Compute the helpers position
			this.position.current = { top: e.pageY + 5, left: e.pageX + 5 };
			this.position.absolute = { left: e.pageX + 5, top: e.pageY + 5 };

			//Interconnect with droppables
			if($.ui.ddmanager) $.ui.ddmanager.drag(this, e);
			var intersectsWithDroppable = false;
			$.each($.ui.ddmanager.droppables, function() {
				if(this.isover) intersectsWithDroppable = true;
			});

			//Rearrange
			if(intersectsWithDroppable) {
				if(this.newPositionAt) this.options.sortIndication.remove.call(this.currentItem, this.newPositionAt);
			} else {
				for (var i = this.items.length - 1; i >= 0; i--) {
					
					if(this.currentItem[0].contains(this.items[i].item[0])) continue;
					
					var intersection = this.intersectsWithEdge(this.items[i]);
					if(!intersection) continue;
	
					this.direction = intersection == 1 ? "down" : "up";
					this.rearrange(e, this.items[i]);
					this.propagate("change", e); //Call plugins and callbacks
					break;
				}
			}
			
			//Post events to containers
			this.contactContainers(e);

			this.propagate("sort", e); //Call plugins and callbacks
			this.helper.css({ left: this.position.current.left+'px', top: this.position.current.top+'px' }); // Stick the helper to the cursor
			return false;
			
		},
		rearrange: function(e, i, a) {
			if(i) {
				if(this.newPositionAt) this.options.sortIndication.remove.call(this.currentItem, this.newPositionAt);
				this.newPositionAt = i.item;
				this.options.sortIndication[this.direction].call(this.currentItem, this.newPositionAt);
			} else {
				//Append
			}
		}
	}));
	
	$.extend($.ui.sortableTree, {
		defaults: {
			items: '> *',
			zIndex: 1000,
			distance: 1
		},
		getter: "serialize toArray"
	});



})(jQuery);
