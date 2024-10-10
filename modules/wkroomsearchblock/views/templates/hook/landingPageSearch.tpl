{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($is_index_page) && $is_index_page}
    <div class="header-rmsearch-container header-rmsearch-hide-xs hidden-xs">
        {if isset($hotels_info) && count($hotels_info)}
            <div class="header-rmsearch-wrapper" id="xs_room_search_form">
                <div class="header-rmsearch-primary">
                    <div class="fancy_search_header_xs">
                        <p>{l s='Search Rooms' mod='wkroomsearchblock'}</p>
                        <hr>
                    </div>
                    <div class="container">
                        <div class="header-rmsearch-inner-wrapper">
			                {include file="./searchForm.tpl"}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/if}