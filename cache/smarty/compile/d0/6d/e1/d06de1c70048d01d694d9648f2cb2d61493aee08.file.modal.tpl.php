<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 17:14:08
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin382ubvnd2/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1551624714563b4108763154-83108998%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd06de1c70048d01d694d9648f2cb2d61493aee08' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin382ubvnd2/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1446723175,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1551624714563b4108763154-83108998',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b41087654c4_81266054',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b41087654c4_81266054')) {function content_563b41087654c4_81266054($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
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
