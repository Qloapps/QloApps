{*
* 2010-2023 Webkul.
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
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<span title="" data-toggle="tooltip" class="label-tooltip" data-html="true"
    data-original-title="{$row['group_names']}" >
    {$row['group_access_count']}&nbsp;
    {if $row['group_access_count'] > 1}
        {l s='Groups' mod='hotelreservationsystem'}
    {else}
        {l s='Group' mod='hotelreservationsystem'}
    {/if}
</span>