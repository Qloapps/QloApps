<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:56
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/modules/page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1587268546563b5650135998-90030439%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a94e151361844b72cc848d71ee4391e77853e54c' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/modules/page.tpl',
      1 => 1446729264,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1587268546563b5650135998-90030439',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logged_on_addons' => 0,
    'addons_register_link' => 0,
    'kpis' => 0,
    'add_permission' => 0,
    'currentIndex' => 0,
    'token' => 0,
    'upgrade_available' => 0,
    'module' => 0,
    'categoryFiltered' => 0,
    'nb_modules_favorites' => 0,
    'nb_modules' => 0,
    'list_modules_categories' => 0,
    'module_category_key' => 0,
    'module_category' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b56501a6658_41238622',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b56501a6658_41238622')) {function content_563b56501a6658_41238622($_smarty_tpl) {?>
<?php if (!isset($_smarty_tpl->tpl_vars['logged_on_addons']->value)||!$_smarty_tpl->tpl_vars['logged_on_addons']->value) {?>
<div class="alert bg-info">
	<div class="row modules-addons-info">
		<h4><?php echo smartyTranslate(array('s'=>'Addons membership provides access to all our PrestaShop modules.'),$_smarty_tpl);?>
</h4>
		<p><?php echo smartyTranslate(array('s'=>'Once connected, your new modules will be automatically installed.'),$_smarty_tpl);?>
</p>
	</div>
	<div class="row modules-addons-buttons">
		<a class="btn btn-default btn-primary _blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['addons_register_link']->value, ENT_QUOTES, 'UTF-8', true);?>
"><i class="icon-pencil"></i> <?php echo smartyTranslate(array('s'=>'Sign up'),$_smarty_tpl);?>
</a><a href="#" class="addons_connect btn btn-default" data-toggle="modal" data-target="#modal_addons_connect" title="<?php echo smartyTranslate(array('s'=>'Connect to PrestaShop Marketplace account'),$_smarty_tpl);?>
"><i class="icon-user"></i> <?php echo smartyTranslate(array('s'=>'Log in'),$_smarty_tpl);?>
</a>
	</div>
</div>
<?php }?>
<?php echo $_smarty_tpl->tpl_vars['kpis']->value;?>

<?php if ($_smarty_tpl->tpl_vars['add_permission']->value=='1') {?>
<div id="module_install" class="row" style="<?php if (!isset($_POST['downloadflag'])) {?>display: none;<?php }?>">

	<div class="panel col-lg-12">
		<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post" enctype="multipart/form-data" class="form-horizontal">
			<h3><?php echo smartyTranslate(array('s'=>'Add a new module'),$_smarty_tpl);?>
</h3>
			<p class="alert alert-info"><?php echo smartyTranslate(array('s'=>'The module must either be a Zip file (.zip) or a tarball file (.tar, .tar.gz, .tgz).'),$_smarty_tpl);?>
</p>
			<div class="form-group">
				<label for="file" class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'Upload a module from your computer.'),$_smarty_tpl);?>
">
						<?php echo smartyTranslate(array('s'=>'Module file'),$_smarty_tpl);?>

					</span>
				</label>
				<div class="col-sm-9">
					<div class="row">
						<div class="col-lg-7">
							<input id="file" type="file" name="file" class="hide" />
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input id="file-name" type="text" class="disabled" name="filename" readonly />
								<span class="input-group-btn">
									<button id="file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
										<i class="icon-folder-open"></i> <?php echo smartyTranslate(array('s'=>'Choose a file'),$_smarty_tpl);?>

									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-9 col-lg-push-3">
					<button class="btn btn-default" type="submit" name="download">
						<i class="icon-upload-alt" ></i>
						<?php echo smartyTranslate(array('s'=>'Upload this module'),$_smarty_tpl);?>

					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php }?>
