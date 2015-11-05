<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 16:50:14
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin464itamgr/themes/default/template/controllers/products/features.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5668647563b3b6ecced94-50183099%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bcd582e36ac592c143f94cd5e637e3545c159915' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin464itamgr/themes/default/template/controllers/products/features.tpl',
      1 => 1446549621,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5668647563b3b6ecced94-50183099',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'available_features' => 0,
    'available_feature' => 0,
    'value' => 0,
    'base_url' => 0,
    'link' => 0,
    'languages' => 0,
    'language' => 0,
    'default_form_language' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b3b6f01fca0_48419015',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b3b6f01fca0_48419015')) {function content_563b3b6f01fca0_48419015($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)) {?>
<div id="product-features" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Features" />
	<h3><?php echo smartyTranslate(array('s'=>'Assign features to this product'),$_smarty_tpl);?>
</h3>

	<div class="alert alert-info">
		<?php echo smartyTranslate(array('s'=>'You can specify a value for each relevant feature regarding this product. Empty fields will not be displayed.'),$_smarty_tpl);?>
<br/>
		<?php echo smartyTranslate(array('s'=>'You can either create a specific value, or select among the existing pre-defined values you\'ve previously added.'),$_smarty_tpl);?>

	</div>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Feature'),$_smarty_tpl);?>
</span></th>
				<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Feature Image'),$_smarty_tpl);?>
</span></th>
				<!-- <th><span class="title_box"><u><?php echo smartyTranslate(array('s'=>'or'),$_smarty_tpl);?>
</u> <?php echo smartyTranslate(array('s'=>'Customized value'),$_smarty_tpl);?>
</span></th> --><!-- by webkul -->
			</tr>
		</thead>

		<tbody>
		<?php  $_smarty_tpl->tpl_vars['available_feature'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['available_feature']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['available_features']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['available_feature']->key => $_smarty_tpl->tpl_vars['available_feature']->value) {
$_smarty_tpl->tpl_vars['available_feature']->_loop = true;
?>

			<tr>
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['available_feature']->value['featureValues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
				<td>
					<input type="checkbox" class="checkbox select_hotel_feature" name="feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_check" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id_feature_value'];?>
" <?php if ($_smarty_tpl->tpl_vars['available_feature']->value['current_item']==$_smarty_tpl->tpl_vars['value']->value['id_feature_value']) {?>checked="checked"<?php }?>/>
				</td>
				<td><?php echo $_smarty_tpl->tpl_vars['available_feature']->value['name'];?>
</td>
				<td>
					<input type="hidden" id="feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_value" name="feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_value" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id_feature_value'];?>
">
					<img height="40px" width="40px" src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
img/rf/<?php echo $_smarty_tpl->tpl_vars['value']->value['value'];?>
" title="Room image" />
				</td>
				<?php } ?>
				<!--<td>
				 <?php if (sizeof($_smarty_tpl->tpl_vars['available_feature']->value['featureValues'])) {?>
					<select id="feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_value" name="feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_value"
						onchange="$('.custom_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_').val('');">
						<option value="0">---</option>
						<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['available_feature']->value['featureValues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id_feature_value'];?>
"<?php if ($_smarty_tpl->tpl_vars['available_feature']->value['current_item']==$_smarty_tpl->tpl_vars['value']->value['id_feature_value']) {?>selected="selected"<?php }?> >
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['value']->value['value'],40);?>

						</option>
						<?php } ?>
					</select>
				<?php } else { ?>
					<input type="hidden" name="feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_value" value="0" />
					<span><?php echo smartyTranslate(array('s'=>'N/A'),$_smarty_tpl);?>
 -
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminFeatures'), ENT_QUOTES, 'UTF-8', true);?>
&amp;addfeature_value&amp;id_feature=<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
"
					 	class="confirm_leave btn btn-link"><i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Add pre-defined values first'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i></a>
					</span>
				<?php }?> --><!-- By Webkul -->
				</td>
				<!-- <td>

				<div class="row lang-0" style='display: none;'>
					<div class="col-lg-9">
						<textarea class="custom_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_ALL textarea-autosize"	name="custom_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_ALL"
								cols="40" style='background-color:#CCF'	rows="1" onkeyup="<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>$('.custom_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').val($(this).val());<?php } ?>" ><?php echo (($tmp = @htmlspecialchars($_smarty_tpl->tpl_vars['available_feature']->value['val'][1]['value'], ENT_QUOTES, 'UTF-8', true))===null||$tmp==='' ? '' : $tmp);?>
