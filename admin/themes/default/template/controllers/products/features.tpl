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

{if isset($product->id)}
<div id="product-features" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Features" />
	<h3>{l s='Assign features to this room type'}</h3>

	<div class="alert alert-info">
		{l s='Select one value for each room feature. Unselected features will not be displayed for this room type.'}
	</div>

	<div class="feature-tree-wrapper">
		{foreach from=$available_features item=available_feature}
			<div class="panel panel-default room-feature-group">
				<div class="panel-heading">
					<strong>{$available_feature.name|escape:'html':'UTF-8'}</strong>
				</div>
				<div class="panel-body">
					{if isset($available_feature.featureValues) && $available_feature.featureValues}
						<div class="radio">
							<label>
								<input type="radio" name="feature_{$available_feature.id_feature}_check" value="" {if !$available_feature.current_item}checked="checked"{/if} />
								{l s='None'}
							</label>
						</div>
						{foreach from=$available_feature.featureValues item=value}
							<div class="radio">
								<label>
									<input type="radio" name="feature_{$available_feature.id_feature}_check" value="{$value.id_feature_value|intval}" {if $value.selected}checked="checked"{/if} />
									{$value.value|escape:'html':'UTF-8'}
								</label>
							</div>
						{/foreach}
					{else}
						<p class="text-muted">{l s='No values available for this feature.'}</p>
					{/if}
				</div>
			</div>
		{foreachelse}
			<div class="alert alert-warning">
				<i class="icon-warning-sign"></i> {l s='No features have been defined'}
			</div>
		{/foreach}
	</div>

	<a href="{$link->getAdminLink('AdminFeatures')|escape:'html':'UTF-8'}&amp;addfeature" class="btn btn-link confirm_leave button">
		<i class="icon-plus-sign"></i> {l s='Add a new feature'} <i class="icon-external-link-sign"></i>
	</a>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
{/if}
