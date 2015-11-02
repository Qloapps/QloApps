/* =========================================================
 * bootstrap-datepicker.js 
 * http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Copyright 2012 Stefan Petre
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

//click action
!function( $ ) {
	var click, switched, val, start, end, over, compare, startCompare, endCompare;

	// Picker object
	var DateRangePicker = function(element, options){
		this.element = $(element);
		compare = false;

		if (typeof options.dates !== 'undefined'){
			DPGlobal.dates = options.dates;
		}

		if (typeof options.start !== 'undefined'){
			if (options.start.constructor === String){
				start = DPGlobal.parseDate(options.start, DPGlobal.parseFormat('Y-m-d')).getTime();
			} else if (options.start.constructor === Number){
				start = options.start;
			} else if (options.start.constructor === Date){
				start = options.start.getTime();
			}
		}

		if (typeof options.end !== 'undefined'){
			if (options.end.constructor === String){
				end = DPGlobal.parseDate(options.end, DPGlobal.parseFormat('Y-m-d')).getTime();
			} else if (options.end.constructor === Number) {
				end = options.end;
			} else if (options.end.constructor === Date) {
				end = options.end.getTime();
			}
		}

		if (typeof options.compare !== 'undefined'){
			compare = options.compare;
		}

		this.format  = DPGlobal.parseFormat(options.format||this.element.data('date-format')||'Y-m-d');
		this.picker  = $(DPGlobal.template).appendTo(this.element).show()
			.on({
				click: $.proxy(this.click, this),
				mouseover: $.proxy(this.mouseover, this),
				mouseout: $.proxy(this.mouseout, this)
			});

		this.minViewMode = options.minViewMode||this.element.data('date-minviewmode')||0;
		if (typeof this.minViewMode === 'string') {
			switch (this.minViewMode) {
				case 'months':
					this.minViewMode = 1;
					break;
				case 'years':
					this.minViewMode = 2;
					break;
				default:
					this.minViewMode = 0;
					break;
			}
		}
		this.viewMode = options.viewMode||this.element.data('date-viewmode')||0;
		if (typeof this.viewMode === 'string') {
			switch (this.viewMode) {
				case 'months':
					this.viewMode = 1;
					break;
				case 'years':
					this.viewMode = 2;
					break;
				default:
					this.viewMode = 0;
					break;
			}
		}
		this.startViewMode = this.viewMode;
		this.weekStart = options.weekStart||this.element.data('date-weekstart')||0;
		this.weekEnd = this.weekStart === 0 ? 6 : this.weekStart - 1;
		this.onRender = options.onRender;
		this.fillDow();
		this.fillMonths();
		this.update();
		this.showMode();
	};

	DateRangePicker.prototype = {
		constructor: DateRangePicker,

		show: function(e) {
			this.picker.show();

			if (e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			var that = this;
			$(document).on('mousedown', function(ev){
				if ($(ev.target).closest('.daterangepicker').length === 0) {
					that.hide();
				}
			});
			this.element.trigger({
				type: 'show',
				date: this.date
			});
		},

		set: function() {
			var formated = DPGlobal.formatDate(this.date, this.format);
			this.element.data('date', formated);
		},

		setCompare: function(value) {
			compare = value;
			this.updateRange();
		},

		setStart: function(date) {
			if (date.constructor === String) {
				start = DPGlobal.parseDate(date, DPGlobal.parseFormat('Y-m-d')).getTime();
			} else if (date.constructor === Number){
				start = date;
			} else if (date.constructor === Date){
				start = date.getTime();
			}
		},

		setEnd: function(date) {
			if (date.constructor === String){
				end = DPGlobal.parseDate(date, DPGlobal.parseFormat('Y-m-d')).getTime();
			} else if (date.constructor === Number){
				end = date;
			} else if (date.constructor === Date){
				end = date.getTime();
			}
		},

		setStartCompare: function(date) {
			if (date === null){
				startCompare = date;
			}
			else if (date.constructor === String){
				startCompare = DPGlobal.parseDate(date, DPGlobal.parseFormat('Y-m-d')).getTime();
			}
			else if (date.constructor === Number){
				startCompare = date;
			}
			else if (date.constructor === Date){
				startCompare = date.getTime();
			}
		},

		setEndCompare: function(date) {
			if (date === null){
				endCompare = date;
			} else if (date.constructor === String){
				endCompare = DPGlobal.parseDate(date, DPGlobal.parseFormat('Y-m-d')).getTime();
			} else if (date.constructor === Number){
				endCompare = date;
			} else if (date.constructor === Date){
				endCompare = date.getTime();
			}
		},

		setValue: function(newDate) {
			if (typeof newDate === 'string') {
				this.date = DPGlobal.parseDate(newDate, this.format);
			} else {
				this.date = new Date(newDate);
			}
			this.set();
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},

		update: function(newDate){
			this.date = DPGlobal.parseDate(
				typeof newDate === 'string' ? newDate : (this.isInput ? this.element.prop('value') : this.element.data('date')),
				this.format
			);
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},

		fillDow: function(){
			var dowCnt = this.weekStart;
			var html = '<tr>';
			while (dowCnt < this.weekStart + 7) {
				html += '<th class="dow">'+DPGlobal.dates.daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.daterangepicker-days thead').append(html);
		},

		fillMonths: function(){
			var html = '';
			var i = 0;
			while (i < 12) {
				html += '<span class="month">'+DPGlobal.dates.monthsShort[i++]+'</span>';
			}
			this.picker.find('.daterangepicker-months td').append(html);
		},

		fill: function() {
			var d = new Date(this.viewDate),
				year = d.getFullYear(),
				month = d.getMonth(),
				currentDate = this.date.valueOf();
			this.picker.find('.daterangepicker-days th:eq(1)')
						.text(year+' / '+DPGlobal.dates.months[month]).append('&nbsp;<small><i class="icon-angle-down"></i><small>');
			var prevMonth = new Date(year, month-1, 28,0,0,0,0),
				day = DPGlobal.getDaysInMonth(prevMonth.getFullYear(), prevMonth.getMonth());
			prevMonth.setDate(day);
			prevMonth.setDate(day - (prevMonth.getDay() - this.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setDate(nextMonth.getDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName, prevY, prevM;
			while(prevMonth.valueOf() < nextMonth) {
				if (prevMonth.getDay() === this.weekStart) {
					html.push('<tr>');
				}
				clsName = this.onRender(prevMonth);
				prevY = prevMonth.getFullYear();
				prevM = prevMonth.getMonth();
				if ((prevM < month &&  prevY === year) ||  prevY < year) {
					clsName += ' old';
				} else if ((prevM > month && prevY === year) || prevY > year) {
					clsName += ' new';
				}
				if (!clsName){
					html.push('<td class="day" data-val="'+prevMonth.getTime()+'">' + prevMonth.getDate() + '</td>');
				} else {
					html.push('<td class="'+clsName+'"></td>');
				}

				if (prevMonth.getDay() === this.weekEnd) {
					html.push('</tr>');
				}
				prevMonth.setDate(prevMonth.getDate()+1);
			}
			this.picker.find('.daterangepicker-days tbody').empty().append(html.join(''));
			var currentYear = this.date.getFullYear();

			var months = this.picker.find('.daterangepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');
			if (currentYear === year) {
				months.eq(this.date.getMonth()).addClass('active');
			}

			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.daterangepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			for (var i = -1; i < 11; i++) {
				html += '<span class="year'+(i === -1 || i === 10 ? ' old' : '')+(currentYear === year ? ' active' : '')+'">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
			this.updateRange();
			//click = 2;
		},

		click: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var target = $(e.target).closest('span, td, th');
			if (target.length === 1) {
				switch(target[0].nodeName.toLowerCase()) {
					case 'th':
						switch(target[0].className) {
							case 'month-switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								this.viewDate['set'+DPGlobal.modes[this.viewMode].navFnc].call(
									this.viewDate,
									this.viewDate['get'+DPGlobal.modes[this.viewMode].navFnc].call(this.viewDate) +
									DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1)
								);

								this.date = new Date(this.viewDate);
								this.element.trigger({
									type: 'changeDate',
									date: this.date,
									viewMode: DPGlobal.modes[this.viewMode].clsName
								});

								this.fill();
								this.set();
								break;
						}
						break;

					case 'span':
						if (target.is('.month')) {
							var month = target.parent().find('span').index(target);
							this.viewDate.setMonth(month);
						} else {
							var year = parseInt(target.text(), 10)||0;
							this.viewDate.setFullYear(year);
						}
						if (this.viewMode !== 0) {
							this.date = new Date(this.viewDate);
							this.element.trigger({
								type: 'changeDate',
								date: this.date,
								viewMode: DPGlobal.modes[this.viewMode].clsName
							});
						}
						this.showMode(-1);
						this.fill();
						this.set();
						break;

					case 'td':
						//reset
						if (target.is('.day') && !target.is('.disabled')){
							// reset process for a new range
							if (start && end) {
								if (!compare) {
									click = 2 ;
									$(".range").removeClass('range');
									$(".start-selected").removeClass("start-selected");
									$(".end-selected").removeClass("end-selected");
								}
							}
							if(click === 2) {
								if (compare) {
									startCompare = null;
									endCompare = null;
								}
								else {
									start = null;
									end = null;
								}
								click = null;
								switched = false;
								if (compare) {
									$("td.day").removeClass("start-selected-compare").removeClass("end-selected-compare");
									$(".date-input").removeClass("input-selected").removeClass("input-complete");
									$(".range-compare").removeClass("range-compare");
								} else {
									$("td.day").removeClass("start-selected").removeClass("end-selected");
									$(".date-input").removeClass("input-selected").removeClass("input-complete");
									$(".range").removeClass("range");
								}
							}
							//define start with first click or switched one
							if (!click || switched === true) {
								if (compare) {
									$(".start-selected-compare").removeClass("start-selected-compare");
									target.addClass("start-selected-compare");
									startCompare = target.data("val");
									$("#date-start-compare").val(DPGlobal.formatDate(new Date(startCompare), DPGlobal.parseFormat('Y-m-d')));
								} else {
									$(".start-selected").removeClass("start-selected");
									target.addClass("start-selected");
									start = target.data("val");
									$("#date-start").val(DPGlobal.formatDate(new Date(start), DPGlobal.parseFormat('Y-m-d')));
									$('#date-start').trigger('change');
								}
																	
								if(!switched) {click = 1;} else {click = 2;}
								if(!switched) {
									if (compare) {
										$("#date-end-compare").val(null).focus().addClass("input-selected");
										target.addClass("start-selected-compare").addClass("end-selected-compare");
									} else {
										$("#date-end").val(null).focus().addClass("input-selected");
										target.addClass("start-selected").addClass("end-selected");
									}
								}

								if (compare) {
									$("#date-start-compare").removeClass("input-selected").addClass("input-complete");
								}
								else {
									$("#date-start").removeClass("input-selected").addClass("input-complete");
								}
							}
							//define end
							else {
								if (compare) {
									$(".end-selected-compare").removeClass("end-selected-compare");
									target.addClass("end-selected-compare");
									endCompare = target.data("val");
									$("#date-end-compare").val(DPGlobal.formatDate(new Date(endCompare), DPGlobal.parseFormat('Y-m-d')));
									click = 2;
									$("#date-end-compare").removeClass("input-selected").addClass("input-complete");
								} else {
									$(".end-selected").removeClass("end-selected");
									target.addClass("end-selected");
									end = target.data("val");
									$("#date-end").val(DPGlobal.formatDate(new Date(end), DPGlobal.parseFormat('Y-m-d')));
									click = 2;
									$("#date-end").removeClass("input-selected").addClass("input-complete");
									$('#date-end').trigger('change');
								}
							}
						}
						break;
				}
			}
		},

		updateRange: function() {
			$("#datepicker .day").each(function(){
				var date_val = parseInt($(this).data('val'),10);

				if (end && start) {
					if(date_val > start && date_val < end) {
						$(this).addClass("range");
					}
					if(date_val === start) {
						$(this).addClass("start-selected");
					}
					if(date_val === end) {
						$(this).addClass("end-selected");
					}
				}

				if (endCompare && startCompare) {
					$(this).removeClass("range-compare").removeClass("start-selected-compare").removeClass("end-selected-compare");

					if(date_val > startCompare && date_val < endCompare) {
						$(this).addClass("range-compare");
					}
					if(date_val === startCompare) {
						$(this).addClass("start-selected-compare");
					}
					if(date_val === endCompare) {
						$(this).addClass("end-selected-compare");
					}
				} else {
					$(this).removeClass("range-compare").removeClass("start-selected-compare").removeClass("end-selected-compare");
				}
			});
		},

		mouseoverRange: function(){
			//range
			$("#datepicker .day").each(function(){
				var date_val = parseInt($(this).data('val'),10);
				if (compare) {
					if (!endCompare && date_val > startCompare && date_val < over) {
						$(this).not(".old").not(".new").addClass("range-compare");
					}
					else if (!startCompare && date_val > over && date_val < endCompare) {
						$(this).not(".old").not(".new").addClass("range-compare");
					}
					else if (startCompare && endCompare) {
						$(this).addClass("range-compare");
					}
				}
				else {
					if (!end && date_val > start && date_val < over) {
						$(this).not(".old").not(".new").addClass("range");
					}
					else if (!start && date_val > over && date_val < end) {
						$(this).not(".old").not(".new").addClass("range");
					}
				}
			});
		},

		mouseover: function(e){
			//data-val from day overed
			over = $(e.target).data("val");

			//action when one of two dates has been set
			if(click === 1 && over){
				if (compare) {
					$("#datepicker .range-compare").removeClass("range-compare");

					if (startCompare && over < startCompare) {
						endCompare = startCompare;
						$("#date-end-compare").val(DPGlobal.formatDate(new Date(startCompare), DPGlobal.parseFormat('Y-m-d'))).removeClass("input-selected");
						$("#date-start-compare").val(null).focus().addClass("input-selected");
						$("#datepicker .start-selected-compare").removeClass("start-selected-compare").addClass("end-selected-compare");
						startCompare = null;
						switched = true;
					}
					else if (endCompare && over > endCompare) {
						startCompare = endCompare;
						$("#date-start-compare").val(DPGlobal.formatDate(new Date(endCompare), DPGlobal.parseFormat('Y-m-d'))).removeClass("input-selected");
						$("#date-end-compare").val(null).focus().addClass("input-selected");
						$("#datepicker .end-selected-compare").removeClass("end-selected-compare").addClass("start-selected-compare");
						endCompare = null;
						switched = false;
					}

					if (startCompare) {
						$(".end-selected-compare").removeClass("end-selected-compare");
						$(e.target).addClass("end-selected-compare");
					}
					else if (endCompare) {
						$(".start-selected-compare").removeClass("start-selected-compare");
						$(e.target).addClass("start-selected-compare");
					}
				}
				else {
					$("#datepicker .range").removeClass("range");

					if (start && over < start) {
						end = start;
						$("#date-end").val(DPGlobal.formatDate(new Date(start), DPGlobal.parseFormat('Y-m-d'))).removeClass("input-selected");
						$('#date-end').trigger('change');
						$("#date-start").val(null).focus().addClass("input-selected");
						$("#datepicker .start-selected").removeClass("start-selected").addClass("end-selected");
						start = null;
						switched = true;
					}
					else if (end && over > end) {
						start = end;
						$("#date-start").val(DPGlobal.formatDate(new Date(end), DPGlobal.parseFormat('Y-m-d'))).removeClass("input-selected");
						$('#date-start').trigger('change');
						$("#date-end").val(null).focus().addClass("input-selected");
						$("#datepicker .end-selected").removeClass("end-selected").addClass("start-selected");
						end = null;
						switched = false;
					}

					if (start) {
						$(".end-selected").removeClass("end-selected");
						$(e.target).addClass("end-selected");
					}
					else if (end) {
						$(".start-selected").removeClass("start-selected");
						$(e.target).addClass("start-selected");
					}
				}
				//switch
				$(".date-input").removeClass("input-complete");
				this.mouseoverRange();
			}
		},

		mouseout: function(){
			if (compare) {
				if (!startCompare||!endCompare) {
					$("#datepicker .range-compare").removeClass("range-compare");
				}
				if (!endCompare) {
					$(".end-selected-compare").removeClass("end-selected-compare");
				}
				else if (!startCompare)
					$(".start-selected-compare").removeClass("start-selected-compare");
			}
			else {
				if (!start||!end) {
					$("#datepicker .range").removeClass("range");
				}
				if (!end) {
					$(".end-selected").removeClass("end-selected");
				}
				else if (!start) {
					$(".start-selected").removeClass("start-selected");
				}
			}			
		},

		mousedown: function(e){
			e.stopPropagation();
			e.preventDefault();
		},

		showMode: function(dir) {
			if (dir)
				this.viewMode = Math.max(this.minViewMode, Math.min(2, this.viewMode + dir));

			this.picker.find('>div').hide().filter('.daterangepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
		}
	};

	$.fn.daterangepicker = function ( option, val ) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('daterangepicker'),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data('daterangepicker', (data = new DateRangePicker(this, $.extend({}, $.fn.daterangepicker.defaults,options))));
			}
			if (typeof option === 'string') { data[option](val);}
		});
	};

	$.fn.daterangepicker.defaults = {
		onRender: function() {
			return '';
		}
	};
	$.fn.daterangepicker.Constructor = DateRangePicker;

	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		dates:{
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
		},
		isLeapYear: function (year) {
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
		},
		getDaysInMonth: function (year, month) {
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
		},
		parseFormat: function(format){
			var separator = format.match(/[.\/\-\s].*?/),
				parts = format.split(/\W+/);
			if (!separator || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separator: separator, parts: parts};
		},
		parseDate: function(date, format) {
			var parts = date.split(format.separator),
				date = new Date(),
				val;
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);
			date.setMilliseconds(0);
			if (parts.length === format.parts.length) {
				var year = date.getFullYear(), day = date.getDate(), month = date.getMonth();
				for (var i=0, cnt = format.parts.length; i < cnt; i++) {
					val = parseInt(parts[i], 10)||1;
					switch(format.parts[i]) {
						case 'dd':
						case 'd':
							day = val;
							date.setDate(val);
							break;
						case 'mm':
						case 'm':
							month = val - 1;
							date.setMonth(val - 1);
							break;
						case 'yy':
						case 'y':
							year = 2000 + val;
							date.setFullYear(2000 + val);
							break;
						case 'yyyy':
						case 'Y':
							year = val;
							date.setFullYear(val);
							break;
					}
				}
				date = new Date(year, month, day, 0 ,0 ,0);
			}
			return date;
		},
		formatDate: function(date, format){
			var val = {
				d: date.getDate(),
				m: date.getMonth() + 1,
				yy: date.getFullYear().toString().substring(2),
				y: date.getFullYear().toString().substring(2),
				yyyy: date.getFullYear(),
				Y: date.getFullYear()
			};
			val.d = (val.d < 10 ? '0' : '') + val.d;
			val.m = (val.m < 10 ? '0' : '') + val.m;
			var date = [];
			for (var i=0, cnt = format.parts.length; i < cnt; i++) {
				date.push(val[format.parts[i]]);
			}
			return date.join(format.separator);
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev"><i class="icon-angle-left"></i></th>'+
								'<th colspan="5" class="month-switch"></th>'+
								'<th class="next"><i class="icon-angle-right"</th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>'
	};
	DPGlobal.template = '<div class="daterangepicker">'+
							'<div class="daterangepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
								'</table>'+
							'</div>'+
							'<div class="daterangepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
							'<div class="daterangepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
						'</div>';

}( window.jQuery );