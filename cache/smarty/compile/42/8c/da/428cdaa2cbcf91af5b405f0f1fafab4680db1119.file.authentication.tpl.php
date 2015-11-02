<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 11:19:10
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/authentication.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19985361556378cfe781b24-13839163%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '428cdaa2cbcf91af5b405f0f1fafab4680db1119' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/authentication.tpl',
      1 => 1446454876,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19985361556378cfe781b24-13839163',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'email_create' => 0,
    'link' => 0,
    'navigationPipe' => 0,
    'back' => 0,
    'authentification_error' => 0,
    'account_error' => 0,
    'v' => 0,
    'inOrderProcess' => 0,
    'PS_GUEST_CHECKOUT_ENABLED' => 0,
    'genders' => 0,
    'gender' => 0,
    'days' => 0,
    'day' => 0,
    'sl_day' => 0,
    'months' => 0,
    'k' => 0,
    'sl_month' => 0,
    'month' => 0,
    'years' => 0,
    'year' => 0,
    'sl_year' => 0,
    'newsletter' => 0,
    'optin' => 0,
    'dlv_all_fields' => 0,
    'field_name' => 0,
    'required_fields' => 0,
    'countries' => 0,
    'sl_country' => 0,
    'stateExist' => 0,
    'postCodeExist' => 0,
    'dniExist' => 0,
    'one_phone_at_least' => 0,
    'inv_all_fields' => 0,
    'HOOK_CREATE_ACCOUNT_FORM' => 0,
    'HOOK_CREATE_ACCOUNT_TOP' => 0,
    'field_required' => 0,
    'b2b_enable' => 0,
    'PS_REGISTRATION_PROCESS_TYPE' => 0,
    'address' => 0,
    'vatnumber_ajax_call' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56378cfec56df4_87256673',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56378cfec56df4_87256673')) {function content_56378cfec56df4_87256673($_smarty_tpl) {?>
<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?>
	<?php if (!isset($_smarty_tpl->tpl_vars['email_create']->value)) {?><?php echo smartyTranslate(array('s'=>'Authentication'),$_smarty_tpl);?>
<?php } else { ?>
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true), ENT_QUOTES, 'UTF-8', true);?>
" rel="nofollow" title="<?php echo smartyTranslate(array('s'=>'Authentication'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Authentication'),$_smarty_tpl);?>
</a>
		<span class="navigation-pipe"><?php echo $_smarty_tpl->tpl_vars['navigationPipe']->value;?>
</span><?php echo smartyTranslate(array('s'=>'Create your account'),$_smarty_tpl);?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<h1 class="page-heading htl-reservation-page-heading"><?php if (!isset($_smarty_tpl->tpl_vars['email_create']->value)) {?><?php echo smartyTranslate(array('s'=>'Authentication'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
<?php }?></h1>
<?php if (isset($_smarty_tpl->tpl_vars['back']->value)&&preg_match("/^http/",$_smarty_tpl->tpl_vars['back']->value)) {?><?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('login', null, 0);?><?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
<?php }?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(false, null, 0);?>
<?php $_smarty_tpl->tpl_vars["postCodeExist"] = new Smarty_variable(false, null, 0);?>
<?php $_smarty_tpl->tpl_vars["dniExist"] = new Smarty_variable(false, null, 0);?>
<?php if (!isset($_smarty_tpl->tpl_vars['email_create']->value)) {?>
	<!--<?php if (isset($_smarty_tpl->tpl_vars['authentification_error']->value)) {?>
	<div class="alert alert-danger">
		<?php ob_start();?><?php echo count($_smarty_tpl->tpl_vars['authentification_error']->value);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==1) {?>
			<p><?php echo smartyTranslate(array('s'=>'There\'s at least one error'),$_smarty_tpl);?>
 :</p>
			<?php } else { ?>
			<p><?php echo smartyTranslate(array('s'=>'There are %s errors','sprintf'=>array(count($_smarty_tpl->tpl_vars['account_error']->value))),$_smarty_tpl);?>
 :</p>
		<?php }?>
		<ol>
			<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['authentification_error']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</li>
			<?php } ?>
		</ol>
	</div>
	<?php }?>-->
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true), ENT_QUOTES, 'UTF-8', true);?>
" method="post" id="create-account_form" class="box">
				<h3 class="page-subheading htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
</h3>
				<div class="form_content clearfix">
					<p class="htl-reservation-page-content"><?php echo smartyTranslate(array('s'=>'Please enter your email address to create an account.'),$_smarty_tpl);?>
</p>
					<div class="alert alert-danger" id="create_account_error" style="display:none"></div>
					<div class="form-group">
						<label for="email_create" class="htl-reservation-form-label"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
						<input type="email" class="is_required validate account_input form-control htl-reservation-form-input" data-validate="isEmail" id="email_create" name="email_create" value="<?php if (isset($_POST['email_create'])) {?><?php echo stripslashes($_POST['email_create']);?>
<?php }?>" />
					</div>
					<div class="submit">
						<?php if (isset($_smarty_tpl->tpl_vars['back']->value)) {?><input type="hidden" class="hidden" name="back" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['back']->value, ENT_QUOTES, 'UTF-8', true);?>
" /><?php }?>
						<button class="btn btn-default htl-reservation-form-btn-default exclusive" type="submit" id="SubmitCreate" name="SubmitCreate">
							<span>
								<i class="icon-user left"></i>
								<?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>

							</span>
						</button>
						<input type="hidden" class="hidden" name="SubmitCreate" value="<?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
" />
					</div>
				</div>
			</form>
		</div>
		<div class="col-xs-12 col-sm-6">
			<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true), ENT_QUOTES, 'UTF-8', true);?>
