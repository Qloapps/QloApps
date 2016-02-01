<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 13:14:49
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin_htl/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:131309186456ab8ff9495b78-48377505%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8faf3d41fc81854c4694988f8c1a8c9e833c13c6' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin_htl/themes/default/template/content.tpl',
      1 => 1454061940,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '131309186456ab8ff9495b78-48377505',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab8ff949bd83_78642564',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab8ff949bd83_78642564')) {function content_56ab8ff949bd83_78642564($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
