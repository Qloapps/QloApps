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

{extends file='helpers/list/list_content.tpl'}

{block name='td_content'}
	{if isset($tr.$key) && isset($params.position)}
		{if $order_by == 'position' && $order_way != 'DESC'}
			{assign var=filters_has_value_no_location_hotel value=false}

			{foreach $fields_display AS $key => $params}
				{if $key != 'id_category_default' && (isset($params['value']) && $params['value'] !== false && $params['value'] !== '')}
					{if is_array($params['value']) && trim(implode('', $params['value'])) == ''}
						{continue}
					{/if}

					{assign var=filters_has_value_no_location_hotel value=true}
					{break}
				{/if}
			{/foreach}

			{if !$filters_has_value_no_location_hotel}
				<div class="dragGroup">
					<div class="positions">
						{$tr.$key.position + 1}
					</div>
				</div>
			{else}
				{$tr.$key.position + 1}
			{/if}
		{else}
			{$tr.$key.position + 1}
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