" method="post" id="login_form" class="box">
				<h3 class="page-subheading htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Already registered?'),$_smarty_tpl);?>
</h3>
				<div class="form_content clearfix">
					<div class="form-group">
						<label class="htl-reservation-form-label" for="email"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
						<input class="htl-reservation-form-input is_required validate account_input form-control" data-validate="isEmail" type="email" id="email" name="email" value="<?php if (isset($_POST['email'])) {?><?php echo stripslashes($_POST['email']);?>
<?php }?>" />
					</div>
					<div class="form-group">
						<label class="htl-reservation-form-label" for="passwd"><?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
</label>
						<input class="htl-reservation-form-input is_required validate account_input form-control" type="password" data-validate="isPasswd" id="passwd" name="passwd" value="" />
					</div>
					<p class="lost_password form-group"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('password'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Recover your forgotten password'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Forgot your password?'),$_smarty_tpl);?>
</a></p>
					<p class="submit">
						<?php if (isset($_smarty_tpl->tpl_vars['back']->value)) {?><input type="hidden" class="hidden" name="back" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['back']->value, ENT_QUOTES, 'UTF-8', true);?>
" /><?php }?>
						<button type="submit" id="SubmitLogin" name="SubmitLogin" class="button btn btn-default htl-reservation-form-btn-default">
							<span>
								<i class="icon-lock left"></i>
								<?php echo smartyTranslate(array('s'=>'Sign in'),$_smarty_tpl);?>

							</span>
						</button>
					</p>
				</div>
			</form>
		</div>
	</div>
	<?php if (isset($_smarty_tpl->tpl_vars['inOrderProcess']->value)&&$_smarty_tpl->tpl_vars['inOrderProcess']->value&&$_smarty_tpl->tpl_vars['PS_GUEST_CHECKOUT_ENABLED']->value) {?>
		<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back']->value)), ENT_QUOTES, 'UTF-8', true);?>
" method="post" id="new_account_form" class="std clearfix">
			<div class="box">
				<div id="opc_account_form" style="display: block; ">
					<h3 class="page-heading bottom-indent"><?php echo smartyTranslate(array('s'=>'Instant checkout'),$_smarty_tpl);?>
</h3>
					<!-- Account -->
					<div class="required form-group">
						<label for="guest_email" class="htl-reservation-form-label"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="is_required validate form-control htl-reservation-form-input" data-validate="isEmail" id="guest_email" name="guest_email" value="<?php if (isset($_POST['guest_email'])) {?><?php echo $_POST['guest_email'];?>
<?php }?>" />
					</div>
					<div class="cleafix gender-line">
						<label class="htl-reservation-form-label"><?php echo smartyTranslate(array('s'=>'Title'),$_smarty_tpl);?>
</label>
						<?php  $_smarty_tpl->tpl_vars['gender'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gender']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['genders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gender']->key => $_smarty_tpl->tpl_vars['gender']->value) {
$_smarty_tpl->tpl_vars['gender']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['gender']->key;
?>
							<div class="radio-inline">
								<label for="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" class="top htl-reservation-form-label">
									<input class="htl-reservation-form-input" type="radio" name="id_gender" id="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
"<?php if (isset($_POST['id_gender'])&&$_POST['id_gender']==$_smarty_tpl->tpl_vars['gender']->value->id) {?> checked="checked"<?php }?> />
									<?php echo $_smarty_tpl->tpl_vars['gender']->value->name;?>

								</label>
							</div>
						<?php } ?>
					</div>
					<div class="required form-group">
						<label for="firstname" class="htl-reservation-form-label"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="htl-reservation-form-input is_required validate form-control" data-validate="isName" id="firstname" name="firstname" value="<?php if (isset($_POST['firstname'])) {?><?php echo $_POST['firstname'];?>
<?php }?>" />
					</div>
					<div class="required form-group">
						<label class="htl-reservation-form-label" for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="htl-reservation-form-input is_required validate form-control" data-validate="isName" id="lastname" name="lastname" value="<?php if (isset($_POST['lastname'])) {?><?php echo $_POST['lastname'];?>
<?php }?>" />
					</div>
					<div class="form-group date-select">
						<label class="htl-reservation-form-label"><?php echo smartyTranslate(array('s'=>'Date of Birth'),$_smarty_tpl);?>
