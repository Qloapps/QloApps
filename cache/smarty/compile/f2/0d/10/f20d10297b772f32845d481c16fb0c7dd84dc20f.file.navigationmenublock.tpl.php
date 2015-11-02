<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 12:11:16
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/blocknavigationmenu/views/templates/hook/navigationmenublock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21182986956376ee24f5d37-60007974%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f20d10297b772f32845d481c16fb0c7dd84dc20f' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/blocknavigationmenu/views/templates/hook/navigationmenublock.tpl',
      1 => 1446483171,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21182986956376ee24f5d37-60007974',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee25a9551_58642299',
  'variables' => 
  array (
    'force_ssl' => 0,
    'base_dir_ssl' => 0,
    'base_dir' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee25a9551_58642299')) {function content_56376ee25a9551_58642299($_smarty_tpl) {?><div class="pull-right clearfix nav_menu_padding">
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
						<a class="navigation-link" id="htl_our_rooms_link" href="#"><?php echo smartyTranslate(array('s'=>'Our Rooms','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_features_link" href="#"><?php echo smartyTranslate(array('s'=>'Features','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_testimonial_link" href="#"><?php echo smartyTranslate(array('s'=>'Testimonials','mod'=>'blocknevigationmenu'),$_smarty_tpl);?>
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
