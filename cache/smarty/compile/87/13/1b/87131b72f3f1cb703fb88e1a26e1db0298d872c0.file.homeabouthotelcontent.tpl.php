<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 05:21:47
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/modules/wkabouthotelblock/views/templates/hook/homeabouthotelcontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:50278746756ab3d3bee5499-26369228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87131b72f3f1cb703fb88e1a26e1db0298d872c0' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/modules/wkabouthotelblock/views/templates/hook/homeabouthotelcontent.tpl',
      1 => 1454061954,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '50278746756ab3d3bee5499-26369228',
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
  'unifunc' => 'content_56ab3d3beedac9_09408200',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3d3beedac9_09408200')) {function content_56ab3d3beedac9_09408200($_smarty_tpl) {?><div id="abouthotelcontent" class="row">
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
