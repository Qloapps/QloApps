/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
//global variables
var responsiveflag = false;

$(document).ready(function(){
	highdpiInit();
	responsiveResize();
	$(window).resize(responsiveResize);
	if (navigator.userAgent.match(/Android/i))
	{
		var viewport = document.querySelector('meta[name="viewport"]');
		viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
		window.scrollTo(0, 1);
	}
	if (typeof quickView !== 'undefined' && quickView)
		quick_view();
	dropDown();

	if (typeof page_name != 'undefined' && !in_array(page_name, ['index', 'product']))
	{
		bindGrid();

 		$(document).on('change', '.selectProductSort', function(e){
			if (typeof request != 'undefined' && request)
				var requestSortProducts = request;
 			var splitData = $(this).val().split(':');
 			var url = '';
			if (typeof requestSortProducts != 'undefined' && requestSortProducts)
			{
				url += requestSortProducts ;
				if (typeof splitData[0] !== 'undefined' && splitData[0])
				{
					url += ( requestSortProducts.indexOf('?') < 0 ? '?' : '&') + 'orderby=' + splitData[0] + (splitData[1] ? '&orderway=' + splitData[1] : '');
					if (typeof splitData[1] !== 'undefined' && splitData[1])
						url += '&orderway=' + splitData[1];
				}
				document.location.href = url;
			}
    	});

		$(document).on('change', 'select[name="n"]', function(){
			$(this.form).submit();
		});

		$(document).on('change', 'select[name="currency_payment"]', function(){
			setCurrency($(this).val());
		});
	}

	$(document).on('change', 'select[name="manufacturer_list"], select[name="supplier_list"]', function(){
		if (this.value != '')
			location.href = this.value;
	});

	$(document).on('click', '.back', function(e){
		e.preventDefault();
		history.back();
	});

	jQuery.curCSS = jQuery.css;
	if (!!$.prototype.cluetip)
		$('a.cluetip').cluetip({
			local:true,
			cursor: 'pointer',
			dropShadow: false,
			dropShadowSteps: 0,
			showTitle: false,
			tracking: true,
			sticky: false,
			mouseOutClose: true,
			fx: {
				open:       'fadeIn',
				openSpeed:  'fast'
			}
		}).css('opacity', 0.8);

	if (typeof(FancyboxI18nClose) !== 'undefined' && typeof(FancyboxI18nNext) !== 'undefined' && typeof(FancyboxI18nPrev) !== 'undefined' && !!$.prototype.fancybox)
		$.extend($.fancybox.defaults.tpl, {
			closeBtn : '<a title="' + FancyboxI18nClose + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
			next     : '<a title="' + FancyboxI18nNext + '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
			prev     : '<a title="' + FancyboxI18nPrev + '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
		});

	// Close Alert messages
	$(".alert.alert-danger").on('click', this, function(e){
		if (e.offsetX >= 16 && e.offsetX <= 39 && e.offsetY >= 16 && e.offsetY <= 34)
			$(this).fadeOut();
	});

	$(document).on('click', '.booking_occupancy_wrapper .remove-room-link', function(e) {
        e.preventDefault();

		booking_occupancy_inner = $(this).closest('.booking_occupancy_inner');
        $(this).closest('.occupancy_info_block').remove();
		$(booking_occupancy_inner).find('.room_num_wrapper').each(function(key, val) {
            $(this).text(room_txt + ' - '+ (key+1) );
        });
        setRoomTypeGuestOccupancy($(booking_occupancy_inner).closest('.booking_occupancy_wrapper'));
    });

	$(document).on('click', '.booking_occupancy_wrapper .occupancy_quantity_up', function(e) {
        e.preventDefault();
		// set input field value
		let max_guests_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_guests').val();
        let element = $(this).closest('.occupancy_count_block').find('.num_occupancy');
		let elementVal = parseInt(element.val());

		let current_room_occupancy = 0;
		$(this).closest('.occupancy_info_block').find('.num_occupancy').each(function(){
			current_room_occupancy += parseInt($(this).val());
		});
		let max_allowed_for_current = (max_guests_in_room - current_room_occupancy) + elementVal;

        let childElement = $(this).closest('.occupancy_count_block').find('.num_children').length;
        if (childElement) {
			let max_child_in_room;
			if ($(this).closest(".booking_occupancy_wrapper").find('.max_children').val()) {
				max_child_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_children').val();
			} else {
				max_child_in_room = window.max_child_in_room;
			}
            if (elementVal < max_child_in_room && elementVal < max_allowed_for_current) {
                element.val(elementVal + 1);
                $(this).closest('.occupancy_info_block').find('.children_age_info_block').show();

                let roomBlockIndex = parseInt($(this).closest('.occupancy_info_block').attr('occ_block_index'));

                let childAgeSelect = '<div class="col-xs-6 col-sm-12 col-md-6">';
                    childAgeSelect += '<select class="guest_child_age room_occupancies" name="occupancy[' +roomBlockIndex+ '][child_ages][]">';
                        childAgeSelect += '<option value="-1">' + select_age_txt + '</option>';
                        childAgeSelect += '<option value="0">' + under_1_age + '</option>';
                        for (let age = 1; age < max_child_age; age++) {
                            childAgeSelect += '<option value="'+age+'">'+age+'</option>';
                        }
                    childAgeSelect += '</select>';
                childAgeSelect += '</div>';

                $(this).closest('.occupancy_info_block').find('.children_ages').append(childAgeSelect);

                // set input field value
                $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal + 1);
            }
        } else {
			let max_adults_in_room;
			if ($(this).closest(".booking_occupancy_wrapper").find('.max_adults').val()) {
				max_adults_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_adults').val();
			}
			if (elementVal < max_adults_in_room && elementVal < max_allowed_for_current) {
				element.val(elementVal + 1);
				$(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal + 1);
			}
        }
        setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));
    });

	$(document).on('click', '.booking_occupancy_wrapper .occupancy_quantity_down', function(e) {
        e.preventDefault();

        // set input field value
        var element = $(this).closest('.occupancy_count_block').find('.num_occupancy');
        var elementVal = parseInt(element.val()) - 1;
        var childElement = $(this).closest('.occupancy_count_block').find('.num_children').length;

        if (childElement) {
            if (elementVal < 0) {
                elementVal = 0;
            } else {
                $(this).closest('.occupancy_info_block').find('.children_ages select').last().closest('div').remove();
                if (elementVal <= 0) {
                    $(this).closest('.occupancy_info_block').find('.children_age_info_block').hide();
                }
            }
        } else {
            if (elementVal == 0) {
                elementVal = 1;
            }
        }

        element.val(elementVal);
        // set input field value
        $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal);

        setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));
    });

	$(document).on('click', '.booking_guest_occupancy', function(e) {
		$(this).parent().toggleClass('open');
    });

	$(document).on('click', function(e) {
        if ($('.booking_occupancy_wrapper:visible').length) {
			var occupancy_wrapper = $('.booking_occupancy_wrapper:visible');
			$(occupancy_wrapper).find(".occupancy_info_block").addClass('selected');
			setRoomTypeGuestOccupancy(occupancy_wrapper);
            if (!($(e.target).closest(".booking_occupancy_wrapper").length || $(e.target).closest(".booking_guest_occupancy").length || $(e.target).closest(".ajax_add_to_cart_button").length || $(e.target).closest(".exclusive.book_now_submit").length)) {
				let hasErrors = 0;

                let adult = $(occupancy_wrapper).find(".num_adults").map(function(){return $(this).val();}).get();
                let children = $(occupancy_wrapper).find(".num_children").map(function(){return $(this).val();}).get();
                let child_ages = $(occupancy_wrapper).find(".guest_child_age").map(function(){return $(this).val();}).get();

                // start validating above values
                if (!adult.length || (adult.length != children.length)) {
                    hasErrors = 1;
                    showErrorMessage(invalid_occupancy_txt);
                } else {
                    $(occupancy_wrapper).find('.occupancy_count').removeClass('error_border');

                    // validate values of adult and children
                    adult.forEach(function (item, index) {
                        if (isNaN(item) || parseInt(item) < 1) {
                            hasErrors = 1;
                            $(occupancy_wrapper).find(".num_adults").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('error_border');
                        }
                        if (isNaN(children[index])) {
                            hasErrors = 1;
                            $(occupancy_wrapper).find(".num_children").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('error_border');
                        }
                    });

                    // validate values of selected child ages
                    $(occupancy_wrapper).find('.guest_child_age').removeClass('error_border');
                    child_ages.forEach(function (age, index) {
                        age = parseInt(age);
                        if (isNaN(age) || (age < 0) || (age >= parseInt(max_child_age))) {
                            hasErrors = 1;
                            $(occupancy_wrapper).find(".guest_child_age").eq(index).addClass('error_border');
                        }
                    });
                }
                if (hasErrors == 0) {
					$(occupancy_wrapper).parent().removeClass('open');
					$(occupancy_wrapper).siblings(".booking_guest_occupancy").removeClass('error_border');

                    $(document).trigger( "QloApps:updateRoomOccupancy", [occupancy_wrapper]);
                } else {
                    $(occupancy_wrapper).siblings(".booking_guest_occupancy").addClass('error_border');
                }
			}
        }
    });

	$(document).on('click', '.booking_occupancy_wrapper .add_new_occupancy_btn', function(e) {
        e.preventDefault();

        var booking_occupancy_wrapper = $(this).closest('.booking_occupancy_wrapper');
        var occupancy_block = '';
        var roomBlockIndex = parseInt($(booking_occupancy_wrapper).find(".occupancy_info_block").last().attr('occ_block_index'));
        roomBlockIndex += 1;


        var countRooms = parseInt($(booking_occupancy_wrapper).find('.occupancy_info_block').length);
        countRooms += 1
        if ($(booking_occupancy_wrapper).find('.max_avail_type_qty').val() > 0
			&& countRooms <= $(booking_occupancy_wrapper).find('.max_avail_type_qty').val()
		) {
            occupancy_block += '<div class="occupancy_info_block" occ_block_index="'+roomBlockIndex+'">';
                occupancy_block += '<div class="occupancy_info_head"><span class="room_num_wrapper">'+ room_txt + ' - ' + countRooms + '</span><a class="remove-room-link pull-right" href="#">' + remove_txt + '</a></div>';
                occupancy_block += '<div class="row">';
                    occupancy_block += '<div class="form-group col-sm-5 col-xs-6 occupancy_count_block">';
                        occupancy_block += '<div class="row">';
                            occupancy_block += '<label class="col-sm-12">' + adults_txt + '</label>';
                            occupancy_block += '<div class="col-sm-12">';
                                occupancy_block += '<input type="hidden" class="num_occupancy num_adults" name="occupancy['+roomBlockIndex+'][adult]" value="1">';
                                occupancy_block += '<div class="occupancy_count pull-left">';
                                    occupancy_block += '<span>1</span>';
                                occupancy_block += '</div>';
                                occupancy_block += '<div class="qty_direction pull-left">';
                                    occupancy_block += '<a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_up">';
                                        occupancy_block += '<span><i class="icon-plus"></i></span>';
                                    occupancy_block += '</a>';
                                    occupancy_block += '<a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_down">';
                                        occupancy_block += '<span><i class="icon-minus"></i></span>';
                                    occupancy_block += '</a>';
                                occupancy_block += '</div>';
                            occupancy_block += '</div>';
                        occupancy_block += '</div>';
                    occupancy_block += '</div>';
                    occupancy_block += '<div class="form-group col-sm-7 col-xs-6 occupancy_count_block">';
                        occupancy_block += '<div class="row">';
                            occupancy_block += '<label class="col-sm-12">' + child_txt + '<span class="label-desc-txt">(' + below_txt + ' ' + max_child_age + ' ' + years_txt + ')</span></label>';
                            occupancy_block += '<div class="col-sm-12">';
                                occupancy_block += '<input type="hidden" class="num_occupancy num_children room_occupancies" name="occupancy['+roomBlockIndex+'][children]" value="0">';
                                occupancy_block += '<div class="occupancy_count pull-left">';
                                    occupancy_block += '<span>0</span>';
                                occupancy_block += '</div>';
                                occupancy_block += '<div class="qty_direction pull-left">';
                                    occupancy_block += '<a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_up">';
                                        occupancy_block += '<span><i class="icon-plus"></i></span>';
                                    occupancy_block += '</a>';
                                    occupancy_block += '<a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_down">';
                                        occupancy_block += '<span><i class="icon-minus"></i></span>';
                                    occupancy_block += '</a>';
                                occupancy_block += '</div>';
                            occupancy_block += '</div>';
                        occupancy_block += '</div>';
                    occupancy_block += '</div>';
                occupancy_block += '</div>';
                occupancy_block += '<div class="form-group row children_age_info_block">';
                    occupancy_block += '<label class="col-sm-12">' + all_children_txt + '</label>';
                    occupancy_block += '<div class="col-sm-12">';
                        occupancy_block += '<div class="row children_ages">';
                        occupancy_block += '</div>';
                    occupancy_block += '</div>';
                occupancy_block += '</div>';
                occupancy_block += '<hr class="occupancy-info-separator">';
            occupancy_block += '</div>';

            $(booking_occupancy_wrapper).find('.booking_occupancy_inner').append(occupancy_block);

            // scroll to the latest added room
            // var objDiv = document.getElementById("booking_occupancy_wrapper");
            // objDiv.scrollTop = objDiv.scrollHeight;
			$(booking_occupancy_wrapper).animate({ scrollTop: $(booking_occupancy_wrapper).prop('scrollHeight') }, "slow");

        }

        setRoomTypeGuestOccupancy(booking_occupancy_wrapper);
    });

	// The button to increment the product value
	$(document).on('click', '.rm_quantity_up', function(e){
		e.preventDefault();

		var element = $(this).closest('.rm_qty_cont').find('.quantity_wanted');
		var elementVal = parseInt(element.val()) + 1;
		let quantityAvailableT = $(this).closest('.rm_qty_cont').find(".max_avail_type_qty").val();
		if (isNaN(elementVal) || elementVal > quantityAvailableT) {
			elementVal = quantityAvailableT;
		}
		element.val(elementVal);
		$(this).closest('.rm_qty_cont').find('.qty_count > span').text(elementVal);
		$(document).trigger( "QloApps:updateRoomQuantity", [element]);
	});

	// The button to decrement the product value
	$(document).on('click', '.rm_quantity_down', function(e){
		e.preventDefault();
		var element = $(this).closest('.rm_qty_cont').find('.quantity_wanted');
		var elementVal = parseInt(element.val()) - 1;
		if (isNaN(elementVal) || elementVal < 1) {
			elementVal = 1;
		}

		element.val(elementVal);
		$(this).closest('.rm_qty_cont').find('.qty_count > span').text(elementVal);
		$(document).trigger( "QloApps:updateRoomQuantity", [element]);

	});
});

