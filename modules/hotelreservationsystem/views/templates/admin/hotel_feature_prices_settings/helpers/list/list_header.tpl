{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{extends file="helpers/list/list_header.tpl"}
{block name="override_form_extra"}
	<script>
		$(document).ready(function(){
			updateRoomTypeFilter();
			function updateRoomTypeFilter() {
				let filterInputHotelName = $('#filter_input_hotel_name');
				let filterInputRoomTypeName = $('#filter_input_product_name');

				let idHotel = parseInt($(filterInputHotelName).val() || '0');

				$.ajax({
					url: currentIndex + '&token=' + token,
					data: {
						ajax: true,
						action: 'GetHotelRoomTypes',
						id_hotel: idHotel,
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(response) {
						if (response.status) {
							let selectedIdRoomType = parseInt($(filterInputRoomTypeName).val() || '0');
							let hasSelectedRoomType = false;
							if (response.has_room_types) {
								let selectElem = $('<select>').append($('<option>').attr('value', '').text('-').attr('selected', true));
								$(response.room_types_info).each(function(index, roomType){
									$(selectElem).append($('<option>').attr('value', roomType.id_product).text(roomType.room_type + ', '+ roomType.hotel_name));
									if (!hasSelectedRoomType && roomType.id_product == selectedIdRoomType) {
										hasSelectedRoomType = true;
									}
								});
								$(filterInputRoomTypeName).html($(selectElem).html());
							} else {
								$(filterInputRoomTypeName).find('option').not(':first').remove();
							}

							if (hasSelectedRoomType && selectedIdRoomType) {
								$(filterInputRoomTypeName).find('option[value="'+ selectedIdRoomType+'"]').attr('selected', true);
							}
							// destroy current chosen and re-initialize
							$(filterInputRoomTypeName).chosen('destroy');
							$(filterInputRoomTypeName).chosen({
								disable_search_threshold: 5,
								search_contains: true,
							});

						}
					},
				});
			}

			// manage Hotel filter input
			$(document).on('change', '#filter_input_hotel_name', function () {
				updateRoomTypeFilter();
			});
		});
	</script>
{/block}
