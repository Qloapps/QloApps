{*
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

{block name='category_page_search_panel'}
    {if isset($hotels_info) && count($hotels_info)}
        <div class="container">
            <div class="col-12 header-rmsearch-wrapper category-rmsearch-wrapper">
                <div class="category-rmsearch-info">
                    {if isset($hotel_location) && $hotel_location}
                        <span class="search_category">
                            <i class="icon-location-dot"></i> {$hotel_location}
                        </span>
                    {/if}
                    {if !empty($search_data['date_from']) && !empty($search_data['date_to'])}
                        <span class="search_date">
                            <i class="icon-calendar"></i> {dateFormat date=$search_data['date_from']} - {dateFormat date=$search_data['date_to']}
                        </span>
                    {/if}
                    {if !empty($search_data['occupancy_adults'])}
                        <span class="search_occupancy">
                            <i class="icon-users"></i> {if !empty($search_data['occupancy_adults'])}{$search_data['occupancy_adults']} {if $search_data['occupancy_adults'] > 1}{l s='Adults' mod='wkroomsearchblock'}{else}{l s='Adult' mod='wkroomsearchblock'}{/if}, {if !empty($search_data['occupancy_children'])}{$search_data['occupancy_children']} {if $search_data['occupancy_children'] > 1} {l s='Children' mod='wkroomsearchblock'}{else}{l s='Child' mod='wkroomsearchblock'}{/if}, {/if}{$search_data['occupancies']|count} {if $search_data['occupancies']|count > 1}{l s='Rooms' mod='wkroomsearchblock'}{else}{l s='Room' mod='wkroomsearchblock'}{/if}{else}{l s='1 Adult, 1 Room' mod='wkroomsearchblock'}{/if}
                        </span>
                    {/if}
                </div>
                <div class="category-rmsearch-info-xs">
                    {if !empty($search_data['date_from']) && !empty($search_data['date_to'])}
                        <span class="search_date">{dateFormat date=$search_data['date_from']} - {dateFormat date=$search_data['date_to']}</span>
                    {/if}
                </div>
                <div>
                    <button class="btn btn-primary rmsearch-modal-btn" data-toggle="modal" data-target="#rmsearchmodal">{l s='Modify Search' mod='wkroomsearchblock'}</button>
                    {block name='search_form'}
                        {include file="./searchModal.tpl" display_form=true}
                    {/block}
                </div>
            </div>
        </div>
    {/if}
{/block}
