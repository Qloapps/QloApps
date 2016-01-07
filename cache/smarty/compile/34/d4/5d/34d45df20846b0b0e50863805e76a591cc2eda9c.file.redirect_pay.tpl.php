<?php /* Smarty version Smarty-3.1.19, created on 2016-01-06 20:18:57
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/front/redirect_pay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1146692343568d2959a7e137-82821123%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '34d45df20846b0b0e50863805e76a591cc2eda9c' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkpaypaladaptive/views/templates/front/redirect_pay.tpl',
      1 => 1451999532,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1146692343568d2959a7e137-82821123',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'payPalURL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d2959a856b4_06609493',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d2959a856b4_06609493')) {function content_568d2959a856b4_06609493($_smarty_tpl) {?><div class="col-md-12 text-center">
	<h1><?php echo smartyTranslate(array('s'=>'If you are not redirected within 10 seconds...','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
</h1>
	<a class="btn btn-primary" id="paypalredirect" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payPalURL']->value, ENT_QUOTES, 'UTF-8', true);?>
">
		<span><?php echo smartyTranslate(array('s'=>'Click here','mod'=>'wkpaypaladaptive'),$_smarty_tpl);?>
</span>
	</a>
</div>
<script type="text/javascript">
	function redirect(){
		document.getElementById("paypalredirect").click();
	}
	setTimeout(redirect, 2000);
</script><?php }} ?>
