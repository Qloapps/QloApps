<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 10:49:09
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/contact-form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:403086085563785f5c73b67-49870099%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e8c362b9591cf2de63665bf03c9007d189e0ca51' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/contact-form.tpl',
      1 => 1446454876,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '403086085563785f5c73b67-49870099',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'confirmation' => 0,
    'base_dir' => 0,
    'alreadySent' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563785f5cf2602_18645949',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563785f5cf2602_18645949')) {function content_563785f5cf2602_18645949($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['confirmation']->value)) {?>
	<p class="alert alert-success"><?php echo smartyTranslate(array('s'=>'Your message has been successfully sent to our team.'),$_smarty_tpl);?>
</p>
	<ul class="footer_links clearfix">
		<li>
			<a class="btn btn-default button button-small" href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
">
				<span>
					<i class="icon-chevron-left"></i><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>

				</span>
			</a>
		</li>
	</ul>
<?php } elseif (isset($_smarty_tpl->tpl_vars['alreadySent']->value)) {?>
	<p class="alert alert-warning"><?php echo smartyTranslate(array('s'=>'Your message has already been sent.'),$_smarty_tpl);?>
</p>
	<ul class="footer_links clearfix">
		<li>
			<a class="btn btn-default button button-small" href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
">
				<span>
					<i class="icon-chevron-left"></i><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>

				</span>
			</a>
		</li>
	</ul>
<?php } else { ?>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

	<div class="row margin-top-50">
		<div class="col-sm-6">
			<p class="contact-header"><?php echo smartyTranslate(array('s'=>'Get in touch with us'),$_smarty_tpl);?>
</p>
			<p class="contact-desc"><?php echo smartyTranslate(array('s'=>'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text.'),$_smarty_tpl);?>
</p>
			<div class="col-sm-12 contact-subdiv">
				<p>
					<i class="icon-map-marker cont_icon_map"></i>
					<span> <?php echo smartyTranslate(array('s'=>'Contrary to popular belief, Lorem Ipsum is not simply random text.'),$_smarty_tpl);?>
</span>
				</p>
				<p>
					<i class="icon-mobile-phone cont_icon_phone"></i>
					<span> +91-9999999999, +91-9876543210</span>
				</p>
				<p>
					<i class="icon-envelope cont_icon_enve"></i>
					<span> noreply@webkul.com</span>
				</p>
			</div>
		</div>
		<div class="col-sm-6">
			<form method="POST" action="#">
				<input type="text" placeholder="Name" class="form-control contact_input">
				<input type="email" placeholder="Email" class="form-control contact_input">
				<textarea placeholder="Message/Query..." class="form-control contact_textarea"></textarea>
				<button class="btn contact_btn"><?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
</button>
			</form>
		</div>
	</div>
<?php }?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'contact_fileDefaultHtml')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'contact_fileDefaultHtml'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'No file selected','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'contact_fileDefaultHtml'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'contact_fileButtonHtml')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'contact_fileButtonHtml'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Choose File','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'contact_fileButtonHtml'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }} ?>
