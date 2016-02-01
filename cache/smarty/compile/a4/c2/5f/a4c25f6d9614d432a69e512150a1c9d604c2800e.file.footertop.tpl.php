<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 05:21:48
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/modules/hotelreservationsystem/views/templates/hook/footertop.tpl" */ ?>
<?php /*%%SmartyHeaderCode:117857171856ab3d3c51d6e8-78340028%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a4c25f6d9614d432a69e512150a1c9d604c2800e' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/modules/hotelreservationsystem/views/templates/hook/footertop.tpl',
      1 => 1454061952,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '117857171856ab3d3c51d6e8-78340028',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'hotel_global_contact_num' => 0,
    'hotel_global_email' => 0,
    'HOOK_FOOTER_TOP' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3d3c542368_71543405',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3d3c542368_71543405')) {function content_56ab3d3c542368_71543405($_smarty_tpl) {?><div class="row margin-lr-0" id="footer_top">
	<div class="col-xs-12 col-sm-6 col-lg-2 htl_admin_dtl">
		<?php echo smartyTranslate(array('s'=>'Call Us : ','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 
		<?php if (isset($_smarty_tpl->tpl_vars['hotel_global_contact_num']->value)&&'hotel_global_contact_num') {?>
			<?php echo $_smarty_tpl->tpl_vars['hotel_global_contact_num']->value;?>

		<?php }?>
	</div>
    <div class="col-xs-12 col-sm-6 col-lg-4 htl_admin_dtl">
    	<?php echo smartyTranslate(array('s'=>'Email : ','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

    	<?php if (isset($_smarty_tpl->tpl_vars['hotel_global_email']->value)&&'hotel_global_email') {?>
    		<?php echo $_smarty_tpl->tpl_vars['hotel_global_email']->value;?>

    	<?php }?>
    </div>
	<?php echo $_smarty_tpl->tpl_vars['HOOK_FOOTER_TOP']->value;?>

</div>

<style type="text/css">
    #footer_top
    {
        background-color: #bf9958;
    }

    .htl_admin_dtl
    {
    	text-align: center;
        font-size:18px;
        color: #ffffff;
        padding-top: 25px; 
    }
	@media (max-width: 400px) {
		.htl_admin_dtl {
			font-size: 15px;
		}
    }
</style><?php }} ?>
