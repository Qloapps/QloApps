<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 10:35:38
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wktestimonialblock/views/templates/admin/hoteltestimonialblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3550835005638d44add7d80-73776604%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '16e943de5615bb1c48e9357c760ace19a587198b' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wktestimonialblock/views/templates/admin/hoteltestimonialblock.tpl',
      1 => 1446564769,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3550835005638d44add7d80-73776604',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'testimonials_data' => 0,
    'data' => 0,
    'module_dir' => 0,
    'iso' => 0,
    'ad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5638d44af0f372_99272171',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5638d44af0f372_99272171')) {function content_5638d44af0f372_99272171($_smarty_tpl) {?><div class="panel">
	<h3 class="tab"> <i class="icon-info"></i> <?php echo smartyTranslate(array('s'=>'Configuration','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</h3>
	<div class="panel-body">
		<form method="post" action="" enctype="multipart/form-data">
			<div class="row">	
				<?php if (isset($_smarty_tpl->tpl_vars['testimonials_data']->value)) {?> 
					<div class="form_testimonial_div">	
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['testimonials_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
						<div class="testimonial_form_elements">
							<div class="col-sm-12">
								<button data-id_row="<?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
" type="submit" class="delete_htl_testimonial btn btn-primary pull-right">
									<?php echo smartyTranslate(array('s'=>'Delete','mod'=>'wktestimonialblock'),$_smarty_tpl);?>

								</button>
							</div>
							<div class="form-group">
								<label for="name" class="control-label">
									<span title="" data-toggle="tooltip" class="label-tooltip"><?php echo smartyTranslate(array('s'=>'Name','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</span>
								</label>
								<div class="">
									<input type="text" name="name[]" class="form-control" <?php if (isset($_smarty_tpl->tpl_vars['data']->value['name'])) {?>value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
"<?php }?>>
									<input type="hidden" name="testimonial_id[]" class="form-control" <?php if (isset($_smarty_tpl->tpl_vars['data']->value['id'])) {?>value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
"<?php }?>>
								</div>
							</div>
							<div class="form-group">
								<label for="testimonial_description" class="control-label required">
									<span title="" data-toggle="tooltip" class="label-tooltip"><?php echo smartyTranslate(array('s'=>'Testimonial Description','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</span>
								</label>
								<div class="">
									<input type="text" name="testimonial_description[]" class="form-control" <?php if (isset($_smarty_tpl->tpl_vars['data']->value['testimonial_description'])) {?>value="<?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_description'];?>
"<?php }?>>
								</div>
							</div>
							<div class="form-group">
								<label for="testimonial_content" class="control-label required">
									<span title="" data-toggle="tooltip" class="label-tooltip"><?php echo smartyTranslate(array('s'=>'Testimonial Content','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</span>
								</label>
								<div class="form-group">
									<textarea name="testimonial_content[]" class="testimonial_content wk_tinymce"><?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_content'];?>
</textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="testimonial_image" class="control-label col-sm-2">
									<span title="" data-toggle="tooltip" class="label-tooltip"><?php echo smartyTranslate(array('s'=>'Person Image','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</span>
								</label>
								<div class="col-sm-4">
									<input type="file" name="testimonial_image[]">
								</div>
								<div class="col-sm-6">
								<?php if (isset($_smarty_tpl->tpl_vars['data']->value['id'])) {?>
									<?php if (isset($_smarty_tpl->tpl_vars['data']->value['testimonial_image'])) {?>
										<img height="50px" width="50px" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
wktestimonialblock/views/img/<?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_image'];?>
">
									<?php } else { ?>
										<img height="50px" width="50px" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
wktestimonialblock/views/img/default.png">
									<?php }?>
								<?php }?>
								</div>
							</div>
							<br><br><hr>
						</div>
					<?php } ?>
					</div>	
					<div class="form-group">
						<div class="col-lg-12">
							<a class="btn btn-default htl-testimonial-btn-more-testimonials">
								<i class="icon-image"></i>
								<span><?php echo smartyTranslate(array('s'=>'Add More Testimonials','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</span>
							</a>
							<div id="wk_testimonials_other_images"></div>
						</div>
					</div>
				<?php }?>
				<div class="">
					<button id="testimonial_submit" name="save_testimonial_data" type="submit" class="btn btn-primary col-sm-1 pull-right">
						<?php echo smartyTranslate(array('s'=>'Save','mod'=>'wktestimonialblock'),$_smarty_tpl);?>

					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('module_dir'=>$_smarty_tpl->tpl_vars['module_dir']->value),$_smarty_tpl);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'some_error_occur_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'some_error_occur_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Some error occured, Please try again.','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'some_error_occur_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'remove_success_var')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'remove_success_var'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Remove successful','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'remove_success_var'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'name_var')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'name_var'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Name','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'name_var'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'testimonial_description_var')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'testimonial_description_var'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Testimonial Description','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'testimonial_description_var'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'testimonial_content_var')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'testimonial_content_var'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Testimonial Content','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'testimonial_content_var'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'person_image_var')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'person_image_var'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Person Image','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'person_image_var'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'remove_var')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'remove_var'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Remove','js'=>1,'mod'=>'wktestimonialblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'remove_var'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>





<script>
// for tiny mce setup
  var iso = "<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['iso']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
";
  var pathCSS = "<?php echo mb_convert_encoding(htmlspecialchars(@constant('_THEME_CSS_DIR_'), ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
";
  var ad = "<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['ad']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
";

  $(document).ready(function(){
    
      tinySetup({
        editor_selector :"wk_tinymce",
      });
    
  });
</script><?php }} ?>
