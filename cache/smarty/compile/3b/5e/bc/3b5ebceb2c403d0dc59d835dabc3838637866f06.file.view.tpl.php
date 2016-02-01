<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:56
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/search/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:185861184256ab3ba0d9a739-47134090%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b5ebceb2c403d0dc59d835dabc3838637866f06' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/search/helpers/view/view.tpl',
      1 => 1454062130,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '185861184256ab3ba0d9a739-47134090',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'query' => 0,
    'nb_results' => 0,
    'features' => 0,
    'feature' => 0,
    'val' => 0,
    'key' => 0,
    'modules' => 0,
    'module' => 0,
    'categories' => 0,
    'category' => 0,
    'products' => 0,
    'customers' => 0,
    'orders' => 0,
    'addons' => 0,
    'addon' => 0,
    'lang_iso' => 0,
    'host_mode' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba0e83a92_67870045',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba0e83a92_67870045')) {function content_56ab3ba0e83a92_67870045($_smarty_tpl) {?>

<script type="text/javascript">
$(function() {
	$('#content .panel').highlight('<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
');
});
</script>

<?php if ($_smarty_tpl->tpl_vars['query']->value) {?>
	<h2>
	<?php if (isset($_smarty_tpl->tpl_vars['nb_results']->value)&&$_smarty_tpl->tpl_vars['nb_results']->value==0) {?>
		<h2><?php echo smartyTranslate(array('s'=>'There are no results matching your query "%s".','sprintf'=>$_smarty_tpl->tpl_vars['query']->value),$_smarty_tpl);?>
</h2>
	<?php } elseif (isset($_smarty_tpl->tpl_vars['nb_results']->value)&&$_smarty_tpl->tpl_vars['nb_results']->value==1) {?>
		<?php echo smartyTranslate(array('s'=>'1 result matches your query "%s".','sprintf'=>$_smarty_tpl->tpl_vars['query']->value),$_smarty_tpl);?>

	<?php } elseif (isset($_smarty_tpl->tpl_vars['nb_results']->value)) {?>
		<?php echo smartyTranslate(array('s'=>'%d results match your query "%s".','sprintf'=>array(intval($_smarty_tpl->tpl_vars['nb_results']->value),$_smarty_tpl->tpl_vars['query']->value)),$_smarty_tpl);?>

	<?php }?>
	</h2>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['query']->value&&isset($_smarty_tpl->tpl_vars['nb_results']->value)&&$_smarty_tpl->tpl_vars['nb_results']->value) {?>

	<?php if (isset($_smarty_tpl->tpl_vars['features']->value)) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['features']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 feature'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d features','sprintf'=>count($_smarty_tpl->tpl_vars['features']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<table class="table">
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['feature'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['feature']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['features']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['feature']->key => $_smarty_tpl->tpl_vars['feature']->value) {
$_smarty_tpl->tpl_vars['feature']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['feature']->key;
?>
				<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['feature']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['val']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['val']->key;
 $_smarty_tpl->tpl_vars['val']->index++;
 $_smarty_tpl->tpl_vars['val']->first = $_smarty_tpl->tpl_vars['val']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['feature_list']['first'] = $_smarty_tpl->tpl_vars['val']->first;
?>
					<tr>
						<td><a href="<?php echo $_smarty_tpl->tpl_vars['val']->value['link'];?>
"<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['feature_list']['first']) {?>><strong><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
</strong><?php }?></a></td>
						<td><a href="<?php echo $_smarty_tpl->tpl_vars['val']->value['link'];?>
"><?php echo $_smarty_tpl->tpl_vars['val']->value['value'];?>
</a></td>
					</tr>
				<?php } ?>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['modules']->value)&&$_smarty_tpl->tpl_vars['modules']->value) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['modules']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 module'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d modules','sprintf'=>count($_smarty_tpl->tpl_vars['modules']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<table class="table">
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['module']->key;
?>
				<tr>
					<td><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->linkto, ENT_QUOTES, 'UTF-8', true);?>
"><strong><?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
</strong></a></td>
					<td><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->linkto, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->tpl_vars['module']->value->description;?>
</a></td>
				</tr>
			<?php } ?>
		</tbody>
		</table>
	</div>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['categories']->value)&&$_smarty_tpl->tpl_vars['categories']->value) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['categories']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 category'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d categories','sprintf'=>count($_smarty_tpl->tpl_vars['categories']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<table class="table" style="border-spacing : 0; border-collapse : collapse;">
			<?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value) {
$_smarty_tpl->tpl_vars['category']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['category']->key;
?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['products']->value)&&$_smarty_tpl->tpl_vars['products']->value) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['products']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 product'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d products','sprintf'=>count($_smarty_tpl->tpl_vars['products']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<?php echo $_smarty_tpl->tpl_vars['products']->value;?>

	</div>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['customers']->value)&&$_smarty_tpl->tpl_vars['customers']->value) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['customers']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 customer'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d customers','sprintf'=>count($_smarty_tpl->tpl_vars['customers']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<?php echo $_smarty_tpl->tpl_vars['customers']->value;?>

	</div>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['orders']->value)&&$_smarty_tpl->tpl_vars['orders']->value) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['orders']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 order'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d orders','sprintf'=>count($_smarty_tpl->tpl_vars['orders']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<?php echo $_smarty_tpl->tpl_vars['orders']->value;?>

	</div>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['addons']->value)&&$_smarty_tpl->tpl_vars['addons']->value) {?>
	<div class="panel">
		<h3>
			<?php if (count($_smarty_tpl->tpl_vars['addons']->value)==1) {?>
				<?php echo smartyTranslate(array('s'=>'1 addon'),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'%d addons','sprintf'=>count($_smarty_tpl->tpl_vars['addons']->value)),$_smarty_tpl);?>

			<?php }?>
		</h3>
		<table class="table">
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['addon'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['addon']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['addons']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['addon']->key => $_smarty_tpl->tpl_vars['addon']->value) {
$_smarty_tpl->tpl_vars['addon']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['addon']->key;
?>
				<tr>
					<td><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['addon']->value['href'], ENT_QUOTES, 'UTF-8', true);?>
