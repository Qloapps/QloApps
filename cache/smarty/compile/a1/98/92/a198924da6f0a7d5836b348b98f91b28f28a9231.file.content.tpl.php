<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 10:47:20
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/controllers/login/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1927022253563785886fe7b5-78631994%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a198924da6f0a7d5836b348b98f91b28f28a9231' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/controllers/login/content.tpl',
      1 => 1446455063,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1927022253563785886fe7b5-78631994',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'img_dir' => 0,
    'ps_version' => 0,
    'errors' => 0,
    'nbErrors' => 0,
    'error' => 0,
    'warningSslMessage' => 0,
    'shop_name' => 0,
    'wrong_folder_name' => 0,
    'wrong_install_name' => 0,
    'redirect' => 0,
    'email' => 0,
    'password' => 0,
    'randomNb' => 0,
    'adminUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563785887a3053_54818445',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563785887a3053_54818445')) {function content_563785887a3053_54818445($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
?>
<div id="login-panel">
	<div id="login-header">
		<h1 class="text-center">
			<img id="logo" src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
prestashop@2x.png" width="123px" height="24px" alt="PrestaShop" />
		</h1>
		<div class="text-center"><?php echo $_smarty_tpl->tpl_vars['ps_version']->value;?>
</div>
		<div id="error" class="hide alert alert-danger">
		<?php if (isset($_smarty_tpl->tpl_vars['errors']->value)) {?>
			<h4>
				<?php if (isset($_smarty_tpl->tpl_vars['nbErrors']->value)&&$_smarty_tpl->tpl_vars['nbErrors']->value>1) {?>
					<?php echo smartyTranslate(array('s'=>'There are %d errors.','sprintf'=>$_smarty_tpl->tpl_vars['nbErrors']->value),$_smarty_tpl);?>

				<?php } else { ?>
					<?php echo smartyTranslate(array('s'=>'There is %d error.','sprintf'=>$_smarty_tpl->tpl_vars['nbErrors']->value),$_smarty_tpl);?>

				<?php }?>
			</h4>
			<ol>
				<?php  $_smarty_tpl->tpl_vars["error"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["error"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["error"]->key => $_smarty_tpl->tpl_vars["error"]->value) {
$_smarty_tpl->tpl_vars["error"]->_loop = true;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</li>
				<?php } ?>
			</ol>
		<?php }?>
		</div>

		<?php if (isset($_smarty_tpl->tpl_vars['warningSslMessage']->value)) {?>
		<div class="alert alert-warning"><?php echo $_smarty_tpl->tpl_vars['warningSslMessage']->value;?>
</div>
		<?php }?>
	</div>
	<div id="shop-img"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
preston-login@2x.png" alt="<?php echo $_smarty_tpl->tpl_vars['shop_name']->value;?>
" width="69.5px" height="118.5px" /></div>
	<div class="flip-container">
		<div class="flipper">
			<div class="front panel">
				<h4 id="shop_name"><?php echo $_smarty_tpl->tpl_vars['shop_name']->value;?>
</h4>
				<?php if (!isset($_smarty_tpl->tpl_vars['wrong_folder_name']->value)&&!isset($_smarty_tpl->tpl_vars['wrong_install_name']->value)) {?>
				<form action="#" id="login_form" method="post">
					<input type="hidden" name="redirect" id="redirect" value="<?php echo $_smarty_tpl->tpl_vars['redirect']->value;?>
"/>
					<div class="form-group">
						<label class="control-label" for="email"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
						<input name="email" type="email" id="email" class="form-control" value="<?php if (isset($_smarty_tpl->tpl_vars['email']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['email']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" autofocus="autofocus" tabindex="1" placeholder="&#xf0e0 test@example.com" />
					</div>
					<div class="form-group">
						<label class="control-label" for="passwd">
							<?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>

						</label>
						<input name="passwd" type="password" id="passwd" class="form-control" value="<?php if (isset($_smarty_tpl->tpl_vars['password']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['password']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" tabindex="2" placeholder="&#xf084 <?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
" />
					</div>
					<div class="form-group row-padding-top">
						<button name="submitLogin" type="submit" tabindex="4" class="btn btn-primary btn-lg btn-block ladda-button" data-style="slide-up" data-spinner-color="white" >
							<span class="ladda-label">
								<?php echo smartyTranslate(array('s'=>'Log in'),$_smarty_tpl);?>

							</span>
						</button>
					</div>
					<div class="form-group">
						<div id="remind-me" class="checkbox pull-left">
							<label for="stay_logged_in">
								<input name="stay_logged_in" type="checkbox" id="stay_logged_in" value="1"	tabindex="3"/>
								<?php echo smartyTranslate(array('s'=>'Stay logged in'),$_smarty_tpl);?>

							</label>
						</div>
						<a href="#" class="show-forgot-password pull-right" >
							<?php echo smartyTranslate(array('s'=>'I forgot my password'),$_smarty_tpl);?>

						</a>
					</div>
				</form>
			</div>

			<div class="back panel">
				<form action="#" id="forgot_password_form" method="post">
					<div class="alert alert-info">
						<h4><?php echo smartyTranslate(array('s'=>'Forgot your password?'),$_smarty_tpl);?>
</h4>
						<p><?php echo smartyTranslate(array('s'=>'In order to receive your access code by email, please enter the address you provided during the registration process.'),$_smarty_tpl);?>
</p>
					</div>
					<div class="form-group">
						<label class="control-label" for="email_forgot">
							<?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>

						</label>
						<input type="text" name="email_forgot" id="email_forgot" class="form-control" autofocus="autofocus" tabindex="5" placeholder="&#xf0e0 test@example.com" />
					</div>
					<div class="panel-footer">
						<button type="button" href="#" class="btn btn-default show-login-form" tabindex="7">
							<i class="icon-caret-left"></i>
							<?php echo smartyTranslate(array('s'=>'Back to login'),$_smarty_tpl);?>

						</button>
						<button class="btn btn-default pull-right" name="submitLogin" type="submit" tabindex="6">
							<i class="icon-ok text-success"></i>
							<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>

						</button>
					</div>
				</form>
			</div>
		</div>
		<?php } else { ?>
		<div class="alert alert-danger">
			<p><?php echo smartyTranslate(array('s'=>'For security reasons, you cannot connect to the back office until you have:'),$_smarty_tpl);?>
</p>
			<ul>
				<?php if (isset($_smarty_tpl->tpl_vars['wrong_install_name']->value)&&$_smarty_tpl->tpl_vars['wrong_install_name']->value==true) {?>
					<li><?php echo smartyTranslate(array('s'=>'deleted the /install folder'),$_smarty_tpl);?>
</li>
				<?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['wrong_folder_name']->value)&&$_smarty_tpl->tpl_vars['wrong_folder_name']->value==true) {?>
					<li><?php echo smartyTranslate(array('s'=>'renamed the /admin folder (e.g. %s)','sprintf'=>$_smarty_tpl->tpl_vars['randomNb']->value),$_smarty_tpl);?>
</li>
				<?php }?>
			</ul>
			<p>
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['adminUrl']->value, ENT_QUOTES, 'UTF-8', true);?>
">
					<?php echo smartyTranslate(array('s'=>'Please then access this page by the new URL (e.g. %s)','sprintf'=>$_smarty_tpl->tpl_vars['adminUrl']->value),$_smarty_tpl);?>

				</a>
			</p>
		</div>
		<?php }?>
	</div>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminLogin"),$_smarty_tpl);?>

	<div id="login-footer">
		<p class="text-center text-muted">
			<a href="http://www.prestashop.com/" onclick="return !window.open(this.href);">
				&copy; PrestaShop&#8482; 2007-<?php echo smarty_modifier_date_format(time(),"%Y");?>
 - All rights reserved
			</a>
		</p>
		<p class="text-center">
			<a class="link-social link-twitter _blank" href="https://twitter.com/PrestaShop" title="Twitter">
				<i class="icon-twitter"></i>
			</a>
			<a class="link-social link-facebook _blank" href="https://www.facebook.com/prestashop" title="Facebook">
				<i class="icon-facebook"></i>
			</a>
			<a class="link-social link-github _blank" href="https://www.prestashop.com/github" title="Github">
				<i class="icon-github"></i>
			</a>
			<a class="link-social link-google _blank" href="https://plus.google.com/+prestashop/" title="Google">
				<i class="icon-google-plus"></i>
			</a>
		</p>
	</div>
</div>
<?php }} ?>
