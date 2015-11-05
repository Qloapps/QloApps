<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 17:58:33
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin338kklw7d/themes/default/template/controllers/products/prices.tpl" */ ?>
<?php /*%%SmartyHeaderCode:500877450563b4b71dc3ca0-22281811%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b70b114c4c7487746547fbae2c8ac7087e40381' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin338kklw7d/themes/default/template/controllers/products/prices.tpl',
      1 => 1446725532,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '500877450563b4b71dc3ca0-22281811',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'ecotax_tax_excl' => 0,
    'country_display_tax_label' => 0,
    'tax_exclude_taxe_option' => 0,
    'currency' => 0,
    'product' => 0,
    'priceDisplayPrecisionFormat' => 0,
    'pack' => 0,
    'taxesRatesByGroup' => 0,
    'tax_by_group' => 0,
    'ecotaxTaxRate' => 0,
    'tax_rules_groups' => 0,
    'tax_rules_group' => 0,
    'ps_use_ecotax' => 0,
    'unit_price' => 0,
    'ps_tax' => 0,
    'specificPriceModificationForm' => 0,
    'combinations' => 0,
    'combination' => 0,
    'multi_shop' => 0,
    'admin_one_shop' => 0,
    'shops' => 0,
    'shop' => 0,
    'currencies' => 0,
    'curr' => 0,
    'countries' => 0,
    'country' => 0,
    'groups' => 0,
    'group' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b4b720af692_36148052',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b4b720af692_36148052')) {function content_563b4b720af692_36148052($_smarty_tpl) {?>

<script type="text/javascript">
var Customer = new Object();
var product_url = '<?php echo addslashes($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts',true));?>
';
var ecotax_tax_excl = parseFloat(<?php echo $_smarty_tpl->tpl_vars['ecotax_tax_excl']->value;?>
);
var priceDisplayPrecision = <?php echo intval(@constant('_PS_PRICE_DISPLAY_PRECISION_'));?>
;

$(document).ready(function () {
	Customer = {
		"hiddenField": jQuery('#id_customer'),
		"field": jQuery('#customer'),
		"container": jQuery('#customers'),
		"loader": jQuery('#customerLoader'),
		"init": function() {
			jQuery(Customer.field).typeWatch({
				"captureLength": 1,
				"highlight": true,
				"wait": 50,
				"callback": Customer.search
			}).focus(Customer.placeholderIn).blur(Customer.placeholderOut);
		},
		"placeholderIn": function() {
			if (this.value == '<?php echo smartyTranslate(array('s'=>'All customers'),$_smarty_tpl);?>
') {
				this.value = '';
			}
		},
		"placeholderOut": function() {
			if (this.value == '') {
				this.value = '<?php echo smartyTranslate(array('s'=>'All customers'),$_smarty_tpl);?>
';
			}
		},
		"search": function()
		{
			Customer.showLoader();
			jQuery.ajax({
				"type": "POST",
				"url": "<?php echo addslashes($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers'));?>
",
				"async": true,
				"dataType": "json",
				"data": {
					"ajax": "1",
					"token": "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
",
					"tab": "AdminCustomers",
					"action": "searchCustomers",
					"customer_search": Customer.field.val()
				},
				"success": Customer.success
			});
		},
		"success": function(result)
		{
			if(result.found) {
				var html = '<ul class="list-unstyled">';
				jQuery.each(result.customers, function() {
					html += '<li><a class="fancybox" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers');?>
&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a>'+(this.birthday ? ' - '+this.birthday:'');
					html += ' - '+this.email;
					html += '<a onclick="Customer.select('+this.id_customer+', \''+this.firstname+' '+this.lastname+'\'); return false;" href="#" class="btn btn-default"><?php echo smartyTranslate(array('s'=>'Choose'),$_smarty_tpl);?>
</a></li>';
				});
				html += '</ul>';
			}
			else
				html = '<div class="alert alert-warning"><?php echo smartyTranslate(array('s'=>'No customers found'),$_smarty_tpl);?>
</div>';
			Customer.hideLoader();
			Customer.container.html(html);
			jQuery('.fancybox', Customer.container).fancybox();
		},
		"select": function(id_customer, fullname)
		{
			Customer.hiddenField.val(id_customer);
			Customer.field.val(fullname);
			Customer.container.empty();
			return false;
		},
		"showLoader": function() {
			Customer.loader.fadeIn();
		},
		"hideLoader": function() {
			Customer.loader.fadeOut();
		}
	};
	Customer.init();
});
</script>
<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'priceDisplayPrecisionFormat', null); ob_start(); ?><?php echo (('%.').(@constant('_PS_PRICE_DISPLAY_PRECISION_'))).('f');?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<div id="product-prices" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Prices" />
	<h3><?php echo smartyTranslate(array('s'=>'Product price'),$_smarty_tpl);?>
</h3>
	<div class="alert alert-info" <?php if (!$_smarty_tpl->tpl_vars['country_display_tax_label']->value||$_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?>style="display:none;"<?php }?>>
		<?php echo smartyTranslate(array('s'=>'You must enter either the pre-tax retail price, or the retail price with tax. The input field will be automatically calculated.'),$_smarty_tpl);?>

	</div>
	<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('product_tab'=>"Prices"), 0);?>

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"wholesale_price",'type'=>"default"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="wholesale_price">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'The wholesale price is the price you paid for the product. Do not include the tax.'),$_smarty_tpl);?>
"><?php if (!$_smarty_tpl->tpl_vars['country_display_tax_label']->value||$_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?><?php echo smartyTranslate(array('s'=>'Wholesale price'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Pre-tax wholesale price'),$_smarty_tpl);?>
<?php }?></span>
		</label>
		<div class="col-lg-2">
			<div class="input-group">
				<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
				<input maxlength="27" name="wholesale_price" id="wholesale_price" type="text" value="<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value->wholesale_price),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php echo sprintf($_smarty_tpl->tpl_vars['priceDisplayPrecisionFormat']->value,$_tmp1);?>
" onchange="this.value = this.value.replace(/,/g, '.');" />
			</div>
			<?php if (isset($_smarty_tpl->tpl_vars['pack']->value)&&$_smarty_tpl->tpl_vars['pack']->value->isPack($_smarty_tpl->tpl_vars['product']->value->id)) {?><p class="help-block"><?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>sprintf($_smarty_tpl->tpl_vars['priceDisplayPrecisionFormat']->value,$_smarty_tpl->tpl_vars['pack']->value->noPackWholesalePrice($_smarty_tpl->tpl_vars['product']->value->id))),$_smarty_tpl);?>
<?php $_tmp2=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'The sum of wholesale prices of the products in the pack is %s%s%s','sprintf'=>array($_smarty_tpl->tpl_vars['currency']->value->prefix,$_tmp2,$_smarty_tpl->tpl_vars['currency']->value->suffix)),$_smarty_tpl);?>
</p><?php }?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"price",'type'=>"price"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="priceTE">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'The pre-tax retail price is the price for which you intend sell this product to your customers. It should be higher than the pre-tax wholesale price: the difference between the two will be your margin.'),$_smarty_tpl);?>
"><?php if (!$_smarty_tpl->tpl_vars['country_display_tax_label']->value||$_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?><?php echo smartyTranslate(array('s'=>'Retail price'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Pre-tax retail price'),$_smarty_tpl);?>
<?php }?></span>
		</label>
		<div class="col-lg-2">
			<div class="input-group">
				<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
				<input type="hidden" id="priceTEReal" name="price" value="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value->price),$_smarty_tpl);?>
"/>
				<input size="11" maxlength="27" id="priceTE" name="price_displayed" type="text" value="<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value->price),$_smarty_tpl);?>
<?php $_tmp3=ob_get_clean();?><?php echo sprintf('%.6f',$_tmp3);?>
" onchange="noComma('priceTE'); $('#priceTEReal').val(this.value);" onkeyup="$('#priceType').val('TE'); $('#priceTEReal').val(this.value.replace(/,/g, '.')); if (isArrowKey(event)) return; calcPriceTI();" />
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"id_tax_rules_group",'type'=>"default"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="id_tax_rules_group">
			<?php echo smartyTranslate(array('s'=>'Tax rule:'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-8">
			<script type="text/javascript">
				noTax = <?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?>true<?php } else { ?>false<?php }?>;
				taxesArray = new Array();
				<?php  $_smarty_tpl->tpl_vars['tax_by_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tax_by_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['taxesRatesByGroup']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tax_by_group']->key => $_smarty_tpl->tpl_vars['tax_by_group']->value) {
$_smarty_tpl->tpl_vars['tax_by_group']->_loop = true;
?>
					taxesArray[<?php echo $_smarty_tpl->tpl_vars['tax_by_group']->value['id_tax_rules_group'];?>
] = <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['tax_by_group']->value);?>
;
				<?php } ?>
				ecotaxTaxRate = <?php echo $_smarty_tpl->tpl_vars['ecotaxTaxRate']->value/100;?>
;
			</script>
			<div class="row">
				<div class="col-lg-6">
					<select onchange="javascript:calcPrice(); unitPriceWithTax('unit');" name="id_tax_rules_group" id="id_tax_rules_group" <?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?>disabled="disabled"<?php }?> >
						<option value="0"><?php echo smartyTranslate(array('s'=>'No Tax'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['tax_rules_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tax_rules_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tax_rules_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tax_rules_group']->key => $_smarty_tpl->tpl_vars['tax_rules_group']->value) {
$_smarty_tpl->tpl_vars['tax_rules_group']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group'];?>
" <?php if ($_smarty_tpl->tpl_vars['product']->value->getIdTaxRulesGroup()==$_smarty_tpl->tpl_vars['tax_rules_group']->value['id_tax_rules_group']) {?>selected="selected"<?php }?> >
					<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['tax_rules_group']->value['name']);?>

						</option>
					<?php } ?>
					</select>
				</div>
				<div class="col-lg-2">
					<a class="btn btn-link confirm_leave" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminTaxRulesGroup'), ENT_QUOTES, 'UTF-8', true);?>
&amp;addtax_rules_group&amp;id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
"<?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?> disabled="disabled"<?php }?>>
						<i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Create new tax'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php if ($_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?>
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert">
				<?php echo smartyTranslate(array('s'=>'Taxes are currently disabled:'),$_smarty_tpl);?>

				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminTaxes'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Click here to open the Taxes configuration page.'),$_smarty_tpl);?>
</a>
				<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->getIdTaxRulesGroup();?>
" name="id_tax_rules_group" />
			</div>
		</div>
	</div>
	<?php }?>
	<div class="form-group" <?php if (!$_smarty_tpl->tpl_vars['ps_use_ecotax']->value) {?> style="display:none;"<?php }?>>
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"ecotax",'type'=>"default"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="ecotax">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'The ecotax is a local set of taxes intended to "promote ecologically sustainable activities via economic incentives". It is already included in retail price: the higher this ecotax is, the lower your margin will be.'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Ecotax (tax incl.)'),$_smarty_tpl);?>
</span>
		</label>
		<div class="input-group col-lg-2">
			<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
			<input maxlength="27" id="ecotax" name="ecotax" type="text" value="<?php echo sprintf($_smarty_tpl->tpl_vars['priceDisplayPrecisionFormat']->value,$_smarty_tpl->tpl_vars['product']->value->ecotax);?>
" onkeyup="$('#priceType').val('TI');if (isArrowKey(event))return; calcPriceTE(); this.value = this.value.replace(/,/g, '.'); if (parseInt(this.value) > getE('priceTE').value) this.value = getE('priceTE').value; if (isNaN(this.value)) this.value = 0;" />
		</div>
	</div>
	<div class="form-group" <?php if (!$_smarty_tpl->tpl_vars['country_display_tax_label']->value||$_smarty_tpl->tpl_vars['tax_exclude_taxe_option']->value) {?>style="display:none;"<?php }?> >
		<label class="control-label col-lg-3" for="priceTI"><?php echo smartyTranslate(array('s'=>'Retail price with tax'),$_smarty_tpl);?>
</label>
		<div class="input-group col-lg-2">
			<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
			<input id="priceType" name="priceType" type="hidden" value="TE" />
			<input id="priceTI" name="priceTI" type="text" value="" onchange="noComma('priceTI');" maxlength="27" onkeyup="$('#priceType').val('TI');if (isArrowKey(event)) return;  calcPriceTE();" />
		</div>
		<?php if (isset($_smarty_tpl->tpl_vars['pack']->value)&&$_smarty_tpl->tpl_vars['pack']->value->isPack($_smarty_tpl->tpl_vars['product']->value->id)) {?><p class="col-lg-9 col-lg-offset-3 help-block"><?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['toolsConvertPrice'][0][0]->toolsConvertPrice(array('price'=>sprintf($_smarty_tpl->tpl_vars['priceDisplayPrecisionFormat']->value,$_smarty_tpl->tpl_vars['pack']->value->noPackPrice($_smarty_tpl->tpl_vars['product']->value->id))),$_smarty_tpl);?>
<?php $_tmp4=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'The sum of prices of the products in the pack is %s%s%s','sprintf'=>array($_smarty_tpl->tpl_vars['currency']->value->prefix,$_tmp4,$_smarty_tpl->tpl_vars['currency']->value->suffix)),$_smarty_tpl);?>
</p><?php }?>
	</div>

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"unit_price",'type'=>"unit_price"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="unit_price">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'When selling a pack of items, you can indicate the unit price for each item of the pack. For instance, "per bottle" or "per pound".'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Unit price (tax excl.)'),$_smarty_tpl);?>
</span>
		</label>
		<div class="col-lg-4">
			<div class="input-group">
				<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
				<input id="unit_price" name="unit_price" type="text" value="<?php echo sprintf('%.6f',$_smarty_tpl->tpl_vars['unit_price']->value);?>
" maxlength="27" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); unitPriceWithTax('unit');"/>
				<span class="input-group-addon"><?php echo smartyTranslate(array('s'=>'per'),$_smarty_tpl);?>
</span>
				<input id="unity" name="unity" type="text" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->unity);?>
"  maxlength="255" onkeyup="if (isArrowKey(event)) return ;unitySecond();" onchange="unitySecond();"/>
			</div>
		</div>
	</div>
	<?php if (isset($_smarty_tpl->tpl_vars['product']->value->unity)&&$_smarty_tpl->tpl_vars['product']->value->unity) {?>
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-warning">
				<span><?php echo smartyTranslate(array('s'=>'or'),$_smarty_tpl);?>

					<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<span id="unit_price_with_tax">0.00</span><?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

					<?php echo smartyTranslate(array('s'=>'per'),$_smarty_tpl);?>
 <span id="unity_second"><?php echo $_smarty_tpl->tpl_vars['product']->value->unity;?>
</span><?php if ($_smarty_tpl->tpl_vars['ps_tax']->value&&$_smarty_tpl->tpl_vars['country_display_tax_label']->value) {?> <?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?>
				</span>
			</div>
		</div>
	</div>
	<?php }?>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"on_sale",'type'=>"default"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="on_sale">&nbsp;</label>
		<div class="col-lg-9">
			<div class="checkbox">
				<label class="control-label" for="on_sale" >
					<input type="checkbox" name="on_sale" id="on_sale" <?php if ($_smarty_tpl->tpl_vars['product']->value->on_sale) {?>checked="checked"<?php }?> value="1" />
					<?php echo smartyTranslate(array('s'=>'Display the "on sale" icon on the product page, and in the text found within the product listing.'),$_smarty_tpl);?>

				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-warning">
				<strong><?php echo smartyTranslate(array('s'=>'Final retail price:'),$_smarty_tpl);?>
</strong>
				<span>
					<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>

					<span id="finalPrice" >0.00</span>
					<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

					<span<?php if (!$_smarty_tpl->tpl_vars['ps_tax']->value) {?> style="display:none;"<?php }?>> (<?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
)</span>
				</span>
				<span<?php if (!$_smarty_tpl->tpl_vars['ps_tax']->value) {?> style="display:none;"<?php }?> >
				<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value) {?>
					/
				<?php }?>
					<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>

				<span id="finalPriceWithoutTax"></span>
					<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

					<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value) {?>(<?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
)<?php }?>
				</span>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>&amp;submitFilterproduct=<?php echo intval($_REQUEST['page']);?>
<?php }?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>
</button>
	</div>
</div>
<?php if (isset($_smarty_tpl->tpl_vars['specificPriceModificationForm']->value)) {?>
<div class="panel">
	<h3><?php echo smartyTranslate(array('s'=>'Specific prices'),$_smarty_tpl);?>
</h3>
	<div class="alert alert-info">
		<?php echo smartyTranslate(array('s'=>'You can set specific prices for clients belonging to different groups, different countries, etc.'),$_smarty_tpl);?>

	</div>
	<div class="form-group">
		<div class="col-lg-12">
			<a class="btn btn-default" href="#" id="show_specific_price">
				<i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Add a new specific price'),$_smarty_tpl);?>

			</a>
			<a class="btn btn-default" href="#" id="hide_specific_price" style="display:none">
				<i class="icon-remove text-danger"></i> <?php echo smartyTranslate(array('s'=>'Cancel new specific price'),$_smarty_tpl);?>

			</a>
		</div>
	</div>
	<script type="text/javascript">
		var product_prices = new Array();
		<?php  $_smarty_tpl->tpl_vars['combination'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['combination']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['combinations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['combination']->key => $_smarty_tpl->tpl_vars['combination']->value) {
$_smarty_tpl->tpl_vars['combination']->_loop = true;
?>
			product_prices['<?php echo $_smarty_tpl->tpl_vars['combination']->value['id_product_attribute'];?>
'] = '<?php echo addcslashes($_smarty_tpl->tpl_vars['combination']->value['price'],'\'');?>
';
		<?php } ?>
	</script>
	<div id="add_specific_price" class="well clearfix" style="display: none;">
		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label col-lg-2" for="<?php if (!$_smarty_tpl->tpl_vars['multi_shop']->value) {?>spm_currency_0<?php } else { ?>sp_id_shop<?php }?>"><?php echo smartyTranslate(array('s'=>'For'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<div class="row">
					<?php if (!$_smarty_tpl->tpl_vars['multi_shop']->value) {?>
						<input type="hidden" name="sp_id_shop" value="0" />
					<?php } else { ?>
						<div class="col-lg-3">
							<select name="sp_id_shop" id="sp_id_shop">
								<?php if (!$_smarty_tpl->tpl_vars['admin_one_shop']->value) {?><option value="0"><?php echo smartyTranslate(array('s'=>'All shops'),$_smarty_tpl);?>
</option><?php }?>
								<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value) {
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['shop']->value['id_shop'];?>
"><?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['shop']->value['name']);?>
</option>
								<?php } ?>
							</select>
						</div>
					<?php }?>
						<div class="col-lg-3">
							<select name="sp_id_currency" id="spm_currency_0" onchange="changeCurrencySpecificPrice(0);">
								<option value="0"><?php echo smartyTranslate(array('s'=>'All currencies'),$_smarty_tpl);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['curr'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['curr']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['curr']->key => $_smarty_tpl->tpl_vars['curr']->value) {
$_smarty_tpl->tpl_vars['curr']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['curr']->value['id_currency'];?>
"><?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['curr']->value['name']);?>
</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-lg-3">
							<select name="sp_id_country" id="sp_id_country">
								<option value="0"><?php echo smartyTranslate(array('s'=>'All countries'),$_smarty_tpl);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value) {
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['country']->value['id_country'];?>
"><?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['country']->value['name']);?>
</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-lg-3">
							<select name="sp_id_group" id="sp_id_group">
								<option value="0"><?php echo smartyTranslate(array('s'=>'All groups'),$_smarty_tpl);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="customer"><?php echo smartyTranslate(array('s'=>'Customer'),$_smarty_tpl);?>
</label>
				<div class="col-lg-4">
					<input type="hidden" name="sp_id_customer" id="id_customer" value="0" />
					<div class="input-group">
						<input type="text" name="customer" value="<?php echo smartyTranslate(array('s'=>'All customers'),$_smarty_tpl);?>
" id="customer" autocomplete="off" />
						<span class="input-group-addon"><i id="customerLoader" class="icon-refresh icon-spin" style="display: none;"></i> <i class="icon-search"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-10 col-lg-offset-2">
					<div id="customers"></div>
				</div>
			</div>
			<?php if (count($_smarty_tpl->tpl_vars['combinations']->value)!=0) {?>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_id_product_attribute"><?php echo smartyTranslate(array('s'=>'Combination:'),$_smarty_tpl);?>
</label>
				<div class="col-lg-4">
					<select id="sp_id_product_attribute" name="sp_id_product_attribute">
						<option value="0"><?php echo smartyTranslate(array('s'=>'Apply to all combinations'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['combination'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['combination']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['combinations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['combination']->key => $_smarty_tpl->tpl_vars['combination']->value) {
$_smarty_tpl->tpl_vars['combination']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['combination']->value['id_product_attribute'];?>
"><?php echo $_smarty_tpl->tpl_vars['combination']->value['attributes'];?>
</option>
					<?php } ?>
					</select>
				</div>
			</div>
			<?php }?>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_from"><?php echo smartyTranslate(array('s'=>'Available'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-4">
							<div class="input-group">
								<span class="input-group-addon"><?php echo smartyTranslate(array('s'=>'from'),$_smarty_tpl);?>
</span>
								<input type="text" name="sp_from" class="datepicker" value="" style="text-align: center" id="sp_from" />
								<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="input-group">
								<span class="input-group-addon"><?php echo smartyTranslate(array('s'=>'to'),$_smarty_tpl);?>
</span>
								<input type="text" name="sp_to" class="datepicker" value="" style="text-align: center" id="sp_to" />
								<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_from_quantity"><?php echo smartyTranslate(array('s'=>'Starting at'),$_smarty_tpl);?>
</label>
				<div class="col-lg-4">
					<div class="input-group">
						<span class="input-group-addon"><?php echo smartyTranslate(array('s'=>'unit'),$_smarty_tpl);?>
</span>
						<input type="text" name="sp_from_quantity" id="sp_from_quantity" value="1" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_price"><?php echo smartyTranslate(array('s'=>'Product price'),$_smarty_tpl);?>

					<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value) {?>
						<?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>

					<?php }?>
				</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-4">
							<div class="input-group">
								<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
								<input type="text" disabled="disabled" name="sp_price" id="sp_price" value="<?php echo sprintf($_smarty_tpl->tpl_vars['priceDisplayPrecisionFormat']->value,$_smarty_tpl->tpl_vars['product']->value->price);?>
" />
							</div>
							<p class="checkbox">
								<label for="leave_bprice"><?php echo smartyTranslate(array('s'=>'Leave base price:'),$_smarty_tpl);?>
</label>
								<input type="checkbox" id="leave_bprice" name="leave_bprice"  value="1" checked="checked"  />
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_reduction"><?php echo smartyTranslate(array('s'=>'Apply a discount of'),$_smarty_tpl);?>
</label>
				<div class="col-lg-4">
					<div class="row">
						<div class="col-lg-3">
							<input type="text" name="sp_reduction" id="sp_reduction" value="0.00"/>
						</div>
						<div class="col-lg-6">
							<select name="sp_reduction_type" id="sp_reduction_type">
								<option value="amount" selected="selected"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
								<option value="percentage"><?php echo smartyTranslate(array('s'=>'%'),$_smarty_tpl);?>
</option>
							</select>
						</div>
						<div class="col-lg-3">
							<select name="sp_reduction_tax" id="sp_reduction_tax">
								<option value="0"><?php echo smartyTranslate(array('s'=>'Tax excluded'),$_smarty_tpl);?>
</option>
								<option value="1" selected="selected"><?php echo smartyTranslate(array('s'=>'Tax included'),$_smarty_tpl);?>
</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var currencyName = '<?php echo addcslashes(htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->name, ENT_QUOTES, 'UTF-8', true),'\'');?>
';
		$(document).ready(function(){
			product_prices['0'] = $('#sp_current_ht_price').html();
			$('#id_product_attribute').change(function() {
				$('#sp_current_ht_price').html(product_prices[$('#id_product_attribute option:selected').val()]);
			});
			$('#leave_bprice').click(function() {
				if (this.checked)
					$('#sp_price').attr('disabled', 'disabled');
				else
					$('#sp_price').removeAttr('disabled');
			});
			$('.datepicker').datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				// Define a custom regional settings in order to use PrestaShop translation tools
				currentText: '<?php echo smartyTranslate(array('s'=>'Now','js'=>1),$_smarty_tpl);?>
',
				closeText: '<?php echo smartyTranslate(array('s'=>'Done','js'=>1),$_smarty_tpl);?>
',
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: '<?php echo smartyTranslate(array('s'=>'Choose Time','js'=>1),$_smarty_tpl);?>
',
				timeText: '<?php echo smartyTranslate(array('s'=>'Time','js'=>1),$_smarty_tpl);?>
',
				hourText: '<?php echo smartyTranslate(array('s'=>'Hour','js'=>1),$_smarty_tpl);?>
',
				minuteText: '<?php echo smartyTranslate(array('s'=>'Minute','js'=>1),$_smarty_tpl);?>
'
			});
			$('#sp_reduction_type').on('change', function() {
				if (this.value == 'percentage')
					$('#sp_reduction_tax').hide();
				else
					$('#sp_reduction_tax').show();
			});
		});
	</script>
	<div class="table-responsive">
	<table id="specific_prices_list" class="table table-bordered">
		<thead>
			<tr>
				<th><?php echo smartyTranslate(array('s'=>'Rule'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Combination'),$_smarty_tpl);?>
</th>
				<?php if ($_smarty_tpl->tpl_vars['multi_shop']->value) {?><th><?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>
</th><?php }?>
				<th><?php echo smartyTranslate(array('s'=>'Currency'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Group'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Customer'),$_smarty_tpl);?>
</th>
				<?php if ($_smarty_tpl->tpl_vars['country_display_tax_label']->value) {?>
					<th><?php echo smartyTranslate(array('s'=>'Fixed price (tax excl.)'),$_smarty_tpl);?>
</th>
				<?php } else { ?>
					<th><?php echo smartyTranslate(array('s'=>'Fixed price'),$_smarty_tpl);?>
</th>
				<?php }?>
				<th><?php echo smartyTranslate(array('s'=>'Impact'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Period'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'From (quantity)'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $_smarty_tpl->tpl_vars['specificPriceModificationForm']->value;?>

				<script type="text/javascript">
					$(document).ready(function() {
						delete_price_rule = '<?php echo smartyTranslate(array('s'=>"Do you really want to remove this price rule?"),$_smarty_tpl);?>
';
						calcPriceTI();
						unitPriceWithTax('unit');
						});
				</script>
			<?php }?>
<?php }} ?>