function setRoomTypeGuestOccupancy(booking_occupancy_wrapper)
{
    var adult = 0;
    var children = 0;
	var rooms = $(booking_occupancy_wrapper).find('.occupancy_info_block').length;

	$(booking_occupancy_wrapper).find(".num_adults" ).each(function(key, val) {
        adult += parseInt($(this).val());
    });
    $(booking_occupancy_wrapper).find(".num_children" ).each(function(key, val) {
        children += parseInt($(this).val());
    });
	guestButtonVal = getRoomTypeGuestOccupancyFormated(adult, children, rooms);
	// console.log($(booking_occupancy_wrapper).siblings('.booking_guest_occupancy > span'));
	$(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(guestButtonVal);
}

function getRoomTypeGuestOccupancyFormated(adult, children, rooms)
{
	var guestButtonVal = parseInt(adult) + ' ';
    if (parseInt(adult) > 1) {
        guestButtonVal += adults_txt;
    } else {
        guestButtonVal += adult_txt;
    }
    if (parseInt(children) > 0) {
        if (parseInt(children) > 1) {
            guestButtonVal += ', ' + parseInt(children) + ' ' + children_txt;
        } else {
            guestButtonVal += ', ' + parseInt(children) + ' ' + child_txt;
        }
    }
    if (parseInt(rooms) > 1) {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + rooms_txt;
    } else {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + room_txt;
    }

	return guestButtonVal;
}


function resetOccupancyField(booking_occupancy_wrapper)
{
	$(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(select_occupancy_txt);
	$(booking_occupancy_wrapper).find('.booking_occupancy_inner > div').each(function(index, element){
		let num_adults = $(booking_occupancy_wrapper).find('.base_adult').val();
		if (index == 0) {
			$(this).removeClass('selected');
			$(this).find('.num_adults').val(num_adults).siblings('.occupancy_count').find('span').text(num_adults);
			$(this).find('.num_children').val(0).siblings('.occupancy_count').find('span').text(0);
			$(this).find('.children_ages > div').remove();
		} else {
			$(element).remove();
		}
	});
}


function highdpiInit()
{
	if (typeof highDPI === 'undefined')
		return;
	if(highDPI && $('.replace-2x').css('font-size') == "1px")
	{
		var els = $("img.replace-2x").get();
		for(var i = 0; i < els.length; i++)
		{
			src = els[i].src;
			extension = src.substr( (src.lastIndexOf('.') +1) );
			src = src.replace("." + extension, "2x." + extension);

			var img = new Image();
			img.src = src;
			img.height != 0 ? els[i].src = src : els[i].src = els[i].src;
		}
	}
}


// Used to compensante Chrome/Safari bug (they don't care about scroll bar for width)
function scrollCompensate()
{
	var inner = document.createElement('p');
	inner.style.width = "100%";
	inner.style.height = "200px";

	var outer = document.createElement('div');
	outer.style.position = "absolute";
	outer.style.top = "0px";
	outer.style.left = "0px";
	outer.style.visibility = "hidden";
	outer.style.width = "200px";
	outer.style.height = "150px";
	outer.style.overflow = "hidden";
	outer.appendChild(inner);

	document.body.appendChild(outer);
	var w1 = inner.offsetWidth;
	outer.style.overflow = 'scroll';
	var w2 = inner.offsetWidth;
	if (w1 == w2) w2 = outer.clientWidth;

	document.body.removeChild(outer);

	return (w1 - w2);
}

function responsiveResize()
{
	compensante = scrollCompensate();
	if (($(window).width()+scrollCompensate()) <= 767 && responsiveflag == false)
	{
		accordion('enable');
		accordionFooter('enable');
		responsiveflag = true;
	}
	else if (($(window).width()+scrollCompensate()) >= 768)
	{
		accordion('disable');
		accordionFooter('disable');
		responsiveflag = false;
		// if (typeof bindUniform !=='undefined')
		// 	bindUniform();
	}
	blockHover();
}

function blockHover(status)
{
	var screenLg = $('body').find('.container').width() == 1170;

	if ($('.product_list').is('.grid'))
		if (screenLg)
			$('.product_list .button-container').hide();
		else
			$('.product_list .button-container').show();

	$(document).off('mouseenter').on('mouseenter', '.product_list.grid li.ajax_block_product .product-container', function(e){
		if (screenLg)
		{
			var pcHeight = $(this).parent().outerHeight();
			var pcPHeight = $(this).parent().find('.button-container').outerHeight() + $(this).parent().find('.comments_note').outerHeight() + $(this).parent().find('.functional-buttons').outerHeight();
			$(this).parent().addClass('hovered').css({'height':pcHeight + pcPHeight, 'margin-bottom':pcPHeight * (-1)});
			$(this).find('.button-container').show();
		}
	});

	$(document).off('mouseleave').on('mouseleave', '.product_list.grid li.ajax_block_product .product-container', function(e){
		if (screenLg)
		{
			$(this).parent().removeClass('hovered').css({'height':'auto', 'margin-bottom':'0'});
			$(this).find('.button-container').hide();
		}
	});
}

function quick_view()
{
	$(document).on('click', '.quick-view:visible, .quick-view-mobile:visible', function(e){
		e.preventDefault();
		var url = this.rel;
		var anchor = '';

		if (url.indexOf('#') != -1)
		{
			anchor = url.substring(url.indexOf('#'), url.length);
			url = url.substring(0, url.indexOf('#'));
		}

		if (url.indexOf('?') != -1)
			url += '&';
		else
			url += '?';

		if (!!$.prototype.fancybox)
			$.fancybox({
				'padding':  0,
				'width':    1087,
				'height':   610,
				'type':     'iframe',
				'href':     url + 'content_only=1' + anchor
			});
	});
}

function bindGrid()
{
	var storage = false;
	if (typeof(getStorageAvailable) !== 'undefined') {
		storage = getStorageAvailable();
	}
	if (!storage) {
		return;
	}

	var view = $.totalStorage('display');

	if (!view && (typeof displayList != 'undefined') && displayList)
		view = 'list';

	if (view && view != 'grid')
		display(view);
	else
		$('.display').find('li#grid').addClass('selected');

	$(document).on('click', '#grid', function(e){
		e.preventDefault();
		display('grid');
	});

	$(document).on('click', '#list', function(e){
		e.preventDefault();
		display('list');
	});
}

function display(view)
{
	if (view == 'list')
	{
		$('ul.product_list').removeClass('grid').addClass('list row');
		$('.product_list > li').removeClass('col-xs-12 col-sm-6 col-md-4').addClass('col-xs-12');
		$('.product_list > li').each(function(index, element) {
			var html = '';
			html = '<div class="product-container"><div class="row">';
				html += '<div class="left-block col-xs-4 col-sm-5 col-md-4">' + $(element).find('.left-block').html() + '</div>';
				html += '<div class="center-block col-xs-4 col-sm-7 col-md-4">';
					html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
					html += '<h5 itemprop="name">'+ $(element).find('h5').html() + '</h5>';
					var rating = $(element).find('.comments_note').html(); // check : rating
					if (rating != null) {
						html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">'+ rating + '</div>';
					}
					html += '<p class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
					var colorList = $(element).find('.color-list-container').html();
					if (colorList != null) {
						html += '<div class="color-list-container">'+ colorList +'</div>';
					}
					var availability = $(element).find('.availability').html();	// check : catalog mode is enabled
					if (availability != null) {
						html += '<span class="availability">'+ availability +'</span>';
					}
				html += '</div>';
				html += '<div class="right-block col-xs-4 col-sm-12 col-md-4"><div class="right-block-content row">';
					var price = $(element).find('.content_price').html();       // check : catalog mode is enabled
					if (price != null) {
						html += '<div class="content_price col-xs-5 col-md-12">'+ price + '</div>';
					}
					html += '<div class="button-container col-xs-7 col-md-12">'+ $(element).find('.button-container').html() +'</div>';
					html += '<div class="functional-buttons clearfix col-sm-12">' + $(element).find('.functional-buttons').html() + '</div>';
				html += '</div>';
			html += '</div></div>';
			$(element).html(html);
		});
		$('.display').find('li#list').addClass('selected');
		$('.display').find('li#grid').removeAttr('class');
		$.totalStorage('display', 'list');
	}
	else
	{
		$('ul.product_list').removeClass('list').addClass('grid row');
		$('.product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-4');
		$('.product_list > li').each(function(index, element) {
			var html = '';
			html += '<div class="product-container">';
			html += '<div class="left-block">' + $(element).find('.left-block').html() + '</div>';
			html += '<div class="right-block">';
				html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
				html += '<h5 itemprop="name">'+ $(element).find('h5').html() + '</h5>';
				var rating = $(element).find('.comments_note').html(); // check : rating
					if (rating != null) {
						html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">'+ rating + '</div>';
					}
				html += '<p itemprop="description" class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
				var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
					if (price != null) {
						html += '<div class="content_price">'+ price + '</div>';
					}
				html += '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="button-container">'+ $(element).find('.button-container').html() +'</div>';
				var colorList = $(element).find('.color-list-container').html();
				if (colorList != null) {
					html += '<div class="color-list-container">'+ colorList +'</div>';
				}
				var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
				if (availability != null) {
					html += '<span class="availability">'+ availability +'</span>';
				}
			html += '</div>';
			html += '<div class="functional-buttons clearfix">' + $(element).find('.functional-buttons').html() + '</div>';
			html += '</div>';
			$(element).html(html);
		});
		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$.totalStorage('display', 'grid');
	}
}

function dropDown()
{
	elementClick = '#header .current, #footer .current';
	elementSlide =  'ul.toogle_content';
	activeClass = 'active';
	$(elementClick).on('click', function(e){
		e.stopPropagation();
		var subUl = $(this).next(elementSlide);
		if(subUl.is(':hidden'))
		{
			subUl.slideDown();
			$(this).addClass(activeClass);
		}
		else
		{
			subUl.slideUp();
			$(this).removeClass(activeClass);
		}
		$(elementClick).not(this).next(elementSlide).slideUp();
		$(elementClick).not(this).removeClass(activeClass);
		e.preventDefault();
	});

	$(elementSlide).on('click', function(e){
		e.stopPropagation();
	});

	$(document).on('click', function(e){
		e.stopPropagation();
		var elementHide = $(elementClick).next(elementSlide);
		$(elementHide).slideUp();
		$(elementClick).removeClass('active');
	});
}

function accordionFooter(status)
{
	if(status == 'enable')
	{
		$('#footer .footer-block h4').on('click', function(e){
			$(this).toggleClass('active').parent().find('.toggle-footer').stop().slideToggle('medium');
			e.preventDefault();
		})
		$('#footer').addClass('accordion').find('.toggle-footer').slideUp('fast');
	}
	else
	{
		$('.footer-block h4').removeClass('active').off().parent().find('.toggle-footer').removeAttr('style').slideDown('fast');
		$('#footer').removeClass('accordion');
	}
}

function accordion(status)
{
	if(status == 'enable')
	{
		var accordion_selector = '#right_column .block .title_block, #left_column .block .title_block, #left_column #newsletter_block_left h4,' +
								'#left_column .shopping_cart > a:first-child, #right_column .shopping_cart > a:first-child';

		$(accordion_selector).on('click', function(e){
			$(this).toggleClass('active').parent().find('.block_content').stop().slideToggle('medium');
		});
		$('#right_column, #left_column').addClass('accordion').find('.block .block_content').slideUp('fast');
		if (typeof(ajaxCart) !== 'undefined')
			ajaxCart.collapse();
	}
	else
	{
		$('#right_column .block .title_block, #left_column .block .title_block, #left_column #newsletter_block_left h4').removeClass('active').off().parent().find('.block_content').removeAttr('style').slideDown('fast');
		$('#left_column, #right_column').removeClass('accordion');
	}
}

function bindUniform()
{
	if (!!$.prototype.uniform) {
		$("select.form-control,input[type='radio'],input[type='checkbox']").not(".not_uniform").uniform();
	}
}

/*Growl plulin implementation to show notifications on front office*/
function showSuccessMessage(msg) {
	$.growl.notice({ title: "", message:msg});
}

function showErrorMessage(msg) {
	$.growl.error({ title: "", message:msg});
}

function showNoticeMessage(msg) {
	$.growl.notice({ title: "", message:msg});
}


// highlight dates of the selected date range
function highlightSelectedDateRange(date, checkIn, checkOut)
{
    if (checkIn || checkOut) {
        // Lets make the date in the required format
        var currentDate = date.getDate();
        var currentMonth = date.getMonth()+1;
        if (currentMonth < 10) {
            currentMonth = '0' + currentMonth;
        }
        if (currentDate < 10) {
            currentDate = '0' + currentDate;
        }
        dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;

        if (checkIn) {
            checkIn = checkIn.split("-");
            checkIn = (checkIn[2]) + '-' + (checkIn[1]) + '-' + (checkIn[0]);
        }
        if (checkOut) {
            checkOut = checkOut.split("-");
            checkOut = (checkOut[2]) + '-' + (checkOut[1]) + '-' + (checkOut[0]);
        }

        if (dmy == checkIn || dmy == checkOut) {
            return [true, 'selectedCheckedDate', ''];
        } else if ((checkIn && checkOut) && (dmy >= checkIn && dmy <= checkOut)) {
            return [true, 'in-select-date-range', ''];
        }
    }

    return [true, ''];
}
