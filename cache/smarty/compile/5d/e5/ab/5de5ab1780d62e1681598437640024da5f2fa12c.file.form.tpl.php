<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:30
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/cart_rules/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2039155729568df21a5d2424-28155461%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5de5ab1780d62e1681598437640024da5f2fa12c' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/cart_rules/form.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2039155729568df21a5d2424-28155461',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currentIndex' => 0,
    'currentToken' => 0,
    'currentObject' => 0,
    'table' => 0,
    'product_rule_groups_counter' => 0,
    'languages' => 0,
    'k' => 0,
    'language' => 0,
    'id_lang_default' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df21a613ee3_69202892',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df21a613ee3_69202892')) {function content_568df21a613ee3_69202892($_smarty_tpl) {?><div class="panel">
	<h3><i class="icon-tag"></i> <?php echo smartyTranslate(array('s'=>'Cart rule'),$_smarty_tpl);?>
</h3>
	<div class="productTabs">
		<ul class="tab nav nav-tabs">
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_informations" href="javascript:displayCartRuleTab('informations');"><i class="icon-info"></i> <?php echo smartyTranslate(array('s'=>'Information'),$_smarty_tpl);?>
</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_conditions" href="javascript:displayCartRuleTab('conditions');"><i class="icon-random"></i> <?php echo smartyTranslate(array('s'=>'Conditions'),$_smarty_tpl);?>
</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_actions" href="javascript:displayCartRuleTab('actions');"><i class="icon-wrench"></i> <?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</a>
			</li>
		</ul>
	</div>
	<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentToken']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;addcart_rule" id="cart_rule_form" class="form-horizontal" method="post">
		<?php if ($_smarty_tpl->tpl_vars['currentObject']->value->id) {?><input type="hidden" name="id_cart_rule" value="<?php echo intval($_smarty_tpl->tpl_vars['currentObject']->value->id);?>
" /><?php }?>
		<input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
		<div id="cart_rule_informations" class="panel cart_rule_tab">
			<?php echo $_smarty_tpl->getSubTemplate ('controllers/cart_rules/informations.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

		</div>
		<div id="cart_rule_conditions" class="panel cart_rule_tab">
			<?php echo $_smarty_tpl->getSubTemplate ('controllers/cart_rules/conditions.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

		</div>
		<div id="cart_rule_actions" class="panel cart_rule_tab">
			<?php echo $_smarty_tpl->getSubTemplate ('controllers/cart_rules/actions.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

		</div>
		<button type="submit" class="btn btn-default pull-right" name="submitAddcart_rule" id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['table']->value, ENT_QUOTES, 'UTF-8', true);?>
_form_submit_btn"><?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>

		</button>
		<!--<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>
" class="button" name="submitAddcart_ruleAndStay" id="" />-->
	</form>

	<script type="text/javascript">
		var product_rule_groups_counter = <?php if (isset($_smarty_tpl->tpl_vars['product_rule_groups_counter']->value)) {?><?php echo intval($_smarty_tpl->tpl_vars['product_rule_groups_counter']->value);?>
<?php } else { ?>0<?php }?>;
		var product_rule_counters = new Array();
		var currentToken = '<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['currentToken']->value);?>
';
		var currentFormTab = '<?php if (isset($_POST['currentFormTab'])) {?><?php echo preg_replace("%(?<!\\\\)'%", "\'",$_POST['currentFormTab']);?>
<?php } else { ?>informations<?php }?>';
		var currentText = '<?php echo smartyTranslate(array('s'=>'Now','js'=>1),$_smarty_tpl);?>
';
		var closeText = '<?php echo smartyTranslate(array('s'=>'Done','js'=>1),$_smarty_tpl);?>
';
		var timeOnlyTitle = '<?php echo smartyTranslate(array('s'=>'Choose Time','js'=>1),$_smarty_tpl);?>
';
		var timeText = '<?php echo smartyTranslate(array('s'=>'Time','js'=>1),$_smarty_tpl);?>
';
		var hourText = '<?php echo smartyTranslate(array('s'=>'Hour','js'=>1),$_smarty_tpl);?>
';
		var minuteText = '<?php echo smartyTranslate(array('s'=>'Minute','js'=>1),$_smarty_tpl);?>
';
		
		var languages = new Array();
		<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
			languages[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
] = {
				id_lang: <?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
,
				iso_code: '<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['language']->value['iso_code']);?>
',
				name: '<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['language']->value['name']);?>
'
			};
		<?php } ?>
		displayFlags(languages, <?php echo $_smarty_tpl->tpl_vars['id_lang_default']->value;?>
);
	</script>
	<script type="text/javascript" src="themes/default/template/controllers/cart_rules/form.js"></script>
	<?php echo $_smarty_tpl->getSubTemplate ("footer_toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

</div>
<?php }} ?>
