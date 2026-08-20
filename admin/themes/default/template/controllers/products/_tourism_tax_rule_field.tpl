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
