$(document).ready(function()
{
	$(".clear_filter").on("click", function()
	{
		var filter_wrapper = $(this).parents(".layered_filter_heading").siblings(".lf_sub_cont");
		var make_diff = filter_wrapper.children("div"); //use to make difference between checkbox and price slider
		if (make_diff.hasClass("layered_filt")) // for checkbox
		{
			var selected_filter = filter_wrapper.find("div.layered_filt input.filter:checked").prop("checked", false);
			selected_filter.parent("span.checked").removeClass("checked");

			if (selected_filter.length)
			{
				triggerFilter();
			}
		}
		else if (make_diff.hasClass("price_filter_subcont")) //for price bar
		{
			ini_price = $("#filter_price_silder").slider("values");
			if ((ini_price[0] - ini_price[1]) != (max_price - min_price))
			{
				$("#filter_price_silder").slider("values", [min_price, max_price]);

				var slider_price_cont = filter_wrapper.find("div.price_filter_subcont");
				slider_price_cont.find("span#filter_price_from").html(min_price);
				slider_price_cont.find("span#filter_price_to").html(max_price);

				/* -------NOTICE------- */
				//triggerFilter();
				//on need of triggerFilter as it is triggered from base function(from which slider is created)
			}
		}
	});

	function triggerFilter(way, sort_by, sort_value, filter_price)
	{
		if (way === undefined) 
	        way = 0;
	    if (filter_price === undefined) 
	        filter_price = 1;
	    if (sort_by === undefined && sort_value === undefined) 
	    {
	    	var sort_filter = $(".sort_btn_span[data-sort-value!='0']");
	    	if (sort_filter.length)
	    	{
	    		sort_by = sort_filter.attr("data-sort-by");
	    		sort_value = sort_filter.attr("data-sort-value");
	    	}
	    	else
	    	{
		        sort_by = 0;
		        sort_value = 0;
	    	}
	    }

		var filter_data = {};
		filter_data = createFilterObj(filter_data, filter_price);
		getFilterResult(filter_data, way, sort_by, sort_value);
	}

	var slider_diff = 0;

	$("#filter_price_silder").slider(
	{
		range: true,
		min: min_price,
		max: max_price,
		values: [min_price, max_price],
		slide: function(event, ui) 
		{
			$("#filter_price_from").html(ui.values[0]);
			$("#filter_price_to").html(ui.values[1]);
		},
		change: function(event, ui)
		{
			if (slider_diff != (parseInt(ui.values[0]) - parseInt(ui.values[1])))
			{
				slider_diff = parseInt(ui.values[0]) - parseInt(ui.values[1]);

				triggerFilter();
			}
		}
	});
	
	$(".sort_result").on("click", function(e)
	{
		e.preventDefault();

		$('.sort_btn_span').attr('data-sort-by', 0);
		$('.sort_btn_span').attr('data-sort-value', 0);

		$('#gst_rating .sort_btn_span').html($('#gst_rating .sort_btn_span').attr('data-sort-for'));
		$('#price_ftr .sort_btn_span').html($('#price_ftr .sort_btn_span').attr('data-sort-for'));

		// select btn data enter
		var sort_text = $(this).html();
		var dp_btn_span = $(this).parents('div.filter_dw_cont').find('button span.sort_btn_span');
		dp_btn_span.html(sort_text);
		dp_btn_span.attr('data-sort-by', $(this).attr('data-sort-by'));
		dp_btn_span.attr('data-sort-value', $(this).attr('data-value'));

		var sort_by = $(this).attr('data-sort-by');
		var sort_value = $(this).attr('data-value');

		triggerFilter(0, sort_by, sort_value);
	});

	var filter_ajax = '';

	triggerFilter(1, 0, 0, 0);
	$('.filter').on('click', function()
	{
		triggerFilter();
	});

	function createFilterObj(filter, filter_price)
	{
		$('.filter').each(function()
		{
			if ($(this).is(':checked'))
			{
				var temp_type = $(this).attr('data-type');
				if (typeof filter[temp_type] != 'undefined')
				{
					filter[temp_type].push($(this).val());
				}
				else
				{
					filter[temp_type] = [];
					filter[temp_type].push($(this).val());
				}
			}
		});

		if (filter_price)
		{
			var slider_val = $("#filter_price_silder").slider("values");
			filter['price'] = [];
			filter['price'].push(slider_val[0]);
			filter['price'].push(slider_val[1]);
		}

		return filter;
	}

	function getFilterResult(data, way, sort_by, sort_value)
	{
		if (way && !Object.getOwnPropertyNames(data).length)
			return false;

		if (filter_ajax)
			filter_ajax.abort();

		data = { ajax_filter : 1, filter_data: data};

		if (sort_by && sort_value)
		{
			data.sort_by = sort_by;
			data.sort_value = sort_value;
		}

		filter_ajax = $.ajax(
		{
            url: cat_link,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (result)
            {
				console.log(result);
            	if (result)
            	{
            		$('#category_data_cont').empty();

            		$.each(result.rm_data, function(key, value)
            		{
            			if (value.data.available.length) 
            			{
	            			var html  = '<div class="col-sm-12 room_cont">';
	            					html += '<div class="row">';
	            						html += '<div class="col-sm-4">';
	            							html += '<a href="'+value.product_link+'">';
	            								html += '<img src="'+value.image+'" class="img-responsive">';
	            							html += '</a>';
	            						html += '</div>';
	            						html += '<div class="col-sm-8">';
	            							html += '<p class="rm_heading">'+value.name+'</p>';
	            							html += '<div class="rm_desc">'+value.description;
		            							html += '&nbsp;<a href="'+value.product_link+'">'+viewMoreTxt+'....';
		            							html += '</a>';
	            							html += '</div>';
	            							html += '<p><span class="capa_txt">Max Capacity:</span><span class="capa_data"> '+value.adult+' Adults, '+value.children+' child</span></p>';
	            							html += '<div class="rm_review_cont pull-left">';
	            								for (var i = 1; i <= 5; i++) 
	            								{
	            									if (i <= value.ratting) 
	            									{
	            										html += '<div class="rm_ratting_yes" style="background-image:url('+ratting_img+');"></div>'
	            									}
	            									else
	            									{
	            										html += '<div class="rm_ratting_no" style="background-image:url('+ratting_img+');"></div>';
	            									}
	            								}
	            								html += '<span class="rm_review">'+value.num_review+' Reviews</span>';
	            							html += '</div>';
	            							if (typeof value.room_left != 'undefined')
	            							{
	            								html += '<span class="rm_left pull-right" ';
	            								if (value.room_left > warning_num)
	            								{
	            									html += ' style="display:none;"';
	            								}

	            								html += '>Hurry! ';


	            								html +='<span class="cat_remain_rm_qty_'+value.id_product+'">'+value.room_left+'</span>';
	            								html +=' rooms left</span>';
	            							}
	            							if (value.feature.length)
	            							{
	            								html += '<div class="rm_amenities_cont">';
	            									$.each(value.feature, function(f_k, f_v)
	            									{
	            										html += '<img src="'+feat_img_dir+f_v.value+'" class="rm_amen">';
	            									});
	            								html += '</div>';
	            							}
											html += '<div class="row margin-lr-0 pull-left rm_price_cont">';
											console.log(value.price_without_reduction - value.feature_price);
												if (value.price_without_reduction - value.feature_price > 0) {
													html += '<span class="pull-left rm_price_val';
													if (value.price_without_reduction-value.feature_price > 0) {
														html += ' room_type_old_price';
													}
													html += '">';
														html += currency_prefix+value.price_without_reduction.toFixed(2)+currency_suffix;
													html += '</span>';
												}
												html += '<span class="pull-left rm_price_val">';
													html += currency_prefix+value.feature_price.toFixed(2)+currency_suffix;
												html += '</span>';
												html += '<span class="pull-left rm_price_txt">/Per Night</span>';
				                            html += '</div>';

				                            // html += ' <a cat_rm_check_in="'+date_from+'" cat_rm_check_out="'+date_to+'" href="" rm_product_id="'+value.id_product+'" cat_rm_book_nm_days="'+num_days+'" class="btn rm_book_btn pull-right">Book Now</a>';
				                            html += ' <a cat_rm_check_in="'+date_from+'" cat_rm_check_out="'+date_to+'" href="" rm_product_id="'+value.id_product+'" cat_rm_book_nm_days="'+num_days+'" data-id-product-attribute="0" data-id-product="'+value.id_product+'" class="btn btn-default button button-medium ajax_add_to_cart_button pull-right"><span>'+bookNowTxt+'</span></a>';

				                            html += '<div class="rm_qty_cont pull-right clearfix" id="cat_rm_quantity_wanted_'+value.id_product+'">';
				                            	html += '<span class="qty_txt">Qty.:</span>';
				                            	html += '<div class="qty_sec_cont row">';
				                            		html += '<div class="qty_input_cont row margin-lr-0">';
				                            			html += '<input autocomplete="off" type="text" min="1" name="qty_'+value.id_product+'" id="cat_quantity_wanted_'+value.id_product+'" class="text-center form-control cat_quantity_wanted" value="1" id_room_product="'+value.id_product+'">';
				                            		html += '</div>';
				                            	
					                            	html += '<div class="qty_direction">';
					                            		html += '<a href="#" data-room_id_product="'+value.id_product+'" data-field-qty="qty_'+value.id_product+'" class="btn btn-default cat_rm_quantity_up">';
							                            	html += '<span><i class="icon-plus"></i></span>';
							                            html += '</a>';

							                            html += '<a href="#" data-field-qty="qty_'+value.id_product+'" class="btn btn-default cat_rm_quantity_down">';
							                            	html += '<span><i class="icon-minus"></i></span>';
					                            		html += '</a>';
						                            html += '</div>';
						                        html += '</div>';
						                    html += '</div>';
				            			html += '</div>';
				            		html += '</div>';
				            	html += '</div>';
				            	$('#category_data_cont').append(html);
            			}
            		});
            	}
            }
        });
		return 1;
	}
});