<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:10:10
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin6039ognxn/themes/default/template/helpers/options/options.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1351184376563775ca078a79-87613490%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '096a9ae47b6cc2b64d020508cb147d2a0b6ec981' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin6039ognxn/themes/default/template/helpers/options/options.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1351184376563775ca078a79-87613490',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'current_id_lang' => 0,
    'tabs' => 0,
    'table' => 0,
    'table_bk' => 0,
    'current' => 0,
    'token' => 0,
    'option_list' => 0,
    'categoryData' => 0,
    'category' => 0,
    'use_multishop' => 0,
    'field' => 0,
    'key' => 0,
    'hint' => 0,
    'option' => 0,
    'input' => 0,
    'k' => 0,
    'v' => 0,
    'currency_left_sign' => 0,
    'currency_right_sign' => 0,
    'id_lang' => 0,
    'value' => 0,
    'languages' => 0,
    'language' => 0,
    'p' => 0,
    'btn' => 0,
    'name_controller' => 0,
    'hookName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563775ca4ac724_42399115',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563775ca4ac724_42399115')) {function content_563775ca4ac724_42399115($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/function.counter.php';
if (!is_callable('smarty_modifier_escape')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_replace')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/modifier.replace.php';
?>

<div class="leadin"></div>

<script type="text/javascript">
	id_language = Number(<?php echo $_smarty_tpl->tpl_vars['current_id_lang']->value;?>
);
	<?php if (isset($_smarty_tpl->tpl_vars['tabs']->value)&&count($_smarty_tpl->tpl_vars['tabs']->value)) {?>
		var helper_tabs= <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['tabs']->value);?>
;
		var unique_field_id = '<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_';
	<?php }?>
</script>

