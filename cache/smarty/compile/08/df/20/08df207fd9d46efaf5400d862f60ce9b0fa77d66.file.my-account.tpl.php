<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:59:21
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/modules/blockwishlist/my-account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:283847946568d37b150aca6-78936814%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08df207fd9d46efaf5400d862f60ce9b0fa77d66' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/modules/blockwishlist/my-account.tpl',
      1 => 1452142872,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '283847946568d37b150aca6-78936814',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d37b15152d5_91220579',
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d37b15152d5_91220579')) {function content_568d37b15152d5_91220579($_smarty_tpl) {?>

<!-- MODULE WishList -->
<li class="lnk_wishlist">
	<a 	href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('blockwishlist','mywishlist',array(),true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'My wishlists','mod'=>'blockwishlist'),$_smarty_tpl);?>
">
		<i class="icon-heart"></i>
		<span><?php echo smartyTranslate(array('s'=>'My wishlists','mod'=>'blockwishlist'),$_smarty_tpl);?>
</span>
	</a>
</li>
<!-- END : MODULE WishList --><?php }} ?>
