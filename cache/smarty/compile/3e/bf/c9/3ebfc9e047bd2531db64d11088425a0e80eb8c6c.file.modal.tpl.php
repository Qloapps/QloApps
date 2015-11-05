<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 16:03:53
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin945qmi5wu/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:604633897563b309109a021-38549980%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3ebfc9e047bd2531db64d11088425a0e80eb8c6c' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin945qmi5wu/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '604633897563b309109a021-38549980',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b309109c464_98715052',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b309109c464_98715052')) {function content_563b309109c464_98715052($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
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
