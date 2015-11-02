<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:44
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/carrier_wizard/helpers/form/form_ranges.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19968395765637740c5bb1a3-80741716%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7c2a77efbd078066b588152ab570c84faf1bc14c' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/carrier_wizard/helpers/form/form_ranges.tpl',
      1 => 1446455073,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19968395765637740c5bb1a3-80741716',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'zones' => 0,
    'ranges' => 0,
    'PS_WEIGHT_UNIT' => 0,
    'currency_sign' => 0,
    'range' => 0,
    'form_id' => 0,
    'change_ranges' => 0,
    'zone' => 0,
    'fields_value' => 0,
    'price_by_range' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5637740c67a9d0_48233261',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5637740c67a9d0_48233261')) {function content_5637740c67a9d0_48233261($_smarty_tpl) {?>		<script>var zones_nbr = <?php echo count($_smarty_tpl->tpl_vars['zones']->value)+3;?>
 ; /*corresponds to the third input text (max, min and all)*/</script>
		<div id="zone_ranges" style="overflow:auto">
			<h4><?php echo smartyTranslate(array('s'=>'Ranges'),$_smarty_tpl);?>
</h4>
			<table id="zones_table" class="table" style="max-width:100%">
				<tbody>
					<tr class="range_inf">
						<td class="range_type"></td>
						<td class="border_left border_bottom range_sign">&gt;=</td>
						<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value) {
$_smarty_tpl->tpl_vars['range']->_loop = true;
 $_smarty_tpl->tpl_vars['r']->value = $_smarty_tpl->tpl_vars['range']->key;
?>
						<td class="border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit"><?php echo $_smarty_tpl->tpl_vars['PS_WEIGHT_UNIT']->value;?>
</span>
								<span class="input-group-addon price_unit"><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input class="form-control" name="range_inf[<?php echo intval($_smarty_tpl->tpl_vars['range']->value['id_range']);?>
]" type="text" value="<?php echo sprintf("%.6f",$_smarty_tpl->tpl_vars['range']->value['delimiter1']);?>
" />
							</div>
						</td>
						<?php }
if (!$_smarty_tpl->tpl_vars['range']->_loop) {
?>
						<td class="border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit"><?php echo $_smarty_tpl->tpl_vars['PS_WEIGHT_UNIT']->value;?>
</span>
								<span class="input-group-addon price_unit"><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input name="form-control range_inf[<?php echo intval($_smarty_tpl->tpl_vars['range']->value['id_range']);?>
]" type="text" />
							</div>
						</td>
						<?php } ?>
					</tr>
					<tr class="range_sup">
						<td class="range_type"></td>
						<td class="border_left range_sign">&lt;</td>
						<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value) {
$_smarty_tpl->tpl_vars['range']->_loop = true;
 $_smarty_tpl->tpl_vars['r']->value = $_smarty_tpl->tpl_vars['range']->key;
?>
						<td class="range_data">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit"><?php echo $_smarty_tpl->tpl_vars['PS_WEIGHT_UNIT']->value;?>
</span>
								<span class="input-group-addon price_unit"><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input class="form-control" name="range_sup[<?php echo intval($_smarty_tpl->tpl_vars['range']->value['id_range']);?>
]" type="text" <?php if (isset($_smarty_tpl->tpl_vars['form_id']->value)&&!$_smarty_tpl->tpl_vars['form_id']->value) {?> value="" <?php } else { ?> value="<?php if (isset($_smarty_tpl->tpl_vars['change_ranges']->value)&&$_smarty_tpl->tpl_vars['range']->value['id_range']==0) {?> <?php } else { ?><?php echo sprintf("%.6f",$_smarty_tpl->tpl_vars['range']->value['delimiter2']);?>
<?php }?>" <?php }?> autocomplete="off"/>
							</div>
						</td>
						<?php }
