<?php /* Smarty version Smarty-3.1.19, created on 2016-01-30 01:02:45
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin652sof9ht/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:103217551856ab8d257909c7-73251602%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bde4437b1428cb3779526e9df83149e4294ecd7f' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin652sof9ht/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1454062117,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '103217551856ab8d257909c7-73251602',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab8d25792c86_95025516',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab8d25792c86_95025516')) {function content_56ab8d25792c86_95025516($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
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