</textarea>

					</div>
					<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
						<div class="col-lg-3">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<?php echo smartyTranslate(array('s'=>'ALL'),$_smarty_tpl);?>

								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
									<li>
										<a href="javascript:void(0);" onclick="restore_lng($(this),<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
);"><?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
</a>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php }?>
				</div>

				<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
					<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
					<div class="row translatable-field lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
">
						<div class="col-lg-9">
						<?php }?>
						<textarea
								class="custom_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
 textarea-autosize"
								name="custom_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
"
								cols="40"
								rows="1"
								onkeyup="if (isArrowKey(event)) return ;$('#feature_<?php echo $_smarty_tpl->tpl_vars['available_feature']->value['id_feature'];?>
_value').val(0);" ><?php echo (($tmp = @htmlspecialchars($_smarty_tpl->tpl_vars['available_feature']->value['val'][$_smarty_tpl->tpl_vars['language']->value['id_lang']]['value'], ENT_QUOTES, 'UTF-8', true))===null||$tmp==='' ? '' : $tmp);?>
</textarea>

					<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
						</div>
						<div class="col-lg-3">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>

								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="javascript:void(0);" onclick="all_languages($(this));"><?php echo smartyTranslate(array('s'=>'ALL'),$_smarty_tpl);?>
</a></li>
								<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
								<li>
									<a href="javascript:hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
);"><?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<?php }?>
					<?php } ?>
				</td> --><!-- By Webkul -->

			</tr>
			<?php }
if (!$_smarty_tpl->tpl_vars['available_feature']->_loop) {
?>
			<tr>
				<td colspan="3" style="text-align:center;"><i class="icon-warning-sign"></i> <?php echo smartyTranslate(array('s'=>'No features have been defined'),$_smarty_tpl);?>
</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminFeatures'), ENT_QUOTES, 'UTF-8', true);?>
&amp;addfeature" class="btn btn-link confirm_leave button">
		<i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Add a new feature'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i>
	</a>
	<div class="panel-footer">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>&amp;submitFilterproduct=<?php echo intval($_REQUEST['page']);?>
<?php }?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>
</button>
	</div>
</div>
<script type="text/javascript">
	if (tabs_manager.allow_hide_other_languages)
		hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['default_form_language']->value;?>
);

	$(".textarea-autosize").autosize();

	function all_languages(pos)
	{

<?php if (isset($_smarty_tpl->tpl_vars['languages']->value)&&is_array($_smarty_tpl->tpl_vars['languages']->value)) {?>
	<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
			pos.parents('td').find('.lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').addClass('nolang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').removeClass('lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
');
	<?php } ?>
<?php }?>
		pos.parents('td').find('.translatable-field').hide();
		pos.parents('td').find('.lang-0').show();

	}

	function restore_lng(pos,i)
	{

<?php if (isset($_smarty_tpl->tpl_vars['languages']->value)&&is_array($_smarty_tpl->tpl_vars['languages']->value)) {?>
	<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
			pos.parents('td').find('.nolang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').addClass('lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').removeClass('nolang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
');
	<?php } ?>
<?php }?>

		pos.parents('td').find('.lang-0').hide();
		hideOtherLanguage(i);
	}
</script>


<?php }?>
<?php }} ?>
