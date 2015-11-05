<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 19:11:01
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/onboarding/views/templates/hook/backoffice_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:142341177956376ee13e8bb6-60472501%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1ea41271cdcd1e8449abd5158b9ca9c782875fc2' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/onboarding/views/templates/hook/backoffice_top.tpl',
      1 => 1446730827,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '142341177956376ee13e8bb6-60472501',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee159d3b1_78660056',
  'variables' => 
  array (
    'display_onboarding_modal' => 0,
    'current_step' => 0,
    'link' => 0,
    'employee' => 0,
    'has_psp' => 0,
    'continue_editing_links' => 0,
    'next_step_link' => 0,
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee159d3b1_78660056')) {function content_56376ee159d3b1_78660056($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/modifier.escape.php';
?>

<script>
var display_onboarding_modal= <?php echo intval($_smarty_tpl->tpl_vars['display_onboarding_modal']->value);?>
;
var current_step_onboarding = <?php echo intval(htmlspecialchars($_smarty_tpl->tpl_vars['current_step']->value, ENT_QUOTES, 'UTF-8', true));?>
;
var onboarding_ajax_url = "<?php echo strtr($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOnboarding'), array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
";
</script>
<?php $_smarty_tpl->_capture_stack[0][] = array("onboardingStepParagraph", null, null); ob_start(); ?>
	<?php if ($_smarty_tpl->tpl_vars['current_step']->value==0) {?>
		<?php echo smartyTranslate(array('s'=>'Hey %s, welcome on your own online shop.[1]Follow the guide and take the first steps with your online shop!','sprintf'=>array($_smarty_tpl->tpl_vars['employee']->value->firstname),'tags'=>array('<br />'),'mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
		<?php echo smartyTranslate(array('s'=>'Check out our catalog to get a new theme or customize the current default theme.[1]Add your logo, play on fonts and colors... Give this special look to your shop!','tags'=>array('<br />'),'mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
		<?php echo smartyTranslate(array('s'=>'Start your product catalog with a first product.[1]Make sure you cover the basics by setting its price, having a nice description and uploading a catchy image![1]If you already have your product base in a .CSV file, save time and make an import!','tags'=>array('<br />'),'mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
        <?php if ($_smarty_tpl->tpl_vars['has_psp']->value) {?>
            <?php echo smartyTranslate(array('s'=>'Your shop runs with PrestaShop Payments by HiPay, so that you can accept payments by card right now. Other payment methods are available too, make sure you set everything up!','mod'=>'onboarding'),$_smarty_tpl);?>

        <?php } else { ?>
		    <?php echo smartyTranslate(array('s'=>'Select which payment methods you want to offer to customers on your shop, and manage the various restrictions you can apply (per currency, country or group of customers).','mod'=>'onboarding'),$_smarty_tpl);?>

        <?php }?>
	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
		<?php echo smartyTranslate(array('s'=>'If you feel you need more information, you can still have a look at PrestaShop Documentation: click on "Help" in the top right corner of your back office!','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
		<?php echo smartyTranslate(array('s'=>'You have completed all the essential first steps to configure your online shop. You can repeat those steps if you have more products, payment methods or shipping partners to add.[1]To dive deeper in the configuration of your shop, you should read the [2]"First steps with PrestaShop 1.6"[/2] chapter of the PrestaShop User Guide.[1]Once you are certain that your shop is ready to sell your products, click on the Launch button to make your shop public.','tags'=>array('<br />','<a href="http://doc.prestashop.com/display/PS16/First+steps+with+PrestaShop+1.6" class="_blank">'),'mod'=>'onboarding'),$_smarty_tpl);?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->_capture_stack[0][] = array("onboardingStepButton", null, null); ob_start(); ?>
	<?php if ($_smarty_tpl->tpl_vars['current_step']->value==0) {?>
		<?php echo smartyTranslate(array('s'=>'Let\'s start!','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
		<?php echo smartyTranslate(array('s'=>'I\'m all good, let\'s launch!','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } else { ?>
		<?php echo smartyTranslate(array('s'=>'I\'m done, take me to next step','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->_capture_stack[0][] = array("onboardingStepBannerTitle", null, null); ob_start(); ?>
	<?php if ($_smarty_tpl->tpl_vars['current_step']->value==0) {?>
		<?php echo smartyTranslate(array('s'=>'Take a tour: get started with PrestaShop','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
		<?php echo smartyTranslate(array('s'=>'Customize your shop\'s look and feel','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
		<?php echo smartyTranslate(array('s'=>'Add your first products','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
		<?php echo smartyTranslate(array('s'=>'Get your shop ready for payments','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
		<?php echo smartyTranslate(array('s'=>'You are now ready to launch your shop.','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
		<?php echo smartyTranslate(array('s'=>'You are now ready to launch your shop.','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php $_smarty_tpl->_capture_stack[0][] = array("onboardingStepModalTitle", null, null); ob_start(); ?>
	<?php if ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
		<?php echo smartyTranslate(array('s'=>'A few steps before launching!','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
		<?php echo smartyTranslate(array('s'=>'Let\'s create your first products','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
		<?php echo smartyTranslate(array('s'=>'Get your shop ready for payments','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
		<?php echo smartyTranslate(array('s'=>'Choose your shipping options','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
		<?php echo smartyTranslate(array('s'=>'Hurrah!','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->_capture_stack[0][] = array("onboardingComplete", null, null); ob_start(); ?>
	<?php if ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
		<?php echo smartyTranslate(array('s'=>'1/4 complete','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
		<?php echo smartyTranslate(array('s'=>'2/4 complete','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
		<?php echo smartyTranslate(array('s'=>'3/4 complete','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
		<?php echo smartyTranslate(array('s'=>'4/4 complete','mod'=>'onboarding'),$_smarty_tpl);?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->_capture_stack[0][] = array("onboardingCompletePercentage", null, null); ob_start(); ?>
	<?php if ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
	10%%
	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
	25%%
	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
	50%%
	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
	75%%
	<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
	100%%
	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<div class="onboarding minimized">
	<div class="overlay"></div>
	<div class="panel onboarding-steps">
		<div id="onboarding-starter" class="hide">
			<div class="row">
				<div class="col-md-12">
					<h3><?php echo smartyTranslate(array('s'=>'Getting Started with PrestaShop','mod'=>'onboarding'),$_smarty_tpl);?>
</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-3 col-md-2 col-md-offset-2">
					<div class="onboarding-step step-first <?php if ($_smarty_tpl->tpl_vars['current_step']->value==0) {?>step-todo<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>step-in-progress active<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>1) {?>active step-success<?php }?>"></div>
				</div>
				<div class="col-xs-3 col-md-2">
					<div class="onboarding-step <?php if ($_smarty_tpl->tpl_vars['current_step']->value<=1) {?>step-todo<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>step-in-progress active<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>2) {?>active step-success<?php }?>"></div>
				</div>
				<div class="col-xs-3 col-md-2">
					<div class="onboarding-step <?php if ($_smarty_tpl->tpl_vars['current_step']->value<=2) {?>step-todo<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>step-in-progress active<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>3) {?>active step-success<?php }?>"></div>
				</div>
				<div class="col-xs-3 col-md-2">
					<div class="onboarding-step step-final <?php if ($_smarty_tpl->tpl_vars['current_step']->value<=3) {?>step-todo<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>step-in-progress active<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>4) {?>active step-success<?php }?>"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-3 col-md-2 col-md-offset-2 text-center">
					<a style="<?php if ($_smarty_tpl->tpl_vars['current_step']->value<1) {?> color:gray; text-decoration:none <?php }?>"<?php if ($_smarty_tpl->tpl_vars['current_step']->value>=1) {?> href="<?php echo $_smarty_tpl->tpl_vars['continue_editing_links']->value['theme'];?>
"<?php }?>><?php echo smartyTranslate(array('s'=>'Customize your shop','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
				</div>
				<div class="col-xs-3 col-md-2 text-center">
					<a style="<?php if ($_smarty_tpl->tpl_vars['current_step']->value<2) {?> color:gray; text-decoration:none <?php }?>"<?php if ($_smarty_tpl->tpl_vars['current_step']->value>=2) {?> href="<?php echo $_smarty_tpl->tpl_vars['continue_editing_links']->value['product'];?>
"<?php }?>><?php echo smartyTranslate(array('s'=>'Add products','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
				</div>
				<div class="col-xs-3 col-md-2 text-center">
					<a style="<?php if ($_smarty_tpl->tpl_vars['current_step']->value<3) {?> color:gray; text-decoration:none <?php }?>"<?php if ($_smarty_tpl->tpl_vars['current_step']->value>=3) {?> href="<?php echo $_smarty_tpl->tpl_vars['continue_editing_links']->value['payment'];?>
"<?php }?>><?php echo smartyTranslate(array('s'=>'Configure payments','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
				</div>
				<div class="col-xs-3 col-md-2 text-center">
					<a style="<?php if ($_smarty_tpl->tpl_vars['current_step']->value<4) {?> color:gray; text-decoration:none <?php }?>"<?php if ($_smarty_tpl->tpl_vars['current_step']->value>=4) {?> href="<?php echo $_smarty_tpl->tpl_vars['continue_editing_links']->value['carrier'];?>
"<?php }?>><?php echo smartyTranslate(array('s'=>'Choose your shipping options','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-lg-8">
					<h4><?php echo htmlspecialchars(Smarty::$_smarty_vars['capture']['onboardingStepBannerTitle'], ENT_QUOTES, 'UTF-8', true);?>
</h4>
					<p><?php echo smarty_modifier_escape(Smarty::$_smarty_vars['capture']['onboardingStepParagraph'], 'UTF-8');?>
</p>
				</div>
				<div class="col-lg-4 onboarding-action-container">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['next_step_link']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="btn btn-default btn-lg quick-start-button pull-right">
						<?php echo htmlspecialchars(Smarty::$_smarty_vars['capture']['onboardingStepButton'], ENT_QUOTES, 'UTF-8', true);?>
&nbsp;&nbsp;
						<i class="icon icon-angle-right icon-lg"></i>
					</a>
					<a class="btn btn-default btn-lg pull-right" href="#" id="onboarding-close">
						<?php echo smartyTranslate(array('s'=>'No thanks!','mod'=>'onboarding'),$_smarty_tpl);?>
&nbsp;&nbsp;
						<i class="icon icon-times icon-lg"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="onboarding-intro">
			<h3 class="text-center">
			<?php echo htmlspecialchars(Smarty::$_smarty_vars['capture']['onboardingStepModalTitle'], ENT_QUOTES, 'UTF-8', true);?>

			</h3>
			<a class="close-button" href="#" id="quick-start-button">
				<i class="icon icon-times-circle"></i>
			</a>
		</div>

		<div class="steps-list-container">
			<ul class="steps-list">
				<li <?php if ($_smarty_tpl->tpl_vars['current_step']->value>1) {?>class="checked"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>class="active"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value<1) {?>class="inactive"<?php }?>>
					<span class="title"><?php echo smartyTranslate(array('s'=>'Customize your shop\'s look and feel','mod'=>'onboarding'),$_smarty_tpl);?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
						<p class="desc">
						<?php echo smartyTranslate(array('s'=>'Give your shop its own identity based on your brand.','mod'=>'onboarding'),$_smarty_tpl);?>
<br/>
						<?php echo smartyTranslate(array('s'=>'You can change your theme or install a new one, and make sure to upload your own logo to make your shop truly unique.','mod'=>'onboarding'),$_smarty_tpl);?>
<br/><br/>
							<a class="btn btn-primary continue_editing" href="#">
							<i class="icon icon-pencil icon-lg"></i>
							<?php echo smartyTranslate(array('s'=>'OK, take me to my theme','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>1) {?>
						<p class="desc">
							<a class="continue_editing" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['continue_editing_links']->value['theme'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Continue editing','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
						</p>
					<?php }?>
				</li>
				<li <?php if ($_smarty_tpl->tpl_vars['current_step']->value>2) {?>class="checked"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>class="active"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value<2) {?>class="inactive"<?php }?>>
					<span class="title"><?php echo smartyTranslate(array('s'=>'Add products to your catalog','mod'=>'onboarding'),$_smarty_tpl);?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
						<p class="desc">
							<?php echo smartyTranslate(array('s'=>'Start your product catalog with a first product.','mod'=>'onboarding'),$_smarty_tpl);?>

							<br/>
							<?php echo smartyTranslate(array('s'=>'Make sure you cover the basics by setting its price, having a nice description and uploading a catchy image!','mod'=>'onboarding'),$_smarty_tpl);?>

							<?php $_smarty_tpl->tpl_vars["onboardingstep2importcsv"] = new Smarty_variable($_smarty_tpl->tpl_vars['continue_editing_links']->value['import'], null, 0);?>
							<?php echo smartyTranslate(array('s'=>'If you already have your product base in a .CSV file, save time and make an import!','tags'=>array("<a href='".((string)$_smarty_tpl->tpl_vars['onboardingstep2importcsv']->value)."&amp;addproduct'>"),'mod'=>'onboarding'),$_smarty_tpl);?>

							<br/><br/>
							<a class="btn btn-primary continue_editing" href="#">
							<i class="icon icon-book icon-lg"></i>
							<?php echo smartyTranslate(array('s'=>'Ok, Go to my catalog','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>2) {?>
						<p class="desc">
							<a class="" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['continue_editing_links']->value['product'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Continue adding products','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
						</p>
					<?php }?>
				</li>
				<li <?php if ($_smarty_tpl->tpl_vars['current_step']->value>3) {?>class="checked"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>class="active"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value<3) {?>class="inactive"<?php }?>>
					<span class="title"><?php echo smartyTranslate(array('s'=>'Set up your payment methods','mod'=>'onboarding'),$_smarty_tpl);?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
						<p class="desc">
                            <?php if ($_smarty_tpl->tpl_vars['has_psp']->value) {?>
                                <?php echo smartyTranslate(array('s'=>'Your shop runs with PrestaShop Payments by HiPay, so that you can accept payments by card right now. Other payment methods are available too, make sure you set everything up!','mod'=>'onboarding'),$_smarty_tpl);?>

                            <?php } else { ?>
                                <?php echo smartyTranslate(array('s'=>'Select which payment methods you want to offer to customers on your shop, and manage the various restrictions you can apply (per currency, country or group of customers).','mod'=>'onboarding'),$_smarty_tpl);?>

                            <?php }?>
							<br/><br/>
							<a class="btn btn-primary continue_editing" href="#">
								<i class="icon icon-credit-card icon-lg"></i>
								<?php echo smartyTranslate(array('s'=>'Show me payment methods','mod'=>'onboarding'),$_smarty_tpl);?>

							</a>
						</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>3) {?>
						<p class="desc">
							<a class="" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['continue_editing_links']->value['payment'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Continue selecting payment methods','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
						</p>
					<?php }?>
				</li>
				<li <?php if ($_smarty_tpl->tpl_vars['current_step']->value>4) {?>class="checked"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>class="active"<?php }?><?php if ($_smarty_tpl->tpl_vars['current_step']->value<4) {?>class="inactive"<?php }?>>
					<span class="title" ><?php echo smartyTranslate(array('s'=>'Set up your shipping methods','mod'=>'onboarding'),$_smarty_tpl);?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
					<p class="desc">
						<?php echo smartyTranslate(array('s'=>'Unless you are only selling virtual products, you must register your shipping partners into PrestaShop.','mod'=>'onboarding'),$_smarty_tpl);?>
<br/>
						<?php echo smartyTranslate(array('s'=>'Without this your customers won\'t be able to enjoy your products!','mod'=>'onboarding'),$_smarty_tpl);?>

						<br/>
						<br/>
						<a class="btn btn-primary continue_editing" href="#">
							<i class="icon icon-truck icon-lg"></i>
							<?php echo smartyTranslate(array('s'=>'Let\'s see about shipping','mod'=>'onboarding'),$_smarty_tpl);?>

						</a>
					</p>
					<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value>4) {?>
						<p class="desc">
							<a class="" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['continue_editing_links']->value['carrier'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Continue selecting shipping methods','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
						</p>
					<?php }?>
				</li>
			</ul>
			<?php if ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
				<div class="step-launch">
					<button id="onboarding-launch" class="btn btn-block btn-lg btn-primary">
						<i class="icon icon-check icon-lg"></i>
						<?php echo smartyTranslate(array('s'=>'Launch','mod'=>'onboarding'),$_smarty_tpl);?>

					</button>
				</div>
			<?php } else { ?>
				<a href="#" class="skip"><?php echo smartyTranslate(array('s'=>'Skip Tutorial','mod'=>'onboarding'),$_smarty_tpl);?>
</a>
			<?php }?>
		</div>
		<div class="steps-animation-container">
			<?php if ($_smarty_tpl->tpl_vars['current_step']->value==1) {?>
				<img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true);?>
img/step0.jpg" alt="Step 1">
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['current_step']->value==2) {?>
				<img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true);?>
img/step1.jpg" alt="Step 2">
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['current_step']->value==3) {?>
				<img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true);?>
img/step2.jpg" alt="Step 3">
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
				<img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true);?>
img/step3.jpg" alt="Step 4">
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
				<?php echo $_smarty_tpl->getSubTemplate ("./launch_animation.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			<?php }?>
			<div class="step-before-launch text-center">
				<?php if ($_smarty_tpl->tpl_vars['current_step']->value==4) {?>
					<?php echo smartyTranslate(array('s'=>'Last step before launch!','mod'=>'onboarding'),$_smarty_tpl);?>

				<?php } elseif ($_smarty_tpl->tpl_vars['current_step']->value==5) {?>
					<?php echo smartyTranslate(array('s'=>'You are all set!','mod'=>'onboarding'),$_smarty_tpl);?>

				<?php } else { ?>
					<?php echo smartyTranslate(array('s'=>'You are only %s steps away from launch!','sprintf'=>array(5-(int)$_smarty_tpl->tpl_vars['current_step']->value),'mod'=>'onboarding'),$_smarty_tpl);?>

				<?php }?>
			</div>
		</div>
	</div>
	<div class="panel final" style="display: none;">
		<div class="onboarding-intro">
			<h3 class="text-center">
			<?php echo htmlspecialchars(Smarty::$_smarty_vars['capture']['onboardingStepModalTitle'], ENT_QUOTES, 'UTF-8', true);?>

			</h3>
			<a class="close-button" href="" id="final-button">
				<i class="icon icon-times-circle"></i>
			</a>
		</div>
		<div class="final-container">
			<span class="title">
				<?php echo smartyTranslate(array('s'=>'You are now ready to launch your shop. If you feel you need more information, you can still have a look at PrestaShop Documentation:','mod'=>'onboarding'),$_smarty_tpl);?>

				<br />
				<?php echo smartyTranslate(array('s'=>'click on "Help" in the top right corner of your back office!','mod'=>'onboarding'),$_smarty_tpl);?>

			</span>
			<br />
			<textarea name="social-text" id="social-text"><?php echo smartyTranslate(array('s'=>'I just launched my online shop with @PrestaShop. Check it out!','mod'=>'onboarding'),$_smarty_tpl);?>
</textarea>
			<br />
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_facebook_click();">
					<i class="icon icon-facebook icon-4x icon-fw"></i>
				</a>
			</div>
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_twitter_click($('#social-text').text());">
					<i class="icon icon-twitter icon-4x icon-fw"></i>
				</a>
			</div>
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_linkedin_click();">
					<i class="icon icon-linkedin icon-4x icon-fw"></i>
				</a>
			</div>
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_google_click();">
					<i class="icon icon-google-plus icon-4x icon-fw"></i>
				</a>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
