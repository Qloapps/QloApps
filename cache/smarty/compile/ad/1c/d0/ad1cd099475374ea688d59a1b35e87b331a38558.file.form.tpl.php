<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:47:56
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/hotelreservationsystem/views/templates/admin/hotel_features/helpers/form/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:432175972568e0314525977-83589418%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ad1cd099475374ea688d59a1b35e87b331a38558' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/hotelreservationsystem/views/templates/admin/hotel_features/helpers/form/form.tpl',
      1 => 1452142909,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '432175972568e0314525977-83589418',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'addfeatures' => 0,
    'current' => 0,
    'submit_action' => 0,
    'token' => 0,
    'name_controller' => 0,
    'features_list' => 0,
    'value' => 0,
    'val' => 0,
    'edit' => 0,
    'hotel_id' => 0,
    'hotels' => 0,
    'hotel' => 0,
    'i' => 0,
    'link' => 0,
    'table' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568e0314639ee7_43316467',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568e0314639ee7_43316467')) {function content_568e0314639ee7_43316467($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['addfeatures']->value)) {?>
	<form method="post" action="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&<?php if (!empty($_smarty_tpl->tpl_vars['submit_action']->value)) {?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['submit_action']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }?>&token=<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="defaultForm form-horizontal <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['name_controller']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" enctype="multipart/form-data">
		<div class="panel" style="float:left">
			<div class="panel-heading">
				<i class="icon-plus"></i>&nbsp <?php echo smartyTranslate(array('s'=>'Add New Features','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</div>

			<a data-mfp-src="#test-popup" class="btn btn-primary open-popup-link-feature" data-toggle="modal" data-target="#basicModal_addNewFeature"><span><i class="icon-plus"></i>&nbsp<?php echo smartyTranslate(array('s'=>'Add New Features','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span></a>

			<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['features_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
				<div class="col-sm-12 feature_div" id="grand_feature_div_<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
">
					<div class="row row-margin-bottom row-margin-top">
						<div class="col-sm-12">
							<div class="row feature-border-div">
								<div class="col-sm-12 feature-header-div">
									<span><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
<?php $_tmp1=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp1,'mod'=>'hotelreservationsyatem'),$_smarty_tpl);?>
</span>
									<a class="btn btn-primary pull-right edit_feature col-sm-1" data-feature='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['value']->value);?>
'><span><i class="icon-pencil"></i>&nbsp&nbsp&nbsp&nbsp<?php echo smartyTranslate(array('s'=>'Edit','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span></a>
									<button class="btn btn-primary pull-right dlt-feature col-sm-1" data-feature-id="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><i class="icon-trash"></i>&nbsp&nbsp&nbsp&nbsp<?php echo smartyTranslate(array('s'=>'Delete','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</button>
								</div>
							</div>
						</div>
					</div>
					<div class="row child-features-container">
						<div class="col-sm-12">
						<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['value']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->_loop = true;
?>
							<p><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['val']->value['name'];?>
<?php $_tmp2=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp2,'mod'=>'hotelreservationsyatem'),$_smarty_tpl);?>
</p>
						<?php } ?>
						</div>
					</div>
				</div>
			<?php }
if (!$_smarty_tpl->tpl_vars['value']->_loop) {
?>
				<!-- code for foreachelse -->
			<?php } ?>
		</div>
	</form>
<?php } else { ?>
		<form method="post" action="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&<?php if (!empty($_smarty_tpl->tpl_vars['submit_action']->value)) {?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['submit_action']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }?>&token=<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="defaultForm form-horizontal <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['name_controller']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" enctype="multipart/form-data">
			<?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?>
				<input name="edit_hotel_id" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['hotel_id']->value;?>
">
			<?php }?>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-user"></i> <?php echo smartyTranslate(array('s'=>'Assign Features','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

				</div>
				<?php if (isset($_smarty_tpl->tpl_vars['hotels']->value)&&$_smarty_tpl->tpl_vars['hotels']->value) {?>
					<div class="form-wrapper">
						<div class="form-group">
							<?php if (isset($_smarty_tpl->tpl_vars['edit']->value)) {?>
								<label class="control-label col-sm-5">
									<span><?php echo smartyTranslate(array('s'=>'Hotel Name','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 : </span>
								</label>
								<select class="fixed-width-xl" name="id_hotel">
									<?php  $_smarty_tpl->tpl_vars['hotel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hotel']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hotels']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hotel']->key => $_smarty_tpl->tpl_vars['hotel']->value) {
$_smarty_tpl->tpl_vars['hotel']->_loop = true;
?>
										<?php if ($_smarty_tpl->tpl_vars['hotel_id']->value==$_smarty_tpl->tpl_vars['hotel']->value['id']) {?>
											<option readonly="true" selected="true" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['hotel']->value['id'], ENT_QUOTES, 'UTF-8', true);?>
" ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['hotel']->value['hotel_name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
										<?php }?>
									<?php } ?>
								</select>
							<?php } else { ?>
								<label class="control-label col-sm-5">
									<span><?php echo smartyTranslate(array('s'=>'Select Hotel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 : </span>
								</label>
								<div class="col-sm-4">	
									<select class="fixed-width-xl" name="id_hotel">
									<option value='0'><?php echo smartyTranslate(array('s'=>'Select Hotel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</option>>
										<?php  $_smarty_tpl->tpl_vars['hotel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hotel']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hotels']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hotel']->key => $_smarty_tpl->tpl_vars['hotel']->value) {
$_smarty_tpl->tpl_vars['hotel']->_loop = true;
?>
											<option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['hotel']->value['id'], ENT_QUOTES, 'UTF-8', true);?>
" ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['hotel']->value['hotel_name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
										<?php } ?>
									</select>
								</div>
							<?php }?>
						</div>
					</div>
					<?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(1, null, 0);?>
					<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['features_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
					<div class="accordion">
					    <div class="accordion-section">
					        <a class="accordion-section-title" href="#accordion<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><span class="icon-plus"></span>&nbsp&nbsp<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
<?php $_tmp3=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp3,'mod'=>'hotelreservationsyatem'),$_smarty_tpl);?>
</a>
					        <div id="accordion<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="accordion-section-content">
					        	<table id="" class="table" style="max-width:100%">
									<tbody>
										<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['value']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->_loop = true;
