<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:57:24
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/hotelreservationsystem/views/templates/admin/order_refund_rules/helpers/form/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1353553941568e054c75f5b8-72793905%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '65ad7de3479318e8299e2333abd0809fe5f8a8a5' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/hotelreservationsystem/views/templates/admin/order_refund_rules/helpers/form/form.tpl',
      1 => 1452142909,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1353553941568e054c75f5b8-72793905',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'edit' => 0,
    'table' => 0,
    'name_controller' => 0,
    'current' => 0,
    'submit_action' => 0,
    'token' => 0,
    'style' => 0,
    'refund_rules_info' => 0,
    'defaultcurrency_sign' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568e054c872589_79306341',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568e054c872589_79306341')) {function content_568e054c872589_79306341($_smarty_tpl) {?><div class="panel">
	<div class="panel-heading">
		<?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?>
			<i class='icon-pencil'></i>&nbsp<?php echo smartyTranslate(array('s'=>'Edit Refund Rule','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

		<?php } else { ?>
			<i class='icon-plus'></i>&nbsp<?php echo smartyTranslate(array('s'=>'Add New Refund Rule','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

		<?php }?>
	</div>
	<form id="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['table']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
_form" class="defaultForm <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['name_controller']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
 form-horizontal" action="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&<?php if (!empty($_smarty_tpl->tpl_vars['submit_action']->value)) {?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['submit_action']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }?>&token=<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" method="post" <?php if (isset($_smarty_tpl->tpl_vars['style']->value)) {?>style="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['style']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"<?php }?>>
		<?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?>
			<input type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['refund_rules_info']->value['id'], ENT_QUOTES, 'UTF-8', true);?>
" name="id" />
		<?php }?>
		<div class="form-group">
			<label for="refund_payment_type" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='<?php echo smartyTranslate(array('s'=>"Select type of payment you want.",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
'><?php echo smartyTranslate(array('s'=>"Select Payment Type",'mod'=>"hotelreservationsystem"),$_smarty_tpl);?>
</span>
			</label>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-lg-3">
						<select id="refund_payment_type" name="refund_payment_type">
							<option <?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?> <?php if ($_smarty_tpl->tpl_vars['refund_rules_info']->value['payment_type']==1) {?>selected<?php }?><?php }?> value="1"><?php echo smartyTranslate(array('s'=>"Percentage",'mod'=>"hotelreservationsystem"),$_smarty_tpl);?>
</option>
							<option value="2" <?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?> <?php if ($_smarty_tpl->tpl_vars['refund_rules_info']->value['payment_type']==2) {?>selected<?php }?><?php } else { ?>selected="true"<?php }?>><?php echo smartyTranslate(array('s'=>"Fixed Amount",'mod'=>"hotelreservationsystem"),$_smarty_tpl);?>
</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="deduction_value_adv_pay" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='<?php echo smartyTranslate(array('s'=>"Enter How much percent of total amount will be deducted as cancellation charges.",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
'><?php echo smartyTranslate(array('s'=>'Deduction Value For Advance Payment','mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon"><?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?> <?php if ($_smarty_tpl->tpl_vars['refund_rules_info']->value['payment_type']==2) {?><?php echo $_smarty_tpl->tpl_vars['defaultcurrency_sign']->value;?>
<?php } else { ?>%<?php }?><?php } else { ?><?php echo $_smarty_tpl->tpl_vars['defaultcurrency_sign']->value;?>
<?php }?></span>
					<input type="text" id="deduction_value_adv_pay" name="deduction_value_adv_pay" <?php if (isset($_smarty_tpl->tpl_vars['edit']->value)&&$_smarty_tpl->tpl_vars['refund_rules_info']->value['deduction_value_adv_pay']) {?> value="<?php echo $_smarty_tpl->tpl_vars['refund_rules_info']->value['deduction_value_adv_pay'];?>
" <?php }?>>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="deduction_value_full_pay" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='<?php echo smartyTranslate(array('s'=>"Enter How much percent of total amount will be deducted as cancellation charges.",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
'><?php echo smartyTranslate(array('s'=>'Deduction Value For Full Payment','mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon"><?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?> <?php if ($_smarty_tpl->tpl_vars['refund_rules_info']->value['payment_type']==2) {?><?php echo $_smarty_tpl->tpl_vars['defaultcurrency_sign']->value;?>
<?php } else { ?>%<?php }?><?php } else { ?><?php echo $_smarty_tpl->tpl_vars['defaultcurrency_sign']->value;?>
<?php }?></span>
					<input type="text" id="deduction_value_full_pay" name="deduction_value_full_pay" <?php if (isset($_smarty_tpl->tpl_vars['edit']->value)&&$_smarty_tpl->tpl_vars['refund_rules_info']->value['deduction_value_full_pay']) {?> value="<?php echo $_smarty_tpl->tpl_vars['refund_rules_info']->value['deduction_value_full_pay'];?>
" <?php }?>>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="cancellation_days" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='<?php echo smartyTranslate(array('s'=>"Enter the days How much days before this rule will be applied.",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
'><?php echo smartyTranslate(array('s'=>'Days Before Cancellation','mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<input type="text" id="cancellation_days" name="cancellation_days" <?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?> <?php if (isset($_smarty_tpl->tpl_vars['refund_rules_info']->value['days'])) {?>style = "display:block;" value="<?php echo $_smarty_tpl->tpl_vars['refund_rules_info']->value['days'];?>
" <?php }?><?php }?>>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrderRefundRules'), ENT_QUOTES, 'UTF-8', true);?>
" class="btn btn-default">
				<i class="process-icon-cancel"></i><?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</a>
			<button type="submit" name="submitAddorder_refund_rules" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Save','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</button>
			<button type="submit" name="submitAdd<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['table']->value, ENT_QUOTES, 'UTF-8', true);?>
AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Save and stay','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</button>
		</div>
	</form>
</div>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('defaultcurrency_sign'=>$_smarty_tpl->tpl_vars['defaultcurrency_sign']->value,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php }} ?>
