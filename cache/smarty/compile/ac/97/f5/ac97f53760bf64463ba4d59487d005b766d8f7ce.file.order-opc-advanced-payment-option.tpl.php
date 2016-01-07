<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:33
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/order-opc-advanced-payment-option.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1960630745568d3049e31e90-38017266%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac97f53760bf64463ba4d59487d005b766d8f7ce' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/order-opc-advanced-payment-option.tpl',
      1 => 1452142844,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1960630745568d3049e31e90-38017266',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d3049e55d51_63462064',
  'variables' => 
  array (
    'advance_payment_active' => 0,
    'link' => 0,
    'customer_adv_dtl' => 0,
    'adv_amount' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d3049e55d51_63462064')) {function content_568d3049e55d51_63462064($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['advance_payment_active']->value)) {?>
	<div class="opc-main-block">
		<div class="row margin-lr-0">
			<div class="col-sm-12 col-xs-12 box">
				<h3 class="page-subheading"><?php echo smartyTranslate(array('s'=>'Payment Types'),$_smarty_tpl);?>
</h3>
				<div class="row">
					<form method="POST" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc'), ENT_QUOTES, 'UTF-8', true);?>
" id="advanced-payment">
						<div class="col-sm-12 col-xs-12">
							<label>
								<input type="radio" value="1" name="payment_type" class="payment_type" <?php if (!isset($_smarty_tpl->tpl_vars['customer_adv_dtl']->value)) {?>checked="checked"<?php }?>>
								<span><?php echo smartyTranslate(array('s'=>'Full Payment'),$_smarty_tpl);?>
</span>
							</label>
						</div>
						<div class="col-sm-12 col-xs-12">
							<label>
								<input type="radio" value="2" name="payment_type" class="payment_type" <?php if (isset($_smarty_tpl->tpl_vars['customer_adv_dtl']->value)) {?>checked="checked"<?php }?>>
								<span><?php echo smartyTranslate(array('s'=>'Partial Payment'),$_smarty_tpl);?>
</span>
							</label>

							<?php if (isset($_smarty_tpl->tpl_vars['customer_adv_dtl']->value)) {?>
								<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['customer_adv_dtl']->value['id'];?>
" name="id_customer_adv">
							<?php }?>

							<div class="row" id="partial_data">
								<div class="row margin-lr-0">
									<div class="col-xs-offset-2 col-xs-6 col-sm-offset-1 col-sm-5 partial_subcont">
										<span class="partial_txt"><?php echo smartyTranslate(array('s'=>'Currently Payment Amount'),$_smarty_tpl);?>
 - </span>
										<span class="partial_mim_cost"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['adv_amount']->value),$_smarty_tpl);?>
</span>
									</div>
								</div>
								
								<?php if (isset($_smarty_tpl->tpl_vars['customer_adv_dtl']->value)) {?>
									<div class="row margin-lr-0">
										<div class="col-xs-offset-2 col-xs-6 col-sm-offset-1 col-sm-5 partial_subcont">
											<span class="partial_txt"><?php echo smartyTranslate(array('s'=>'Due Amount'),$_smarty_tpl);?>
 - </span>
											<span class="partial_mim_cost"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['customer_adv_dtl']->value['due_amount']),$_smarty_tpl);?>
</span>
										</div>
									</div>
								<?php }?>
							</div>
						</div>
						<div class="col-sm-12 col-xs-12 margin-top-10">
							<button class="btn btn-default" name="submitAdvPayment" type="submit">
								<span><?php echo smartyTranslate(array('s'=>'OK'),$_smarty_tpl);?>
</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php }?><?php }} ?>
