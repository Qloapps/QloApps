<?php /* Smarty version Smarty-3.1.19, created on 2016-01-30 00:51:54
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin977xro9m8/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:156602387356ab8a9a11e6b0-02637469%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '51444dba4d6e1eb1c679cf730873dd95c4f25a8b' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin977xro9m8/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1454062117,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '156602387356ab8a9a11e6b0-02637469',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab8a9a146a70_00981708',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab8a9a146a70_00981708')) {function content_56ab8a9a146a70_00981708($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
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
