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

{block name='landing_page_search_panel'}
    {if isset($is_index_page) && $is_index_page}
        <div class="header-rmsearch-container header-rmsearch-hide-xs hidden-xs">
            {if isset($hotels_info) && count($hotels_info)}
                <div class="header-rmsearch-wrapper" id="xs_room_search_form">
                    <div class="header-rmsearch-primary">
                        <div class="fancy_search_header_xs" style="display:none;">
                            <p>{l s='Search Rooms' mod='wkroomsearchblock'}</p>
                            <hr>
                        </div>
                        <div class="container">
                            <div class="header-rmsearch-inner-wrapper">
                            {block name='search_form'}
                                {include file="./searchForm.tpl"}
                            {/block}
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    {/if}
{/block}
