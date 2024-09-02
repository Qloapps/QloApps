{*
* Copyright since 2007 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $type == 'orders' && isset($ids_order)}
    {foreach from=$ids_order item=id_order name=orders}
        <a href="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$id_order}" style="margin: 1px 0;" target="_blank">#{$id_order}</a>{if !$smarty.foreach.orders.last},{/if}
    {/foreach}
{elseif $type == 'abandoned'}
    <span class="badge badge-danger">{l s='Abandoned cart'}</span>
{elseif $type == 'non_orderd'}
    <span class="badge badge-danger">{l s='Non-orderd cart'}</span>
{/if}
