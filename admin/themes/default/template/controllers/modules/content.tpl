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

{if isset($module_content)}
	{$module_content}
{else}

	{if isset($smarty.get.addnewmodule) && ($context_mode == Context::MODE_HOST)}

		<div class="defaultForm form-horizontal">

			{if $logged_on_addons}

				<div class="panel">
					<div class="row">
						<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
							<img class="img-responsive" alt="PrestaShop Addons" src="themes/default/img/prestashop-addons-logo.png">
						</div>
						<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-7 col-xs-12 addons-style-search-bar">
							<form id="addons-search-form" method="get" action="http://addons.prestashop.com/{$iso_code}/search" class="float">
							<label>{l s='Search on PrestaShop Marketplace:'}</label>
							<div class="input-group">
								<input id="addons-search-box" class="form-control" type="text" autocomplete="off" name="query" value="" placeholder="Search on PrestaShop Marketplace">
								<div id="addons-search-btn" class="btn btn-primary input-group-addon">
									<i class="icon-search"></i>
								</div>
							</div>
							</form>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 addons-see-all-themes">
							{l s='Or'}<a href="http://addons.prestashop.com/{$iso_code}/2-modules-prestashop" class="btn btn-primary" onclick="return !window.open(this.href)">{l s='See all modules'}</a>
						</div>
					</div>
				</div>

			{else}

				<div class="panel" id="">
					<div class="panel-heading">
						<i class="icon-picture"></i> {l s='Add a new module'}
					</div>

					<div class="form-wrapper">
						<div class="form-group">
							<p>{l s='To add a new module, simply connect to your PrestaShop Addons account and all your purchases will be automatically imported.'}</p>
						</div>
					</div><!-- /.form-wrapper -->

					<div class="panel-footer">
						<a href="{$link->getAdminLink('AdminModules', true)|escape:'html':'UTF-8'}" class="btn btn-default">
							<i class="process-icon-cancel"></i> {l s='Cancel'}
						</a>
						<a href="#" data-toggle="modal" data-target="#modal_addons_connect" class="btn btn-default pull-right">
							<i class="process-icon-next"></i> {l s='Next'}
						</a>
					</div>
				</div>

			{/if}

				<div class="alert alert-info">
					<h4>{l s='Can I add my own modules?'}</h4>
					<p>{l s='Please note that for security reasons, you can only add modules that are being distributed on PrestaShop Addons, the official marketplace.'}</p>
				</div>

		</div>

	{elseif !isset($smarty.get.configure)}
		{include file='controllers/modules/js.tpl'}
		{include file='controllers/modules/page.tpl'}
	{/if}
{/if}
