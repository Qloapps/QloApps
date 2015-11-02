/*!
 * dragtable - jquery ui widget to re-order table columns 
 * version 3.0
 * 
 * Copyright (c) 2010, Jesse Baird <jebaird@gmail.com>
 * 12/2/2010
 * https://github.com/jebaird/dragtable
 * 
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * 
 * 
 * 
 * Forked from https://github.com/akottr/dragtable - Andres Koetter akottr@gmail.com
 * 
 *
 * 
 * 
 * quick down and and dirty on how this works
 * ###########################################
 * so when a column is selected we grab all of the cells in that row and clone append them to a semi copy of the parent table and the 
 * "real" cells get a place holder class witch is removed when the dragstop event is triggered
 * 
 * 
 * make it easy to have a button swap columns
 * 
 * 
 * Events - in order of trigger
 * start - when the user mouses down on handle or th, use in favor of display helper
 * beforechagne - called when a col will be moved
 * change - called after the col has been moved
 * stop - after the user mouses up and stops dragging
 * 
 * 
 * 
 * 
 * IE notes
 * 	ie8 in quirks mode will only drag once after that the events are lost
 * 
 */

(function($) {
  $.widget("jb.dragtable", {
  		//TODO: implement this
  		eventWidgetPrefix: 'dragtable',
		options: {
			//used to the col headers, data contained in here is used to set / get the name of the col
			dataHeader:'data-header',
			//class name that handles have 
			handle:'dragtable-drag-handle',
			//draggable items in cols, .dragtable-drag-handle has to match the handle options
			items: 'th:not( :has( .dragtable-drag-handle ) ), .dragtable-drag-handle',
			//if a col header as this class, cols cant be dragged past it
			boundary: 'dragtable-drag-boundary',
			//classnames that get applied to the real td, th
			placeholder: 'dragtable-col-placeholder',
			//the drag display will be appended to this element, some reason this is blank, also if your body tag has been zeroed off it wont be exact
			appendTarget: $(  document.body ),
			//if true,this will scroll the appendTarget offsetParent when the dragDisplay is dragged past its boundaries
			scroll: false
			
		},
		// when a col is dragged use this to find the semantic elements, for speed
  		tableElemIndex:{  
			head: '0',
			body: '1',
			foot: '2'
		},
		tbodyRegex: /(tbody|TBODY)/,
		theadRegex: /(thead|THEAD)/,
		tfootRegex: /(tfoot|TFOOT)/,
			
		_create: function() {
			
			//console.log(this);
			//used start/end of drag
			this.startIndex = null;
			this.endIndex = null;
			//the references to the table cells that are getting dragged
			this.currentColumnCollection = [];
			//the references the position of the first element in the currentColunmCollection position
			this.currentColumnCollectionOffset = {};
			//the div wrapping the drag display table
			this.dragDisplay = $([])

			
			var self = this,
				o = self.options,
				el = self.element;
		
			//offsetappendTarget catch for this
			if( o.appendTarget.length == 0 ){
				o.appendTarget = $( document.body );
			}
			//grab the ths and the handles and bind them 
			el.delegate(o.items, 'mousedown.' + self.widgetEventPrefix, function(e){
				
				var $handle = $(this),
					elementOffsetTop = self.element.position().top;

				//make sure we are working with a th instead of a handle
				if( $handle.hasClass( o.handle ) ){
					
					$handle = $handle.closest('th');
					//change the target to the th, so the handler can pick up the offsetleft
					e.currentTarget = $handle.closest('th')[0]
				}
				
							
			self.getCol( $handle.index() )
					.attr( 'tabindex', -1 )
	                .focus()
					.disableSelection()
					.css({
	                    top: elementOffsetTop,
	                    //need to account for the scroll left of the append target, other wise the display will be off by that many pix
	                    left: ( self.currentColumnCollectionOffset.left + o.appendTarget[0].scrollLeft )
					})
	                .appendTo( o.appendTarget )
				

				
				self._mousemoveHandler( e );
				//############
			});
                
		},
		
		/*
		 * e.currentTarget is used for figuring out offsetLeft
		 * getCol must be called before this is 
		 * 
		 */
		_mousemoveHandler: function( e ){
			//call this first, catch any drag display issues
			this._start( e )
				
			var self = this,
				o = self.options,
            	prevMouseX = e.pageX,
            	dragDisplayWidth = self.dragDisplay.outerWidth(),
            	halfDragDisplayWidth = dragDisplayWidth / 2,
            	appendTargetOP = o.appendTarget.offsetParent()[0],
            	scroll = o.scroll,
				//get the col count, used to contain col swap
				colCount = self.element[ 0 ]
								.getElementsByTagName( 'thead' )[ 0 ]
								.getElementsByTagName( 'tr' )[ 0 ]
								.getElementsByTagName( 'th' )
								.length - 1;

            $( document ).bind('mousemove.' + self.widgetEventPrefix, function( e ){
            	var columnPos = self._setCurrentColumnCollectionOffset(),
            		mouseXDiff = e.pageX - prevMouseX,
            		appendTarget = o.appendTarget[0],
            		left =  ( parseInt( self.dragDisplay[0].style.left ) + mouseXDiff  );
                self.dragDisplay.css( 'left', left )
                
               /*
                * when moving left and e.pageX and prevMouseX are the same it will trigger right when moving left
                * 
                * it should only swap cols when the col dragging is half over the prev/next col
                */                    
               if( e.pageX  < prevMouseX ){
                   //move left
                   var threshold = columnPos.left - halfDragDisplayWidth;
                   
                   
                   //scroll left
				 	if( left < ( appendTarget.clientWidth  - dragDisplayWidth ) && scroll == true ) {
				 		var scrollLeft =  appendTarget.scrollLeft + mouseXDiff
				 		/*
				 		 * firefox does scroll the body with target being body but chome does
				 		 */
				 		if( appendTarget.tagName == 'BODY' ) {
							window.scroll( window.scrollX + scrollLeft, window.scrollY );
						} else {
							appendTarget.scrollLeft = scrollLeft;
						}
				 		
				 	}
                   
                   
                   if( left  < threshold ){
                   		self._swapCol(self.startIndex-1);
                   }

				}else{
				 //move right
				 	var threshold = columnPos.left + halfDragDisplayWidth ;
				 	
				 	//scroll right
				 	if( left > (appendTarget.clientWidth - dragDisplayWidth ) && scroll == true ) {
				 		//console.log(  o.appendTarget[0].clientWidth + (e.pageX - prevMouseX))
				 		
				 		var scrollLeft =  appendTarget.scrollLeft + mouseXDiff
				 		/*
				 		 * firefox does scroll the body with target being body but chome does
				 		 */
				 		if( appendTarget.tagName == 'BODY' ) {
							window.scroll( window.scrollX + scrollLeft, window.scrollY );
						} else {
							appendTarget.scrollLeft = scrollLeft;
						}
				 		
				 	}
				 	
                    //move to the right only if x is greater than threshold and the current col isn' the last one
                    if( left  > threshold  && colCount != self.startIndex ){
                    	self._swapCol( self.startIndex + 1 );
                    }
				}
				//update mouse position
				prevMouseX = e.pageX;
		
            })
            .one( 'mouseup.' + self.widgetEventPrefix ,function(e ){
                self._stop( e );
            });
                          
		},
		
		_start: function( e ){
			
			$( document )
                	//move disableselection and cursor to default handlers of the start event
	                .disableSelection()
	                .css( 'cursor', 'move')

				
			return this._eventHelper('start',e,{
					//'draggable': $dragDisplay
				});
                
                
		},
		_stop: function( e ){

			if( this._eventHelper('stop',e,{}) == true ){
				 $( document )
			 	 .unbind( 'mousemove.' + this.widgetEventPrefix )
			 	 .enableSelection()
			 	 .css( 'cursor', 'move')
				
				this.dropCol();
				this.dragDisplay.remove()
			};  
	                    
		},
		
		_setOption: function(option, value) {
			$.Widget.prototype._setOption.apply( this, arguments );
           
		},
		
		/*
		 * get the selected index cell out of table row
		 * needs to work as fast as possible. and performance gains in this method are worth the time
		 * 	because its used to build the drag display and get the cells on col swap
		 * http://jsperf.com/binary-regex-vs-string-equality/4
		 */
		_getCells: function( elem, index ){
			//console.time('getcells');
			var ei = this.tableElemIndex,
				//TODO: clean up this format 
				tds = {
					//store where the cells came from
					'semantic':{
						'0': [],//head throws error if ei.head or ei['head']
						'1': [],//body
						'2': []//footer
					},
					//keep a ref in a flat array for easy access
					'array':[]
				},
				//cache regex, reduces looking up the chain
				tbodyRegex = this.tbodyRegex,
				theadRegex = this.theadRegex,
				//reduce looking up the chain, dont do it for the foot think thats more overhead since not many tables have a tfoot
				tdsSemanticBody = tds.semantic[ei.body],
				tdsSemanticHead = tds.semantic[ei.head];
			
			//console.log(index);
			//check does this col exsist
			if(index <= -1 || typeof elem.rows[0].cells[index] == undefined){
				return tds;
			}
			
			for(var i = 0, length = elem.rows.length; i < length; i++){
				
				var td = elem.rows[i].cells[index];
				
				//if the row has no cells dont error out;
				if( td == undefined ){
					continue;
				}
				
				var parentNodeName = td.parentNode.parentNode.nodeName;
				tds.array.push(td);
				//faster to leave out ^ and $ in the regular expression
				if( tbodyRegex.test( parentNodeName ) ){
					
					tdsSemanticBody.push( td );
					
				}else if( theadRegex.test( parentNodeName ) ){
					
					tdsSemanticHead.push( td );
				
				}else if( this.tfootRegex.test( parentNodeName ) ){
					
					tds.semantic[ei.foot].push( td );
				}
				
					 		
		 	}
		 	//console.timeEnd('getcells');
		 	return tds;
		},
		/*
		 * returns all element attrs in a string key="value" key2="value"
		 */
		_getElementAttributes: function(element){
			
        	var attrsString = '',
	        	attrs = element.attributes;
	        for(var i=0, length = attrs.length; i < length; i++) {
	            attrsString += attrs[i].nodeName + '="' + attrs[i].nodeValue+'"';
	        }
	        return attrsString;
		},

     	/*
     	 * faster than swap nodes
     	 * only works if a b parent are the same, works great for columns
     	 */
     	_swapCells: function(a, b) {
        	a.parentNode.insertBefore(b, a);
     	},

		/*
		 * used to trigger optional events
		 */
		_eventHelper: function(eventName ,eventObj, additionalData){
			return this._trigger( 
				eventName, 
				eventObj, 
				$.extend({
					column: this.currentColumnCollection,
					order: this.order(),
					startIndex: this.startIndex,
					endIndex: this.endIndex,
					dragDisplay: this.dragDisplay,
					columnOffset: this.currentColumnCollectionOffset			
				},additionalData)
			);
		},
		/*
		 * build copy of table and attach the selected col to it, also removes the select col out of the table
		 * @returns copy of table with the selected col
		 * 
		 * populates self.dragDisplay
		 * TODO: name this something better, like select col or get dragDisplay
		 * 
		 */		
		getCol: function(index){
			//console.log('index of col '+index);
			//drag display is just simple html
			//console.profile('selectCol');
			
			//colHeader.addClass('ui-state-disabled')

			var $table = this.element,
				self = this,
				eIndex = self.tableElemIndex,
				placholderClassnames = ' ' + this.options.placeholder;
				
				//BUG: IE thinks that this table is disabled, dont know how that happend
				self.dragDisplay = $('<table '+self._getElementAttributes($table[0])+'></table>')
									.addClass('dragtable-drag-col');
			
			//start and end are the same to start out with
			self.startIndex = self.endIndex = index;
		

		 	var cells = self._getCells($table[0], index);
			self.currentColumnCollection = cells.array;
			//console.log(cells);
			//################################
			
			//TODO: convert to for in // its faster than each
			$.each(cells.semantic,function(k,collection){
				//dont bother processing if there is nothing here
				
				if(collection.length == 0){
					return;
				}
                
                if ( k == '0' ){
                    var target = document.createElement('thead');
						self.dragDisplay[0].appendChild(target);

                }else{ 
                    var target = document.createElement('tbody');
						self.dragDisplay[0].appendChild(target);

                }

				for(var i = 0,length = collection.length; i < length; i++){
					
					var clone = collection[i].cloneNode(true);
					collection[i].className+=placholderClassnames;
					var tr = document.createElement('tr');
					tr.appendChild(clone);
					//console.log(tr);
					
					
					target.appendChild(tr);
					//collection[i]=;
				}
			});
    		
    		
    		this._setCurrentColumnCollectionOffset();
    		
    		
            self.dragDisplay  = $('<div class="dragtable-drag-wrapper"></div>').append(self.dragDisplay)
    		return self.dragDisplay;
		},
		
		
		_setCurrentColumnCollectionOffset: function(){
			return this.currentColumnCollectionOffset = $( this.currentColumnCollection[0] ).position();
		},
		
		/*
		 * move column left or right
		 */
		_swapCol: function( to ){
			
			//cant swap if same position
			if(to == this.startIndex){
				return false;
			}
			
			var from = this.startIndex;
			this.endIndex = to;
			//this col cant be moved past me
			var th = this.element.find('th').eq( to );
			//check on th
			if( th.hasClass( this.options.boundary ) == true ){
				return false;
			}
			//check handle element
			if( th.find( '.' + this.options.handle ).hasClass( this.options.boundary ) == true ){
				return false;
			}
			
			if( this._eventHelper('breforechange',{}) === false ){
				return false;
			};
			
			
	        if(from < to) {
	        	//console.log('move right');
	        	for(var i = from; i < to; i++) {
	        		var row2 = this._getCells(this.element[0],i+1);
	        	//	console.log(row2)
	        		for(var j = 0, length = row2.array.length; j < length; j++){
	          			this._swapCells(this.currentColumnCollection[j],row2.array[j]);
	          		}
	          	}
	        } else {
	        	//console.log('move left');
	        	for(var i = from; i > to; i--) {
	            	var row2 = this._getCells(this.element[0],i-1);
	            	for(var j = 0, length = row2.array.length; j < length; j++){
	          			this._swapCells(row2.array[j],this.currentColumnCollection[j]);
	          		}
	        	}
	        }
	        this._eventHelper('change',{});
	        
	        this.startIndex = this.endIndex;
		},
		/*
		 * called when drag start is finished
		 */
		dropCol: function(){
			//TODO: cache this when the option is set
			var regex = new RegExp("(?:^|\\s)" + this.options.placeholder + "(?!\\S)",'g');
			//remove placeholder class
			//dont use jquery.fn.removeClass for performance reasons
			for(var i = 0, length = this.currentColumnCollection.length; i < length; i++){
				var td = this.currentColumnCollection[i];
				
				td.className = td.className.replace(regex,'')
			}
			

		},
		/*
		 * get / set the current order of the cols
		 */
		order: function(order){
			var self = this,
				elem = self.element,
				options = self.options,
				headers = elem.find('thead tr:first').children('th');
				
			
			if(order == undefined){
				//get
				var ret = [];
				headers.each(function(){
					var header = this.getAttribute(options.dataHeader);
					if(header == null){
						//the attr is missing so grab the text and use that
						header = $(this).text();
					}
					
					ret.push(header);
					
				});
				
				return ret;
				
			}else{
				//set
				//headers and order have to match up
				if(order.length != headers.length){
					//console.log('length not the same')
					return self;
				}
				for(var i = 0, length = order.length; i < length; i++){
					 
					 var start = headers.filter('['+ options.dataHeader +'='+ order[i] +']').index();
					 if(start != -1){
					 	//console.log('start index '+start+' - swap to '+i);
					 	self.startIndex = start;
					 	
						self.currentColumnCollection = self._getCells(self.element[0], start).array;

					 	self._swapCol(i);
					 }
					 
					 
				}
				return self;
			}
		},
				
		destroy: function() {
			var self = this,
				o = self.options;
			
			this.element.undelegate( o.items, 'mousedown.' + self.widgetEventPrefix );
			
			$( document ).unbind('.' + self.widgetEventPrefix )
            
		}

        
	});

})(jQuery);