<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:33
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/tax_rules/helpers/list/list_content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:116376304568df21d8f5f59-64838809%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb997d19111373faa57fe49a51dc807630e2d85c' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/tax_rules/helpers/list/list_content.tpl',
      1 => 1452142912,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '116376304568df21d8f5f59-64838809',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'list' => 0,
    'position_identifier' => 0,
    'id_category' => 0,
    'identifier' => 0,
    'tr' => 0,
    'color_on_bg' => 0,
    'bulk_actions' => 0,
    'has_bulk_actions' => 0,
    'list_skip_actions' => 0,
    'value' => 0,
    'bulkActionPossible' => 0,
    'table' => 0,
    'fields_display' => 0,
    'params' => 0,
    'no_link' => 0,
    'order_by' => 0,
    'current_index' => 0,
    'view' => 0,
    'token' => 0,
    'key' => 0,
    'order_way' => 0,
    'positions' => 0,
    'shop_link_type' => 0,
    'name' => 0,
    'has_actions' => 0,
    'actions' => 0,
    'action' => 0,
    'compiled_actions' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df21da7f670_68056123',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df21da7f670_68056123')) {function content_568df21da7f670_68056123($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_regex_replace')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.regex_replace.php';
?>
<tbody>
	<?php  $_smarty_tpl->tpl_vars['tr'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tr']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tr']->key => $_smarty_tpl->tpl_vars['tr']->value) {
$_smarty_tpl->tpl_vars['tr']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['tr']->key;
?>
		<tr
		<?php if ($_smarty_tpl->tpl_vars['position_identifier']->value) {?>id="tr_<?php echo $_smarty_tpl->tpl_vars['id_category']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['identifier']->value];?>
_<?php echo $_smarty_tpl->tpl_vars['tr']->value['position']['position'];?>
"<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['tr']->value['color'])&&$_smarty_tpl->tpl_vars['color_on_bg']->value) {?>style="background-color: <?php echo $_smarty_tpl->tpl_vars['tr']->value['color'];?>
"<?php }?> >
			<?php if ($_smarty_tpl->tpl_vars['bulk_actions']->value&&$_smarty_tpl->tpl_vars['has_bulk_actions']->value) {?>
				<td class="text-center">
					<?php $_smarty_tpl->tpl_vars['bulkActionPossible'] = new Smarty_variable(true, null, 0);?>
					<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list_skip_actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
						<?php if (in_array($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['identifier']->value],$_smarty_tpl->tpl_vars['value']->value)==true) {?>
							<?php $_smarty_tpl->tpl_vars['bulkActionPossible'] = new Smarty_variable(false, null, 0);?>
						<?php }?>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['bulkActionPossible']->value==true) {?>
						<input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Box[]" value="<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['identifier']->value];?>
" class="noborder" />
					<?php }?>
				</td>
			<?php }?>
			<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value) {
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
				<td
					<?php if (isset($_smarty_tpl->tpl_vars['params']->value['position'])) {?>
						id="td_<?php if ($_smarty_tpl->tpl_vars['id_category']->value) {?><?php echo $_smarty_tpl->tpl_vars['id_category']->value;?>
<?php } else { ?>0<?php }?>_<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['identifier']->value];?>
"
					<?php }?>
					class="<?php if (!$_smarty_tpl->tpl_vars['no_link']->value) {?>pointer<?php }?><?php if (isset($_smarty_tpl->tpl_vars['params']->value['position'])&&$_smarty_tpl->tpl_vars['order_by']->value=='position') {?> dragHandle<?php }?><?php if (isset($_smarty_tpl->tpl_vars['params']->value['align'])) {?> <?php echo $_smarty_tpl->tpl_vars['params']->value['align'];?>
