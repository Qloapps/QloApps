<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 12:11:16
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkfooterblock/views/templates/hook/wkfooterblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:107893318556376ee2c63759-04135562%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4c4bd20917c91da09452d75eec98c6f5c5db2746' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkfooterblock/views/templates/hook/wkfooterblock.tpl',
      1 => 1446483102,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '107893318556376ee2c63759-04135562',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee2cf9017_18660624',
  'variables' => 
  array (
    'logo_url' => 0,
    'base_dir' => 0,
    'link' => 0,
    'redirect_link_about' => 0,
    'redirect_link_terms' => 0,
    'hotel_establish_year' => 0,
    'hotel_chain_name' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee2cf9017_18660624')) {function content_56376ee2cf9017_18660624($_smarty_tpl) {?><div id="footer_block" class="row margin-lr-0">
	<div class="footer_logo_block">
		<img class="img img-responsive" src="<?php echo $_smarty_tpl->tpl_vars['logo_url']->value;?>
">
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 footer_links_block hidden-xs">
		<a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" class="footer_links"><span><?php echo smartyTranslate(array('s'=>'Home','mod'=>'wkfooterblock'),$_smarty_tpl);?>
</span></a>
		<a href="#" class="footer_links footer_our_rooms_link" id="htl_sss_link"><span><?php echo smartyTranslate(array('s'=>'Our Rooms','mod'=>'wkfooterblock'),$_smarty_tpl);?>
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
