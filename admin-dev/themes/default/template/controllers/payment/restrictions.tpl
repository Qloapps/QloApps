{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<form action="{$url_submit|escape:'html':'UTF-8'}" method="post" id="form_{$list['name_id']}" class="form-horizontal">
	<div class="panel">
		<h3>
			<i class="{$list['icon']}"></i>
			{$list['title']}
		</h3>
		<p class="help-block">{$list['desc']}</p>
		<div class="row table-responsive clearfix ">
			<div class="overflow-y">
				<table class="table">
					<thead>
						<tr>
							<th style="width:40%"><span class="title_box">{$list['title']}</span></th>
							{foreach $payment_modules as $module}
								{if $module->active}
									<th class="text-center">
										{if $list['name_id'] != 'currency' || $module->currencies_mode == 'checkbox'}
											<input type="hidden" id="checkedBox_{$list['name_id']}_{$module->name}" value="checked"/>
											<a href="javascript:checkPaymentBoxes('{$list['name_id']}', '{$module->name}')">
										{/if}
										{$module->displayName}
										{if $list['name_id'] != 'currency' || $module->currencies_mode == 'checkbox'}
											</a>
										{/if}
									</th>
								{/if}
							{/foreach}
						</tr>
					</thead>
					<tbody>
					{foreach $list['items'] as $item}
						<tr>
							<td>
								<span>{$item['name']}</span>
							</td>
							{foreach $payment_modules as $key_module => $module}
								{if $module->active}
									<td class="text-center">
										{assign var='type' value='null'}
										{if !$item['check_list'][$key_module]}
											{* Keep $type to null *}
										{elseif $list['name_id'] === 'currency'}
											{if $module->currencies && $module->currencies_mode == 'checkbox'}
												{$type = 'checkbox'}
											{elseif $module->currencies && $module->currencies_mode == 'radio'}
												{$type = 'radio'}
											{/if}
										{else}
											{$type = 'checkbox'}
										{/if}
										{if $type != 'null'}
											<input type="{$type}" name="{$module->name}_{$list['name_id']}[]" value="{$item[$list['identifier']]}" {if $item['check_list'][$key_module] == 'checked'}checked="checked"{/if}/>
										{else}
											<input type="hidden" name="{$module->name}_{$list['name_id']}[]" value="{$item[$list['identifier']]}"/>--
										{/if}
									</td>
								{/if}
							{/foreach}
						</tr>
					{/foreach}
					{if $list['name_id'] === 'currency'}
						<tr>
							<td>
								<span>{l s='Customer currency'}</span>
							</td>
							{foreach $payment_modules as $module}
								{if $module->active}
									<td class="text-center">
										{if $module->currencies && $module->currencies_mode == 'radio'}
											<input type="radio" name="{$module->name}_{$list['name_id']}[]" value="-1"{if in_array(-1, $module->$list['name_id'])} checked="checked"
										{/if} />
										{else}
											--
										{/if}
									</td>
								{/if}
							{/foreach}
						</tr>
						<tr>
							<td>
								<span>{l s='Shop default currency'}</span>
							</td>
							{foreach $payment_modules as $module}
								{if $module->active}
									<td class="text-center">
										{if $module->currencies && $module->currencies_mode == 'radio'}
											<input type="radio" name="{$module->name}_{$list['name_id']}[]" value="-2"{if in_array(-2, $module->$list['name_id'])} checked="checked"
										{/if} 
											/>
										{else}
											--
										{/if}
									</td>
								{/if}
							{/foreach}
						</tr>
					{/if}
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right" name="submitModule{$list['name_id']}">
				<i class="process-icon-save"></i> {l s='Save restrictions'}
			</button>
		</div>
	</div>
</form>
