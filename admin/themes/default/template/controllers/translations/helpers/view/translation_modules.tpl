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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{include file="./translation_inputs_update.tpl"}
	{if $mod_security_warning}
	<div class="alert alert-warning">
		{l s='Apache mod_security is activated on your server. This could result in some Bad Request errors'}
	</div>
	{/if}
	{if !empty($limit_warning)}
	<div class="alert alert-warning">
		{if $limit_warning['error_type'] == 'suhosin'}
			{l s='Warning! Your hosting provider is using the Suhosin patch for PHP, which limits the maximum number of fields allowed in a form:'}

			<b>{$limit_warning['post.max_vars']}</b> {l s='for suhosin.post.max_vars.'}<br/>
			<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the Suhosin limit to'}
		{else}
			{l s='Warning! Your PHP configuration limits the maximum number of fields allowed in a form:'}<br/>
			<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.'}<br/>
			{l s='Please ask your hosting provider to increase this limit to'}
		{/if}
		{l s='%s at least, or you will have to edit the translation files.' sprintf=$limit_warning['needed_limit']}
	</div>
	{else}
		<div class="alert alert-info">
			<p>
				{l s='Click on the title of a section to open its fieldsets.'}
			</p>
		</div>
		<div class="panel">
			<p>{l s='Expressions to translate:'} <span class="badge">{l s='%d' sprintf=$count}</span></p>
			<p>{l s='Total missing expressions:'} <span class="badge">{l s='%d' sprintf=$missing_translations}</p>
		</div>

		<form method="post" id="{$table}_form" action="{$url_submit|escape:'html':'UTF-8'}" class="form-horizontal">
			<div class="panel">
				<input type="hidden" name="lang" value="{$lang}" />
				<input type="hidden" name="type" value="{$type}" />
				<input type="hidden" name="theme" value="{$theme}" />
				<div id="BoxUseSpecialSyntax">
					<div class="alert alert-warning">
						<p>
							{l s='Some of these expressions use this special syntax: %s.' sprintf='%d'}
							<br />
							{l s='You MUST use this syntax in your translations. Here are several examples:'}
						</p>
						<ul>
							<li>"{l s='There are [1]%d[/1] products' tags=['<strong>']}": {l s='"%s" will be replaced by a number.' sprintf='%d'}</li>
							<li>"{l s='List of pages in [1]%s[/1]' tags=['<strong>']}": {l s='"%s" will be replaced by a string.' sprintf='%s'}</li>
							<li>"{l s='Feature: [1]%1$s[/1] ([1]%2$d[/1] values)' tags=['<strong>']}": {l s='The numbers enable you to reorder the variables when necessary.'}</li>
						</ul>
					</div>
				</div>
				<script type="text/javascript">
					$(document).ready(function(){
						$('a.useSpecialSyntax').click(function(){
							var syntax = $(this).find('img').attr('alt');
							$('#BoxUseSpecialSyntax .syntax span').html(syntax+".");
						});
					});
				</script>
				<div class="panel-footer">
					<a name="submitTranslations{$type|ucfirst}" href="{$cancel_url|escape:'html':'UTF-8'}" class="btn btn-default">
						<i class="process-icon-cancel"></i>
						{l s='Cancel'}
					</a>
					{$toggle_button}
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}AndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
				</div>
			</div>

			{foreach $modules_translations as $theme_name => $theme}
				{if $theme_name}<h2>&gt;{l s='Theme:'} <a name="{$theme_name}">{$theme_name}</h2>{/if}
				{foreach $theme as $module_name => $module}
					<h2>{l s='Module:'} <a name="{$module_name}">{$module_name}</a></h2>
					{foreach $module as $template_name => $newLang}
						{if !empty($newLang)}
							{assign var=occurrences value=0}
							{foreach $newLang as $key => $value}
								{if empty($value['trad'])}{assign var=occurrences value=$occurrences+1}{/if}
							{/foreach}
							{if $occurrences > 0}
								{$missing_translations_module = $occurrences}
							{else}
								{$missing_translations_module = 0}
							{/if}
							<div class="panel">
								<h3 onclick="$('#{$theme_name}_{$module_name}_{$template_name|replace:'.':'_'}').slideToggle();">{if $theme_name}{$theme_name} - {/if}{$template_name}
									<span class="badge">{$newLang|count}</span> {l s='expressions'} <span class="label label-danger">{$missing_translations_module}</span>
								</h3>
								<div name="{$type}_div" id="{$theme_name}_{$module_name}_{$template_name|replace:'.':'_'}" style="display:{if $missing_translations_module}block{else}none{/if}">
									<table class="table">
										{foreach $newLang as $key => $value}
											<tr>
												<td width="40%">{$key|stripslashes}</td>
												<td>=</td>
												<td>
													{* Prepare name string for md5() *}
													{capture assign="name"}{strtolower($module_name)}{if $theme_name}_{strtolower($theme_name)}{/if}_{strtolower($template_name)}_{md5($key)}{/capture}
													{if $key|strlen < $textarea_sized}
														<input type="text"
															style="width: 450px{if empty($value.trad)};background:#FBB{/if}"
															name="{$name|md5}"
															value="{$value.trad|regex_replace:'#"#':'&quot;'|stripslashes}"' />
													{else}
														<textarea rows="{($key|strlen / $textarea_sized)|intval}"
															style="width: 450px{if empty($value.trad)};background:#FBB{/if}"
															name="{$name|md5}">{$value.trad|regex_replace:'#"#':'&quot;'|stripslashes}</textarea>
													{/if}
												</td>
												<td>
													{if isset($value.use_sprintf) && $value.use_sprintf}
														<a class="useSpecialSyntax" title="{l s='This expression uses a special syntax:'} {$value.use_sprintf}">
															<img src="{$smarty.const._PS_IMG_}admin/error.png" alt="{$value.use_sprintf}" />
														</a>
													{/if}
												</td>
											</tr>
										{/foreach}
									</table>
								</div>
								<div class="panel-footer">
									<a name="submitTranslations{$type|ucfirst}" href="{$cancel_url|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
									<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
									<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}AndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
								</div>
							</div>
						{/if}
					{/foreach}
				{/foreach}
			{/foreach}
		{/if}

{/block}
