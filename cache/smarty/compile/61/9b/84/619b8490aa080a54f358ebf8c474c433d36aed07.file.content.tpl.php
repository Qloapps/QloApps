<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:59
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2104564073563b5653a9a6f4-38562178%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '619b8490aa080a54f358ebf8c474c433d36aed07' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/content.tpl',
      1 => 1446729261,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2104564073563b5653a9a6f4-38562178',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b5653aa1cd1_64728213',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b5653aa1cd1_64728213')) {function content_563b5653aa1cd1_64728213($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