<?php if (count($_smarty_tpl->tpl_vars['upgrade_available']->value)) {?>
<div class="alert alert-info">
	<?php echo smartyTranslate(array('s'=>'An upgrade is available for some of your modules!'),$_smarty_tpl);?>

	<ul>
	<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['upgrade_available']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
		<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;anchor=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value['anchor'], ENT_QUOTES, 'UTF-8', true);?>
"><b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value['displayName'], ENT_QUOTES, 'UTF-8', true);?>
</b></a></li>
	<?php } ?>
	</ul>
</div>
<?php }?>
<div class="panel">
	<div class="panel-heading">
		<i class="icon-list-ul"></i>
		<?php echo smartyTranslate(array('s'=>'Modules list'),$_smarty_tpl);?>

	</div>
	<!--start sidebar module-->
	<div class="row">
		<div class="categoriesTitle col-md-3">
			<div class="list-group">
				<form id="filternameForm" method="post" class="list-group-item form-horizontal">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="icon-search"></i>
						</span>
						<input class="form-control" placeholder="<?php echo smartyTranslate(array('s'=>'Search'),$_smarty_tpl);?>
" type="text" value="" name="moduleQuicksearch" id="moduleQuicksearch" autocomplete="off" />
					</div>
				</form>
				<a class="categoryModuleFilterLink list-group-item <?php if (isset($_smarty_tpl->tpl_vars['categoryFiltered']->value['favorites'])) {?>active<?php }?>" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;filterCategory=favorites" id="filter_favorite">
					<?php echo smartyTranslate(array('s'=>'Favorites'),$_smarty_tpl);?>
 <span id="favorite-count" class="badge pull-right"><?php echo $_smarty_tpl->tpl_vars['nb_modules_favorites']->value;?>
</span>
				</a>
				<a class="categoryModuleFilterLink list-group-item <?php if (count($_smarty_tpl->tpl_vars['categoryFiltered']->value)<=0) {?>active<?php }?>" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;unfilterCategory=yes" id="filter_all">
					<?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>
 <span class="badge pull-right"><?php echo $_smarty_tpl->tpl_vars['nb_modules']->value;?>
</span>
				</a>
				<?php  $_smarty_tpl->tpl_vars['module_category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module_category']->_loop = false;
 $_smarty_tpl->tpl_vars['module_category_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list_modules_categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module_category']->key => $_smarty_tpl->tpl_vars['module_category']->value) {
$_smarty_tpl->tpl_vars['module_category']->_loop = true;
 $_smarty_tpl->tpl_vars['module_category_key']->value = $_smarty_tpl->tpl_vars['module_category']->key;
?>
					<a class="categoryModuleFilterLink list-group-item <?php if (isset($_smarty_tpl->tpl_vars['categoryFiltered']->value[$_smarty_tpl->tpl_vars['module_category_key']->value])) {?>active<?php }?>" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;<?php if (isset($_smarty_tpl->tpl_vars['categoryFiltered']->value[$_smarty_tpl->tpl_vars['module_category_key']->value])) {?>un<?php }?>filterCategory=<?php echo $_smarty_tpl->tpl_vars['module_category_key']->value;?>
" id="filter_<?php echo $_smarty_tpl->tpl_vars['module_category_key']->value;?>
">
						<?php echo $_smarty_tpl->tpl_vars['module_category']->value['name'];?>
 <span class="badge pull-right"><?php echo $_smarty_tpl->tpl_vars['module_category']->value['nb'];?>
</span>
					</a>
				<?php } ?>
			</div>
		</div>
		<div id="moduleContainer" class="col-md-9">
			<?php echo $_smarty_tpl->getSubTemplate ('controllers/modules/list.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#file-selectbutton').click(function(e){
			$('#file').trigger('click');
		});
		$('#file-name').click(function(e){
			$('#file').trigger('click');
		});
		$('#file').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#file-name').val(file[file.length-1]);
		});
	});
</script>
<?php }} ?>
