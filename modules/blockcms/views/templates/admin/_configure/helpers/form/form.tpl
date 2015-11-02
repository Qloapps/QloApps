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

{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.type == 'cms_blocks'}
		
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="legend"}
	<h3>
		{if isset($field.image)}<img src="{$field.image}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
		{if isset($field.icon)}<i class="{$field.icon}"></i>{/if}
		{$field.title}
		<span class="panel-heading-action">
			{foreach from=$toolbar_btn item=btn key=k}
				{if $k != 'modules-list' && $k != 'back'}
					<a id="desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="list-toolbar-btn" {if isset($btn.href)}href="{$btn.href}"{/if} {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js}"{/if}>
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s=$btn.desc}" data-html="true">
							<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if} {if isset($btn.class)}{$btn.class}{/if}" ></i>
						</span>
					</a>
				{/if}
			{/foreach}
			</span>
	</h3>
{/block}

{block name="input"}
	{if $input.type == 'select_category'}
		{function name=render_select level=0}
			{foreach $items as $item}
				{if (isset($item['id_cms_category']))}
					<option id="category_{$item['id_cms_category']}" value="{$item['id_cms_category']}"
						{if (isset($fields_value['id_category']) && ($item['id_cms_category'] == $fields_value['id_category']))}
							selected
						{/if} >
						{str_repeat('-', $level)|cat:$item['name']}
					</option>
					{if isset($item['sub_categories']) && count($item['sub_categories']) > 0}
						{call name=render_select items=$item['sub_categories'] level=$level+1}
					{/if}
				{/if}
			{/foreach}
		{/function}
		{if isset($input.options.query) && count($input.options.query) > 0}
			{assign var=categories value=$input.options.query}
			<select class="form-control fixed-width-xl" id="{$input.name}" name="{$input.name}">
				{call render_select items=$categories}
			</select>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="input_row"}
	{if $input.type == 'cms_blocks'}
		<div class="row">
			<script type="text/javascript">
				var come_from = '{$name_controller}';
				var token = '{$token}';
				var alternate = 1;
			</script>
			{assign var=cms_blocks_positions value=$input.values}
			{if isset($cms_blocks_positions) && count($cms_blocks_positions) > 0}
				{foreach $cms_blocks_positions as $key => $cms_blocks_position}
					<div class="col-lg-6">
						<div class="panel">
							<div class="panel-heading">
								{if $key == 0}
									{l s='Left blocks' mod='blockcms'}
								{else}
									{l s='Right blocks' mod='blockcms'}
								{/if}
							</div>
							<table class="table tableDnD cms" id="cms_block_{$key%2}">
								<thead>
									<tr class="nodrag nodrop">
										<th>{l s='ID' mod='blockcms'}</th>
										<th>{l s='Name of the block' mod='blockcms'}</th>
										<th>{l s='Category name' mod='blockcms'}</th>
										<th>{l s='Position' mod='blockcms'}</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									{foreach $cms_blocks_position as $key => $cms_block}
										<tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover" id="tr_{$key%2}_{$cms_block['id_cms_block']}_{$cms_block['position']}">
											<td>{$cms_block['id_cms_block']}</td>
											<td>{$cms_block['block_name']}</td>
											<td>{$cms_block['category_name']}</td>
											<td class="center pointer dragHandle" id="td_{$key%2}_{$cms_block['id_cms_block']}">
												<div class="dragGroup">
													<div class="positions">
														{$cms_block['position'] + 1}
													</div>
												</div>
											</td>
											<td>
												<div class="btn-group-action">
													<div class="btn-group pull-right">
														<a class="btn btn-default" href="{$current}&amp;token={$token}&amp;editBlockCMS&amp;id_cms_block={(int)$cms_block['id_cms_block']}" title="{l s='Edit' mod='blockcms'}">
															<i class="icon-edit"></i> {l s='Edit' mod='blockcms'}
														</a>
														<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
															<i class="icon-caret-down"></i>&nbsp;
														</button>
														<ul class="dropdown-menu">
														<li>
															<a href="{$current}&amp;token={$token}&amp;deleteBlockCMS&amp;id_cms_block={(int)$cms_block['id_cms_block']}" title="{l s='Delete' mod='blockcms'}">
																<i class="icon-trash"></i> {l s='Delete' mod='blockcms'}
															</a>
														</li>
														</ul>
													</div>
												</div>
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				{/foreach}
			{/if}
		</div>

	{elseif $input.type == 'cms_pages'}

		{assign var=cms value=$input.values}
		{if isset($cms) && count($cms) > 0}
			<div class="row">
				<div class="col-lg-9 col-lg-offset-3">
					<div class="panel">
						<div class="panel-heading">
							{$input.label}
						</div>
						<table class="table">
							<thead>
								<tr>
									<th>
										<input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name}', this.checked)" />
									</th>
									<th>{l s='ID' mod='blockcms'}</th>
									<th>{l s='Name' mod='blockcms'}</th>
								</tr>
							</thead>
							<tbody>
								{foreach $cms as $key => $cms_category}
									<tr {if $key%2}class="alt_row"{/if}>
										<td>
											{assign var=id_checkbox value=1|cat:'_'|cat:$cms_category['id_cms_category']}
											<input type="checkbox" class="cmsBox" name="{$input.name}" id="{$id_checkbox}" value="{$id_checkbox}" {if isset($fields_value[$id_checkbox])}checked="checked"{/if} />
										</td>
										<td>
											{$cms_category['id_cms_category']}
										</td>
										<td>
											<label class="control-label" for="{$id_checkbox}">
												{if $cms_category['level_depth'] > 0}
													{str_repeat('- ', ($cms_category['level_depth'] - 1))}
												{/if}
												{$cms_category['name']|escape}
											</label>
										</td>
									</tr>
									{foreach $cms_category['cms_pages'] as $subkey => $cms_page}
										<tr class="subitem{if ($subkey+$key-1)%2} alt_row{/if}">
											<td>
												{assign var=id_checkbox value=0|cat:'_'|cat:$cms_page['id_cms']}
												<input type="checkbox" class="cmsBox" name="{$input.name}" id="{$id_checkbox}" value="{$id_checkbox}" {if isset($fields_value[$id_checkbox])}checked="checked"{/if} />
											</td>
											<td>
												{$cms_page['id_cms']}
											</td>
											<td>
												<label class="control-label" for="{$id_checkbox}">
													{str_repeat('- ', $cms_category['level_depth'])|cat:$cms_page['meta_title']}
												</label>
											</td>
										</tr>
									{/foreach}
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			{else}
			<p>{l s='No pages have been created.' mod='blockcms'}</p>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
