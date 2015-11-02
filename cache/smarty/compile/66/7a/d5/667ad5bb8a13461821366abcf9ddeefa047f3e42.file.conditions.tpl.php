<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:43
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/cart_rules/conditions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2021870635637740b657f83-59881587%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '667ad5bb8a13461821366abcf9ddeefa047f3e42' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/cart_rules/conditions.tpl',
      1 => 1446455062,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2021870635637740b657f83-59881587',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currentObject' => 0,
    'currentTab' => 0,
    'customerFilter' => 0,
    'defaultDateFrom' => 0,
    'defaultDateTo' => 0,
    'currencies' => 0,
    'currency' => 0,
    'defaultCurrency' => 0,
    'countries' => 0,
    'country' => 0,
    'carriers' => 0,
    'carrier' => 0,
    'groups' => 0,
    'group' => 0,
    'cart_rules' => 0,
    'product_rule_groups' => 0,
    'product_rule_group' => 0,
    'shops' => 0,
    'shop' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5637740b763415_46983692',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5637740b763415_46983692')) {function content_5637740b763415_46983692($_smarty_tpl) {?><div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'Optional: The cart rule will be available to everyone if you leave this field blank.'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Limit to a single customer'),$_smarty_tpl);?>

		</span>
	</label>
	<div class="col-lg-9">
		<div class="input-group col-lg-12">		
			<span class="input-group-addon"><i class="icon-user"></i></span>
			<input type="hidden" id="id_customer" name="id_customer" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'id_customer'));?>
" />
			<input type="text" id="customerFilter" class="input-xlarge" name="customerFilter" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['customerFilter']->value, ENT_QUOTES, 'UTF-8', true);?>
" />
			<span class="input-group-addon"><i class="icon-search"></i></span>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'The default period is one month.'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Valid'),$_smarty_tpl);?>

		</span>
	</label>
	<div class="col-lg-9">
		<div class="row">
			<div class="col-lg-6">
				<div class="input-group">
					<span class="input-group-addon"><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</span>
					<input type="text" class="datepicker input-medium" name="date_from"
					value="<?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_from')) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_from'), ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['defaultDateFrom']->value;?>
<?php }?>" />
					<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="input-group">
					<span class="input-group-addon"><?php echo smartyTranslate(array('s'=>'To'),$_smarty_tpl);?>
</span>
					<input type="text" class="datepicker input-medium" name="date_to"
					value="<?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_to')) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'date_to'), ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['defaultDateTo']->value;?>
<?php }?>" />
					<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'You can choose a minimum amount for the cart either with or without the taxes and shipping.'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Minimum amount'),$_smarty_tpl);?>

		</span>
	</label>
	<div class="col-lg-9">
		<div class="row">
			<div class="col-lg-3">
				<input type="text" name="minimum_amount" value="<?php echo floatval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount'));?>
" />
			</div>
			<div class="col-lg-2">
				<select name="minimum_amount_currency">
				<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value) {
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
					<option value="<?php echo intval($_smarty_tpl->tpl_vars['currency']->value['id_currency']);?>
"
					<?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_currency')==$_smarty_tpl->tpl_vars['currency']->value['id_currency']||(!$_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_currency')&&$_smarty_tpl->tpl_vars['currency']->value['id_currency']==$_smarty_tpl->tpl_vars['defaultCurrency']->value)) {?>
						selected="selected"
					<?php }?>
					>
						<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>

					</option>
				<?php } ?>
				</select>
			</div>
			<div class="col-lg-3">
				<select name="minimum_amount_tax">
					<option value="0" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_tax')==0) {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Tax excluded'),$_smarty_tpl);?>
</option>
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_tax')==1) {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Tax included'),$_smarty_tpl);?>
</option>
				</select>
			</div>
			<div class="col-lg-4">
				<select name="minimum_amount_shipping">
					<option value="0" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_shipping')==0) {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Shipping excluded'),$_smarty_tpl);?>
</option>
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'minimum_amount_shipping')==1) {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Shipping included'),$_smarty_tpl);?>
</option>
				</select>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'The cart rule will be applied to the first "X" customers only.'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Total available'),$_smarty_tpl);?>

		</span>
	</label>
	<div class="col-lg-9">
		<input class="form-control" type="text" name="quantity" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'quantity'));?>
