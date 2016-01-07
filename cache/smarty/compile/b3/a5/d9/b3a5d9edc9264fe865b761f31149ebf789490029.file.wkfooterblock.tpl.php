<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:33
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkfooterblock/views/templates/hook/wkfooterblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1713183470568d37acc5c033-63304387%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b3a5d9edc9264fe865b761f31149ebf789490029' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkfooterblock/views/templates/hook/wkfooterblock.tpl',
      1 => 1452142878,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1713183470568d37acc5c033-63304387',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d37acc8cfa7_68134247',
  'variables' => 
  array (
    'logo_url' => 0,
    'base_dir' => 0,
    'page_name' => 0,
    'link' => 0,
    'redirect_link_about' => 0,
    'redirect_link_terms' => 0,
    'hotel_establish_year' => 0,
    'hotel_chain_name' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d37acc8cfa7_68134247')) {function content_568d37acc8cfa7_68134247($_smarty_tpl) {?><div id="footer_block" class="row margin-lr-0">
	<div class="footer_logo_block">
		<img class="img img-responsive" src="<?php echo $_smarty_tpl->tpl_vars['logo_url']->value;?>
">
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 footer_links_block hidden-xs">
		<a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'Home','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		<a href="<?php if (($_smarty_tpl->tpl_vars['page_name']->value=='index')) {?>#<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
#htmlcontent_home<?php }?>" class="footer_links footer_our_rooms_link" id="htl_sss_link"><span><?php echo smartyTranslate(array('s'=>'Our Rooms','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
" class="footer_links footer_contact_link"><span><?php echo smartyTranslate(array('s'=>'Contact','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		<a href="<?php echo $_smarty_tpl->tpl_vars['redirect_link_about']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'About us','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		<a href="<?php echo $_smarty_tpl->tpl_vars['redirect_link_terms']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'Terms and Conditions','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 footer_links_block visible-xs">
		<p>
			<a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'Home','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		</p>
		<p>
			<a href="#" class="footer_links footer_our_rooms_link" id="htl_sss_link"><span><?php echo smartyTranslate(array('s'=>'Our Rooms','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		</p>
		<p>
			<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
" class="footer_links footer_contact_link"><span><?php echo smartyTranslate(array('s'=>'Contact','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		</p>
		<p>
			<a href="<?php echo $_smarty_tpl->tpl_vars['redirect_link_about']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'About us','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		</p>
		<p>
			<a href="<?php echo $_smarty_tpl->tpl_vars['redirect_link_terms']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'Terms and Conditions','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		</p>
	</div>
	<div class="copyright_block row margin-lr-0">
		&copy; <?php echo $_smarty_tpl->tpl_vars['hotel_establish_year']->value;?>
-<?php echo date('Y');?>
 <a class="webkul_link_footer" href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
">&nbsp;<?php echo $_smarty_tpl->tpl_vars['hotel_chain_name']->value;?>
.</a>&nbsp;<?php echo smartyTranslate(array('s'=>' All rights reserved.','mod'=>'wkfooterblock'),$_smarty_tpl);?>

	</div>
</div><?php }} ?>
