<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:40
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/front/paypal_error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1610668208568d3053110454-94180279%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '630bbf17b52171dd0ec215eb5498b50011d311a1' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/front/paypal_error.tpl',
      1 => 1452142878,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1610668208568d3053110454-94180279',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d30531587a8_21988379',
  'variables' => 
  array (
    'error_code' => 0,
    'error_msg' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d30531587a8_21988379')) {function content_568d30531587a8_21988379($_smarty_tpl) {?><div class="row">
	<div class="col-lg-12">
		<div class="alert alert-danger">
			<?php echo smartyTranslate(array('s'=>'Error Code','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
 : <?php echo intval($_smarty_tpl->tpl_vars['error_code']->value);?>
<br/>
			<?php echo smartyTranslate(array('s'=>'Message','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
 : <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['error_msg']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

		</div>
	</div>
</div><?php }} ?>