</label>
						<div class="row">
							<div class="col-xs-4">
								<select id="days" name="days" class="form-control">
									<option value="">-</option>
									<?php  $_smarty_tpl->tpl_vars['day'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['day']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['days']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['day']->key => $_smarty_tpl->tpl_vars['day']->value) {
$_smarty_tpl->tpl_vars['day']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['day']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_day']->value==$_smarty_tpl->tpl_vars['day']->value)) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['day']->value;?>
&nbsp;&nbsp;</option>
									<?php } ?>
								</select>
								
							</div>
							<div class="col-xs-4">
								<select id="months" name="months" class="form-control">
									<option value="">-</option>
									<?php  $_smarty_tpl->tpl_vars['month'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['month']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['months']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['month']->key => $_smarty_tpl->tpl_vars['month']->value) {
$_smarty_tpl->tpl_vars['month']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['month']->key;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_month']->value==$_smarty_tpl->tpl_vars['k']->value)) {?> selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['month']->value),$_smarty_tpl);?>
&nbsp;</option>
									<?php } ?>
								</select>
							</div>
							<div class="col-xs-4">
								<select id="years" name="years" class="form-control">
									<option value="">-</option>
									<?php  $_smarty_tpl->tpl_vars['year'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['year']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['years']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['year']->key => $_smarty_tpl->tpl_vars['year']->value) {
$_smarty_tpl->tpl_vars['year']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['year']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_year']->value==$_smarty_tpl->tpl_vars['year']->value)) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['year']->value;?>
&nbsp;&nbsp;</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<?php if (isset($_smarty_tpl->tpl_vars['newsletter']->value)&&$_smarty_tpl->tpl_vars['newsletter']->value) {?>
						<div class="checkbox">
							<label class="htl-reservation-form-label" for="newsletter">
							<input class="htl-reservation-form-input" type="checkbox" name="newsletter" id="newsletter" value="1" <?php if (isset($_POST['newsletter'])&&$_POST['newsletter']=='1') {?>checked="checked"<?php }?> />
							<?php echo smartyTranslate(array('s'=>'Sign up for our newsletter!'),$_smarty_tpl);?>
</label>
						</div>
					<?php }?>
					<?php if (isset($_smarty_tpl->tpl_vars['optin']->value)&&$_smarty_tpl->tpl_vars['optin']->value) {?>
						<div class="checkbox">
							<label for="optin">
							<input type="checkbox" name="optin" id="optin" value="1" <?php if (isset($_POST['optin'])&&$_POST['optin']=='1') {?>checked="checked"<?php }?> />
							<?php echo smartyTranslate(array('s'=>'Receive special offers from our partners!'),$_smarty_tpl);?>
</label>
						</div>
					<?php }?>
					<h3 class="page-heading bottom-indent top-indent"><?php echo smartyTranslate(array('s'=>'Delivery address'),$_smarty_tpl);?>
</h3>
					<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value) {
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company") {?>
							<div class="form-group">
								<label class="htl-reservation-form-label" for="company"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
								<input type="text" class="form-control htl-reservation-form-input" id="company" name="company" value="<?php if (isset($_POST['company'])) {?><?php echo $_POST['company'];?>
<?php }?>" />
							</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="vat_number") {?>
							<div id="vat_number" style="display:none;">
								<div class="form-group">
									<label class="htl-reservation-form-label" for="vat-number"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
									<input id="vat-number" type="text" class="htl-reservation-form-input form-control" name="vat_number" value="<?php if (isset($_POST['vat_number'])) {?><?php echo $_POST['vat_number'];?>
<?php }?>" />
								</div>
							</div>
							<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="dni") {?>
							<?php $_smarty_tpl->tpl_vars['dniExist'] = new Smarty_variable(true, null, 0);?>
							<div class="required dni form-group">
								<label class="htl-reservation-form-label" for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<input type="text" name="dni" id="dni" class="htl-reservation-form-input"
								 value="<?php if (isset($_POST['dni'])) {?><?php echo $_POST['dni'];?>
<?php }?>" />
								<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
							</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="address1") {?>
							<div class="required form-group">
								<label class="htl-reservation-form-label" for="address1"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<input type="text" class="htl-reservation-form-input form-control" name="address1" id="address1" value="<?php if (isset($_POST['address1'])) {?><?php echo $_POST['address1'];?>
<?php }?>" />
							</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="address2") {?>
							<div class="form-group is_customer_param">
								<label class="htl-reservation-form-label" for="address2"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
								<input type="text" class="htl-reservation-form-input form-control" name="address2" id="address2" value="<?php if (isset($_POST['address2'])) {?><?php echo $_POST['address2'];?>
<?php }?>" />
							</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="postcode") {?>
							<?php $_smarty_tpl->tpl_vars['postCodeExist'] = new Smarty_variable(true, null, 0);?>
							<div class="required postcode form-group">
								<label class="htl-reservation-form-label" for="postcode"><?php echo smartyTranslate(array('s'=>'Zip/Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<input type="text" class="htl-reservation-form-input validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="<?php if (isset($_POST['postcode'])) {?><?php echo $_POST['postcode'];?>
<?php }?>"/>
							</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="city") {?>
							<div class="required form-group">
								<label class="htl-reservation-form-label" for="city"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<input type="text" class="htl-reservation-form-input form-control" name="city" id="city" value="<?php if (isset($_POST['city'])) {?><?php echo $_POST['city'];?>
<?php }?>" />
							</div>
							<!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="Country:name"||$_smarty_tpl->tpl_vars['field_name']->value=="country") {?>
							<div class="required select form-group">
								<label class="htl-reservation-form-label" for="id_country"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<select name="id_country" id="id_country" class="form-control">
									<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id_country'];?>
"<?php if ((isset($_POST['id_country'])&&$_POST['id_country']==$_smarty_tpl->tpl_vars['v']->value['id_country'])||(!isset($_POST['id_country'])&&$_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
									<?php } ?>
								</select>
							</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="State:name") {?>
							<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>
							<div class="required id_state select form-group">
								<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<select name="id_state" id="id_state" class="form-control">
									<option value="">-</option>
								</select>
							</div>
						<?php }?>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['stateExist']->value==false) {?>
						<div class="required id_state select unvisible form-group">
							<label class="htl-reservation-form-label" for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_state" id="id_state" class="form-control">
								<option value="">-</option>
							</select>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['postCodeExist']->value==false) {?>
						<div class="required postcode unvisible form-group">
							<label class="htl-reservation-form-label" for="postcode"><?php echo smartyTranslate(array('s'=>'Zip/Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="<?php if (isset($_POST['postcode'])) {?><?php echo $_POST['postcode'];?>
<?php }?>"/>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['dniExist']->value==false) {?>
						<div class="required form-group dni">
							<label for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="text form-control" name="dni" id="dni" value="<?php if (isset($_POST['dni'])&&$_POST['dni']) {?><?php echo $_POST['dni'];?>
<?php }?>" />
							<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
						</div>
					<?php }?>
					<div class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?>required <?php }?>form-group">
						<label class="htl-reservation-form-label" for="phone_mobile"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?> <sup>*</sup><?php }?></label>
						<input type="text" class="htl-reservation-form-input form-control" name="phone_mobile" id="phone_mobile" value="<?php if (isset($_POST['phone_mobile'])) {?><?php echo $_POST['phone_mobile'];?>