<?php }?>"

				<?php if ((!isset($_smarty_tpl->tpl_vars['params']->value['position'])&&!$_smarty_tpl->tpl_vars['no_link']->value)) {?>
					onclick="document.location = '<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current_index']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['identifier']->value, ENT_QUOTES, 'UTF-8', true);?>
=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['identifier']->value], ENT_QUOTES, 'UTF-8', true);?>
<?php if ($_smarty_tpl->tpl_vars['view']->value) {?>&amp;view<?php } else { ?>&amp;update<?php }?><?php echo $_smarty_tpl->tpl_vars['table']->value;?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
'"><?php if (isset($_smarty_tpl->tpl_vars['params']->value['prefix'])) {?><?php echo $_smarty_tpl->tpl_vars['params']->value['prefix'];?>
<?php }?>
				<?php } else { ?>
					>
				<?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['params']->value['active'])) {?>
				    <?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['activeVisu'])) {?>
					<img src="../img/admin/<?php if ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]) {?>enabled.gif<?php } else { ?>disabled.gif<?php }?>"
					alt="<?php if ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]) {?><?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
<?php }?>" title="<?php if ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]) {?><?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
<?php }?>" />
				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['position'])) {?>
					<?php if ($_smarty_tpl->tpl_vars['order_by']->value=='position'&&$_smarty_tpl->tpl_vars['order_way']->value!='DESC') {?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]['position_url_down'];?>
" <?php if (!($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]['position']!=$_smarty_tpl->tpl_vars['positions']->value[count($_smarty_tpl->tpl_vars['positions']->value)-1])) {?>style="display: none;"<?php }?>>
							<img src="../img/admin/<?php if ($_smarty_tpl->tpl_vars['order_way']->value=='ASC') {?>down<?php } else { ?>up<?php }?>.gif" alt="<?php echo smartyTranslate(array('s'=>'Down'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Down'),$_smarty_tpl);?>
" />
						</a>

						<a href="<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]['position_url_up'];?>
" <?php if (!($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]['position']!=$_smarty_tpl->tpl_vars['positions']->value[0])) {?>style="display: none;"<?php }?>>
							<img src="../img/admin/<?php if ($_smarty_tpl->tpl_vars['order_way']->value=='ASC') {?>up<?php } else { ?>down<?php }?>.gif" alt="<?php echo smartyTranslate(array('s'=>'Up'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Up'),$_smarty_tpl);?>
" />
						</a>
					<?php } else { ?>
						<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]['position']+1;?>

					<?php }?>
				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['image'])) {?>
					<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif ((isset($_smarty_tpl->tpl_vars['params']->value['icon']))) {?>
					<img src="../img/admin/<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>
" title="<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>
" />
	            <?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['price'])) {?>
					<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['float'])) {?>
					<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['type'])&&$_smarty_tpl->tpl_vars['params']->value['type']=='date') {?>
					<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['type'])&&$_smarty_tpl->tpl_vars['params']->value['type']=='datetime') {?>
					<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif (isset($_smarty_tpl->tpl_vars['params']->value['callback'])) {?>
					<?php echo $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value];?>

				<?php } elseif (isset($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value])) {?>
					<?php if ($_smarty_tpl->tpl_vars['key']->value=='behavior') {?>
						<?php if ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]==0) {?>
							<?php echo smartyTranslate(array('s'=>'This tax only'),$_smarty_tpl);?>

						<?php } elseif ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]==1) {?>
							<?php echo smartyTranslate(array('s'=>'Combine'),$_smarty_tpl);?>

						<?php } elseif ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]==2) {?>
							<?php echo smartyTranslate(array('s'=>'One after another'),$_smarty_tpl);?>

						<?php }?>
					<?php } elseif ($_smarty_tpl->tpl_vars['key']->value=='rate') {?>
						<?php echo sprintf("%.3f",$_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]);?>
%
					<?php } elseif ($_smarty_tpl->tpl_vars['key']->value=='zipcode') {?>
						<?php if ($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value]=='0 - 0') {?>
							--
						<?php } else { ?>
							<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value], ENT_QUOTES, 'UTF-8', true);?>

						<?php }?>
					<?php } else { ?>
						<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['key']->value], ENT_QUOTES, 'UTF-8', true);?>

					<?php }?>
				<?php } else { ?>
					--
				<?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['params']->value['suffix'])) {?><?php echo $_smarty_tpl->tpl_vars['params']->value['suffix'];?>
<?php }?>
				</td>
			<?php } ?>

		<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value) {?>
			<td <?php if ($_smarty_tpl->tpl_vars['name']->value!=$_smarty_tpl->tpl_vars['tr']->value['shop_name']) {?>title="<?php echo $_smarty_tpl->tpl_vars['tr']->value['shop_name'];?>
"<?php }?>><?php if (isset($_smarty_tpl->tpl_vars['tr']->value['shop_short_name'])) {?><?php echo $_smarty_tpl->tpl_vars['tr']->value['shop_short_name'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['tr']->value['shop_name'];?>
<?php }?></td>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['has_actions']->value) {?>
			<td class="text-right fixed-width-lg">
				<?php $_smarty_tpl->tpl_vars['compiled_actions'] = new Smarty_variable(array(), null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['action'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['action']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['action']->key => $_smarty_tpl->tpl_vars['action']->value) {
$_smarty_tpl->tpl_vars['action']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['action']->key;
?>
					<?php if (isset($_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['action']->value])) {?>
						<?php if ($_smarty_tpl->tpl_vars['key']->value==0) {?>
							<?php $_smarty_tpl->tpl_vars['action'] = new Smarty_variable($_smarty_tpl->tpl_vars['action']->value, null, 0);?>
						<?php }?>
						<?php $_smarty_tpl->createLocalArrayVariable('compiled_actions', null, 0);
$_smarty_tpl->tpl_vars['compiled_actions']->value[] = $_smarty_tpl->tpl_vars['tr']->value[$_smarty_tpl->tpl_vars['action']->value];?>
					<?php }?>
				<?php } ?>
				<?php if (count($_smarty_tpl->tpl_vars['compiled_actions']->value)>0) {?>
					<?php if (count($_smarty_tpl->tpl_vars['compiled_actions']->value)>1) {?><div class="btn-group-action"><?php }?>
					<div class="btn-group pull-right">
						<?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['compiled_actions']->value[0],'/class\s*=\s*"(\w*)"/','class="$1 btn btn-default"');?>

						<?php if (count($_smarty_tpl->tpl_vars['compiled_actions']->value)>1) {?>
						<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<span class="caret"></span>&nbsp;
						</button>
							<ul class="dropdown-menu">
							<?php  $_smarty_tpl->tpl_vars['action'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['action']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['compiled_actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['action']->key => $_smarty_tpl->tpl_vars['action']->value) {
$_smarty_tpl->tpl_vars['action']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['action']->key;
?>
								<?php if ($_smarty_tpl->tpl_vars['key']->value!=0) {?>
								<li>
									<?php echo $_smarty_tpl->tpl_vars['action']->value;?>

								</li>
								<?php }?>
							<?php } ?>
							</ul>
						<?php }?>
					</div>
					<?php if (count($_smarty_tpl->tpl_vars['compiled_actions']->value)>1) {?></div><?php }?>
				<?php }?>
			</td>
		<?php }?>
		</tr>
	<?php } ?>
</tbody>
<?php }} ?>