<?php if (isset($_smarty_tpl->tpl_vars['table_bk']->value)&&$_smarty_tpl->tpl_vars['table_bk']->value==$_smarty_tpl->tpl_vars['table']->value) {?><?php $_smarty_tpl->_capture_stack[0][] = array('table_count', null, null); ob_start(); ?><?php echo smarty_function_counter(array('name'=>'table_count'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?><?php }?>
<?php $_smarty_tpl->tpl_vars['table_bk'] = new Smarty_variable($_smarty_tpl->tpl_vars['table']->value, null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['table_bk'] = clone $_smarty_tpl->tpl_vars['table_bk'];?>
<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
" id="<?php if ($_smarty_tpl->tpl_vars['table']->value==null) {?>configuration_form<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form<?php }?><?php if (isset(Smarty::$_smarty_vars['capture']['table_count'])&&Smarty::$_smarty_vars['capture']['table_count']) {?>_<?php echo intval(Smarty::$_smarty_vars['capture']['table_count']);?>
<?php }?>" method="post" enctype="multipart/form-data" class="form-horizontal">
	<?php  $_smarty_tpl->tpl_vars['categoryData'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['categoryData']->_loop = false;
 $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['option_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['categoryData']->key => $_smarty_tpl->tpl_vars['categoryData']->value) {
$_smarty_tpl->tpl_vars['categoryData']->_loop = true;
 $_smarty_tpl->tpl_vars['category']->value = $_smarty_tpl->tpl_vars['categoryData']->key;
?>
		<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['top'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['top'];?>
<?php }?>
		<div class="panel <?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['class'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['class'];?>
<?php }?>" id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_fieldset_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
">
			
			<div class="panel-heading">
				<i class="<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['icon'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['icon'];?>
<?php } else { ?>icon-cogs<?php }?>"></i>
				<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['title'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['title'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Options'),$_smarty_tpl);?>
<?php }?>
			</div>

			

			<?php if ((isset($_smarty_tpl->tpl_vars['categoryData']->value['description'])&&$_smarty_tpl->tpl_vars['categoryData']->value['description'])) {?>
				<div class="alert alert-info"><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['description'];?>
</div>
			<?php }?>
			
			<?php if ((isset($_smarty_tpl->tpl_vars['categoryData']->value['info'])&&$_smarty_tpl->tpl_vars['categoryData']->value['info'])) {?>
				<div><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['info'];?>
</div>
			<?php }?>

			<?php if (!$_smarty_tpl->tpl_vars['categoryData']->value['hide_multishop_checkbox']&&$_smarty_tpl->tpl_vars['use_multishop']->value) {?>
			<div class="well clearfix">
				<label class="control-label col-lg-3">
					<i class="icon-sitemap"></i> <?php echo smartyTranslate(array('s'=>'Multistore'),$_smarty_tpl);?>

				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_multishop_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_multishop_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
_on" value="1" onclick="toggleAllMultishopDefaultValue($('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_fieldset_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
'), true)"/><label for="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_multishop_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
_on"><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</label><input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_multishop_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_multishop_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
_off" value="0" checked="checked" onclick="toggleAllMultishopDefaultValue($('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_fieldset_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
'), false)"/><label for="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_multishop_<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
_off"><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</label>
						<a class="slide-button btn"></a>
					</span>
					<div class="row">
						<div class="col-lg-12">
							<p class="help-block">
								<strong><?php echo smartyTranslate(array('s'=>'Check / Uncheck all'),$_smarty_tpl);?>
</strong><br />
								<?php echo smartyTranslate(array('s'=>'You are editing this page for a specific shop or group. Click "Yes" to check all fields, "No" to uncheck all.'),$_smarty_tpl);?>
<br />
 								<?php echo smartyTranslate(array('s'=>'If you check a field, change its value, and save, the multistore behavior will not apply to this shop (or group), for this particular parameter.'),$_smarty_tpl);?>

							</p>
						</div>
					</div>
				</div>
			</div>
			<?php }?>

			<div class="form-wrapper">
			<?php  $_smarty_tpl->tpl_vars['field'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categoryData']->value['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field']->key => $_smarty_tpl->tpl_vars['field']->value) {
$_smarty_tpl->tpl_vars['field']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['field']->key;
?>
					<?php if ($_smarty_tpl->tpl_vars['field']->value['type']=='hidden') {?>
						<input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value['value'];?>
" />
					<?php } else { ?>
						<div class="form-group<?php if (isset($_smarty_tpl->tpl_vars['field']->value['form_group_class'])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['form_group_class'];?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['tabs']->value)&&isset($_smarty_tpl->tpl_vars['field']->value['tab'])) {?> data-tab-id="<?php echo $_smarty_tpl->tpl_vars['field']->value['tab'];?>
"<?php }?>>
							<div id="conf_id_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['field']->value['is_invisible']) {?> class="isInvisible"<?php }?>>
								
									<?php if (isset($_smarty_tpl->tpl_vars['field']->value['title'])&&isset($_smarty_tpl->tpl_vars['field']->value['hint'])) {?>
										<label class="control-label col-lg-3<?php if (isset($_smarty_tpl->tpl_vars['field']->value['required'])&&$_smarty_tpl->tpl_vars['field']->value['required']&&$_smarty_tpl->tpl_vars['field']->value['type']!='radio') {?> required<?php }?>">
											<?php if (!$_smarty_tpl->tpl_vars['categoryData']->value['hide_multishop_checkbox']&&$_smarty_tpl->tpl_vars['field']->value['multishop_default']&&empty($_smarty_tpl->tpl_vars['field']->value['no_multishop_checkbox'])) {?>
											<input type="checkbox" name="multishopOverrideOption[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="1"<?php if (!$_smarty_tpl->tpl_vars['field']->value['is_disabled']) {?> checked="checked"<?php }?> onclick="toggleMultishopDefaultValue(this, '<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
')"/>
											<?php }?>
											<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="
												<?php if (is_array($_smarty_tpl->tpl_vars['field']->value['hint'])) {?>
													<?php  $_smarty_tpl->tpl_vars['hint'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hint']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['field']->value['hint']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hint']->key => $_smarty_tpl->tpl_vars['hint']->value) {
$_smarty_tpl->tpl_vars['hint']->_loop = true;
?>
														<?php if (is_array($_smarty_tpl->tpl_vars['hint']->value)) {?>
															<?php echo $_smarty_tpl->tpl_vars['hint']->value['text'];?>

														<?php } else { ?>
															<?php echo $_smarty_tpl->tpl_vars['hint']->value;?>

														<?php }?>
													<?php } ?>
												<?php } else { ?>
													<?php echo $_smarty_tpl->tpl_vars['field']->value['hint'];?>

												<?php }?>
											" data-html="true">
												<?php echo $_smarty_tpl->tpl_vars['field']->value['title'];?>

											</span>
										</label>
									<?php } elseif (isset($_smarty_tpl->tpl_vars['field']->value['title'])) {?>
										<label class="control-label col-lg-3">
											<?php if (!$_smarty_tpl->tpl_vars['categoryData']->value['hide_multishop_checkbox']&&$_smarty_tpl->tpl_vars['field']->value['multishop_default']&&empty($_smarty_tpl->tpl_vars['field']->value['no_multishop_checkbox'])) {?>
											<input type="checkbox" name="multishopOverrideOption[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="1"<?php if (!$_smarty_tpl->tpl_vars['field']->value['is_disabled']) {?> checked="checked"<?php }?> onclick="checkMultishopDefaultValue(this, '<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
')" />
											<?php }?>
											<?php echo $_smarty_tpl->tpl_vars['field']->value['title'];?>

										</label>
									<?php }?>
								
								

								
									<?php if ($_smarty_tpl->tpl_vars['field']->value['type']=='select') {?>
										<div class="col-lg-9">
											<?php if ($_smarty_tpl->tpl_vars['field']->value['list']) {?>
												<select class="form-control fixed-width-xxl <?php if (isset($_smarty_tpl->tpl_vars['field']->value['class'])) {?><?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"<?php if (isset($_smarty_tpl->tpl_vars['field']->value['js'])) {?> onchange="<?php echo $_smarty_tpl->tpl_vars['field']->value['js'];?>
"<?php }?> id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])) {?> size="<?php echo $_smarty_tpl->tpl_vars['field']->value['size'];?>
"<?php }?>>
													<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value) {
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['option']->key;
?>
														<option value="<?php echo $_smarty_tpl->tpl_vars['option']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']];?>
"<?php if ($_smarty_tpl->tpl_vars['field']->value['value']==$_smarty_tpl->tpl_vars['option']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']]) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['option']->value['name'];?>
</option>
													<?php } ?>
												</select>
											<?php } elseif (isset($_smarty_tpl->tpl_vars['input']->value['empty_message'])) {?>
												<?php echo $_smarty_tpl->tpl_vars['input']->value['empty_message'];?>

											<?php }?>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='bool') {?>
										<div class="col-lg-9">
											<span class="switch prestashop-switch fixed-width-lg">
												<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_on" value="1" <?php if ($_smarty_tpl->tpl_vars['field']->value['value']) {?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js']['on'])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js']['on'];?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['disabled'])&&(bool)$_smarty_tpl->tpl_vars['field']->value['disabled']) {?> disabled="disabled"<?php }?>/><label for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_on" class="radioCheck"><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</label><input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_off" value="0" <?php if (!$_smarty_tpl->tpl_vars['field']->value['value']) {?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js']['off'])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js']['off'];?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['disabled'])&&(bool)$_smarty_tpl->tpl_vars['field']->value['disabled']) {?> disabled="disabled"<?php }?>/><label for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_off" class="radioCheck"><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</label>
												<a class="slide-button btn"></a>
											</span>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='radio') {?>
										<div class="col-lg-9">
											<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['choices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
												<p class="radio">
													<label for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['field']->value['value']) {?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value];?>
<?php }?>/><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</label>
												</p>
											<?php } ?>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='checkbox') {?>
										<div class="col-lg-9">
											<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['choices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
												<p class="checkbox">
													<label class="col-lg-3" for="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
_on"><input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
_on" value="<?php echo intval($_smarty_tpl->tpl_vars['k']->value);?>
"<?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['field']->value['value']) {?> checked="checked"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['js'][$_smarty_tpl->tpl_vars['k']->value];?>
<?php }?>/><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</label>
												</p>
											<?php } ?>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='text') {?>
										<div class="col-lg-9"><?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])) {?><div class="input-group<?php if (isset($_smarty_tpl->tpl_vars['field']->value['class'])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
<?php }?>"><?php }?>
											<input class="form-control <?php if (isset($_smarty_tpl->tpl_vars['field']->value['class'])) {?><?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
<?php }?>" type="<?php echo $_smarty_tpl->tpl_vars['field']->value['type'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['field']->value['id'])) {?> id="<?php echo $_smarty_tpl->tpl_vars['field']->value['id'];?>
"<?php }?> size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])) {?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php } else { ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['no_escape'])&&$_smarty_tpl->tpl_vars['field']->value['no_escape']) {?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['field']->value['value'], 'UTF-8');?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value['value'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['field']->value['autocomplete'])&&!$_smarty_tpl->tpl_vars['field']->value['autocomplete']) {?>autocomplete="off"<?php }?>/>
											<?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])) {?>
											<span class="input-group-addon">
												<?php echo strval($_smarty_tpl->tpl_vars['field']->value['suffix']);?>

											</span>
											<?php }?>
											<?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])) {?></div><?php }?>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='password') {?>
										<div class="col-lg-9"><?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])) {?><div class="input-group<?php if (isset($_smarty_tpl->tpl_vars['field']->value['class'])) {?> <?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
<?php }?>"><?php }?>
											<input type="<?php echo $_smarty_tpl->tpl_vars['field']->value['type'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['field']->value['id'])) {?> id="<?php echo $_smarty_tpl->tpl_vars['field']->value['id'];?>
