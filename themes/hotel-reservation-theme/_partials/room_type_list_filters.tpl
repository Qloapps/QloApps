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

<div id="room_types_list_tab" class="catSortBlock">
    <div class="sortBlockHeading">
        <div>{l s='Sort By:'}</div>
    </div>
    <div>
        <div class="filter_dw_cont">
            <button class="btn btn-default border dropdown-toggle" type="button" id="price_ftr" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="{l s='Recommended'}">{l s='Recommended'}</span>
                <span class="caret pull-right margin-top-7"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="price_ftr">
                <li><a href="#" class="sort_result dropdown-item" data-sort-by="0" data-value="0">{l s='Recommended'}</a></li>
                <li><a href="#" class="sort_result dropdown-item" data-sort-by="2" data-value="1">{l s='Price : Lowest First'}</a></li>
                <li><a href="#" class="sort_result dropdown-item" data-sort-by="2" data-value="2">{l s='Price : Highest first '}</a></li>
            </ul>
        </div>
    </div>
</div>

{hook h='displayRoomTypeCategoryFilter'}