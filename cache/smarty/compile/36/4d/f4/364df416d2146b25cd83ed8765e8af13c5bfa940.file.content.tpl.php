<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:41:28
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:302553866568d3caa90cba7-02544580%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '364df416d2146b25cd83ed8765e8af13c5bfa940' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/content.tpl',
      1 => 1452142870,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '302553866568d3caa90cba7-02544580',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d3caa913d83_98322978',
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d3caa913d83_98322978')) {function content_568d3caa913d83_98322978($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
