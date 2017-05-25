<?php /* Smarty version Smarty-3.1.19, created on 2016-10-14 12:50:29
         compiled from "/home/rjain/www/html/d4/htld6/adminhtl/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2995733205800873df35ec4-53956342%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c9b0f6e10e5edd2753a4a13ffaee94ec9a2fa942' => 
    array (
      0 => '/home/rjain/www/html/d4/htld6/adminhtl/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1475505998,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2995733205800873df35ec4-53956342',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5800873df38080_17435765',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5800873df38080_17435765')) {function content_5800873df38080_17435765($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><?php echo smartyTranslate(array('s'=>'Recommended Modules and Services'),$_smarty_tpl);?>
</h3>
			</div>
			<div class="modal-body">
				<div id="modules_list_container_tab_modal" style="display:none;"></div>
				<div id="modules_list_loader"><i class="icon-refresh icon-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
