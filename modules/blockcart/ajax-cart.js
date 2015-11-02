/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Retrocompatibility with 1.4
if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
	baseUri = baseDir;

//JS Object : update the cart by ajax actions
var ajaxCart = {
	nb_total_products: 0,

	//override every button in the page in relation to the cart
	overrideButtonsInThePage : function(){
		//for every 'add' buttons...
		$('.ajax_add_to_cart_button').unbind('click').click(function(){
			var idProduct =  $(this).attr('rel').replace('nofollow', '').replace('ajax_id_product_', '');
			if ($(this).attr('disabled') != 'disabled')
				ajaxCart.add(idProduct, null, false, this);
			return false;
		});
		//for product page 'add' button...
		$('#add_to_cart input').unbind('click').click(function(){
			ajaxCart.add( $('#product_page_product_id').val(), $('#idCombination').val(), true, null, $('#quantity_wanted').val(), null);
			return false;
		});

		//for 'delete' buttons in the cart block...
		$('#cart_block_list .ajax_cart_block_remove_link').unbind('click').click(function(){
			// Customized product management
			var customizationId = 0;
			var productId = 0;
			var productAttributeId = 0;
			var customizableProductDiv = $($(this).parent().parent()).find("div[id^=deleteCustomizableProduct_]");

			if (customizableProductDiv && $(customizableProductDiv).length)
			{
				$(customizableProductDiv).each(function(){
					var ids = $(this).attr('id').split('_');
					if (typeof(ids[1]) != 'undefined')
					{
						customizationId = parseInt(ids[1]);
						productId = parseInt(ids[2]);
						if (typeof(ids[3]) != 'undefined')
							productAttributeId = parseInt(ids[3]);
						return false;
					}
				});
			}

			// Common product management
			if (!customizationId)
			{
				//retrieve idProduct and idCombination from the displayed product in the block cart
				var firstCut = $(this).parent().parent().attr('id').replace('cart_block_product_', '');
				firstCut = firstCut.replace('deleteCustomizableProduct_', '');
				ids = firstCut.split('_');
				productId = parseInt(ids[0]);
				if (typeof(ids[1]) != 'undefined')
					productAttributeId = parseInt(ids[1]);
			}

			var idAddressDelivery = $(this).parent().parent().attr('id').match(/.*_\d+_\d+_(\d+)/)[1];

			// Removing product from the cart
			ajaxCart.remove(productId, productAttributeId, customizationId, idAddressDelivery);
			return false;
		});
	},

	// try to expand the cart
	expand : function(){
		if ($('#cart_block_list').hasClass('collapsed'))
		{
			$('#cart_block_summary').slideUp(200, function(){
				$(this).addClass('collapsed').removeClass('expanded');
				$('#cart_block_list').slideDown({
					duration: 450,
					complete: function(){$(this).addClass('expanded').removeClass('collapsed');}
				});
			});
			// toogle the button expand/collapse button
			$('#block_cart_expand').fadeOut('slow', function(){
				$('#block_cart_collapse').fadeIn('fast');
			});

			// save the expand statut in the user cookie
			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				data: 'ajax_blockcart_display=expand'
			});


		}
	},
	// Fix display when using back and previous browsers buttons
	refresh : function(){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&ajax=true&token=' + static_token,
			success: function(jsonData)
			{
				ajaxCart.updateCart(jsonData);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	},

	// try to collapse the cart
	collapse : function(){

		if ($('#cart_block_list').hasClass('expanded'))
		{
			$('#cart_block_list').slideUp('slow', function(){
				$(this).addClass('collapsed').removeClass('expanded');
				$('#cart_block_summary').slideDown(450, function(){
					$(this).addClass('expanded').removeClass('collapsed');
				});
			});
			$('#block_cart_collapse').fadeOut('slow', function(){
				$('#block_cart_expand').fadeIn('fast');
			});

			// save the expand statut in the user cookie
			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
				async: true,
				cache: false,
				data: 'ajax_blockcart_display=collapse' + '&rand=' + new Date().getTime()
			});
		}
	},

	// Update the cart information
	updateCartInformation : function (jsonData, addedFromProductPage)
	{
		ajaxCart.updateCart(jsonData);

		//reactive the button when adding has finished
		if (addedFromProductPage)
			$('#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');
		else
			$('.ajax_add_to_cart_button').removeAttr('disabled');
	},

	// add a product in the cart via ajax
	add : function(idProduct, idCombination, addedFromProductPage, callerElement, quantity, wishlist){
		if (addedFromProductPage && !checkCustomizations())
		{
			alert(fieldRequired);
			return ;
		}
		emptyCustomizations();
		//disabled the button when adding to not double add if user double click
		if (addedFromProductPage)
		{
			$('#add_to_cart input').attr('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
			$('.filled').removeClass('filled');
		}
		else
			$(callerElement).attr('disabled', true);

		if ($('#cart_block_list').hasClass('collapsed'))
			this.expand();
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&add=1&ajax=true&qty=' + ((quantity && quantity != null) ? quantity : '1') + '&id_product=' + idProduct + '&token=' + static_token + ( (parseInt(idCombination) && idCombination != null) ? '&ipa=' + parseInt(idCombination): ''),
			success: function(jsonData,textStatus,jqXHR)
			{
				// add appliance to wishlist module
				if (wishlist && !jsonData.errors)
					WishlistAddProductCart(wishlist[0], idProduct, idCombination, wishlist[1]);

				// add the picture to the cart
				var $element = $(callerElement).parent().parent().find('a.product_image img,a.product_img_link img');
				if (!$element.length)
					$element = $('#bigpic');
				var $picture = $element.clone();
				var pictureOffsetOriginal = $element.offset();
				pictureOffsetOriginal.right = $(window).innerWidth() - pictureOffsetOriginal.left - $element.width();

				if ($picture.length)
				{
					$picture.css({
						position: 'absolute',
						top: pictureOffsetOriginal.top,
						right: pictureOffsetOriginal.right
					});
				}

				var pictureOffset = $picture.offset();
				var cartBlock = $('#cart_block');
				if (!$('#cart_block')[0] || !$('#cart_block').offset().top || !$('#cart_block').offset().left)
					cartBlock = $('#shopping_cart');
				var cartBlockOffset = cartBlock.offset();
				cartBlockOffset.right = $(window).innerWidth() - cartBlockOffset.left - cartBlock.width();

				// Check if the block cart is activated for the animation
				if (cartBlockOffset != undefined && $picture.length)
				{
					$picture.appendTo('body');
					$picture
						.css({
							position: 'absolute',
							top: pictureOffsetOriginal.top,
							right: pictureOffsetOriginal.right,
							zIndex: 4242
						})
						.animate({
							width: $element.attr('width')*0.66,
							height: $element.attr('height')*0.66,
							opacity: 0.2,
							top: cartBlockOffset.top + 30,
							right: cartBlockOffset.right + 15
						}, 1000)
						.fadeOut(100, function() {
							ajaxCart.updateCartInformation(jsonData, addedFromProductPage);
							$(this).remove();
						});
				}
				else
					ajaxCart.updateCartInformation(jsonData, addedFromProductPage);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("Impossible to add the product to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
				//reactive the button when adding has finished
				if (addedFromProductPage)
					$('#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');
				else
					$(callerElement).removeAttr('disabled');
			}
		});
	},

	//remove a product from the cart via ajax
	remove : function(idProduct, idCombination, customizationId, idAddressDelivery){
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&delete=1&id_product=' + idProduct + '&ipa=' + ((idCombination != null && parseInt(idCombination)) ? idCombination : '') + ((customizationId && customizationId != null) ? '&id_customization=' + customizationId : '') + '&id_address_delivery=' + idAddressDelivery + '&token=' + static_token + '&ajax=true',
			success: function(jsonData)	{
				ajaxCart.updateCart(jsonData);
				if ($('body').attr('id') == 'order' || $('body').attr('id') == 'order-opc')
					deleteProductFromSummary(idProduct+'_'+idCombination+'_'+customizationId+'_'+idAddressDelivery);
			},
			error: function() {alert('ERROR: unable to delete the product');}
		});
	},

	//hide the products displayed in the page but no more in the json data
	hideOldProducts : function(jsonData) {
		//delete an eventually removed product of the displayed cart (only if cart is not empty!)
		if ($('#cart_block_list dl.products').length > 0)
		{
			var removedProductId = null;
			var removedProductData = null;
			var removedProductDomId = null;
			//look for a product to delete...
			$('#cart_block_list dl.products dt').each(function(){
				//retrieve idProduct and idCombination from the displayed product in the block cart
				var domIdProduct = $(this).attr('id');
				var firstCut =  domIdProduct.replace('cart_block_product_', '');
				var ids = firstCut.split('_');

				//try to know if the current product is still in the new list
				var stayInTheCart = false;
				for (aProduct in jsonData.products)
				{
					//we've called the variable aProduct because IE6 bug if this variable is called product
					//if product has attributes
					if (jsonData.products[aProduct]['id'] == ids[0] && (!ids[1] || jsonData.products[aProduct]['idCombination'] == ids[1]))
					{
						stayInTheCart = true;
						// update the product customization display (when the product is still in the cart)
						ajaxCart.hideOldProductCustomizations(jsonData.products[aProduct], domIdProduct);
					}
				}
				//remove product if it's no more in the cart
				if (!stayInTheCart)
				{
					removedProductId = $(this).attr('id');
					if (removedProductId != null)
					{
						var firstCut =  removedProductId.replace('cart_block_product_', '');
						var ids = firstCut.split('_');

						$('#'+removedProductId).addClass('strike').fadeTo('slow', 0, function(){
							$(this).slideUp('slow', function(){
								$(this).remove();
								// If the cart is now empty, show the 'no product in the cart' message and close detail
								if($('#cart_block dl.products dt').length == 0)
								{
									$("#header #cart_block").stop(true, true).slideUp(200);
									$('#cart_block_no_products:hidden').slideDown(450);
									$('#cart_block dl.products').remove();
								}
							});
						});
						$('#cart_block_combination_of_' + ids[0] + (ids[1] ? '_'+ids[1] : '') + (ids[2] ? '_'+ids[2] : '')).fadeTo('fast', 0, function(){
							$(this).slideUp('fast', function(){
								$(this).remove();
							});
						});
					}
				}
			});
		}
	},

	hideOldProductCustomizations : function (product, domIdProduct)
	{
		var customizationList = $('#customization_' + product['id'] + '_' + product['idCombination']);
		if(customizationList.length > 0)
		{
			$(customizationList).find("li").each(function(){
				$(this).find("div").each(function() {
					var customizationDiv = $(this).attr('id');
					var tmp = customizationDiv.replace('deleteCustomizableProduct_', '');
					var ids = tmp.split('_');
					if ((parseInt(product.idCombination) == parseInt(ids[2])) && !ajaxCart.doesCustomizationStillExist(product, ids[0]))
						$('#' + customizationDiv).parent().addClass('strike').fadeTo('slow', 0, function(){
							$(this).slideUp('slow');
							$(this).remove();
						});
				});
			});
		}

		var removeLinks = $('#' + domIdProduct).find('.ajax_cart_block_remove_link');
		if (!product.hasCustomizedDatas && !removeLinks.length)
			$('#' + domIdProduct + ' span.remove_link').html('<a class="ajax_cart_block_remove_link" rel="nofollow" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + product['id'] + '&amp;ipa=' + product['idCombination'] + '&amp;token=' + static_token + '"> </a>');
		if (product.is_gift)
			$('#' + domIdProduct + ' span.remove_link').html('');
	},

	doesCustomizationStillExist : function (product, customizationId)
	{
		var exists = false;

		$(product.customizedDatas).each(function() {
			if (this.customizationId == customizationId)
			{
				exists = true;
				// This return does not mean that we found nothing but simply break the loop
				return false;
			}
		});
		return (exists);
	},

	//refresh display of vouchers (needed for vouchers in % of the total)
	refreshVouchers : function (jsonData) {
		if (typeof(jsonData.discounts) == 'undefined' || jsonData.discounts.length == 0)
			$('#vouchers').hide();
		else
		{
			$('#vouchers tbody').html('');
			for (i=0; i < jsonData.discounts.length; i++)
			{
				if (parseFloat(jsonData.discounts[i].price_float) > 0)
				{
					var delete_link = '';
					if (jsonData.discounts[i].code.length)
						delete_link = '<a class="delete_voucher" href="'+jsonData.discounts[i].link+'" title="'+delete_txt+'"><img src="'+img_dir+'icon/delete.gif" alt="'+delete_txt+'" class="icon" /></a>';
					$('#vouchers tbody').append($(
						'<tr class="bloc_cart_voucher" id="bloc_cart_voucher_'+jsonData.discounts[i].id+'">'
						+ '<td class="quantity">1x</td>'
						+ '<td class="name" title="'+jsonData.discounts[i].description+'">'+jsonData.discounts[i].name+'</td>'
						+ '<td class="price">-'+jsonData.discounts[i].price+'</td>'
						+ '<td class="delete">' + delete_link + '</td>'
						+ '</tr>'
					));
				}
			}
			$('#vouchers').show();
		}

	},

	// Update product quantity
	updateProductQuantity : function (product, quantity) {

		$('#cart_block_product_' + product.id + '_' + (product.idCombination ? product.idCombination : '0')+ '_' + (product.idAddressDelivery ? product.idAddressDelivery : '0') + ' .quantity').fadeTo('fast', 0, function() {
			$(this).text(quantity);
			$(this).fadeTo('fast', 1, function(){
				$(this).fadeTo('fast', 0, function(){
					$(this).fadeTo('fast', 1, function(){
						$(this).fadeTo('fast', 0, function(){
							$(this).fadeTo('fast', 1);
						});
					});
				});
			});
		});
	},


	//display the products witch are in json data but not already displayed
	displayNewProducts : function(jsonData) {

		//add every new products or update displaying of every updated products
		$(jsonData.products).each(function(){
			//fix ie6 bug (one more item 'undefined' in IE6)
			if (this.id != undefined)
			{
				//create a container for listing the products and hide the 'no product in the cart' message (only if the cart was empty)

				if ($('#cart_block dl.products').length == 0)
				{
					$('#cart_block_no_products').before('<dl class="products"></dl>');
					$('#cart_block_no_products').hide();
				}
				//if product is not in the displayed cart, add a new product's line
				var domIdProduct = this.id + '_' + (this.idCombination ? this.idCombination : '0') + '_' + (this.idAddressDelivery ? this.idAddressDelivery : '0');
				var domIdProductAttribute = this.id + '_' + (this.idCombination ? this.idCombination : '0');
				if ($('#cart_block_product_'+ domIdProduct).length == 0)
				{
					var productId = parseInt(this.id);
					var productAttributeId = (this.hasAttributes ? parseInt(this.attributes) : 0);
					var content =  '<dt class="hidden" id="cart_block_product_' + domIdProduct + '">';
					content += '<span class="quantity-formated"><span class="quantity">' + this.quantity + '</span>x</span>';
					var name = $('<span />').html(this.name).text();
					name = (name.length > 12 ? name.substring(0, 10) + '...' : name);
					content += '<a href="' + this.link + '" title="' + this.name + '" class="cart_block_product_name">' + name + '</a>';

					if (typeof(this.is_gift) == 'undefined' || this.is_gift == 0)
						content += '<span class="remove_link"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;token=' + static_token + (this.hasAttributes ? '&amp;ipa=' + parseInt(this.idCombination) : '') + '"> </a></span>';
					else
						content += '<span class="remove_link"></span>';
					if (typeof(freeProductTranslation) != 'undefined')
						content += '<span class="price">' + (parseFloat(this.price_float) > 0 ? this.priceByLine : freeProductTranslation) + '</span>';
					content += '</dt>';
					if (this.hasAttributes)
						  content += '<dd id="cart_block_combination_of_' + domIdProduct + '" class="hidden"><a href="' + this.link + '" title="' + this.name + '">' + this.attributes + '</a>';
					if (this.hasCustomizedDatas)
						content += ajaxCart.displayNewCustomizedDatas(this);
					if (this.hasAttributes) content += '</dd>';

					$('#cart_block dl.products').append(content);
				}
				//else update the product's line
				else
				{
					var jsonProduct = this;
					if($.trim($('#cart_block_product_' + domIdProduct + ' .quantity').html()) != jsonProduct.quantity || $.trim($('#cart_block_product_' + domIdProduct + ' .price').html()) != jsonProduct.priceByLine)
					{
						// Usual product
						if (!this.is_gift)
							$('#cart_block_product_' + domIdProduct + ' .price').text(jsonProduct.priceByLine);
						else
							$('#cart_block_product_' + domIdProduct + ' .price').html(freeProductTranslation);
						ajaxCart.updateProductQuantity(jsonProduct, jsonProduct.quantity);

						// Customized product
						if (jsonProduct.hasCustomizedDatas)
						{
							customizationFormatedDatas = ajaxCart.displayNewCustomizedDatas(jsonProduct);
							if (!$('#customization_' + domIdProductAttribute).length)
							{
								if (jsonProduct.hasAttributes)
									$('#cart_block_combination_of_' + domIdProduct).append(customizationFormatedDatas);
								else
									$('#cart_block dl.products').append(customizationFormatedDatas);
							}
							else
							{
								$('#customization_' + domIdProductAttribute).html('');
								$('#customization_' + domIdProductAttribute).append(customizationFormatedDatas);
							}
						}
					}
				}
				$('#cart_block dl.products .hidden').slideDown(450).removeClass('hidden');

			var removeLinks = $('#cart_block_product_' + domIdProduct).find('a.ajax_cart_block_remove_link');
			if (this.hasCustomizedDatas && removeLinks.length)
				$(removeLinks).each(function() {
					$(this).remove();
				});
			}
		});
	},

	displayNewCustomizedDatas : function(product)
	{
		var content = '';
		var productId = parseInt(product.id);
		var productAttributeId = typeof(product.idCombination) == 'undefined' ? 0 : parseInt(product.idCombination);
		var hasAlreadyCustomizations = $('#customization_' + productId + '_' + productAttributeId).length;

		if (!hasAlreadyCustomizations)
		{
			if (!product.hasAttributes)
				content += '<dd id="cart_block_combination_of_' + productId + '" class="hidden">';
			if ($('#customization_' + productId + '_' + productAttributeId).val() == undefined)
				content += '<ul class="cart_block_customizations" id="customization_' + productId + '_' + productAttributeId + '">';
		}

		$(product.customizedDatas).each(function()
		{
			var done = 0;
			customizationId = parseInt(this.customizationId);
			productAttributeId = typeof(product.idCombination) == 'undefined' ? 0 : parseInt(product.idCombination);
			content += '<li name="customization"><div class="deleteCustomizableProduct" id="deleteCustomizableProduct_' + customizationId + '_' + productId + '_' + (productAttributeId ?  productAttributeId : '0') + '"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;ipa=' + productAttributeId + '&amp;id_customization=' + customizationId + '&amp;token=' + static_token + '"></a></div><span class="quantity-formated"><span class="quantity">' + parseInt(this.quantity) + '</span>x</span>';

			// Give to the customized product the first textfield value as name
			$(this.datas).each(function(){
				if (this['type'] == CUSTOMIZE_TEXTFIELD)
				{
					$(this.datas).each(function(){
						if (this['index'] == 0)
						{
							content += ' ' + this.truncatedValue.replace(/<br \/>/g, ' ');
							done = 1;
							return false;
						}
					})
				}
			});

			// If the customized product did not have any textfield, it will have the customizationId as name
			if (!done)
				content += customizationIdMessage + customizationId;
			if (!hasAlreadyCustomizations) content += '</li>';
			// Field cleaning
			if (customizationId)
			{
				$('#uploadable_files li div.customizationUploadBrowse img').remove();
				$('#text_fields input').attr('value', '');
			}
		});

		if (!hasAlreadyCustomizations)
		{
			content += '</ul>';
			if (!product.hasAttributes) content += '</dd>';
		}
		return (content);
	},


	//genarally update the display of the cart
	updateCart : function(jsonData) {
		//user errors display
		if (jsonData.hasError)
		{
			var errors = '';
			for (error in jsonData.errors)
				//IE6 bug fix
				if (error != 'indexOf')
					errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
			alert(errors);
		}
		else
		{
			ajaxCart.updateCartEverywhere(jsonData);
			ajaxCart.hideOldProducts(jsonData);
			ajaxCart.displayNewProducts(jsonData);
			ajaxCart.refreshVouchers(jsonData);

			//update 'first' and 'last' item classes
			$('#cart_block .products dt').removeClass('first_item').removeClass('last_item').removeClass('item');
			$('#cart_block .products dt:first').addClass('first_item');
			$('#cart_block .products dt:not(:first,:last)').addClass('item');
			$('#cart_block .products dt:last').addClass('last_item');

			//reset the onlick events in relation to the cart block (it allow to bind the onclick event to the new 'delete' buttons added)
			ajaxCart.overrideButtonsInThePage();
		}
	},

	//update general cart informations everywhere in the page
	updateCartEverywhere : function(jsonData) {
		$('.ajax_cart_total').text($.trim(jsonData.productTotal));

		if (parseFloat(jsonData.shippingCostFloat) > 0 || jsonData.nbTotalProducts < 1)
			$('.ajax_cart_shipping_cost').text(jsonData.shippingCost);
		else if (typeof(freeShippingTranslation) != 'undefined')
				$('.ajax_cart_shipping_cost').html(freeShippingTranslation);
		$('.ajax_cart_tax_cost').text(jsonData.taxCost);
		$('.cart_block_wrapping_cost').text(jsonData.wrappingCost);
		$('.ajax_block_cart_total').text(jsonData.total);

		this.nb_total_products = jsonData.nbTotalProducts;

		if (parseInt(jsonData.nbTotalProducts) > 0)
		{
			$('.ajax_cart_no_product').hide();
			$('.ajax_cart_quantity').text(jsonData.nbTotalProducts);
			$('.ajax_cart_quantity').fadeIn('slow');
			$('.ajax_cart_total').fadeIn('slow');

			if (parseInt(jsonData.nbTotalProducts) > 1)
			{
				$('.ajax_cart_product_txt').each( function () {
					$(this).hide();
				});

				$('.ajax_cart_product_txt_s').each( function () {
					$(this).show();
				});
			}
			else
			{
				$('.ajax_cart_product_txt').each( function () {
					$(this).show();
				});

				$('.ajax_cart_product_txt_s').each( function () {
					$(this).hide();
				});
			}
		}
		else
		{
			$('.ajax_cart_quantity, .ajax_cart_product_txt_s, .ajax_cart_product_txt, .ajax_cart_total').each(function(){
				$(this).hide();
			});
			$('.ajax_cart_no_product').show('slow');
		}
	}
};