"<?php }?> size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])) {?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php } else { ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value=""<?php if (isset($_smarty_tpl->tpl_vars['field']->value['autocomplete'])&&!$_smarty_tpl->tpl_vars['field']->value['autocomplete']) {?> autocomplete="off"<?php }?> />
											<?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])) {?>
											<span class="input-group-addon">
												<?php echo strval($_smarty_tpl->tpl_vars['field']->value['suffix']);?>

											</span>
											<?php }?>
											<?php if (isset($_smarty_tpl->tpl_vars['field']->value['suffix'])) {?></div><?php }?>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='textarea') {?>
										<div class="col-lg-9">
											<textarea class="textarea-autosize" name=<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
 cols="<?php echo $_smarty_tpl->tpl_vars['field']->value['cols'];?>
" rows="<?php echo $_smarty_tpl->tpl_vars['field']->value['rows'];?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value['value'], ENT_QUOTES, 'UTF-8', true);?>
</textarea>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='file') {?>
										<div class="col-lg-9"><?php echo $_smarty_tpl->tpl_vars['field']->value['file'];?>
</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='color') {?>
										<div class="col-lg-2">
											<div class="input-group">
												<input type="color" size="<?php echo $_smarty_tpl->tpl_vars['field']->value['size'];?>
" data-hex="true" <?php if (isset($_smarty_tpl->tpl_vars['input']->value['class'])) {?>class="<?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
" <?php } else { ?>class="color mColorPickerInput"<?php }?> name="<?php echo $_smarty_tpl->tpl_vars['field']->value['name'];?>
" class="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['class'])) {?><?php echo $_smarty_tpl->tpl_vars['field']->value['class'];?>
<?php }?>" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value['value'], ENT_QUOTES, 'UTF-8', true);?>
" />
											</div>
							            </div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='price') {?>
										<div class="col-lg-9">
											<div class="input-group fixed-width-lg">
												<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency_left_sign']->value;?>
<?php echo $_smarty_tpl->tpl_vars['currency_right_sign']->value;?>
 <?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>
</span>
												<input type="text" size="<?php if (isset($_smarty_tpl->tpl_vars['field']->value['size'])) {?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['size']);?>
<?php } else { ?>5<?php }?>" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value['value'], ENT_QUOTES, 'UTF-8', true);?>
" />
											</div>
										</div>
									<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='textLang'||$_smarty_tpl->tpl_vars['field']->value['type']=='textareaLang'||$_smarty_tpl->tpl_vars['field']->value['type']=='selectLang') {?>
										<?php if ($_smarty_tpl->tpl_vars['field']->value['type']=='textLang') {?>
											<div class="col-lg-9">
												<div class="row">
												<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_lang']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
													<?php if (count($_smarty_tpl->tpl_vars['field']->value['languages'])>1) {?>
													<div class="translatable-field lang-<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['id_lang']->value!=$_smarty_tpl->tpl_vars['current_id_lang']->value) {?>style="display:none;"<?php }?>>
														<div class="col-lg-9">
													<?php } else { ?>
													<div class="col-lg-12">
													<?php }?>
															<input type="text"
																name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
"
																value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['value']->value, ENT_QUOTES, 'UTF-8', true);?>
"
																<?php if (isset($_smarty_tpl->tpl_vars['input']->value['class'])) {?>class="<?php echo $_smarty_tpl->tpl_vars['input']->value['class'];?>
"<?php }?>
															/>
													<?php if (count($_smarty_tpl->tpl_vars['field']->value['languages'])>1) {?>
														</div>
														<div class="col-lg-2">
															<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
																<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
																	<?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang']==$_smarty_tpl->tpl_vars['id_lang']->value) {?><?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
<?php }?>
																<?php } ?>
																<span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
																<li>
																	<a href="javascript:hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
);"><?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</a>
																</li>
																<?php } ?>
															</ul>
														</div>
													</div>
													<?php } else { ?>
													</div>
													<?php }?>
												<?php } ?>
												</div>
											</div>
										<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='textareaLang') {?>
											<div class="col-lg-9">
												<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_lang']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
													<div class="row translatable-field lang-<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['id_lang']->value!=$_smarty_tpl->tpl_vars['current_id_lang']->value) {?>style="display:none;"<?php }?>>
														<div id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" class="col-lg-9" >
															<textarea class="textarea-autosize" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
"><?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['value']->value,'\r\n',"\n");?>
</textarea>
														</div>
														<div class="col-lg-2">
															<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
																<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
																	<?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang']==$_smarty_tpl->tpl_vars['id_lang']->value) {?><?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
<?php }?>
																<?php } ?>
																<span class="caret"></span>
															</button>
															<ul class="dropdown-menu">
																<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
																<li>
																	<a href="javascript:hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
);"><?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</a>
																</li>
																<?php } ?>
															</ul>
														</div>

													</div>
												<?php } ?>
												<script type="text/javascript">
													$(document).ready(function() {
														$(".textarea-autosize").autosize();
													});
												</script>
											</div>
										<?php } elseif ($_smarty_tpl->tpl_vars['field']->value['type']=='selectLang') {?>
											<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
												<div id="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" style="display: <?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang']==$_smarty_tpl->tpl_vars['current_id_lang']->value) {?>block<?php } else { ?>none<?php }?>;" class="col-lg-9">
													<select name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['language']->value['iso_code'], 'UTF-8');?>