<?php }?>" />
					</div>
					<input type="hidden" name="alias" id="alias" value="<?php echo smartyTranslate(array('s'=>'My address'),$_smarty_tpl);?>
" />
					<input type="hidden" name="is_new_customer" id="is_new_customer" value="0" />
					<div class="checkbox">
						<label class="htl-reservation-form-label" for="invoice_address">
						<input class="htl-reservation-form-input" type="checkbox" name="invoice_address" id="invoice_address"<?php if ((isset($_POST['invoice_address'])&&$_POST['invoice_address'])||(isset($_POST['invoice_address'])&&$_POST['invoice_address'])) {?> checked="checked"<?php }?> autocomplete="off"/>
						<?php echo smartyTranslate(array('s'=>'Please use another address for invoice'),$_smarty_tpl);?>
</label>
					</div>
					<div id="opc_invoice_address"  class="unvisible">
						<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(false, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['postCodeExist'] = new Smarty_variable(false, null, 0);?>
						<?php $_smarty_tpl->tpl_vars['dniExist'] = new Smarty_variable(false, null, 0);?>
						<h3 class="page-subheading top-indent htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>
</h3>
						<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['inv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value) {
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company") {?>
						<div class="form-group">
							<label class="htl-reservation-form-label" for="company_invoice"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
							<input type="text" class="htl-reservation-form-input text form-control" id="company_invoice" name="company_invoice" value="<?php if (isset($_POST['company_invoice'])&&$_POST['company_invoice']) {?><?php echo $_POST['company_invoice'];?>
<?php }?>" />
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="vat_number") {?>
						<div id="vat_number_block_invoice" style="display:none;">
							<div class="form-group">
								<label class="htl-reservation-form-label" for="vat_number_invoice"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
								<input type="text" class="htl-reservation-form-input form-control" id="vat_number_invoice" name="vat_number_invoice" value="<?php if (isset($_POST['vat_number_invoice'])&&$_POST['vat_number_invoice']) {?><?php echo $_POST['vat_number_invoice'];?>
<?php }?>" />
							</div>
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="dni") {?>
						<?php $_smarty_tpl->tpl_vars['dniExist'] = new Smarty_variable(true, null, 0);?>
						<div class="required form-group dni_invoice">
							<label class="htl-reservation-form-label" for="dni_invoice"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input text form-control" name="dni_invoice" id="dni_invoice" value="<?php if (isset($_POST['dni_invoice'])&&$_POST['dni_invoice']) {?><?php echo $_POST['dni_invoice'];?>
<?php }?>" />
							<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="firstname") {?>
						<div class="required form-group">
							<label class="htl-reservation-form-label" for="firstname_invoice"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" id="firstname_invoice" name="firstname_invoice" value="<?php if (isset($_POST['firstname_invoice'])&&$_POST['firstname_invoice']) {?><?php echo $_POST['firstname_invoice'];?>
<?php }?>" />
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="lastname") {?>
						<div class="required form-group">
							<label class="htl-reservation-form-label" for="lastname_invoice"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" id="lastname_invoice" name="lastname_invoice" value="<?php if (isset($_POST['lastname_invoice'])&&$_POST['lastname_invoice']) {?><?php echo $_POST['lastname_invoice'];?>
<?php }?>" />
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="address1") {?>
						<div class="required form-group">
							<label class="htl-reservation-form-label" for="address1_invoice"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" name="address1_invoice" id="address1_invoice" value="<?php if (isset($_POST['address1_invoice'])&&$_POST['address1_invoice']) {?><?php echo $_POST['address1_invoice'];?>
<?php }?>" />
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="address2") {?>
						<div class="form-group is_customer_param">
							<label class="htl-reservation-form-label" for="address2_invoice"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
							<input type="text" class="htl-reservation-form-input form-control" name="address2_invoice" id="address2_invoice" value="<?php if (isset($_POST['address2_invoice'])&&$_POST['address2_invoice']) {?><?php echo $_POST['address2_invoice'];?>
<?php }?>" />
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="postcode") {?>
						<?php $_smarty_tpl->tpl_vars['postCodeExist'] = new Smarty_variable(true, null, 0);?>
						<div class="required postcode_invoice form-group">
							<label class="htl-reservation-form-label" for="postcode_invoice"><?php echo smartyTranslate(array('s'=>'Zip/Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input validate form-control" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="<?php if (isset($_POST['postcode_invoice'])&&$_POST['postcode_invoice']) {?><?php echo $_POST['postcode_invoice'];?>
<?php }?>"/>
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="city") {?>
						<div class="required form-group">
							<label class="htl-reservation-form-label" for="city_invoice"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="form-control htl-reservation-form-input" name="city_invoice" id="city_invoice" value="<?php if (isset($_POST['city_invoice'])&&$_POST['city_invoice']) {?><?php echo $_POST['city_invoice'];?>
<?php }?>" />
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="country"||$_smarty_tpl->tpl_vars['field_name']->value=="Country:name") {?>
						<div class="required form-group">
							<label class="htl-reservation-form-label" for="id_country_invoice"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_country_invoice" id="id_country_invoice" class="htl-reservation-form-input form-control">
								<option value="">-</option>
								<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id_country'];?>
"<?php if ((isset($_POST['id_country_invoice'])&&$_POST['id_country_invoice']==$_smarty_tpl->tpl_vars['v']->value['id_country'])||(!isset($_POST['id_country_invoice'])&&$_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])) {?> selected="selected"<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
								<?php } ?>
							</select>
						</div>
						<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="state"||$_smarty_tpl->tpl_vars['field_name']->value=='State:name') {?>
						<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>
						<div class="required id_state_invoice form-group" style="display:none;">
							<label class="htl-reservation-form-label" for="id_state_invoice"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_state_invoice" id="id_state_invoice" class="form-control">
								<option value="">-</option>
							</select>
						</div>
						<?php }?>
						<?php } ?>
						<?php if (!$_smarty_tpl->tpl_vars['postCodeExist']->value) {?>
						<div class="required postcode_invoice form-group unvisible">
							<label class="htl-reservation-form-label" for="postcode_invoice"><?php echo smartyTranslate(array('s'=>'Zip/Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" name="postcode_invoice" id="postcode_invoice" value="<?php if (isset($_POST['postcode_invoice'])&&$_POST['postcode_invoice']) {?><?php echo $_POST['postcode_invoice'];?>
<?php }?>"/>
						</div>
						<?php }?>
						<?php if (!$_smarty_tpl->tpl_vars['stateExist']->value) {?>
						<div class="required id_state_invoice form-group unvisible">
							<label class="htl-reservation-form-label" for="id_state_invoice"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_state_invoice" id="id_state_invoice" class="htl-reservation-form-input form-control">
								<option value="">-</option>
							</select>
						</div>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['dniExist']->value==false) {?>
							<div class="required form-group dni_invoice">
								<label class="htl-reservation-form-label" for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
 <sup>*</sup></label>
								<input type="text" class="htl-reservation-form-input text form-control" name="dni_invoice" id="dni_invoice" value="<?php if (isset($_POST['dni_invoice'])&&$_POST['dni_invoice']) {?><?php echo $_POST['dni_invoice'];?>
<?php }?>" />
								<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
							</div>
						<?php }?>
						<div class="form-group is_customer_param">
							<label class="htl-reservation-form-label other_invoice"><?php echo smartyTranslate(array('s'=>'Additional information'),$_smarty_tpl);?>
