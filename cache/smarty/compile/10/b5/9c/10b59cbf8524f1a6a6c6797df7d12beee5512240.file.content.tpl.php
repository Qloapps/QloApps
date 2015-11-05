<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:56
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/images/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:835562074563b56507768a3-35463782%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '10b59cbf8524f1a6a6c6797df7d12beee5512240' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/images/content.tpl',
      1 => 1446729263,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '835562074563b56507768a3-35463782',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
    'display_move' => 0,
    'safe_mode' => 0,
    'current' => 0,
    'token' => 0,
    'table' => 0,
    'display_regenerate' => 0,
    'types' => 0,
    'k' => 0,
    'type' => 0,
    'formats' => 0,
    'format' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b56507c6ff3_37696603',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b56507c6ff3_37696603')) {function content_563b56507c6ff3_37696603($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
	<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['display_move']->value)&&$_smarty_tpl->tpl_vars['display_move']->value) {?>
	<?php if ($_smarty_tpl->tpl_vars['safe_mode']->value) {?>
        <div class="alert alert-warning">
            <p><?php echo smartyTranslate(array('s'=>'PrestaShop has detected that your server configuration is not compatible with the new storage system (directive "safe_mode" is activated). You should therefore continue to use the existing system.'),$_smarty_tpl);?>
</p>
        </div>
    <?php } else { ?>
        <form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post" class="form-horizontal">
            <div class="panel">
                <h3>
                    <i class="icon-picture"></i>
                    <?php echo smartyTranslate(array('s'=>'Move images'),$_smarty_tpl);?>

                </h3>
                <div class="alert alert-warning">
                    <p><?php echo smartyTranslate(array('s'=>'You can choose to keep your images stored in the previous system. There\'s nothing wrong with that.'),$_smarty_tpl);?>
</p>
                    <p><?php echo smartyTranslate(array('s'=>'You can also decide to move your images to the new storage system. In this case, click on the "Move images" button below. Please be patient. This can take several minutes.'),$_smarty_tpl);?>
</p>
                </div>
                <div class="alert alert-info">&nbsp;
                    <?php echo smartyTranslate(array('s'=>'After moving all of your product images, set the "Use the legacy image filesystem" option above to "No" for best performance.'),$_smarty_tpl);?>

                </div>
                <div class="row">
                    <div class="col-lg-12 pull-right">
                        <button type="submit" name="submitMoveImages<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" class="btn btn-default pull-right" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Are you sure?'),$_smarty_tpl);?>
');"><i class="process-icon-cogs"></i> <?php echo smartyTranslate(array('s'=>'Move images'),$_smarty_tpl);?>
</button>
                    </div>
                </div>
            </div>
        </form>
    <?php }?>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['display_regenerate']->value)) {?>

	<form class="form-horizontal" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post">
		<div class="panel">
			<h3>
                <i class="icon-picture"></i>
                <?php echo smartyTranslate(array('s'=>'Regenerate thumbnails'),$_smarty_tpl);?>

            </h3>

			<div class="alert alert-info">
				<?php echo smartyTranslate(array('s'=>'Regenerates thumbnails for all existing images'),$_smarty_tpl);?>
<br />
				<?php echo smartyTranslate(array('s'=>'Please be patient. This can take several minutes.'),$_smarty_tpl);?>
<br />
				<?php echo smartyTranslate(array('s'=>'Be careful! Manually uploaded thumbnails will be erased and replaced by automatically generated thumbnails.'),$_smarty_tpl);?>

			</div>
			
			<div class="form-group">
				<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Select an image'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<select name="type" onchange="changeFormat(this)">
						<option value="all"><?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>
</option>
						<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value) {
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['type']->value;?>
</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value) {
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
			<div class="form-group second-select format_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" style="display:none;">			
				<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Select a format'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9 margin-form">
					<select name="format_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
">
						<option value="all"><?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>
</option>
						<?php  $_smarty_tpl->tpl_vars['format'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['format']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['formats']->value[$_smarty_tpl->tpl_vars['k']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['format']->key => $_smarty_tpl->tpl_vars['format']->value) {
$_smarty_tpl->tpl_vars['format']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['format']->value['id_image_type'];?>
"><?php echo $_smarty_tpl->tpl_vars['format']->value['name'];?>
</option>
						<?php } ?>
					</select>
				</div>
			</div>
			<?php } ?>
			<script>
				function changeFormat(elt)
				{
					$('.second-select').hide();
					$('.format_' + $(elt).val()).show();
				}
			</script>

			<div class="form-group">
				<label class="control-label col-lg-3">
					<?php echo smartyTranslate(array('s'=>'Erase previous images'),$_smarty_tpl);?>

				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="erase" id="erase_on" value="1" checked="checked">
						<label for="erase_on" class="radioCheck">
							<?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>

						</label>
						<input type="radio" name="erase" id="erase_off" value="0">
						<label for="erase_off" class="radioCheck">
							<?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>

						</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">
						<?php echo smartyTranslate(array('s'=>'Select "No" only if your server timed out and you need to resume the regeneration.'),$_smarty_tpl);?>

					</p>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitRegenerate<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" class="btn btn-default pull-right" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Are you sure?'),$_smarty_tpl);?>
');">
					<i class="process-icon-cogs"></i> <?php echo smartyTranslate(array('s'=>'Regenerate thumbnails'),$_smarty_tpl);?>

				</button>
			</div>
		</div>
	</form>
<?php }?><?php }} ?>