?>
											<tr>
												<td class="border_top border_bottom border_bold">
													<span class=""> <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['val']->value['name'];?>
<?php $_tmp4=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp4,'mod'=>'hotelreservationsyatem'),$_smarty_tpl);?>
 </span>
												</td>
												<td style="">
													<input name="hotel_fac[]" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['val']->value['id'];?>
" class="form-control" <?php if (isset($_smarty_tpl->tpl_vars['edit']->value)&&$_smarty_tpl->tpl_vars['val']->value['selected']) {?>checked='true'<?php }?>>
												</td>
											</tr>
										<?php }
if (!$_smarty_tpl->tpl_vars['val']->_loop) {
?>
											<!-- code for foreachelse -->
										<?php } ?>
									</tbody>
								</table>
					        </div>
					    </div>
					</div>
					<?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>
					<?php }
if (!$_smarty_tpl->tpl_vars['value']->_loop) {
?>
						<!-- code for foreachelse -->
					<?php } ?>
					<div class="panel-footer">
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminHotelFeatures'), ENT_QUOTES, 'UTF-8', true);?>
" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</a>
						<button type="submit" name="submitAddhtl_features" class="btn btn-default pull-right"><i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Assign','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</button>
						<!-- <button type="submit" name="submitAdd<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['table']->value, ENT_QUOTES, 'UTF-8', true);?>
AndStay" class="btn btn-default pull-right">
							<i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Assign and stay','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

						</button> -->
					</div>
				<?php } else { ?>
					<div class="alert alert-warning">
						<?php echo smartyTranslate(array('s'=>'No hotel found to assign features.','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

					</div>
				<?php }?>
			</div>
		</form>
<?php }?>

<!-- model box for add new features -->
<div class="modal fade" id="basicModal_addNewFeature" tabindex="-1" role="dialog" aria-labelledby="basicModal_features" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content add_hotel_feature_form_div">
			<div class="modal-header" style="padding-left:15px;">
				<h2 class="page-subheading_admin_wallet">
					<?php echo smartyTranslate(array('s'=>'Add Hotel Features','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

				</h2>
			</div>
			<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminHotelFeatures');?>
">
				<div class="modal-body">
					<div class="row row-margin-bottom">	
						<label style="font-weight:600" class="col-sm-3 control-label" ><?php echo smartyTranslate(array('s'=>'Parent Feature :','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
						<div class="col-sm-7">
							<input type="text" name="parent_ftr" class="parent_ftr" placeholder="<?php echo smartyTranslate(array('s'=>'parent feature name','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
" class="form-control" />
							<input type="hidden" name="parent_ftr_id" class="parent_ftr_id"/>
							<p class="error_text" id="prnt_ftr_err_p"></p>
						</div>
					</div>
					<div class="row row-margin-bottom">	
						<label style="font-weight:600" class="col-sm-3 control-label"><?php echo smartyTranslate(array('s'=>'Position :','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
						<div class="col-sm-7">
							<input type="text" name="position" class="position" placeholder="<?php echo smartyTranslate(array('s'=>'feature position','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
" class="form-control"/>
							<p class="error_text" id="pos_err_p"></p>
						</div>
					</div>
					<div class="row row-margin-bottom">	
						<label style="font-weight:600" class="col-sm-3 control-label"><?php echo smartyTranslate(array('s'=>'Child Feature :','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
						<div class="col-sm-5">
							<input type="text" placeholder="child feature name" class="child_ftr col-sm-4" name="child_ftr">
							<p class="error_text" id="chld_ftr_err_p"></p>
						</div>
						<button type="button" class='col-sm-2 btn btn-primary add_feature_to_list'><?php echo smartyTranslate(array('s'=>'Add','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</button>
					</div>
					<div class="row row-margin-bottom added_feature">	
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary admin_submit_feature" name="submit_add_btn_feature">
						<span>
							<?php echo smartyTranslate(array('s'=>'Create Feature','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

						</span>
					</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						<span>
							<?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

						</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END -->




<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('delete_url'=>$_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminHotelFeatures'),'js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'success_delete_msg')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'success_delete_msg'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Successfully Deleted.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'success_delete_msg'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'error_delete_msg')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'error_delete_msg'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Some error occured while deleting feature.Please try again.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'error_delete_msg'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'confirm_delete_msg')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'confirm_delete_msg'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Are you sure?','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'confirm_delete_msg'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'prnt_ftr_err')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'prnt_ftr_err'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Enter Parent feature name first.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'prnt_ftr_err'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'chld_ftr_err')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'chld_ftr_err'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Enter at least one child feature.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'chld_ftr_err'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'chld_ftr_text_err')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'chld_ftr_text_err'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Enter child feature name.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'chld_ftr_text_err'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'pos_numeric_err')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'pos_numeric_err'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Position should be numeric.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'pos_numeric_err'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }} ?>
