<div class="col-lg-12 bootstrap">
	<div class="col-lg-6">
		{l s='Unselected'}
		<select multiple size="10" id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_1">
			{foreach from=$product_rule_itemlist.unselected item='item'}
				<option value="{$item.id|intval}" title="{$item.name}">&nbsp;{$item.name}</option>
			{/foreach}
		</select>
		<div class="clearfix">&nbsp;</div>
		<a id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add" class="btn btn-default btn-block" >
			{l s='Add'}
			<i class="icon-arrow-right"></i>
		</a>
	</div>
	<div class="col-lg-6">
		{l s='Selected'}
		<select multiple size="10" name="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}[]" id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_2" class="product_rule_toselect" >
			{foreach from=$product_rule_itemlist.selected item='item'}
				<option value="{$item.id|intval}" title="{$item.name}">&nbsp;{$item.name}</option>
			{/foreach}
		</select>
		<div class="clearfix">&nbsp;</div>
		<a id="product_rule_select_{$product_rule_group_id}_{$product_rule_id}_remove" class="btn btn-default btn-block" >
			<i class="icon-arrow-left"></i>
			{l s='Remove'}
		</a>
	</div>
</div>
			
<script type="text/javascript">
	$('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_remove').click(function() { removeCartRuleOption(this); updateProductRuleShortDescription(this); });
	$('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add').click(function() { addCartRuleOption(this); updateProductRuleShortDescription(this); });
	$(document).ready(function() { updateProductRuleShortDescription($('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add')); });
</script>