" />
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'A customer will only be able to use the cart rule "X" time(s).'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Total available for each user'),$_smarty_tpl);?>

		</span>
	</label>
	<div class="col-lg-9">
		<input class="form-control" type="text" name="quantity_per_user" value="<?php echo intval($_smarty_tpl->tpl_vars['currentTab']->value->getFieldValue($_smarty_tpl->tpl_vars['currentObject']->value,'quantity_per_user'));?>
" />
	</div>
</div>



<div class="form-group">
	<label class="control-label col-lg-3">
		<?php echo smartyTranslate(array('s'=>'Restrictions'),$_smarty_tpl);?>

	</label>
	<div class="col-lg-9">
		<?php if (count($_smarty_tpl->tpl_vars['countries']->value['unselected'])+count($_smarty_tpl->tpl_vars['countries']->value['selected'])>1) {?>
			<p class="checkbox">
				<label>
					<input type="checkbox" id="country_restriction" name="country_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['countries']->value['unselected'])) {?>checked="checked"<?php }?> />
					<?php echo smartyTranslate(array('s'=>'Country selection'),$_smarty_tpl);?>

				</label>
			</p>
			<span class="help-block"><?php echo smartyTranslate(array('s'=>'This restriction applies to the country of delivery.'),$_smarty_tpl);?>
</span>
			<div id="country_restriction_div">
				<br />
				<table class="table">
					<tr>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Unselected countries'),$_smarty_tpl);?>
</p>
							<select id="country_select_1" multiple>
								<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value) {
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['country']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="country_select_add" class="btn  btn-default btn-block clearfix"><?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Selected countries'),$_smarty_tpl);?>
</p>
							<select name="country_select[]" id="country_select_2" class="input-large" multiple>
								<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value) {
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['country']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="country_select_remove" class="btn btn-default btn-block clearfix"><i class="icon-arrow-left"></i> <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
 </a>
						</td>
					</tr>
				</table>
			</div>
		<?php }?>

		<?php if (count($_smarty_tpl->tpl_vars['carriers']->value['unselected'])+count($_smarty_tpl->tpl_vars['carriers']->value['selected'])>1) {?>
			<p class="checkbox">
				<label>
					<input type="checkbox" id="carrier_restriction" name="carrier_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['carriers']->value['unselected'])) {?>checked="checked"<?php }?> />
					<?php echo smartyTranslate(array('s'=>'Carrier selection'),$_smarty_tpl);?>

				</label>
			</p>
			<div id="carrier_restriction_div">
				<br />
				<table class="table">
					<tr>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Unselected carriers'),$_smarty_tpl);?>
</p>
							<select id="carrier_select_1" class="input-large" multiple>
								<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carriers']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value) {
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['carrier']->value['id_reference']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['carrier']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="carrier_select_add" class="btn btn-default btn-block clearfix" ><?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Selected carriers'),$_smarty_tpl);?>
</p>
							<select name="carrier_select[]" id="carrier_select_2" class="input-large" multiple>
								<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carriers']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value) {
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['carrier']->value['id_reference']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['carrier']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="carrier_select_remove" class="btn btn-default btn-block clearfix"><i class="icon-arrow-left"></i> <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
 </a>
						</td>
					</tr>
				</table>
			</div>
		<?php }?>

		<?php if (count($_smarty_tpl->tpl_vars['groups']->value['unselected'])+count($_smarty_tpl->tpl_vars['groups']->value['selected'])>1) {?>
			<p class="checkbox">
				<label>
					<input type="checkbox" id="group_restriction" name="group_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['groups']->value['unselected'])) {?>checked="checked"<?php }?> />
					<?php echo smartyTranslate(array('s'=>'Customer group selection'),$_smarty_tpl);?>

				</label>
			</p>
			<div id="group_restriction_div">
				<br />
				<table class="table">
					<tr>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Unselected groups'),$_smarty_tpl);?>
</p>
							<select id="group_select_1" class="input-large" multiple>
								<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['group']->value['id_group']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['group']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="group_select_add" class="btn btn-default btn-block clearfix" ><?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Selected groups'),$_smarty_tpl);?>
</p>
							<select name="group_select[]" class="input-large" id="group_select_2" multiple>
								<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['groups']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['group']->value['id_group']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['group']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="group_select_remove" class="btn btn-default btn-block clearfix" ><i class="icon-arrow-left"></i> <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
</a>
						</td>
					</tr>
				</table>
			</div>
		<?php }?>

		<?php if (count($_smarty_tpl->tpl_vars['cart_rules']->value['unselected'])+count($_smarty_tpl->tpl_vars['cart_rules']->value['selected'])>0) {?>
			<p class="checkbox">
				<label>
					<input type="checkbox" id="cart_rule_restriction" name="cart_rule_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['cart_rules']->value['unselected'])) {?>checked="checked"<?php }?> />
					<?php echo smartyTranslate(array('s'=>'Compatibility with other cart rules'),$_smarty_tpl);?>

				</label>
			</p>
			<div id="cart_rule_restriction_div">
				<br />
				<table  class="table">
					<tr>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Uncombinable cart rules'),$_smarty_tpl);?>
</p>
							<input id="cart_rule_select_1_filter" autocomplete="off" class="form-control uncombinable_search_filter" type="text" name="uncombinable_filter" placeholder="<?php echo smartyTranslate(array('s'=>'Search'),$_smarty_tpl);?>
" value="">
							<select id="cart_rule_select_1" class="jscroll" multiple="">
							</select>
							<a class="jscroll-next btn btn-default btn-block clearfix" href=""><?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
</a>
							<a id="cart_rule_select_add" class="btn btn-default btn-block clearfix"><?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Combinable cart rules'),$_smarty_tpl);?>
</p>
							<input id="cart_rule_select_2_filter" autocomplete="off" class="form-control combinable_search_filter" type="text" name="combinable_filter" placeholder="<?php echo smartyTranslate(array('s'=>'Search'),$_smarty_tpl);?>
" value="">
							<select name="cart_rule_select[]" class="jscroll" id="cart_rule_select_2" multiple>
							</select>
							<a class="jscroll-next btn btn-default btn-block clearfix" href=""><?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
</a>
							<a id="cart_rule_select_remove" class="btn btn-default btn-block clearfix" ><i class="icon-arrow-left"></i> <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
</a>
						</td>
					</tr>
				</table>
			</div>
		<?php }?>

			<p class="checkbox">
				<label>
					<input type="checkbox" id="product_restriction" name="product_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['product_rule_groups']->value)) {?>checked="checked"<?php }?> />
					<?php echo smartyTranslate(array('s'=>'Product selection'),$_smarty_tpl);?>

				</label>
			</p>
			<div id="product_restriction_div">
				<br />
				<table id="product_rule_group_table" class="table">
					<?php  $_smarty_tpl->tpl_vars['product_rule_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product_rule_group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product_rule_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product_rule_group']->key => $_smarty_tpl->tpl_vars['product_rule_group']->value) {
$_smarty_tpl->tpl_vars['product_rule_group']->_loop = true;
?>
						<?php echo $_smarty_tpl->tpl_vars['product_rule_group']->value;?>

					<?php } ?>
				</table>
				<a href="javascript:addProductRuleGroup();" class="btn btn-default ">
					<i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Product selection'),$_smarty_tpl);?>

				</a>
			</div>

		<?php if (count($_smarty_tpl->tpl_vars['shops']->value['unselected'])+count($_smarty_tpl->tpl_vars['shops']->value['selected'])>1) {?>
			<p class="checkbox">
				<label>
					<input type="checkbox" id="shop_restriction" name="shop_restriction" value="1" <?php if (count($_smarty_tpl->tpl_vars['shops']->value['unselected'])) {?>checked="checked"<?php }?> />
					<?php echo smartyTranslate(array('s'=>'Shop selection'),$_smarty_tpl);?>

				</label>
			</p>
			<div id="shop_restriction_div">
				<br/>
				<table class="table">
					<tr>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Unselected shops'),$_smarty_tpl);?>
</p>
							<select id="shop_select_1" multiple>
								<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value['unselected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value) {
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['shop']->value['id_shop']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="shop_select_add" class="btn btn-default btn-block clearfix" ><?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
 <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Selected shops'),$_smarty_tpl);?>
</p>
							<select name="shop_select[]" id="shop_select_2" multiple>
								<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value['selected']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value) {
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['shop']->value['id_shop']);?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
							<a id="shop_select_remove" class="btn btn-default btn-block clearfix" ><i class="icon-arrow-left"></i> <?php echo smartyTranslate(array('s'=>'Remove'),$_smarty_tpl);?>
</a>
						</td>
					</tr>
				</table>
			</div>
		<?php }?>
	</div>
</div><?php }} ?>
