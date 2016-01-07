<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:40:20
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin251kmbvhm/themes/default/template/page_header_toolbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1484476688568df33c518468-32095491%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9d303e2ee6900108aaf076c20bb5fa1d8f38579f' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin251kmbvhm/themes/default/template/page_header_toolbar.tpl',
      1 => 1452142870,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1484476688568df33c518468-32095491',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'page_header_toolbar_title' => 0,
    'page_header_toolbar_btn' => 0,
    'breadcrumbs2' => 0,
    'toolbar_btn' => 0,
    'k' => 0,
    'table' => 0,
    'btn' => 0,
    'help_link' => 0,
    'tab_modules_open' => 0,
    'tab_modules_list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df33c63ac98_39384065',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df33c63ac98_39384065')) {function content_568df33c63ac98_39384065($_smarty_tpl) {?>


<?php if (!isset($_smarty_tpl->tpl_vars['title']->value)&&isset($_smarty_tpl->tpl_vars['page_header_toolbar_title']->value)) {?>
	<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['page_header_toolbar_title']->value, null, 0);?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['page_header_toolbar_btn']->value)) {?>
	<?php $_smarty_tpl->tpl_vars['toolbar_btn'] = new Smarty_variable($_smarty_tpl->tpl_vars['page_header_toolbar_btn']->value, null, 0);?>
<?php }?>

<div class="bootstrap">
	<div class="page-head">
		
		<h2 class="page-title">
			
			<?php if (is_array($_smarty_tpl->tpl_vars['title']->value)) {?><?php echo htmlspecialchars(end($_smarty_tpl->tpl_vars['title']->value), ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
		</h2>
		

		
		<ul class="breadcrumb page-breadcrumb">
			
			<?php if ($_smarty_tpl->tpl_vars['breadcrumbs2']->value['container']['name']!='') {?>
				<li class="breadcrumb-container">
					<?php if ($_smarty_tpl->tpl_vars['breadcrumbs2']->value['container']['href']!='') {?><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['breadcrumbs2']->value['container']['href'], ENT_QUOTES, 'UTF-8', true);?>
"><?php }?>
					<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['breadcrumbs2']->value['container']['name'], ENT_QUOTES, 'UTF-8', true);?>

					<?php if ($_smarty_tpl->tpl_vars['breadcrumbs2']->value['container']['href']!='') {?></a><?php }?>
				</li>
			<?php }?>

			
			<?php if ($_smarty_tpl->tpl_vars['breadcrumbs2']->value['tab']['name']!=''&&$_smarty_tpl->tpl_vars['breadcrumbs2']->value['container']['name']!=$_smarty_tpl->tpl_vars['breadcrumbs2']->value['tab']['name']) {?>
				<li class="breadcrumb-current">
					<?php if ($_smarty_tpl->tpl_vars['breadcrumbs2']->value['tab']['href']!='') {?><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['breadcrumbs2']->value['tab']['href'], ENT_QUOTES, 'UTF-8', true);?>
"><?php }?>
					<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['breadcrumbs2']->value['tab']['name'], ENT_QUOTES, 'UTF-8', true);?>

					<?php if ($_smarty_tpl->tpl_vars['breadcrumbs2']->value['tab']['href']!='') {?></a><?php }?>
				</li>
			<?php }?>

			
			
			</ul>
		
		
		<div class="page-bar toolbarBox">
			<div class="btn-toolbar">
				<a href="#" class="toolbar_btn dropdown-toolbar navbar-toggle" data-toggle="collapse" data-target="#toolbar-nav"><i class="process-icon-dropdown"></i><div><?php echo smartyTranslate(array('s'=>'Menu'),$_smarty_tpl);?>
