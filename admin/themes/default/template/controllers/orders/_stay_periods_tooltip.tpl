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

<style>
    .stay-period-tooltip {
        border: none;
        box-shadow: 0 0 5px #aaa;
        -webkit-box-shadow: 0 0 5px #aaa;
        padding-bottom: 10px;
        min-width: 260px;
    }
    .stay-period-tooltip .tip_date {
        font-weight: bold;
    }
    .stay-period-tooltip .tip-body {
        display: table;
        width: 100%;
        margin-top: 6px;
    }
    .stay-period-tooltip .stay_period {
        display: table-row;
    }
    .stay-period-tooltip .tip_element_head,
    .stay-period-tooltip .tip_element_value {
        display: table-cell;
        padding: 3px 6px;
    }
    .stay-period-tooltip .tip_element_value {
        text-align: right;
    }
    .stay-period-tooltip .stay_period_header .tip_element_head,
    .stay-period-tooltip .stay_period_header .tip_element_value {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }
</style>
<div id="stay-period-tpl"
     style="display:none;"
     data-label-dates="{l s='Dates'}"
     data-label-rooms="{l s='Rooms'}">
    <div class="tooltip_cont">
        <div class="tip_header">
            <div class="tip_date">{l s='Stay Periods'}</div>
        </div>
        <div class="tip-body"></div>
    </div>
</div>
