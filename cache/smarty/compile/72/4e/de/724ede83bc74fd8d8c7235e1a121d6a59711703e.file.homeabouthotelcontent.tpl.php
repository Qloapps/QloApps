<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:10:35
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkabouthotelblock/views/templates/hook/homeabouthotelcontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:238531431568d302a994883-10937470%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '724ede83bc74fd8d8c7235e1a121d6a59711703e' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkabouthotelblock/views/templates/hook/homeabouthotelcontent.tpl',
      1 => 1452142879,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '238531431568d302a994883-10937470',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d302a99b7e8_18013560',
  'variables' => 
  array (
    'why_hotel_head' => 0,
    'why_hotel_content' => 0,
    'about_hotel_head' => 0,
    'about_hotel_content' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d302a99b7e8_18013560')) {function content_568d302a99b7e8_18013560($_smarty_tpl) {?><div id="abouthotelcontent" class="row">
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