</div></a>
				<ul id="toolbar-nav" class="nav nav-pills pull-right collapse navbar-collapse">
					<?php  $_smarty_tpl->tpl_vars['btn'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['btn']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toolbar_btn']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['btn']->key => $_smarty_tpl->tpl_vars['btn']->value) {
$_smarty_tpl->tpl_vars['btn']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['btn']->key;
?>
					<?php if ($_smarty_tpl->tpl_vars['k']->value!='back'&&$_smarty_tpl->tpl_vars['k']->value!='modules-list') {?>
					<li>
						<a id="page-header-desc-<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
-<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['imgclass'])) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['imgclass'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
<?php }?>" class="toolbar_btn <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['target'])&&$_smarty_tpl->tpl_vars['btn']->value['target']) {?> _blank<?php }?> pointer"<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['href'])) {?> href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['href'], ENT_QUOTES, 'UTF-8', true);?>
"<?php }?> title="<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['help'])) {?><?php echo $_smarty_tpl->tpl_vars['btn']->value['help'];?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['desc'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['js'])&&$_smarty_tpl->tpl_vars['btn']->value['js']) {?> onclick="<?php echo $_smarty_tpl->tpl_vars['btn']->value['js'];?>
"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['btn']->value['modal_target'])&&$_smarty_tpl->tpl_vars['btn']->value['modal_target']) {?> data-target="<?php echo $_smarty_tpl->tpl_vars['btn']->value['modal_target'];?>
" data-toggle="modal"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['btn']->value['help'])) {?> data-toggle="tooltip" data-placement="bottom"<?php }?>>
							<i class="<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['icon'])) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['icon'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>process-icon-<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['imgclass'])) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['imgclass'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
<?php }?><?php }?><?php if (isset($_smarty_tpl->tpl_vars['btn']->value['class'])) {?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['class'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>"></i>
							<div<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['force_desc'])&&$_smarty_tpl->tpl_vars['btn']->value['force_desc']==true) {?> class="locked"<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btn']->value['desc'], ENT_QUOTES, 'UTF-8', true);?>
</div>
						</a>
					</li>
					<?php }?>
					<?php } ?>
					<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list'])) {?>
					<li>
						<a id="page-header-desc-<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
-<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['imgclass'])) {?><?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['imgclass'];?>
<?php } else { ?>modules-list<?php }?>" class="toolbar_btn<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['class'])) {?> <?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['class'];?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['target'])&&$_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['target']) {?> _blank<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['href'])) {?>href="<?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['href'];?>
"<?php }?> title="<?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['desc'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['js'])&&$_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['js']) {?> onclick="<?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['js'];?>
"<?php }?>>
							<i class="<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['icon'])) {?><?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['icon'];?>
<?php } else { ?>process-icon-<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['imgclass'])) {?><?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['imgclass'];?>
<?php } else { ?>modules-list<?php }?><?php }?>"></i>
							<div<?php if (isset($_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['force_desc'])&&$_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['force_desc']==true) {?> class="locked"<?php }?>><?php echo $_smarty_tpl->tpl_vars['toolbar_btn']->value['modules-list']['desc'];?>
</div>
						</a>
					</li>
					<?php }?>
					<?php if (isset($_smarty_tpl->tpl_vars['help_link']->value)) {?>
					<li>
						<a class="toolbar_btn btn-help" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['help_link']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Help'),$_smarty_tpl);?>
">
							<i class="process-icon-help"></i>
							<div><?php echo smartyTranslate(array('s'=>'Help'),$_smarty_tpl);?>
</div>
						</a>
					</li>
					<?php }?>
				</ul>
				<?php if ((isset($_smarty_tpl->tpl_vars['tab_modules_open']->value)&&$_smarty_tpl->tpl_vars['tab_modules_open']->value)||isset($_smarty_tpl->tpl_vars['tab_modules_list']->value)) {?>
				<script type="text/javascript">
				//<![CDATA[
					var modules_list_loaded = false;
					<?php if (isset($_smarty_tpl->tpl_vars['tab_modules_open']->value)&&$_smarty_tpl->tpl_vars['tab_modules_open']->value) {?>
						$(function() {
								$('#modules_list_container').modal('show');
								openModulesList();

						});
					<?php }?>
					<?php if (isset($_smarty_tpl->tpl_vars['tab_modules_list']->value)) {?>
						$('.process-icon-modules-list').parent('a').unbind().bind('click', function (){
							$('#modules_list_container').modal('show');
							openModulesList();
						});
					<?php }?>
				//]]>
				</script>
				<?php }?>
			</div>
		</div>
		
	</div>
</div>
<?php }} ?>
