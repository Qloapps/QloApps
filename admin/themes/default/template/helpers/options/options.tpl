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

<div class="leadin">{block name="leadin"}{/block}</div>

<script type="text/javascript">
	id_language = Number({$current_id_lang});
	{if isset($tabs) && $tabs|count}
		var helper_tabs= {$tabs|json_encode};
		var unique_field_id = '{$table}_';
	{/if}
</script>
{block name="defaultOptions"}
{if isset($table_bk) && $table_bk == $table}{capture name='table_count'}{counter name='table_count'}{/capture}{/if}
{assign var='table_bk' value=$table scope='parent'}
<form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" id="{if $table == null}configuration_form{else}{$table}_form{/if}{if isset($smarty.capture.table_count) && $smarty.capture.table_count}_{$smarty.capture.table_count|intval}{/if}" method="post" enctype="multipart/form-data" class="form-horizontal">
	{foreach $option_list AS $category => $categoryData}
		{if isset($categoryData['top'])}{$categoryData['top']}{/if}
		<div class="panel {if isset($categoryData['class'])}{$categoryData['class']}{/if}" id="{$table}_fieldset_{$category}">
			{* Options category title *}
			<div class="panel-heading">
				<i class="{if isset($categoryData['icon'])}{$categoryData['icon']}{else}icon-cogs{/if}"></i>
				{if isset($categoryData['title'])}{$categoryData['title']}{else}{l s='Options'}{/if}
			</div>

			{* Category description *}

			{if (isset($categoryData['description']) && $categoryData['description'])}
				<div class="alert alert-info">{$categoryData['description']}</div>
			{/if}
			{* Category info *}
			{if (isset($categoryData['info']) && $categoryData['info'])}
				<div>{$categoryData['info']}</div>
			{/if}

			{if !$categoryData['hide_multishop_checkbox'] && $use_multishop}
			<div class="well clearfix">
				<label class="control-label col-lg-3">
					<i class="icon-sitemap"></i> {l s='Multistore'}
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						{strip}
						<input type="radio" name="{$table}_multishop_{$category}" id="{$table}_multishop_{$category}_on" value="1" onclick="toggleAllMultishopDefaultValue($('#{$table}_fieldset_{$category}'), true)"/>
						<label for="{$table}_multishop_{$category}_on">
							{l s='Yes'}
						</label>
						<input type="radio" name="{$table}_multishop_{$category}" id="{$table}_multishop_{$category}_off" value="0" checked="checked" onclick="toggleAllMultishopDefaultValue($('#{$table}_fieldset_{$category}'), false)"/>
						<label for="{$table}_multishop_{$category}_off">
							{l s='No'}
						</label>
						{/strip}
						<a class="slide-button btn"></a>
					</span>
					<div class="row">
						<div class="col-lg-12">
							<p class="help-block">
								<strong>{l s='Check / Uncheck all'}</strong><br />
								{l s='You are editing this page for a specific shop or group. Click "Yes" to check all fields, "No" to uncheck all.'}<br />
 								{l s='If you check a field, change its value, and save, the multistore behavior will not apply to this shop (or group), for this particular parameter.'}
							</p>
						</div>
					</div>
				</div>
			</div>
			{/if}

			<div class="form-wrapper">
			{foreach $categoryData['fields'] AS $key => $field}
					{if $field['type'] == 'hidden'}
						<input type="hidden" name="{$key}" value="{$field['value']}" />
					{else}
						<div class="form-group{if isset($field.form_group_class)} {$field.form_group_class}{/if}"{if isset($tabs) && isset($field.tab)} data-tab-id="{$field.tab}"{/if}>
							<div id="conf_id_{$key}"{if $field['is_invisible']} class="isInvisible"{/if}>
								{block name="label"}
									{if isset($field['title']) && isset($field['hint'])}
										<label class="control-label col-lg-3{if isset($field['required']) && $field['required'] && $field['type'] != 'radio'} required{/if}">
											{if !$categoryData['hide_multishop_checkbox'] && $field['multishop_default'] && empty($field['no_multishop_checkbox'])}
											<input type="checkbox" name="multishopOverrideOption[{$key}]" value="1"{if !$field['is_disabled']} checked="checked"{/if} onclick="toggleMultishopDefaultValue(this, '{$key}')"/>
											{/if}
											<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="
												{if is_array($field['hint'])}
													{foreach $field['hint'] as $hint}
														{if is_array($hint)}
															{$hint.text}
														{else}
															{$hint}
														{/if}
													{/foreach}
												{else}
													{$field['hint']}
												{/if}
											" data-html="true">
												{$field['title']}
											</span>
										</label>
									{elseif isset($field['title'])}
										<label class="control-label col-lg-3">
											{if !$categoryData['hide_multishop_checkbox'] && $field['multishop_default'] && empty($field['no_multishop_checkbox'])}
											<input type="checkbox" name="multishopOverrideOption[{$key}]" value="1"{if !$field['is_disabled']} checked="checked"{/if} onclick="checkMultishopDefaultValue(this, '{$key}')" />
											{/if}
											{$field['title']}
										</label>
									{/if}
								{/block}
								{block name="field"}

								{block name="input"}
									{if $field['type'] == 'select'}
										<div class="col-lg-9">
											{if $field['list']}
												<select class="form-control fixed-width-xxl {if isset($field['class'])}{$field['class']}{/if}" name="{$key}"{if isset($field['js'])} onchange="{$field['js']}"{/if} id="{$key}" {if isset($field['size'])} size="{$field['size']}"{/if}>
													{foreach $field['list'] AS $k => $option}
														<option value="{$option[$field['identifier']]}"{if $field['value'] == $option[$field['identifier']]} selected="selected"{/if}>{$option['name']}</option>
													{/foreach}
												</select>
											{elseif isset($input.empty_message)}
												{$input.empty_message}
											{/if}
										</div>
									{elseif $field['type'] == 'bool'}
										<div class="col-lg-9">
											<span class="switch prestashop-switch fixed-width-lg">
												{strip}
												<input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}{if isset($field['disabled']) && (bool)$field['disabled']} disabled="disabled"{/if}/>
												<label for="{$key}_on" class="radioCheck">
													{l s='Yes'}
												</label>
												<input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}{if isset($field['disabled']) && (bool)$field['disabled']} disabled="disabled"{/if}/>
												<label for="{$key}_off" class="radioCheck">
													{l s='No'}
												</label>
												{/strip}
												<a class="slide-button btn"></a>
											</span>
										</div>
									{elseif $field['type'] == 'radio'}
										<div class="col-lg-9">
											{foreach $field['choices'] AS $k => $v}
												<p class="radio">
													{strip}
													<label for="{$key}_{$k}">
														<input type="radio" name="{$key}" id="{$key}_{$k}" value="{$k}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
													 	{$v}
													</label>
													{/strip}
												</p>
											{/foreach}
										</div>
									{elseif $field['type'] == 'checkbox'}
										<div class="col-lg-9">
											{foreach $field['choices'] AS $k => $v}
												<p class="checkbox">
													{strip}
													<label class="col-lg-3" for="{$key}{$k}_on">
														<input type="checkbox" name="{$key}" id="{$key}{$k}_on" value="{$k|intval}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
													 	{$v}
													</label>
													{/strip}
												</p>
											{/foreach}
										</div>
									{elseif $field['type'] == 'text'}
										<div class="col-lg-9">{if isset($field['suffix'])}<div class="input-group{if isset($field.class)} {$field.class}{/if}">{/if}
											<input class="form-control {if isset($field['class'])}{$field['class']}{/if}" type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{if isset($field['no_escape']) && $field['no_escape']}{$field['value']|escape:'UTF-8'}{else}{$field['value']|escape:'html':'UTF-8'}{/if}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
											{if isset($field['suffix'])}
											<span class="input-group-addon">
												{$field['suffix']|strval}
											</span>
											{/if}
											{if isset($field['suffix'])}</div>{/if}
										</div>
									{elseif $field['type'] == 'password'}
										<div class="col-lg-9">{if isset($field['suffix'])}<div class="input-group{if isset($field.class)} {$field.class}{/if}">{/if}
											<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value=""{if isset($field['autocomplete']) && !$field['autocomplete']} autocomplete="off"{/if} />
											{if isset($field['suffix'])}
											<span class="input-group-addon">
												{$field['suffix']|strval}
											</span>
											{/if}
											{if isset($field['suffix'])}</div>{/if}
										</div>
									{elseif $field['type'] == 'textarea'}
										<div class="col-lg-9">
											<textarea class="textarea-autosize" name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|escape:'html':'UTF-8'}</textarea>
										</div>
									{elseif $field['type'] == 'file'}
										<div class="col-lg-9">{$field['file']}</div>
									{elseif $field['type'] == 'color'}
										<div class="col-lg-2">
											<div class="input-group">
												<input type="color" size="{$field['size']}" data-hex="true" {if isset($input.class)}class="{$field['class']}" {else}class="color mColorPickerInput"{/if} name="{$field['name']}" class="{if isset($field['class'])}{$field['class']}{/if}" value="{$field['value']|escape:'html':'UTF-8'}" />
											</div>
							            </div>
									{elseif $field['type'] == 'price'}
										<div class="col-lg-9">
											<div class="input-group fixed-width-lg">
												<span class="input-group-addon">{$currency_left_sign}{$currency_right_sign} {l s='(tax excl.)'}</span>
												<input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{$field['value']|escape:'html':'UTF-8'}" />
											</div>
										</div>
									{elseif $field['type'] == 'textLang' || $field['type'] == 'textareaLang' || $field['type'] == 'selectLang'}
										{if $field['type'] == 'textLang'}
											<div class="col-lg-9">
												<div class="row">
												{foreach $field['languages'] AS $id_lang => $value}
													{if $field['languages']|count > 1}
													<div class="translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;"{/if}>
														<div class="col-lg-9">
													{else}
													<div class="col-lg-12">
													{/if}
															<input type="text"
																name="{$key}_{$id_lang}"
																value="{$value|escape:'html':'UTF-8'}"
																{if isset($input.class)}class="{$input.class}"{/if}
															/>
													{if $field['languages']|count > 1}
														</div>
														<div class="col-lg-2">
															<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
																{foreach $languages as $language}
																	{if $language.id_lang == $id_lang}{$language.iso_code}{/if}
																{/foreach}
																<span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																{foreach $languages as $language}
																<li>
																	<a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
																</li>
																{/foreach}
															</ul>
														</div>
													</div>
													{else}
													</div>
													{/if}
												{/foreach}
												</div>
											</div>
										{elseif $field['type'] == 'textareaLang'}
											<div class="col-lg-9">
												{foreach $field['languages'] AS $id_lang => $value}
													<div class="row translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;"{/if}>
														<div id="{$key}_{$id_lang}" class="col-lg-9" >
															<textarea class="textarea-autosize" name="{$key}_{$id_lang}">{$value|replace:'\r\n':"\n"}</textarea>
														</div>
														<div class="col-lg-2">
															<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
																{foreach $languages as $language}
																	{if $language.id_lang == $id_lang}{$language.iso_code}{/if}
																{/foreach}
																<span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																{foreach $languages as $language}
																<li>
																	<a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
																</li>
																{/foreach}
															</ul>
														</div>

													</div>
												{/foreach}
												<script type="text/javascript">
													$(document).ready(function() {
														$(".textarea-autosize").autosize();
													});
												</script>
											</div>
										{elseif $field['type'] == 'selectLang'}
											{foreach $languages as $language}
												<div id="{$key}_{$language.id_lang}" style="display: {if $language.id_lang == $current_id_lang}block{else}none{/if};" class="col-lg-9">
													<select name="{$key}_{$language.iso_code|upper}">
														{foreach $field['list'] AS $k => $v}
															<option value="{if isset($v.cast)}{$v.cast[$v[$field.identifier]]}{else}{$v[$field.identifier]}{/if}"
																{if $field['value'][$language.id_lang] == $v['name']} selected="selected"{/if}>
																{$v['name']}
															</option>
														{/foreach}
													</select>
												</div>
											{/foreach}
										{/if}
									{/if}
									{if isset($field['desc']) && !empty($field['desc'])}
									<div class="col-lg-9 col-lg-offset-3">
										<div class="help-block">
											{if is_array($field['desc'])}
												{foreach $field['desc'] as $p}
													{if is_array($p)}
														<span id="{$p.id}">{$p.text}</span><br />
													{else}
														{$p}<br />
													{/if}
												{/foreach}
											{else}
												{$field['desc']}
											{/if}
										</div>
									</div>
									{/if}
								{/block}{* end block input *}
								{if $field['is_invisible']}
								<div class="col-lg-9 col-lg-offset-3">
									<p class="alert alert-warning row-margin-top">
										{l s='You can\'t change the value of this configuration field in the context of this shop.'}
									</p>
								</div>
								{/if}
								{/block}{* end block field *}
							</div>
						</div>
				{/if}
			{/foreach}
			</div><!-- /.form-wrapper -->

			{if isset($categoryData['bottom'])}{$categoryData['bottom']}{/if}
			{block name="footer"}
				{if isset($categoryData['submit']) || isset($categoryData['buttons'])}
					<div class="panel-footer">
						{if isset($categoryData['submit']) && !empty($categoryData['submit'])}
						<button type="{if isset($categoryData['submit']['type'])}{$categoryData['submit']['type']}{else}submit{/if}" {if isset($categoryData['submit']['id'])}id="{$categoryData['submit']['id']}"{/if} class="btn btn-default pull-right" name="{if isset($categoryData['submit']['name'])}{$categoryData['submit']['name']}{else}submitOptions{$table}{/if}"><i class="process-icon-{if isset($categoryData['submit']['imgclass'])}{$categoryData['submit']['imgclass']}{else}save{/if}"></i> {$categoryData['submit']['title']}</button>
						{/if}
						{if isset($categoryData['buttons'])}
						{foreach from=$categoryData['buttons'] item=btn key=k}
						{if isset($btn.href) && trim($btn.href) != ''}
							<a href="{$btn.href|escape:'html':'UTF-8'}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']}{/if}" {if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title}</a>
						{else}
							<button type="{if isset($btn['type'])}{$btn['type']}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']}"{/if} class="{if isset($btn['class'])}{$btn['class']}{else}btn btn-default{/if}" name="{if isset($btn['name'])}{$btn['name']}{else}submitOptions{$table}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']}" ></i> {/if}{$btn.title}</button>
						{/if}
						{/foreach}
						{/if}
					</div>
				{/if}
			{/block}
		</div>
	{/foreach}
	{hook h='displayAdminOptions'}
	{if isset($name_controller)}
		{capture name=hookName assign=hookName}display{$name_controller|ucfirst}Options{/capture}
		{hook h=$hookName}
	{elseif isset($smarty.get.controller)}
		{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}Options{/capture}
		{hook h=$hookName}
	{/if}
</form>
{/block}
{block name="after"}{/block}
