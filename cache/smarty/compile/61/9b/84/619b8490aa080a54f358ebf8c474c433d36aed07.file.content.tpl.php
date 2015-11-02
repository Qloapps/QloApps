<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:47
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1607380185637740f4c3b76-32178409%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '619b8490aa080a54f358ebf8c474c433d36aed07' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/content.tpl',
      1 => 1446454904,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1607380185637740f4c3b76-32178409',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5637740f4cb528_96896626',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5637740f4cb528_96896626')) {function content_5637740f4cb528_96896626($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
