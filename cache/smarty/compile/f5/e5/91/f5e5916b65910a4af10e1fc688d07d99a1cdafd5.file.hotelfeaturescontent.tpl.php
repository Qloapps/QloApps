<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 19:40:42
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkhotelfeaturesblock/views/templates/hook/hotelfeaturescontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:200812843556376ee2b2a1b4-09423547%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f5e5916b65910a4af10e1fc688d07d99a1cdafd5' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkhotelfeaturesblock/views/templates/hook/hotelfeaturescontent.tpl',
      1 => 1446460206,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '200812843556376ee2b2a1b4-09423547',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'main_blog_data' => 0,
    'features_data' => 0,
    'data' => 0,
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee2bad493_13912032',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee2bad493_13912032')) {function content_56376ee2bad493_13912032($_smarty_tpl) {?><hr style="height:1px;background-color:#999">
<div id="features_block" class="row">
  <p class="hotel_feature_heading">
    <?php if (isset($_smarty_tpl->tpl_vars['main_blog_data']->value['blog_heading'])) {?><?php echo $_smarty_tpl->tpl_vars['main_blog_data']->value['blog_heading'];?>
<?php }?>
  </p>
  <p class="hotel_feature_content">
    <?php if (isset($_smarty_tpl->tpl_vars['main_blog_data']->value['blog_description'])) {?><?php echo $_smarty_tpl->tpl_vars['main_blog_data']->value['blog_description'];?>
<?php }?>
  </p>
  <div class="features_container">
    <?php if (isset($_smarty_tpl->tpl_vars['features_data']->value)&&$_smarty_tpl->tpl_vars['features_data']->value) {?> 
      <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['features_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
        <div class="col-sm-4 single_feature_container">
          <div>
            <?php if (isset($_smarty_tpl->tpl_vars['data']->value['feature_image'])&&$_smarty_tpl->tpl_vars['data']->value['feature_image']) {?>
              <img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/<?php echo $_smarty_tpl->tpl_vars['data']->value['feature_image'];?>
">
            <?php } else { ?>
              <img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/default.jpg">
            <?php }?>
          </div>
          <div class="feature_head"><?php echo $_smarty_tpl->tpl_vars['data']->value['feature_title'];?>
</div>
          <div class="feature_content"><?php echo $_smarty_tpl->tpl_vars['data']->value['feature_description'];?>
</div>
        </div>
      <?php } ?>
    <?php }?>  
  </div>
</div><?php }} ?>