function HoverWatcher(selector){
	this.hovering = false;
	var self = this;

	this.isHoveringOver = function() {
		return self.hovering;
	}

	$(selector).hover(function() {
		self.hovering = true;
	}, function() {
		self.hovering = false;
	})
}

//when document is loaded...
$(document).ready(function(){
	// expand/collapse management
	$('#block_cart_collapse').click(function(){
			ajaxCart.collapse();
	});
	$('#block_cart_expand').click(function(){
			ajaxCart.expand();
	});
	ajaxCart.overrideButtonsInThePage();

	var cart_qty = 0;
	var current_timestamp = parseInt(new Date().getTime() / 1000);

	if (typeof $('.ajax_cart_quantity').html() == 'undefined' || (typeof generated_date != 'undefined' && generated_date != null && (parseInt(generated_date) + 30) < current_timestamp))
		ajaxCart.refresh();
	else
		cart_qty = parseInt($('.ajax_cart_quantity').html());

	/* roll over cart */
	var cart_block = new HoverWatcher('#header #cart_block');
	var shopping_cart = new HoverWatcher('#shopping_cart');

	$("#shopping_cart a:first").hover(
		function() {
			$(this).css('border-radius', '3px 3px 0px 0px');
			if (ajaxCart.nb_total_products > 0 || cart_qty > 0)
				$("#header #cart_block").stop(true, true).slideDown(450);
		},
		function() {
			$('#shopping_cart a').css('border-radius', '3px');
			setTimeout(function() {
				if (!shopping_cart.isHoveringOver() && !cart_block.isHoveringOver())
					$("#header #cart_block").stop(true, true).slideUp(450);
			}, 200);
		}
	);

	$("#header #cart_block").hover(
		function() {
			$('#shopping_cart a').css('border-radius', '3px 3px 0px 0px');
		},
		function() {
			$('#shopping_cart a').css('border-radius', '3px');
			setTimeout(function() {
				if (!shopping_cart.isHoveringOver())
					$("#header #cart_block").stop(true, true).slideUp(450);
			}, 200);
		}
	);

	$(document).on('click', '.delete_voucher', function(){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			async: true,
			cache: false,
			url:$(this).attr('href') + '?rand=' + new Date().getTime(),
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
		$(this).parent().parent().remove();
		ajaxCart.refresh();
		if ($('body').attr('id') == 'order' || $('body').attr('id') == 'order-opc')
		{
			if (typeof(updateAddressSelection) != 'undefined')
				updateAddressSelection();
			else
				location.reload();
		}
		return false;
	});

	$('#cart_navigation input').click(function(){
		$(this).attr('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
		$(this).closest("form").get(0).submit();
	});
});