</label>
							<textarea class="htl-reservation-form-input form-control" name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
						</div>
						<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?>
							<p class="inline-infos required is_customer_param"><?php echo smartyTranslate(array('s'=>'You must register at least one phone number.'),$_smarty_tpl);?>
</p>
						<?php }?>
						<div class="form-group is_customer_param">
							<label class="htl-reservation-form-label" for="phone_invoice"><?php echo smartyTranslate(array('s'=>'Home phone'),$_smarty_tpl);?>
</label>
							<input type="text" class="htl-reservation-form-input form-control" name="phone_invoice" id="phone_invoice" value="<?php if (isset($_POST['phone_invoice'])&&$_POST['phone_invoice']) {?><?php echo $_POST['phone_invoice'];?>
<?php }?>" />
						</div>
						<div class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?>required <?php }?>form-group">
							<label class="htl-reservation-form-label" for="phone_mobile_invoice"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?> <sup>*</sup><?php }?></label>
							<input type="text" class="htl-reservation-form-input form-control" name="phone_mobile_invoice" id="phone_mobile_invoice" value="<?php if (isset($_POST['phone_mobile_invoice'])&&$_POST['phone_mobile_invoice']) {?><?php echo $_POST['phone_mobile_invoice'];?>
<?php }?>" />
						</div>
						<input type="hidden" name="alias_invoice" id="alias_invoice" value="<?php echo smartyTranslate(array('s'=>'My Invoice address'),$_smarty_tpl);?>
" />
					</div>
					<!-- END Account -->
				</div>
				<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_FORM']->value;?>

			</div>
			<p class="cart_navigation required submit clearfix">
				<span><sup>*</sup><?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</span>
				<input type="hidden" name="display_guest_checkout" value="1" />
				<button type="submit" class="button btn btn-default button-medium" name="submitGuestAccount" id="submitGuestAccount">
					<span>
						<?php echo smartyTranslate(array('s'=>'Proceed to checkout'),$_smarty_tpl);?>

						<i class="icon-chevron-right right"></i>
					</span>
				</button>
			</p>
		</form>
	<?php }?>
<?php } else { ?>
	<!--<?php if (isset($_smarty_tpl->tpl_vars['account_error']->value)) {?>
	<div class="error">
		<?php ob_start();?><?php echo count($_smarty_tpl->tpl_vars['account_error']->value);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2==1) {?>
			<p><?php echo smartyTranslate(array('s'=>'There\'s at least one error'),$_smarty_tpl);?>
 :</p>
			<?php } else { ?>
			<p><?php echo smartyTranslate(array('s'=>'There are %s errors','sprintf'=>array(count($_smarty_tpl->tpl_vars['account_error']->value))),$_smarty_tpl);?>
 :</p>
		<?php }?>
		<ol>
			<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['account_error']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</li>
			<?php } ?>
		</ol>
	</div>
	<?php }?>-->
	<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true), ENT_QUOTES, 'UTF-8', true);?>
" method="post" id="account-creation_form" class="std box">
		<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_TOP']->value;?>

		<div class="account_creation">
			<h3 class="page-subheading htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Your personal information'),$_smarty_tpl);?>
</h3>
			<div class="clearfix">
				<label><?php echo smartyTranslate(array('s'=>'Title'),$_smarty_tpl);?>