&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><strong><i class="icon-external-link-sign"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['addon']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
</strong></a></td>
					<td><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['addon']->value['href'], ENT_QUOTES, 'UTF-8', true);?>
&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><?php if (is_string($_smarty_tpl->tpl_vars['addon']->value['description'])) {?><?php echo htmlspecialchars($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['addon']->value['description'],256,'...'), ENT_QUOTES, 'UTF-8', true);?>
<?php }?></a></td>
				</tr>
			<?php } ?>
		</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="text-center"><a href="http://addons.prestashop.com/search.php?search_query=<?php echo urlencode($_smarty_tpl->tpl_vars['query']->value);?>
&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><strong><?php echo smartyTranslate(array('s'=>'Show more results...'),$_smarty_tpl);?>
</strong></a></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?php }?>

<?php }?>
<div class="row">
	<div class="col-lg-4">
		<div class="panel">
			<h3><?php echo smartyTranslate(array('s'=>'Search doc.prestashop.com'),$_smarty_tpl);?>
</h3>
			<a href="http://doc.prestashop.com/dosearchsite.action?spaceSearch=true&amp;queryString=<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="btn btn-default _blank"><?php echo smartyTranslate(array('s'=>'Go to the documentation'),$_smarty_tpl);?>
</a>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel">
			<h3><?php echo smartyTranslate(array('s'=>'Search addons.prestashop.com'),$_smarty_tpl);?>
</h3>
			<a href="http://addons.prestashop.com/search.php?search_query=<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="btn btn-default _blank"><?php echo smartyTranslate(array('s'=>'Go to Addons'),$_smarty_tpl);?>
</a>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel">
			<h3><?php echo smartyTranslate(array('s'=>'Search prestashop.com forum'),$_smarty_tpl);?>
</h3>
			<a href="https://www.google.fr/search?q=site%3Aprestashop.com%2Fforums%2F+<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
" class="btn btn-default _blank"><?php echo smartyTranslate(array('s'=>'Go to the Forum'),$_smarty_tpl);?>
</a>
		</div>
	</div>
</div>




<?php }} ?>
