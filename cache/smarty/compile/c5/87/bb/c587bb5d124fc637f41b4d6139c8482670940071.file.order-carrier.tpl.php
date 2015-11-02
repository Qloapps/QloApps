<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 11:08:15
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/order-carrier.tpl" */ ?>
<?php /*%%SmartyHeaderCode:173375224956378a6fa869b2-31612387%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c587bb5d124fc637f41b4d6139c8482670940071' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/order-carrier.tpl',
      1 => 1446454876,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '173375224956378a6fa869b2-31612387',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'checkedTOS' => 0,
    'link_conditions' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56378a6fa90bc7_69473915',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56378a6fa90bc7_69473915')) {function content_56378a6fa90bc7_69473915($_smarty_tpl) {?><div class="box" id="tc_cont">
    <p class="checkbox">
        <input type="checkbox" name="cgv" id="cgv" value="1" <?php if ($_smarty_tpl->tpl_vars['checkedTOS']->value) {?>checked="checked"<?php }?> />
        <label for="cgv" id="tc_txt"><?php echo smartyTranslate(array('s'=>'I agree to the terms of service and will adhere to them unconditionally.'),$_smarty_tpl);?>
</label>
        <a id="tc_link" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link_conditions']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="iframe" rel="nofollow" ><?php echo smartyTranslate(array('s'=>'(Read the Terms of Service)'),$_smarty_tpl);?>
</a>
    </p>
</div><?php }} ?>
