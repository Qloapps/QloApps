<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:42:42
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/helpers/uploader/simple.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1641198382568d36fd733461-71374402%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '14b3c239107ef4d929457aefe5f82c084e1c3760' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/helpers/uploader/simple.tpl',
      1 => 1452142887,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1641198382568d36fd733461-71374402',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d36fd80c553_42397244',
  'variables' => 
  array (
    'files' => 0,
    'file' => 0,
    'show_thumbnail' => 0,
    'id' => 0,
    'max_files' => 0,
    'name' => 0,
    'multiple' => 0,
    'size' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d36fd80c553_42397244')) {function content_568d36fd80c553_42397244($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['files']->value)&&count($_smarty_tpl->tpl_vars['files']->value)>0) {?>
	<?php $_smarty_tpl->tpl_vars['show_thumbnail'] = new Smarty_variable(false, null, 0);?>
	<?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['file']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['files']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value) {
$_smarty_tpl->tpl_vars['file']->_loop = true;
?>
		<?php if (isset($_smarty_tpl->tpl_vars['file']->value['image'])&&$_smarty_tpl->tpl_vars['file']->value['type']=='image') {?>
			<?php $_smarty_tpl->tpl_vars['show_thumbnail'] = new Smarty_variable(true, null, 0);?>
		<?php }?>
	<?php } ?>
<?php if ($_smarty_tpl->tpl_vars['show_thumbnail']->value) {?>
<div class="form-group">
	<div class="col-lg-12" id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-images-thumbnails">
		<?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['file']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['files']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value) {
$_smarty_tpl->tpl_vars['file']->_loop = true;
?>
		<?php if (isset($_smarty_tpl->tpl_vars['file']->value['image'])&&$_smarty_tpl->tpl_vars['file']->value['type']=='image') {?>
		<div>
			<?php echo $_smarty_tpl->tpl_vars['file']->value['image'];?>

			<?php if (isset($_smarty_tpl->tpl_vars['file']->value['size'])) {?><p><?php echo smartyTranslate(array('s'=>'File size'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['file']->value['size'];?>
kb</p><?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['file']->value['delete_url'])) {?>
			<p>
				<a class="btn btn-default" href="<?php echo $_smarty_tpl->tpl_vars['file']->value['delete_url'];?>
">
					<i class="icon-trash"></i> <?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>

				</a>
			</p>
			<?php }?>
		</div>
		<?php }?>
		<?php } ?>
	</div>
</div>
<?php }?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['max_files']->value)&&count($_smarty_tpl->tpl_vars['files']->value)>=$_smarty_tpl->tpl_vars['max_files']->value) {?>
<div class="row">
	<div class="alert alert-warning"><?php echo smartyTranslate(array('s'=>'You have reached the limit (%s) of files to upload, please remove files to continue uploading','sprintf'=>$_smarty_tpl->tpl_vars['max_files']->value),$_smarty_tpl);?>
</div>
</div>
<?php } else { ?>
<div class="form-group">
	<div class="col-sm-6">
		<input id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
" type="file" name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_smarty_tpl->tpl_vars['multiple']->value)&&$_smarty_tpl->tpl_vars['multiple']->value) {?>[]<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['multiple']->value)&&$_smarty_tpl->tpl_vars['multiple']->value) {?> multiple="multiple"<?php }?> class="hide" />
		<div class="dummyfile input-group">
			<span class="input-group-addon"><i class="icon-file"></i></span>
			<input id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name" type="text" name="filename" readonly />
			<span class="input-group-btn">
				<button id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
					<i class="icon-folder-open"></i> <?php if (isset($_smarty_tpl->tpl_vars['multiple']->value)&&$_smarty_tpl->tpl_vars['multiple']->value) {?><?php echo smartyTranslate(array('s'=>'Add files'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Add file'),$_smarty_tpl);?>
<?php }?>
				</button>
				<?php if ((!isset($_smarty_tpl->tpl_vars['multiple']->value)||!$_smarty_tpl->tpl_vars['multiple']->value)&&isset($_smarty_tpl->tpl_vars['files']->value)&&count($_smarty_tpl->tpl_vars['files']->value)==1&&isset($_smarty_tpl->tpl_vars['files']->value[0]['download_url'])) {?>
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['files']->value[0]['download_url'], ENT_QUOTES, 'UTF-8', true);?>
" class="btn btn-default">
						<i class="icon-cloud-download"></i>
						<?php if (isset($_smarty_tpl->tpl_vars['size']->value)) {?><?php echo smartyTranslate(array('s'=>'Download current file (%skb)','sprintf'=>$_smarty_tpl->tpl_vars['size']->value),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Download current file'),$_smarty_tpl);?>
<?php }?>
					</a>
				<?php }?>
			</span>
		</div>
	</div>
</div>
<script type="text/javascript">
<?php if (isset($_smarty_tpl->tpl_vars['multiple']->value)&&isset($_smarty_tpl->tpl_vars['max_files']->value)) {?>
	var <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
_max_files = <?php echo $_smarty_tpl->tpl_vars['max_files']->value-count($_smarty_tpl->tpl_vars['files']->value);?>
;
<?php }?>

	$(document).ready(function(){
		$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-selectbutton').click(function(e) {
			$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
').trigger('click');
		});

		$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name').click(function(e) {
			$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
').trigger('click');
		});

		$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name').on('dragenter', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name').on('dragover', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name').on('drop', function(e) {
			e.preventDefault();
			var files = e.originalEvent.dataTransfer.files;
			$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
')[0].files = files;
			$(this).val(files[0].name);
		});

		$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
').change(function(e) {
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';

				$.each(files, function(index, value) {
					name += value.name+', ';
				});

				$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name').val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
-name').val(name[name.length-1]);
			}
		});

		if (typeof <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
_max_files !== 'undefined')
		{
			$('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
').closest('form').on('submit', function(e) {
				if ($('#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
')[0].files.length > <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id']->value, ENT_QUOTES, 'UTF-8', true);?>
_max_files) {
					e.preventDefault();
					alert('<?php echo smartyTranslate(array('s'=>sprintf('You can upload a maximum of %s files',$_smarty_tpl->tpl_vars['max_files']->value)),$_smarty_tpl);?>
');
				}
			});
		}
	});
</script>
<?php }?>
<?php }} ?>