</label>
				<br />
				<?php  $_smarty_tpl->tpl_vars['gender'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gender']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['genders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gender']->key => $_smarty_tpl->tpl_vars['gender']->value) {
$_smarty_tpl->tpl_vars['gender']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['gender']->key;
?>
					<div class="radio-inline">
						<label class="htl-reservation-form-label" for="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" class="top">
							<input checked="htl-reservation-form-input" type="radio" name="id_gender" id="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" <?php if (isset($_POST['id_gender'])&&$_POST['id_gender']==$_smarty_tpl->tpl_vars['gender']->value->id) {?>checked="checked"<?php }?> />
						<?php echo $_smarty_tpl->tpl_vars['gender']->value->name;?>

						</label>
					</div>
				<?php } ?>
			</div>
			<div class="required form-group">
				<label class="htl-reservation-form-label" for="customer_firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input onkeyup="$('#firstname').val(this.value);" type="text" class="htl-reservation-form-input is_required validate form-control" data-validate="isName" id="customer_firstname" name="customer_firstname" value="<?php if (isset($_POST['customer_firstname'])) {?><?php echo $_POST['customer_firstname'];?>
<?php }?>" />
			</div>
			<div class="required form-group">
				<label class="htl-reservation-form-label" for="customer_lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input onkeyup="$('#lastname').val(this.value);" type="text" class="htl-reservation-form-input is_required validate form-control" data-validate="isName" id="customer_lastname" name="customer_lastname" value="<?php if (isset($_POST['customer_lastname'])) {?><?php echo $_POST['customer_lastname'];?>
<?php }?>" />
			</div>
			<div class="required form-group">
				<label class="htl-reservation-form-label" for="email"><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="email" class="htl-reservation-form-input is_required validate form-control" data-validate="isEmail" id="email" name="email" value="<?php if (isset($_POST['email'])) {?><?php echo $_POST['email'];?>
<?php }?>" />
			</div>
			<div class="required password form-group">
				<label class="htl-reservation-form-label" for="passwd"><?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="password" class="htl-reservation-form-input is_required validate form-control" data-validate="isPasswd" name="passwd" id="passwd" />
				<span class="form_info"><?php echo smartyTranslate(array('s'=>'(Five characters minimum)'),$_smarty_tpl);?>
</span>
			</div>
			<div class="form-group">
				<label class="htl-reservation-form-label"><?php echo smartyTranslate(array('s'=>'Date of Birth'),$_smarty_tpl);?>
</label>
				<div class="row">
					<div class="col-xs-4">
						<select id="days" name="days" class="form-control">
							<option value="">-</option>
							<?php  $_smarty_tpl->tpl_vars['day'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['day']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['days']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['day']->key => $_smarty_tpl->tpl_vars['day']->value) {
$_smarty_tpl->tpl_vars['day']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['day']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_day']->value==$_smarty_tpl->tpl_vars['day']->value)) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['day']->value;?>
&nbsp;&nbsp;</option>
							<?php } ?>
						</select>
						
					</div>
					<div class="col-xs-4">
						<select id="months" name="months" class="form-control">
							<option value="">-</option>
							<?php  $_smarty_tpl->tpl_vars['month'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['month']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['months']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['month']->key => $_smarty_tpl->tpl_vars['month']->value) {
$_smarty_tpl->tpl_vars['month']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['month']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_month']->value==$_smarty_tpl->tpl_vars['k']->value)) {?> selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['month']->value),$_smarty_tpl);?>
&nbsp;</option>
							<?php } ?>
						</select>
					</div>
					<div class="col-xs-4">
						<select id="years" name="years" class="form-control">
							<option value="">-</option>
							<?php  $_smarty_tpl->tpl_vars['year'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['year']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['years']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['year']->key => $_smarty_tpl->tpl_vars['year']->value) {
$_smarty_tpl->tpl_vars['year']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['year']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_year']->value==$_smarty_tpl->tpl_vars['year']->value)) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['year']->value;?>
&nbsp;&nbsp;</option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<?php if (isset($_smarty_tpl->tpl_vars['newsletter']->value)&&$_smarty_tpl->tpl_vars['newsletter']->value) {?>
				<div class="checkbox">
					<input type="checkbox" name="newsletter" id="newsletter" value="1" <?php if (isset($_POST['newsletter'])&&$_POST['newsletter']==1) {?> checked="checked"<?php }?> />
					<label for="newsletter"><?php echo smartyTranslate(array('s'=>'Sign up for our newsletter!'),$_smarty_tpl);?>
</label>
					<?php if (array_key_exists('newsletter',$_smarty_tpl->tpl_vars['field_required']->value)) {?>
						<sup> *</sup>
					<?php }?>
				</div>
			<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['optin']->value)&&$_smarty_tpl->tpl_vars['optin']->value) {?>
				<div class="checkbox">
					<input type="checkbox" name="optin" id="optin" value="1" <?php if (isset($_POST['optin'])&&$_POST['optin']==1) {?> checked="checked"<?php }?> />
					<label for="optin"><?php echo smartyTranslate(array('s'=>'Receive special offers from our partners!'),$_smarty_tpl);?>
</label>
					<?php if (array_key_exists('optin',$_smarty_tpl->tpl_vars['field_required']->value)) {?>
						<sup> *</sup>
					<?php }?>
				</div>
			<?php }?>
		</div>
		<?php if ($_smarty_tpl->tpl_vars['b2b_enable']->value) {?>
			<div class="account_creation">
				<h3 class="page-subheading htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Your company information'),$_smarty_tpl);?>
</h3>
				<p class="form-group">
					<label class="htl-reservation-form-label" for=""><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
					<input type="text" class=" htl-reservation-form-input form-control" id="company" name="company" value="<?php if (isset($_POST['company'])) {?><?php echo $_POST['company'];?>
<?php }?>" />
				</p>
				<p class="form-group">
					<label class="htl-reservation-form-label" for="siret"><?php echo smartyTranslate(array('s'=>'SIRET'),$_smarty_tpl);?>
