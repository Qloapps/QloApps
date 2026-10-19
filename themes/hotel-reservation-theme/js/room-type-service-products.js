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

function resetRoomtypeServices(refresh = true) {
    $('.room_demands_container').find('input.id_room_type_demand:checked').prop('checked', false).uniform();
    $('#additional_products').empty();
    $('#additional_products div')
    $('.remove_roomtype_product').text(select_txt).removeClass('btn-danger remove_roomtype_product').addClass('btn-success add_roomtype_product');
    if (refresh) {
        BookingForm.refresh();
    }
}

function disableRoomTypeDemands(show) {
    if (show) {
        $('.room_demands_container_overlay').show();
        $('.room_demands_container').find('input:checkbox.id_room_type_demand').prop('checked', false);
        $('.room_demand_block').find('.id_room_type_demand').prop('checked', false).parent().removeClass('checked');
        $('.room_demands_container').find('input:checkbox.id_room_type_demand').attr('disabled', 'disabled');
    } else {
        $('.room_demands_container_overlay').hide();
        $('.room_demands_container').find('input:checkbox.id_room_type_demand').removeAttr('disabled');
        $('.room_demands_container').find('.checker').removeClass('disabled');
    }
}

function disableRoomTypeServices(disable) {
    if (disable) {
        resetRoomtypeServices(false);
        $('#service_products_cont').find('button.add_roomtype_product').attr('disabled', 'disabled');
        $('#service_products_cont').find('.qty_container .qty_direction a').attr('disabled', 'disabled');
    } else {
        $('#service_products_cont').find('button.add_roomtype_product').removeAttr('disabled');
        $('#service_products_cont').find('.qty_container .qty_direction a').removeAttr('disabled');
    }
}

function getRoomsExtraDemands()
{
    var roomDemands = [];

    $('input:checkbox.id_room_type_demand:checked').each(function () {
        roomDemands.push({
            'id_global_demand':$(this).val(),
            'id_option': $(this).closest('.room_demand_block').find('.id_option').val()
        });
    });

    return roomDemands;
}

function getRoomsServiceProducts()
{
    var serviceProducts = [];

    $('#additional_products input.service_product').each(function () {
        serviceProducts.push({
            'id_product': $(this).data('id_product'),
            'quantity':$(this).val(),
        });
    });

    return serviceProducts;
}