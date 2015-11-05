<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:56
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/themes/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1428527076563b5650608432-79017921%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27e21a4f66332b1ac27110c98b48940eccbe3393' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/themes/helpers/view/view.tpl',
      1 => 1446729268,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1428527076563b5650608432-79017921',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'theme_name' => 0,
    'doc' => 0,
    'item' => 0,
    'key' => 0,
    'modules_errors' => 0,
    'module_errors' => 0,
    'error' => 0,
    'image_link' => 0,
    'img_error' => 0,
    'back_link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b5650653003_09187777',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b5650653003_09187777')) {function content_563b5650653003_09187777($_smarty_tpl) {?>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo smartyTranslate(array('s'=>sprintf('The "%1$s" theme has been successfully installed.',$_smarty_tpl->tpl_vars['theme_name']->value)),$_smarty_tpl);?>

</div>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAfterThemeInstallation','theme_name'=>$_smarty_tpl->tpl_vars['theme_name']->value),$_smarty_tpl);?>


<?php if (count($_smarty_tpl->tpl_vars['doc']->value)>0) {?>
    <ul>
        <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['doc']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
        <li><i><a class="_blank" href="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
</a></i>
        <?php } ?>
    </ul>
<?php }?>
<?php if (count($_smarty_tpl->tpl_vars['modules_errors']->value)>0) {?>
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo smartyTranslate(array('s'=>'The following module(s) were not installed properly:'),$_smarty_tpl);?>

        <ul>
            <?php  $_smarty_tpl->tpl_vars['module_errors'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_errors']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules_errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_errors']->key => $_smarty_tpl->tpl_vars['module_errors']->value) {
$_smarty_tpl->tpl_vars['module_errors']->_loop = true;
?>
                <li>
                   <b><?php echo $_smarty_tpl->tpl_vars['module_errors']->value['module_name'];?>
</b> : <?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['module_errors']->value['errors']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->_loop = true;
?><br>  <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['error']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php }?>
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo smartyTranslate(array('s'=>'Warning: You may have to regenerate images to fit with this new theme.'),$_smarty_tpl);?>

    <a href="<?php echo $_smarty_tpl->tpl_vars['image_link']->value;?>
">
        <button class="btn btn-default"><?php echo smartyTranslate(array('s'=>'Go to the thumbnails regeneration page'),$_smarty_tpl);?>
</button>
    </a>
</div>

<?php if (isset($_smarty_tpl->tpl_vars['img_error']->value['error'])) {?>
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo smartyTranslate(array('s'=>'Warning: Copy/paste your errors if you want to manually set the image type (in the "Images" page under the "Preferences" menu):'),$_smarty_tpl);?>

        <ul>
            <?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['img_error']->value['error']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->_loop = true;
?>
                <li>
                    <?php echo smartyTranslate(array('s'=>'Name image type:'),$_smarty_tpl);?>
 <strong><?php echo $_smarty_tpl->tpl_vars['error']->value['name'];?>
</strong> <?php echo smartyTranslate(array('s'=>sprintf('(width: %1$spx, height: %2$spx).',$_smarty_tpl->tpl_vars['error']->value['width'],$_smarty_tpl->tpl_vars['error']->value['height'])),$_smarty_tpl);?>

                </li>
            <?php } ?>
        </ul>

    </div>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['img_error']->value['ok'])) {?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo smartyTranslate(array('s'=>'Images have been correctly updated in the database:'),$_smarty_tpl);?>

        <ul>
            <?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['img_error']->value['ok']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->_loop = true;
?>
                <li>
                    <?php echo smartyTranslate(array('s'=>'Name image type:'),$_smarty_tpl);?>
 <strong><?php echo $_smarty_tpl->tpl_vars['error']->value['name'];?>
</strong> <?php echo smartyTranslate(array('s'=>sprintf('(width: %1$spx, height: %2$spx).',$_smarty_tpl->tpl_vars['error']->value['width'],$_smarty_tpl->tpl_vars['error']->value['height'])),$_smarty_tpl);?>

                </li>
            <?php } ?>
        </ul>

    </div>
<?php }?>

<a href="<?php echo $_smarty_tpl->tpl_vars['back_link']->value;?>
">
    <button class="btn btn-default"><?php echo smartyTranslate(array('s'=>'Finish'),$_smarty_tpl);?>
</button>
</a>

<?php }} ?>
