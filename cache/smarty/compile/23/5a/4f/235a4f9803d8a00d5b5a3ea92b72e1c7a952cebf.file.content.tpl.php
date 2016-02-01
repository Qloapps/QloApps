<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 21:39:33
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin066crc2rr/themes/default/template/controllers/localization/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:42859160856ab79a51066f5-74198629%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '235a4f9803d8a00d5b5a3ea92b72e1c7a952cebf' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin066crc2rr/themes/default/template/controllers/localization/content.tpl',
      1 => 1454062119,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '42859160856ab79a51066f5-74198629',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'localization_form' => 0,
    'localization_options' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab79a51595e0_80215956',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab79a51595e0_80215956')) {function content_56ab79a51595e0_80215956($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['localization_form']->value)) {?><?php echo $_smarty_tpl->tpl_vars['localization_form']->value;?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['localization_options']->value)) {?><?php echo $_smarty_tpl->tpl_vars['localization_options']->value;?>
<?php }?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#PS_CURRENCY_DEFAULT').change(function(e) {
			alert('Before changing the default currency, we strongly recommend that you enable maintenance mode because any change on default currency requires manual adjustment of the price of each product');
		});
	});
</script><?php }} ?>
