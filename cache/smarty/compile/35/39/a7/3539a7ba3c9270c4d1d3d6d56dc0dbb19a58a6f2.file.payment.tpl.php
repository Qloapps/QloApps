<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:33
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/hook/payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:887413181568d3059da6e03-97269672%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3539a7ba3c9270c4d1d3d6d56dc0dbb19a58a6f2' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/hook/payment.tpl',
      1 => 1452144187,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '887413181568d3059da6e03-97269672',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d3059daf930_73062559',
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d3059daf930_73062559')) {function content_568d3059daf930_73062559($_smarty_tpl) {?><div class="row">
	<div class="col-xs-12 col-md-12">
		<p class="payment_module">
			<a class="wk_paypal_adaptive" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('wkpaypaladaptive','payment'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Paypal Adaptive Payment','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Paypal Adaptive Payment','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>

			</a>
		</p>
	</div>
</div><?php }} ?>
