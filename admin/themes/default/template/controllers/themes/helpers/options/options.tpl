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
{extends file="helpers/options/options.tpl"}

{block name="input"}
	{if $field['type'] == 'theme'}
		{if $field['can_display_themes']}
			<div class="col-lg-12">
				<div class="row">
					{foreach $field.themes as $theme}
						<div class="col-sm-4 col-lg-3">
							<div class="theme-container">
								<h4 class="theme-title">{$theme->name}</h4>
								<div class="thumbnail-wrapper">
									<div class="action-wrapper">
										<div class="action-overlay"></div>
										<div class="action-buttons">
											<div class="btn-group">
												<a href="{$link->getAdminLink('AdminThemes')|escape:'html':'UTF-8'}&amp;submitOptionstheme&amp;id_theme={$theme->id}" class="btn btn-default">
													<i class="icon-check"></i> {l s='Use this theme'}
												</a>
												{if $theme->name != 'default-bootstrap' || ($theme->name == 'default-bootstrap'  && $host_mode == 0)}
												<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<i class="icon-caret-down"></i>&nbsp;
												</button>
												<ul class="dropdown-menu">
													<li>
														<a href="{$link->getAdminLink('AdminThemes')|escape:'html':'UTF-8'}&amp;deletetheme&amp;id_theme={$theme->id}" title="Delete this theme" class="delete">
															<i class="icon-trash"></i> {l s='Delete this theme'}
														</a>
													</li>
												</ul>
												{/if}
											</div>
										</div>
									</div>
									<img class="center-block img-thumbnail" src="../themes/{$theme->directory}/preview.jpg" alt="{$theme->name}" />
								</div>
							</div>
						</div>
					{/foreach}
					{foreach $field.not_installed as $theme}
						<div class="col-sm-4 col-lg-3">
							<div class="theme-container">
								<h4 class="theme-title">{$theme.name}</h4>
								<div class="thumbnail-wrapper">
									<div class="action-wrapper">
										<div class="action-overlay"></div>
										<div class="action-buttons">
											<div class="btn-group">
												<a href="{$link->getAdminLink('AdminThemes')|escape:'html':'UTF-8'}&amp;installThemeFromFolder&amp;theme_dir={$theme.directory}" class="btn btn-default">
													<i class="icon-check"></i> {l s='Install this theme'}
												</a>
												<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<i class="icon-caret-down"></i>&nbsp;
												</button>
												<ul class="dropdown-menu">
													<li>
														<a href="{$link->getAdminLink('AdminThemes')|escape:'html':'UTF-8'}&amp;deletetheme&amp;theme_dir={$theme.directory}" title="Delete this theme" class="delete">
															<i class="icon-trash"></i> {l s='Delete this theme'}
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<img class="center-block img-thumbnail" src="../themes/{$theme.directory}/themes/{$theme.directory}/preview.jpg" alt="{$theme.name}" />
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}


{block name="footer"}

	{if isset($categoryData['after_tabs'])}
		{assign var=cur_theme value=$categoryData['after_tabs']['cur_theme']}
		<div class="row row-padding-top">

			<div class="col-md-3">
				<a href="{$base_url}" class="_blank">
					<img class="center-block img-thumbnail" src="../themes/{$cur_theme.theme_directory}/preview.jpg" alt="{$cur_theme.theme_name}" />
				</a>
			</div>

			<div id="js_theme_form_container" class="col-md-9">
				<h2>{$cur_theme.theme_name} {if isset($cur_theme.theme_version)}<small>version {$cur_theme.theme_version}</small>{/if}</h2>
				{if isset($cur_theme.author_name)}
				<p>
					{l s='Designed by %s' sprintf=$cur_theme.author_name}
				</p>
				{/if}

				{if isset($cur_theme.tc) && $cur_theme.tc}
				<hr />
				<h4>{l s='Customize your theme'}</h4>
				<div class="row">
					<div class="col-sm-8">
						<p>{l s='Customize the main elements of your theme: sliders, banners, colors, etc.'}</p>
					</div>
					<div class="col-sm-4">
						<a class="btn btn-default pull-right" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&amp;configure=themeconfigurator">
							<i class="icon icon-list-alt"></i>
							{l s='Theme Configurator'}
						</a>
					</div>
				</div>
				{/if}
				<hr />
				<h4>{l s='Configure your theme'}</h4>
				<div class="row">
					<div class="col-sm-8">
						<p>{l s='Configure your theme\'s advanced settings, such as the number of columns you want for each page. This setting is mostly for advanced users.'}</p>
					</div>
					<div class="col-sm-4">
						<a class="btn btn-default pull-right" href="{$link->getAdminLink('AdminThemes')|escape:'html':'UTF-8'}&amp;updatetheme&amp;id_theme={$cur_theme.theme_id}">
							<i class="icon icon-cog"></i>
							{l s='Advanced settings'}
						</a>
					</div>
				</div>
			</div>
		</div>

	{/if}

	{$smarty.block.parent}

{/block}


{block name="after"}
	<script type="text/javascript">
		// These variable will move the form to another location
		var formToMove = "appearance";
		var formDestination = "js_theme_form_container";
	</script>
{/block}