">
														<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['field']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
															<option value="<?php if (isset($_smarty_tpl->tpl_vars['v']->value['cast'])) {?><?php echo $_smarty_tpl->tpl_vars['v']->value['cast'][$_smarty_tpl->tpl_vars['v']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']]];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['v']->value[$_smarty_tpl->tpl_vars['field']->value['identifier']];?>
<?php }?>"
																<?php if ($_smarty_tpl->tpl_vars['field']->value['value'][$_smarty_tpl->tpl_vars['language']->value['id_lang']]==$_smarty_tpl->tpl_vars['v']->value['name']) {?> selected="selected"<?php }?>>
																<?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>

															</option>
														<?php } ?>
													</select>
												</div>
											<?php } ?>
										<?php }?>
									<?php }?>
									<?php if (isset($_smarty_tpl->tpl_vars['field']->value['desc'])&&!empty($_smarty_tpl->tpl_vars['field']->value['desc'])) {?>
									<div class="col-lg-9 col-lg-offset-3">
										<div class="help-block">
											<?php if (is_array($_smarty_tpl->tpl_vars['field']->value['desc'])) {?>
												<?php  $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['p']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['field']->value['desc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['p']->key => $_smarty_tpl->tpl_vars['p']->value) {
$_smarty_tpl->tpl_vars['p']->_loop = true;
?>
													<?php if (is_array($_smarty_tpl->tpl_vars['p']->value)) {?>
														<span id="<?php echo $_smarty_tpl->tpl_vars['p']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['p']->value['text'];?>
</span><br />
													<?php } else { ?>
														<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
<br />
													<?php }?>
												<?php } ?>
											<?php } else { ?>
												<?php echo $_smarty_tpl->tpl_vars['field']->value['desc'];?>

											<?php }?>
										</div>
									</div>
									<?php }?>
								
								<?php if ($_smarty_tpl->tpl_vars['field']->value['is_invisible']) {?>
								<div class="col-lg-9 col-lg-offset-3">
									<p class="alert alert-warning row-margin-top">
										<?php echo smartyTranslate(array('s'=>'You can\'t change the value of this configuration field in the context of this shop.'),$_smarty_tpl);?>

									</p>
								</div>
								<?php }?>
								
							</div>
						</div>
				<?php }?>
			<?php } ?>
			</div><!-- /.form-wrapper -->

			<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['bottom'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['bottom'];?>
<?php }?>
			
				<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit'])||isset($_smarty_tpl->tpl_vars['categoryData']->value['buttons'])) {?>
					<div class="panel-footer">
						<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit'])&&!empty($_smarty_tpl->tpl_vars['categoryData']->value['submit'])) {?>
						<button type="<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['type'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['type'];?>
