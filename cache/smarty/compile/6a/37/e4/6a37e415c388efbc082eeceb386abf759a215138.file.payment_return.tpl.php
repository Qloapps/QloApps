<?php /* Smarty version Smarty-3.1.19, created on 2016-01-06 21:09:40
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/hook/payment_return.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1004215292568d353cc55b37-22126281%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a37e415c388efbc082eeceb386abf759a215138' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/hook/payment_return.tpl',
      1 => 1452091895,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1004215292568d353cc55b37-22126281',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'valid' => 0,
    'shop_name' => 0,
    'reference' => 0,
    'id_order' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d353cc896b5_77258348',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d353cc896b5_77258348')) {function content_568d353cc896b5_77258348($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['valid']->value==1) {?>
	<div class="alert alert-success">
		<?php echo smartyTranslate(array('s'=>'Your order on %s is complete with','sprintf'=>$_smarty_tpl->tpl_vars['shop_name']->value,'mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>

		<?php if (isset($_smarty_tpl->tpl_vars['reference']->value)) {?>
			<?php echo smartyTranslate(array('s'=>'reference','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
 <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['reference']->value, ENT_QUOTES, 'UTF-8', true);?>
</b>
		<?php } else { ?>
			<?php echo smartyTranslate(array('s'=>'Order ID','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
 <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id_order']->value, ENT_QUOTES, 'UTF-8', true);?>
</b>
		<?php }?>.
	</div>
<?php } else { ?>
	<div class="error">
		<?php echo smartyTranslate(array('s'=>'Unfortunately, an error occurred during the transaction.','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
<br /><br />
		<?php echo smartyTranslate(array('s'=>'Please double-check your credit card details and try again. If you need further assistance, feel free to contact','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>

		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'customer service department.','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
</a>
		<?php echo smartyTranslate(array('s'=>'anytime.','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
<br /><br />
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'customer service department.','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
</a>.
		<?php if (isset($_smarty_tpl->tpl_vars['reference']->value)) {?>
			(<?php echo smartyTranslate(array('s'=>'Your Order\'s Reference:','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
 <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['reference']->value, ENT_QUOTES, 'UTF-8', true);?>
</b>)
		<?php } else { ?>
			(<?php echo smartyTranslate(array('s'=>'Your Order\'s ID:','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
 <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id_order']->value, ENT_QUOTES, 'UTF-8', true);?>
</b>)
		<?php }?>
	</div>
<?php }?>
<?php }} ?>
