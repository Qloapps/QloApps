{*
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

{if isset($tourismTaxRulesGroups)}
<div class="form-group">
	<div class="col-lg-1"></div>
	<label class="control-label col-lg-2" for="id_tourism_tax_rules_group">{l s='Tourism Tax Rule:'}</label>
	<div class="col-lg-8">
		<div class="row">
			<div class="col-lg-6">
				<select name="id_tourism_tax_rules_group" id="id_tourism_tax_rules_group"{if isset($tourismTaxRatesByGroup)} onchange="javascript:calcPrice();"{/if}>
					{foreach from=$tourismTaxRulesGroups item=ttrg}
						<option value="{$ttrg.id_tax_rules_group|intval}"
							{if isset($id_tourism_tax_rules_group) && $id_tourism_tax_rules_group == $ttrg.id_tax_rules_group}selected="selected"{/if}>
							{$ttrg.name|escape:'htmlall':'UTF-8'}
						</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-2">
				<a class="btn btn-link confirm_leave" href="{$link->getAdminLink('AdminTaxRulesGroup')|escape:'html':'UTF-8'}&amp;addtax_rules_group&amp;id_product={$product->id}&amp;is_tourism_tax_rule_group=1">
					<i class="icon-plus-sign"></i> {l s='Create new tax'} <i class="icon-external-link-sign"></i>
				</a>
			</div>
		</div>
	</div>
</div>
{if isset($tourismTaxRatesByGroup)}
<script type="text/javascript">
	var tourismTaxRatesByGroup = {$tourismTaxRatesByGroup|json_encode};
</script>
{/if}
{/if}
