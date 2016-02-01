<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 05:21:47
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/modules/blocknavigationmenu/views/templates/hook/navigationmenublock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:190896277056ab3d3ba2af42-35645307%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0dce6779eb2c57f03d6a3a16c2302ecd99bd6ea8' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/modules/blocknavigationmenu/views/templates/hook/navigationmenublock.tpl',
      1 => 1454061953,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '190896277056ab3d3ba2af42-35645307',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'force_ssl' => 0,
    'base_dir_ssl' => 0,
    'base_dir' => 0,
    'page_name' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3d3ba73c53_55560237',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3d3ba73c53_55560237')) {function content_56ab3d3ba73c53_55560237($_smarty_tpl) {?><div class="pull-right clearfix nav_menu_padding">
	<button type="button" class="nav_toggle">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
</div>

<div id="menu_cont" class="menu_cont_right">
	<div class="row margin-lr-0">
		<div class="col-xs-12 col-sm-12">
			<div class="row margin-lr-0">
				<span class="pull-right close_navbar"><i class="icon-close"></i></span>
			</div>
			<div class="row margin-lr-0 margin-top-20">
				<ul class="nav nav-pills nav-stacked">
					<li>
						<a class="navigation-link" href="<?php if (isset($_smarty_tpl->tpl_vars['force_ssl']->value)&&$_smarty_tpl->tpl_vars['force_ssl']->value) {?><?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
<?php }?>"><?php echo smartyTranslate(array('s'=>'Home','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_our_rooms_link" href="<?php if (($_smarty_tpl->tpl_vars['page_name']->value=='index')) {?>#<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
#htmlcontent_home<?php }?>"><?php echo smartyTranslate(array('s'=>'Our Rooms','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_features_link" href="<?php if (($_smarty_tpl->tpl_vars['page_name']->value=='index')) {?>#<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
#features_block<?php }?>"><?php echo smartyTranslate(array('s'=>'Features','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_testimonial_link" href="<?php if (($_smarty_tpl->tpl_vars['page_name']->value=='index')) {?>#<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
#testimonial_block<?php }?>"><?php echo smartyTranslate(array('s'=>'Testimonials','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Contact','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
				</ul>
			</div>
		</div>
	</div>
</div><?php }} ?>
