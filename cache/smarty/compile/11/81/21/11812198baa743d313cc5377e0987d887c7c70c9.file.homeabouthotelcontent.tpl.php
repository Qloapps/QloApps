<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 19:40:42
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkabouthotelblock/views/templates/hook/homeabouthotelcontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:103196394756376ee2a19a77-59453836%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '11812198baa743d313cc5377e0987d887c7c70c9' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkabouthotelblock/views/templates/hook/homeabouthotelcontent.tpl',
      1 => 1446460206,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '103196394756376ee2a19a77-59453836',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'why_hotel_head' => 0,
    'why_hotel_content' => 0,
    'about_hotel_head' => 0,
    'about_hotel_content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee2a88d43_80725435',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee2a88d43_80725435')) {function content_56376ee2a88d43_80725435($_smarty_tpl) {?><div id="abouthotelcontent" class="row">
    <p class="why_hotel_heading">
        <?php echo $_smarty_tpl->tpl_vars['why_hotel_head']->value;?>

    </p>
    <p class="why_hotel_content">
        <?php echo $_smarty_tpl->tpl_vars['why_hotel_content']->value;?>

    </p>
    <hr class="abt_htl_hr">
    <p class="about_hotel_heading">
        <?php echo $_smarty_tpl->tpl_vars['about_hotel_head']->value;?>

    </p>
    <hr class="abt_htl_hr">
    <p class="about_hotel_content">
        <?php echo $_smarty_tpl->tpl_vars['about_hotel_content']->value;?>

    </p>  
</div>
<?php }} ?>