<?php } else { ?>submit<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['id'])) {?>id="<?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['id'];?>
"<?php }?> class="btn btn-default pull-right" name="<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['name'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['name'];?>
<?php } else { ?>submitOptions<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
<?php }?>"><i class="process-icon-<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['submit']['imgclass'])) {?><?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['imgclass'];?>
<?php } else { ?>save<?php }?>"></i> <?php echo $_smarty_tpl->tpl_vars['categoryData']->value['submit']['title'];?>
</button>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['categoryData']->value['buttons'])) {?>
						<?php  $_smarty_tpl->tpl_vars['btn'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['btn']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categoryData']->value['buttons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['btn']->key => $_smarty_tpl->tpl_vars['btn']->value) {
$_smarty_tpl->tpl_vars['btn']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['btn']->key;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['href'])&&trim($_smarty_tpl->tpl_vars['btn']->value['href'])!='') {?>
							<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['href'], ENT_QUOTES, 'UTF-8', true);?>
" <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['id'])) {?>id="<?php echo $_smarty_tpl->tpl_vars['btn']->value['id'];?>
"<?php }?> class="btn btn-default<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['class'])) {?> <?php echo $_smarty_tpl->tpl_vars['btn']->value['class'];?>
<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['js'])&&$_smarty_tpl->tpl_vars['btn']->value['js']) {?> onclick="<?php echo $_smarty_tpl->tpl_vars['btn']->value['js'];?>
"<?php }?>><?php if (isset($_smarty_tpl->tpl_vars['btn']->value['icon'])) {?><i class="<?php echo $_smarty_tpl->tpl_vars['btn']->value['icon'];?>
" ></i> <?php }?><?php echo $_smarty_tpl->tpl_vars['btn']->value['title'];?>
</a>
						<?php } else { ?>
							<button type="<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['type'])) {?><?php echo $_smarty_tpl->tpl_vars['btn']->value['type'];?>
<?php } else { ?>button<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['id'])) {?>id="<?php echo $_smarty_tpl->tpl_vars['btn']->value['id'];?>
"<?php }?> class="<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['class'])) {?><?php echo $_smarty_tpl->tpl_vars['btn']->value['class'];?>
<?php } else { ?>btn btn-default<?php }?>" name="<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['name'])) {?><?php echo $_smarty_tpl->tpl_vars['btn']->value['name'];?>
<?php } else { ?>submitOptions<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['js'])&&$_smarty_tpl->tpl_vars['btn']->value['js']) {?> onclick="<?php echo $_smarty_tpl->tpl_vars['btn']->value['js'];?>
"<?php }?>><?php if (isset($_smarty_tpl->tpl_vars['btn']->value['icon'])) {?><i class="<?php echo $_smarty_tpl->tpl_vars['btn']->value['icon'];?>
" ></i> <?php }?><?php echo $_smarty_tpl->tpl_vars['btn']->value['title'];?>
</button>
						<?php }?>
						<?php } ?>
						<?php }?>
					</div>
				<?php }?>
			
		</div>
	<?php } ?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminOptions'),$_smarty_tpl);?>

	<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)) {?>
		<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
Options<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

	<?php } elseif (isset($_GET['controller'])) {?>
		<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
Options<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

	<?php }?>
</form>


<?php }} ?>