</label>
					<input type="text" class="htl-reservation-form-input form-control" id="siret" name="siret" value="<?php if (isset($_POST['siret'])) {?><?php echo $_POST['siret'];?>
<?php }?>" />
				</p>
				<p class="form-group">
					<label class="htl-reservation-form-label" for="ape"><?php echo smartyTranslate(array('s'=>'APE'),$_smarty_tpl);?>
</label>
					<input type="text" class="htl-reservation-form-input form-control" id="ape" name="ape" value="<?php if (isset($_POST['ape'])) {?><?php echo $_POST['ape'];?>
<?php }?>" />
				</p>
				<p class="form-group">
					<label class="htl-reservation-form-label" for="website"><?php echo smartyTranslate(array('s'=>'Website'),$_smarty_tpl);?>
</label>
					<input type="text" class="form-control htl-reservation-form-input" id="website" name="website" value="<?php if (isset($_POST['website'])) {?><?php echo $_POST['website'];?>
<?php }?>" />
				</p>
			</div>
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['PS_REGISTRATION_PROCESS_TYPE']->value)&&$_smarty_tpl->tpl_vars['PS_REGISTRATION_PROCESS_TYPE']->value) {?>
			<div class="account_creation">
				<h3 class="page-subheading htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Your address'),$_smarty_tpl);?>
</h3>
				<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value) {
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company") {?>
						<?php if (!$_smarty_tpl->tpl_vars['b2b_enable']->value) {?>
							<p class="form-group">
								<label class="htl-reservation-form-label" for="company"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
								<input type="text" class="htl-reservation-form-input form-control" id="company" name="company" value="<?php if (isset($_POST['company'])) {?><?php echo $_POST['company'];?>
<?php }?>" />
							</p>
						<?php }?>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="vat_number") {?>
						<div id="vat_number" style="display:none;">
							<p class="form-group">
								<label class="htl-reservation-form-label" for="vat_number"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
								<input type="text" class="htl-reservation-form-input form-control" id="vat_number" name="vat_number" value="<?php if (isset($_POST['vat_number'])) {?><?php echo $_POST['vat_number'];?>
<?php }?>" />
							</p>
						</div>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="firstname") {?>
						<p class="required form-group">
							<label class="htl-reservation-form-label" for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" id="firstname" name="firstname" value="<?php if (isset($_POST['firstname'])) {?><?php echo $_POST['firstname'];?>
<?php }?>" />
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="lastname") {?>
						<p class="required form-group">
							<label class="htl-reservation-form-label" for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" id="lastname" name="lastname" value="<?php if (isset($_POST['lastname'])) {?><?php echo $_POST['lastname'];?>
<?php }?>" />
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="address1") {?>
						<p class="required form-group">
							<label class="htl-reservation-form-label" for="address1"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" name="address1" id="address1" value="<?php if (isset($_POST['address1'])) {?><?php echo $_POST['address1'];?>
<?php }?>" />
							<span class="inline-infos"><?php echo smartyTranslate(array('s'=>'Street address, P.O. Box, Company name, etc.'),$_smarty_tpl);?>
</span>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="address2") {?>
						<p class="form-group is_customer_param">
							<label class="htl-reservation-form-label" for="address2"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
<?php if (in_array($_smarty_tpl->tpl_vars['field_name']->value,$_smarty_tpl->tpl_vars['required_fields']->value)) {?> <sup>*</sup><?php }?></label>
							<input type="text" class="htl-reservation-form-input form-control" name="address2" id="address2" value="<?php if (isset($_POST['address2'])) {?><?php echo $_POST['address2'];?>
<?php }?>" />
							<span class="inline-infos"><?php echo smartyTranslate(array('s'=>'Apartment, suite, unit, building, floor, etc...'),$_smarty_tpl);?>
</span>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="postcode") {?>
						<?php $_smarty_tpl->tpl_vars['postCodeExist'] = new Smarty_variable(true, null, 0);?>
						<p class="required postcode form-group">
							<label class="htl-reservation-form-label" for="postcode"><?php echo smartyTranslate(array('s'=>'Zip/Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="<?php if (isset($_POST['postcode'])) {?><?php echo $_POST['postcode'];?>
<?php }?>"/>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="city") {?>
						<p class="required form-group">
							<label class="htl-reservation-form-label" for="city"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="htl-reservation-form-input form-control" name="city" id="city" value="<?php if (isset($_POST['city'])) {?><?php echo $_POST['city'];?>
<?php }?>" />
						</p>
						<!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="Country:name"||$_smarty_tpl->tpl_vars['field_name']->value=="country") {?>
						<p class="required select form-group">
							<label class="htl-reservation-form-label" for="id_country"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_country" id="id_country" class="form-control">
								<option value="">-</option>
								<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id_country'];?>
"<?php if ((isset($_POST['id_country'])&&$_POST['id_country']==$_smarty_tpl->tpl_vars['v']->value['id_country'])||(!isset($_POST['id_country'])&&$_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
								<?php } ?>
							</select>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_name']->value=="State:name"||$_smarty_tpl->tpl_vars['field_name']->value=='state') {?>
						<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>
						<p class="required id_state select form-group">
							<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_state" id="id_state" class="form-control">
								<option value="">-</option>
							</select>
						</p>
					<?php }?>
				<?php } ?>
				<?php if ($_smarty_tpl->tpl_vars['postCodeExist']->value==false) {?>
					<p class="required postcode form-group unvisible">
						<label class="htl-reservation-form-label" for="postcode"><?php echo smartyTranslate(array('s'=>'Zip/Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="htl-reservation-form-input validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="<?php if (isset($_POST['postcode'])) {?><?php echo $_POST['postcode'];?>
<?php }?>"/>
					</p>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['stateExist']->value==false) {?>
					<p class="required id_state select unvisible form-group">
						<label class="htl-reservation-form-label" for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<select name="id_state" id="id_state" class="form-control">
							<option value="">-</option>
						</select>
					</p>
				<?php }?>
				<p class="textarea form-group">
					<label class="htl-reservation-form-label" for="other"><?php echo smartyTranslate(array('s'=>'Additional information'),$_smarty_tpl);?>
</label>
					<textarea class="form-control" name="other" id="other" cols="26" rows="3"><?php if (isset($_POST['other'])) {?><?php echo $_POST['other'];?>
<?php }?></textarea>
				</p>
				<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?>
					<p class="inline-infos"><?php echo smartyTranslate(array('s'=>'You must register at least one phone number.'),$_smarty_tpl);?>
</p>
				<?php }?>
				<p class="form-group">
					<label class="htl-reservation-form-label" for="phone"><?php echo smartyTranslate(array('s'=>'Home phone'),$_smarty_tpl);?>
