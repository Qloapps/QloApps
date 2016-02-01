<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:57
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/shop/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:114545460856ab3ba138d697-33395343%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e6d8b19ba920dc1082a9a1328d649cf49837a5ee' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/shop/content.tpl',
      1 => 1454062120,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '114545460856ab3ba138d697-33395343',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'shops_tree' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba13913e0_49566078',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba13913e0_49566078')) {function content_56ab3ba13913e0_49566078($_smarty_tpl) {?>

<div class="row">
	<div class="col-lg-4">
		<?php echo $_smarty_tpl->tpl_vars['shops_tree']->value;?>

	</div>
	<div class="col-lg-8"><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
</div>
</div><?php }} ?>