if (!$_smarty_tpl->tpl_vars['range']->_loop) {
?>
						<td class="range_data_new">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit"><?php echo $_smarty_tpl->tpl_vars['PS_WEIGHT_UNIT']->value;?>
</span>
								<span class="input-group-addon price_unit"><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input class="form-control" name="range_sup[<?php echo intval($_smarty_tpl->tpl_vars['range']->value['id_range']);?>
]" type="text" autocomplete="off" />
							</div>
						</td>
						<?php } ?>
					</tr>
					<tr class="fees_all">
						<td class="border_top border_bottom border_bold">
							<span class="fees_all" <?php if (count($_smarty_tpl->tpl_vars['ranges']->value)==0) {?>style="display:none" <?php }?>>All</span>
						</td>
						<td style="">
							<input type="checkbox" onclick="checkAllZones(this);" class="form-control">
						</td>
						<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value) {
$_smarty_tpl->tpl_vars['range']->_loop = true;
 $_smarty_tpl->tpl_vars['r']->value = $_smarty_tpl->tpl_vars['range']->key;
?>
						<td class="border_top border_bottom <?php if ($_smarty_tpl->tpl_vars['range']->value['id_range']!=0) {?> validated <?php }?>"  >
							<div class="input-group fixed-width-md">
								<span class="input-group-addon currency_sign" <?php if ($_smarty_tpl->tpl_vars['range']->value['id_range']==0) {?> style="display:none" <?php }?>><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input class="form-control" type="text" <?php if (isset($_smarty_tpl->tpl_vars['form_id']->value)&&!$_smarty_tpl->tpl_vars['form_id']->value) {?> disabled="disabled"<?php }?> <?php if ($_smarty_tpl->tpl_vars['range']->value['id_range']==0) {?> style="display:none"<?php }?> autocomplete="off" />
							</div>
						</td>
						<?php }
if (!$_smarty_tpl->tpl_vars['range']->_loop) {
?>
						<td class="border_top border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon currency_sign" style="display:none"><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input class="form-control" style="display:none" type="text" autocomplete="off" />
							</div>
						</td>
						<?php } ?>
					</tr>
					<?php  $_smarty_tpl->tpl_vars['zone'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['zone']->_loop = false;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['zones']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['zone']->key => $_smarty_tpl->tpl_vars['zone']->value) {
$_smarty_tpl->tpl_vars['zone']->_loop = true;
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['zone']->key;
?>
					<tr class="fees" data-zoneid="<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
">
						<td>
							<label for="zone_<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
"><?php echo $_smarty_tpl->tpl_vars['zone']->value['name'];?>
</label>
						</td>
						<td class="zone">
							<input class="form-control input_zone" id="zone_<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
" name="zone_<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
" value="1" type="checkbox" <?php if (isset($_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']])&&$_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']]) {?> checked="checked"<?php }?>/>
						</td>
						<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value) {
$_smarty_tpl->tpl_vars['range']->_loop = true;
 $_smarty_tpl->tpl_vars['r']->value = $_smarty_tpl->tpl_vars['range']->key;
?>
						<td>
							<div class="input-group fixed-width-md">
								<span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
</span>
								<input class="form-control" name="fees[<?php echo intval($_smarty_tpl->tpl_vars['zone']->value['id_zone']);?>
][<?php echo intval($_smarty_tpl->tpl_vars['range']->value['id_range']);?>
]" type="text"
								<?php if (!isset($_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']])||(isset($_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']])&&!$_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']])) {?> disabled="disabled"<?php }?>

								<?php if (isset($_smarty_tpl->tpl_vars['price_by_range']->value[$_smarty_tpl->tpl_vars['range']->value['id_range']][$_smarty_tpl->tpl_vars['zone']->value['id_zone']])&&$_smarty_tpl->tpl_vars['price_by_range']->value[$_smarty_tpl->tpl_vars['range']->value['id_range']][$_smarty_tpl->tpl_vars['zone']->value['id_zone']]&&isset($_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']])&&$_smarty_tpl->tpl_vars['fields_value']->value['zones'][$_smarty_tpl->tpl_vars['zone']->value['id_zone']]) {?> value="<?php echo sprintf('%.6f',$_smarty_tpl->tpl_vars['price_by_range']->value[$_smarty_tpl->tpl_vars['range']->value['id_range']][$_smarty_tpl->tpl_vars['zone']->value['id_zone']]);?>
" <?php } else { ?> value="" <?php }?> />
							</div>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
					<tr class="delete_range">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['range']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value) {
$_smarty_tpl->tpl_vars['range']->_loop = true;
 $_smarty_tpl->tpl_vars['r']->value = $_smarty_tpl->tpl_vars['range']->key;
 $_smarty_tpl->tpl_vars['range']->index++;
 $_smarty_tpl->tpl_vars['range']->first = $_smarty_tpl->tpl_vars['range']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['ranges']['first'] = $_smarty_tpl->tpl_vars['range']->first;
?>
							<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['ranges']['first']) {?>
								<td>&nbsp;</td>
							<?php } else { ?>
								<td>
									<button class="btn btn-default"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</button>
								</td>
							<?php }?>
						<?php } ?>
					</tr>
				</tbody>
			</table>
		</div>
<?php }} ?>