</label>
					<input type="text" class="htl-reservation-form-input form-control" name="phone" id="phone" value="<?php if (isset($_POST['phone'])) {?><?php echo $_POST['phone'];?>
<?php }?>" />
				</p>
				<p class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?>required <?php }?>form-group">
					<label class="htl-reservation-form-label" for="phone_mobile"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value) {?> <sup>*</sup><?php }?></label>
					<input type="text" class="htl-reservation-form-input form-control" name="phone_mobile" id="phone_mobile" value="<?php if (isset($_POST['phone_mobile'])) {?><?php echo $_POST['phone_mobile'];?>
<?php }?>" />
				</p>
				<p class="required form-group" id="address_alias">
					<label class="htl-reservation-form-label" for="alias"><?php echo smartyTranslate(array('s'=>'Assign an address alias for future reference.'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="htl-reservation-form-input form-control" name="alias" id="alias" value="<?php if (isset($_POST['alias'])) {?><?php echo $_POST['alias'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'My address'),$_smarty_tpl);?>
<?php }?>" />
				</p>
			</div>
			<div class="account_creation dni">
				<h3 class="page-subheading htl-reservation-page-sub-heading"><?php echo smartyTranslate(array('s'=>'Tax identification'),$_smarty_tpl);?>
</h3>
				<p class="required form-group">
					<label class="htl-reservation-form-label" for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="htl-reservation-form-input form-control" name="dni" id="dni" value="<?php if (isset($_POST['dni'])) {?><?php echo $_POST['dni'];?>
<?php }?>" />
					<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
				</p>
			</div>
		<?php }?>
		<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_FORM']->value;?>

		<div class="submit clearfix">
			<input type="hidden" name="email_create" value="1" />
			<input type="hidden" name="is_new_customer" value="1" />
			<?php if (isset($_smarty_tpl->tpl_vars['back']->value)) {?><input type="hidden" class="hidden" name="back" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['back']->value, ENT_QUOTES, 'UTF-8', true);?>
" /><?php }?>
			<button type="submit" name="submitAccount" id="submitAccount" class="btn btn-default htl-reservation-form-btn-default">
				<span><?php echo smartyTranslate(array('s'=>'Register'),$_smarty_tpl);?>
&nbsp;<i class="icon-chevron-right right"></i></span>
			</button>
			<p class="pull-right required"><span><sup>*</sup><?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</span></p>
		</div>
	</form>
<?php }?>
<?php if (isset($_POST['id_state'])&&$_POST['id_state']) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedState'=>intval($_POST['id_state'])),$_smarty_tpl);?>
<?php } elseif (isset($_smarty_tpl->tpl_vars['address']->value->id_state)&&$_smarty_tpl->tpl_vars['address']->value->id_state) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedState'=>intval($_smarty_tpl->tpl_vars['address']->value->id_state)),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedState'=>false),$_smarty_tpl);?>
<?php }?><?php if (isset($_POST['id_state_invoice'])&&isset($_POST['id_state_invoice'])&&$_POST['id_state_invoice']) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedStateInvoice'=>intval($_POST['id_state_invoice'])),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedStateInvoice'=>false),$_smarty_tpl);?>
<?php }?><?php if (isset($_POST['id_country'])&&$_POST['id_country']) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedCountry'=>intval($_POST['id_country'])),$_smarty_tpl);?>
<?php } elseif (isset($_smarty_tpl->tpl_vars['address']->value->id_country)&&$_smarty_tpl->tpl_vars['address']->value->id_country) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedCountry'=>intval($_smarty_tpl->tpl_vars['address']->value->id_country)),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedCountry'=>false),$_smarty_tpl);?>
<?php }?><?php if (isset($_POST['id_country_invoice'])&&isset($_POST['id_country_invoice'])&&$_POST['id_country_invoice']) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedCountryInvoice'=>intval($_POST['id_country_invoice'])),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('idSelectedCountryInvoice'=>false),$_smarty_tpl);?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['countries']->value)) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('countries'=>$_smarty_tpl->tpl_vars['countries']->value),$_smarty_tpl);?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['vatnumber_ajax_call']->value)&&$_smarty_tpl->tpl_vars['vatnumber_ajax_call']->value) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('vatnumber_ajax_call'=>$_smarty_tpl->tpl_vars['vatnumber_ajax_call']->value),$_smarty_tpl);?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['email_create']->value)&&$_smarty_tpl->tpl_vars['email_create']->value) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('email_create'=>$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['boolval'][0][0]->boolval($_smarty_tpl->tpl_vars['email_create']->value)),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('email_create'=>false),$_smarty_tpl);?>
<?php }?>
<?php }} ?>
