<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 05:48:33
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin757uelgkq/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:114391413256389101c7b628-94098776%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '395e4cb6c8a9b9b8fe6abe955ca0e146a53ea8b8' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin757uelgkq/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '114391413256389101c7b628-94098776',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56389101c8ce78_69616070',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56389101c8ce78_69616070')) {function content_56389101c8ce78_69616070($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
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
