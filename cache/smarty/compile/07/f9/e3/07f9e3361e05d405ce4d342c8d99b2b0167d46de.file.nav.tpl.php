<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 05:21:47
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/themes/hotel-reservation-theme/modules/blockuserinfo/nav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:151132028756ab3d3be727e5-70534895%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '07f9e3361e05d405ce4d342c8d99b2b0167d46de' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/themes/hotel-reservation-theme/modules/blockuserinfo/nav.tpl',
      1 => 1454061942,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '151132028756ab3d3be727e5-70534895',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ajaxCustomerLogin' => 0,
    'logged' => 0,
    'cookie' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3d3be9e067_83478207',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3d3be9e067_83478207')) {function content_56ab3d3be9e067_83478207($_smarty_tpl) {?><!-- Block user information module NAV  -->
<?php if (!isset($_smarty_tpl->tpl_vars['ajaxCustomerLogin']->value)) {?>
<div class="nav_menu_padding header_user_info pull-right user_info_xs">
<?php }?>
	<ul class="navbar-nav userlogin_block">
	<?php if ($_smarty_tpl->tpl_vars['logged']->value) {?>
		<li class="dropdown account_info_cont">
			<button class="btn dropdown-toggle" type="button" id="user_info_acc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<span class="account_user_name hide_xs"><?php echo $_smarty_tpl->tpl_vars['cookie']->value->customer_firstname;?>
</span>
				<span class="account_user_name visi_xs"><i class="icon-user"></i></span>
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" aria-labelledby="user_info_acc">
				<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'View my customer account','mod'=>'blockuserinfo'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Accounts','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></li>
				<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('history',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'View my orders','mod'=>'blockuserinfo'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Orders'),$_smarty_tpl);?>
</a></li>
				<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true,null,"mylogout"), ENT_QUOTES, 'UTF-8', true);?>
"  title="<?php echo smartyTranslate(array('s'=>'Log me out','mod'=>'blockuserinfo'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Logout'),$_smarty_tpl);?>
</a></li>
			</ul>
		</li>
	<?php } else { ?>
	    <li class="active"><a class="user_login navigation-link" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true), ENT_QUOTES, 'UTF-8', true);?>
" rel="nofollow" title="<?php echo smartyTranslate(array('s'=>'Log in to your customer account','mod'=>'blockuserinfo'),$_smarty_tpl);?>
"><span class="hide_xs"><?php echo smartyTranslate(array('s'=>'Sign in','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</span><span class="visi_xs"><i class="icon-user"></i></span></a></li>
	<?php }?>
	</ul>
<?php if (!isset($_smarty_tpl->tpl_vars['ajaxCustomerLogin']->value)) {?>
</div>
<?php }?>
<!-- /Block user information module NAV -->
<?php }} ?>
