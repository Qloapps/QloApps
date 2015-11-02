{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
$().ready(function() {
	$('.input_all_shop').live('click', function() {
		var checked = $(this).prop('checked');
		$('.input_shop_group:not(:disabled)').attr('checked', checked);
		$('.input_shop:not(:disabled)').attr('checked', checked);
	});

	// Click on a group shop
	$('.input_shop_group').live('click', function() {
		$('.input_shop[value='+$(this).val()+']').attr('checked', $(this).prop('checked'));
		check_all_shop();
	});

	// Click on a shop
	$('.input_shop').live('click', function() {
		check_shop_group_status($(this).val());
		check_all_shop();
	});

	// Initialize checkbox
	$('.input_shop_group').each(function(k, v) {
		check_shop_group_status($(v).val());
		check_all_shop();
	});
});

function check_shop_group_status(id_group) {
	var groupChecked = true;
	var total = 0;
	$('.input_shop[value='+id_group+']').each(function(k, v) {
		total++;
		if (!$(v).prop('checked'))
			groupChecked = false;
	});

	if (total > 0)
		$('.input_shop_group[value='+id_group+']').attr('checked', groupChecked);
}

function check_all_shop() {
	var allChecked = true;
	$('.input_shop_group:not(:disabled)').each(function(k, v) {
		if (!$(v).prop('checked'))
			allChecked = false;
		});
	$('.input_all_shop').attr('checked', allChecked);
}
</script>

<div class="assoShop">
	<table class="table">
		<tr>
			<th>{l s='Shop'}</th>
		</tr>
		<tr>
			<td>
				<label>
					<input class="input_all_shop" type="checkbox" />{l s='All shops'}
				</label>
			</td>
		</tr>
		{foreach $input.values as $groupID => $groupData}
				{assign var=groupChecked value=false}
			<tr {if $input.type == 'shop'}class="alt_row"{/if}>
				<td>
					<img style="vertical-align:middle;" alt="" src="../img/admin/lv2_b.gif" />
					<label>
						<input class="input_shop_group"
							type="checkbox"
							name="checkBoxShopGroupAsso_{$table}[{$groupID|intval}]"
							value="{$groupID|intval}"
							{if $groupChecked} checked="checked"{/if} />
						{l s='Group:'} {$groupData['name']}
					</label>
				</td>
			</tr>
	
			{if $input.type == 'shop'}
				{assign var=j value=0}
				{foreach $groupData['shops'] as $shopID => $shopData}
					{if (isset($fields_value.shop[$shopID]))}
						{assign var=checked value=true}
					{else}
						{assign var=checked value=false}
					{/if}
					<tr>
						<td {if $groupData['disable_shops']}style="font-style:italic;background-color:#CFC4FF"{/if}>
							<img style="vertical-align:middle;" alt="" src="../img/admin/lv3_{if $j < count($groupData['shops']) - 1}b{else}f{/if}.png" />
							<label class="child">
								<input class="input_shop"
									type="checkbox"
									value="{$groupID|intval}"
									shop_id="{$shopID|intval}"
									name="checkBoxShopAsso_{$table}[{$shopID|intval}]"
									id="checkedBox_{$shopID|intval}"
									{if $checked} checked="checked"{/if} 
									{if $groupData['disable_shops']} readonly="readonly" onclick="return false"{/if}
									/>
								{$shopData['name']}
							</label>
						</td>
					</tr>
					{assign var=j value=$j+1}
				{/foreach}
			{/if}
		{/foreach}
	</table>
</div>