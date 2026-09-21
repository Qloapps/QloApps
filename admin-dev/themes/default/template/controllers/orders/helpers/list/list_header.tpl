{*
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
*}

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
{if isset($updateOrderStatus_mode) && $updateOrderStatus_mode}
	<div class="panel">
		<div class="panel-heading">
			{l s='Choose an order status'}
		</div>
		<form action="{$REQUEST_URI}" method="post">
			<div class="radio">
				<label for="id_order_state">
					<select id="id_order_state" name="id_order_state">
{foreach from=$order_statuses item=order_status_name key=id_order_state}
						<option value="{$id_order_state|intval}">{$order_status_name|escape}</option>
{/foreach}
					</select>
				</label>
			</div>
{foreach $POST as $key => $value}
	{if is_array($value)}
		{foreach $value as $val}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}[]" value="{$val|escape:'html':'UTF-8'}" />
		{/foreach}
	{elseif strtolower($key) != 'id_order_state'}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" />

	{/if}
{/foreach}
			<div class="panel-footer">
				<button type="submit" name="cancel" class="btn btn-default">
					<i class="icon-remove"></i>
					{l s='Cancel'}
				</button>
				<button type="submit" class="btn btn-default" name="submitUpdateOrderStatus">
					<i class="icon-check"></i>
					{l s='Update Order Status'}
				</button>
			</div>
		</form>
	</div>
{/if}
{/block}
{block name="override_form_extra"}
		<script>
			$(document).ready(function(){
				function updateRoomTypeFilter() {
					let filterInputHotelName = $('#filter_input_hotel_name');
					let filterInputRoomTypeName = $('#filter_input_room_type_name');

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
								if (response.has_room_types) {
									$(filterInputRoomTypeName).html(response.html_room_types);
								} else {
									$(filterInputRoomTypeName).find('option').not(':first').remove();
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

				function updateHotelRoomsFilter(useRoomType = true) {
					let filterInputHotelName = $('#filter_input_hotel_name');
					let filterInputRoomTypeName = $('#filter_input_room_type_name');
					let filterInputRoomNumber = $('#filter_input_id_room_information');

					let idHotel = parseInt($(filterInputHotelName).val() || '0');
					let idProduct = parseInt($(filterInputRoomTypeName).val() || '0');

					$.ajax({
						url: currentIndex + '&token=' + token,
						data: {
							ajax: true,
							action: 'GetHotelRooms',
							id_hotel: idHotel,
							id_product: useRoomType ? idProduct : 0,
						},
						type: 'POST',
						dataType: 'JSON',
						success: function(response) {
							if (response.status) {
								if (response.has_hotel_rooms) {
									$(filterInputRoomNumber).html(response.html_hotel_rooms);
								} else {
									$(filterInputRoomNumber).find('option').not(':first').remove();
								}

								// destroy current chosen and re-initialize
								$(filterInputRoomNumber).chosen('destroy');
								$(filterInputRoomNumber).chosen({
									disable_search_threshold: 5,
									search_contains: true,
								});
							}
						},
					});
				}

				// manage Hotel and Room type filter inputs
				$(document).on('change', '#filter_input_hotel_name', function () {
					updateRoomTypeFilter();
					updateHotelRoomsFilter(false);
				});

				$(document).on('change', '#filter_input_room_type_name', function () {
					updateHotelRoomsFilter();
				});
			});
		</script>
{/block}


