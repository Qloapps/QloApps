{**
 * 2010-2023 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 *}

{extends file='helpers/list/list_content.tpl'}

{block name='td_content'}
    {if isset($tr.$key) && isset($params.position)}
        {if $order_by == 'position' && $order_way != 'DESC'}
            <div class="dragGroup">
                <div class="positions">
                    {$tr.$key.position + 1}
                </div>
            </div>
        {else}
            {$tr.$key.position + 1}
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
