<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:48
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/modules/bankwire/views/templates/hook/payment_return.tpl" */ ?>
<?php /*%%SmartyHeaderCode:733259472568d306087b994-42043410%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '788da02e820f3af1c1bd1353680dfa8ac8111fcc' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/modules/bankwire/views/templates/hook/payment_return.tpl',
      1 => 1452142908,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '733259472568d306087b994-42043410',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d306095fef5_48406362',
  'variables' => 
  array (
    'status' => 0,
    'shop_name' => 0,
    'total_to_pay' => 0,
    'reference' => 0,
    'id_order' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d306095fef5_48406362')) {function content_568d306095fef5_48406362($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['status']->value=='ok') {?>
<p class="alert alert-success"><?php echo smartyTranslate(array('s'=>'Your order on %s is complete.','sprintf'=>$_smarty_tpl->tpl_vars['shop_name']->value,'mod'=>'cheque'),$_smarty_tpl);?>
</p><br /><br />
		<?php echo smartyTranslate(array('s'=>'Please send us a bank wire with','mod'=>'bankwire'),$_smarty_tpl);?>

		<br />- <?php echo smartyTranslate(array('s'=>'Amount','mod'=>'bankwire'),$_smarty_tpl);?>
 <span class="price"><strong><?php echo $_smarty_tpl->tpl_vars['total_to_pay']->value;?>
</strong></span>
		<?php if (!isset($_smarty_tpl->tpl_vars['reference']->value)) {?>
			<br />- <?php echo smartyTranslate(array('s'=>'Do not forget to insert your order number #%d in the subject of your bank wire.','sprintf'=>$_smarty_tpl->tpl_vars['id_order']->value,'mod'=>'bankwire'),$_smarty_tpl);?>

		<?php } else { ?>
			<br />- <?php echo smartyTranslate(array('s'=>'Do not forget to insert your order reference %s in the subject of your bank wire.','sprintf'=>$_smarty_tpl->tpl_vars['reference']->value,'mod'=>'bankwire'),$_smarty_tpl);?>

		<?php }?>
	</p>
<?php } else { ?>
	<p class="warning">
		<?php echo smartyTranslate(array('s'=>'We noticed a problem with your order. If you think this is an error, feel free to contact our','mod'=>'bankwire'),$_smarty_tpl);?>
 
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'expert customer support team','mod'=>'bankwire'),$_smarty_tpl);?>
</a>.
	</p>
<?php }?>
<?php }} ?>
