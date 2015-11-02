<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:43
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/modules/list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1011689745637740bae99a5-84194871%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '873fc13d9c7eac6bdeb1958eb35ebb912df701e5' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/modules/list.tpl',
      1 => 1446455063,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1011689745637740bae99a5-84194871',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'modules' => 0,
    'module' => 0,
    'modules_uri' => 0,
    'currentIndex' => 0,
    'token' => 0,
    'option' => 0,
    'key' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5637740bc5b596_61345481',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5637740bc5b596_61345481')) {function content_5637740bc5b596_61345481($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/modifier.replace.php';
?>

<table id="module-list" class="table">
	<thead>
		<tr>
			<th colspan="4">
				<?php echo $_smarty_tpl->getSubTemplate ('controllers/modules/filters.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			</th>
		</tr>
	</thead>
	<?php if (count($_smarty_tpl->tpl_vars['modules']->value)) {?>
		<tbody>
			<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
				<?php $_smarty_tpl->_capture_stack[0][] = array("moduleStatutClass", null, null); ob_start(); ?><?php if (isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0&&$_smarty_tpl->tpl_vars['module']->value->active==1) {?>module_active<?php } else { ?>module_inactive<?php }?><?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
				<tr>
					<td class="<?php ob_start();?><?php echo Smarty::$_smarty_vars['capture']['moduleStatutClass'];?>
<?php $_tmp5=ob_get_clean();?><?php echo $_tmp5;?>
 text-center" style="width: 1%;">
						<?php if ((isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0)||!isset($_smarty_tpl->tpl_vars['module']->value->type)||$_smarty_tpl->tpl_vars['module']->value->type!='addonsMustHave') {?>
						<input type="checkbox" name="modules" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" class="noborder" title="<?php echo smartyTranslate(array('s'=>sprintf('Module %1s ',$_smarty_tpl->tpl_vars['module']->value->name)),$_smarty_tpl);?>
"<?php if (!isset($_smarty_tpl->tpl_vars['module']->value->confirmUninstall)||empty($_smarty_tpl->tpl_vars['module']->value->confirmUninstall)) {?> data-rel="false"<?php } else { ?> data-rel="<?php echo addslashes($_smarty_tpl->tpl_vars['module']->value->confirmUninstall);?>
"<?php }?>/>
						<?php }?>
					</td>
					<td class="fixed-width-xs">
						<img width="57" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->displayName, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->displayName, ENT_QUOTES, 'UTF-8', true);?>
" src="<?php if (isset($_smarty_tpl->tpl_vars['module']->value->image)) {?><?php echo $_smarty_tpl->tpl_vars['module']->value->image;?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['modules_uri']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->logo;?>
<?php }?>" />
					</td>
					<td>
						<div id="anchor<?php echo ucfirst($_smarty_tpl->tpl_vars['module']->value->name);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->displayName, ENT_QUOTES, 'UTF-8', true);?>
">
							<div class="text-muted">
								<?php echo $_smarty_tpl->tpl_vars['module']->value->categoryName;?>

							</div>
							<div class="module_name">
								<span style="display:none"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</span>
								<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->displayName, ENT_QUOTES, 'UTF-8', true);?>

								<small class="text-muted">v<?php echo $_smarty_tpl->tpl_vars['module']->value->version;?>
 - <?php echo smartyTranslate(array('s'=>'by'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['module']->value->author;?>
</small>
								<?php if (isset($_smarty_tpl->tpl_vars['module']->value->type)&&$_smarty_tpl->tpl_vars['module']->value->type=='addonsBought') {?>
								- <span class="module-badge-bought help-tooltip text-warning" data-title="<?php echo smartyTranslate(array('s'=>"You bought this module on PrestaShop Addons. Thank You."),$_smarty_tpl);?>
"><i class="icon-pushpin"></i> <small><?php echo smartyTranslate(array('s'=>"Bought"),$_smarty_tpl);?>
</small></span>
								<?php } elseif (isset($_smarty_tpl->tpl_vars['module']->value->type)&&$_smarty_tpl->tpl_vars['module']->value->type=='addonsMustHave') {?>
									- <span class="module-badge-popular help-tooltip text-primary" data-title="<?php echo smartyTranslate(array('s'=>"This module is available on PrestaShop Addons"),$_smarty_tpl);?>
"><i class="icon-group"></i> <small><?php echo smartyTranslate(array('s'=>"Popular"),$_smarty_tpl);?>
</small></span>
								<?php } elseif (isset($_smarty_tpl->tpl_vars['module']->value->type)&&$_smarty_tpl->tpl_vars['module']->value->type=='addonsPartner') {?>
									- <span class="module-badge-partner help-tooltip text-warning" data-title="<?php echo smartyTranslate(array('s'=>"Official, PrestaShop certified module. Free, secure and includes updates!"),$_smarty_tpl);?>
"><i class="icon-pushpin"></i> <small><?php echo smartyTranslate(array('s'=>"Official"),$_smarty_tpl);?>
</small></span>
								<?php } elseif (isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0) {?>
									<?php if (isset($_smarty_tpl->tpl_vars['module']->value->version_addons)&&$_smarty_tpl->tpl_vars['module']->value->version_addons) {?>
										<span class="label label-warning"><?php echo smartyTranslate(array('s'=>'Need update'),$_smarty_tpl);?>
</span>
									<?php }?>
								<?php }?>
							</div>
							<p class="module_description">
								<?php if (isset($_smarty_tpl->tpl_vars['module']->value->description)&&$_smarty_tpl->tpl_vars['module']->value->description!='') {?>
									<?php echo $_smarty_tpl->tpl_vars['module']->value->description;?>

								<?php }?>
								<?php if (isset($_smarty_tpl->tpl_vars['module']->value->show_quick_view)&&$_smarty_tpl->tpl_vars['module']->value->show_quick_view) {?>
									<br><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;ajax=1&amp;action=GetModuleQuickView&amp;module=<?php echo urlencode($_smarty_tpl->tpl_vars['module']->value->name);?>
" class="fancybox-quick-view"><i class="icon-search"></i> <?php echo smartyTranslate(array('s'=>'Read more'),$_smarty_tpl);?>
</a>
								<?php }?>
							</p>
							<?php if (isset($_smarty_tpl->tpl_vars['module']->value->message)&&(empty($_smarty_tpl->tpl_vars['module']->value->name)!==false)&&(!isset($_smarty_tpl->tpl_vars['module']->value->type)||($_smarty_tpl->tpl_vars['module']->value->type!='addonsMustHave'||$_smarty_tpl->tpl_vars['module']->value->type!=='addonsNative'))) {?><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $_smarty_tpl->tpl_vars['module']->value->message;?>
</div><?php }?>
						</div>
					</td>
					<td class="actions">
						<div class="btn-group-action">
							<div class="btn-group pull-right">
								<?php if (isset($_smarty_tpl->tpl_vars['module']->value->type)&&$_smarty_tpl->tpl_vars['module']->value->type=='addonsMustHave') {?>
									<a class="btn btn-default _blank" href="<?php echo htmlspecialchars(smarty_modifier_replace($_smarty_tpl->tpl_vars['module']->value->addons_buy_url,' ','+'), ENT_QUOTES, 'UTF-8', true);?>
">
										<i class="icon-shopping-cart"></i> &nbsp;<?php if (isset($_smarty_tpl->tpl_vars['module']->value->price)) {?><?php if (floatval($_smarty_tpl->tpl_vars['module']->value->price)==0) {?><?php echo smartyTranslate(array('s'=>'Free'),$_smarty_tpl);?>
<?php } elseif (isset($_smarty_tpl->tpl_vars['module']->value->id_currency)) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['module']->value->price,'currency'=>$_smarty_tpl->tpl_vars['module']->value->id_currency),$_smarty_tpl);?>
<?php }?><?php }?>
									</a>
								<?php } else { ?>
									<?php if (isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0) {?>
										<?php if (isset($_smarty_tpl->tpl_vars['module']->value->version_addons)&&$_smarty_tpl->tpl_vars['module']->value->version_addons) {?>
											<a class="btn btn-warning" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->options['update_url'], ENT_QUOTES, 'UTF-8', true);?>
">
												<i class="icon-refresh"></i> <?php echo smartyTranslate(array('s'=>'Update it!'),$_smarty_tpl);?>

											</a>
										<?php } elseif (!isset($_smarty_tpl->tpl_vars['module']->value->not_on_disk)) {?>
											<?php if (count($_smarty_tpl->tpl_vars['module']->value->optionsHtml)>0) {?>
												<?php $_smarty_tpl->tpl_vars['option'] = new Smarty_variable($_smarty_tpl->tpl_vars['module']->value->optionsHtml[0], null, 0);?>
												<?php echo $_smarty_tpl->tpl_vars['option']->value;?>

											<?php }?>
										<?php } else { ?>
											<a class="btn btn-danger" <?php if (!empty($_smarty_tpl->tpl_vars['module']->value->options['uninstall_onclick'])) {?>onclick="<?php echo $_smarty_tpl->tpl_vars['module']->value->options['uninstall_onclick'];?>
"<?php }?> href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->options['uninstall_url'], ENT_QUOTES, 'UTF-8', true);?>
">
												<i class="icon-minus-sign-alt"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Uninstall'),$_smarty_tpl);?>

											</a>
										<?php }?>
									<?php } else { ?>
										<?php if (isset($_smarty_tpl->tpl_vars['module']->value->trusted)&&$_smarty_tpl->tpl_vars['module']->value->trusted) {?>
											<?php if ($_smarty_tpl->tpl_vars['module']->value->trusted==2) {?>
												<a class="btn btn-success untrustedaddon" href="#" data-target="#moduleNotTrustedCountry" data-toggle="modal" data-link="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->options['install_url'], ENT_QUOTES, 'UTF-8', true);?>
" data-module-name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->displayName, ENT_QUOTES, 'UTF-8', true);?>
">
													<i class="icon-plus-sign-alt"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Install'),$_smarty_tpl);?>

												</a>
											<?php } else { ?>
												<a class="btn btn-success" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->options['install_url'], ENT_QUOTES, 'UTF-8', true);?>
">
													<i class="icon-plus-sign-alt"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Install'),$_smarty_tpl);?>

												</a>
											<?php }?>
										<?php } else { ?>
										<a class="btn btn-success untrustedaddon" href="#" data-target="#moduleNotTrusted" data-toggle="modal" data-link="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->options['install_url'], ENT_QUOTES, 'UTF-8', true);?>
" data-module-display-name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->displayName, ENT_QUOTES, 'UTF-8', true);?>
" data-module-name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" data-module-image="<?php if (isset($_smarty_tpl->tpl_vars['module']->value->image)) {?><?php echo $_smarty_tpl->tpl_vars['module']->value->image;?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['modules_uri']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->logo;?>
<?php }?>" data-author-name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->author, ENT_QUOTES, 'UTF-8', true);?>
" data-author-uri="<?php if (isset($_smarty_tpl->tpl_vars['module']->value->author_uri)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->author_uri, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>">
											<i class="icon-plus-sign-alt"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Install'),$_smarty_tpl);?>

										</a>
										<?php }?>
									<?php }?>

									<?php if (!isset($_smarty_tpl->tpl_vars['module']->value->not_on_disk)&&isset($_smarty_tpl->tpl_vars['module']->value->id)) {?>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
											<span class="caret">&nbsp;</span>
										</button>

										<ul class="dropdown-menu">
											<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['module']->value->optionsHtml; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value) {
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
?>
												<?php if ($_smarty_tpl->tpl_vars['key']->value!=0) {?>
													<?php if (strpos($_smarty_tpl->tpl_vars['option']->value,'title="divider"')!==false) {?>
														<li class="divider"></li>
													<?php } else { ?>
														<li><?php echo $_smarty_tpl->tpl_vars['option']->value;?>
</li>
													<?php }?>
												<?php }?>
											<?php } ?>
										</ul>
									<?php } elseif (!isset($_smarty_tpl->tpl_vars['module']->value->not_on_disk)&&!isset($_smarty_tpl->tpl_vars['module']->value->id)) {?>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
											<span class="caret">&nbsp;</span>
										</button>
										<ul class="dropdown-menu">
											<?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['module']->value->optionsHtml; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value) {
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
?>
												<?php if ($_smarty_tpl->tpl_vars['key']->value!=0) {?>
													<?php if (strpos($_smarty_tpl->tpl_vars['option']->value,'title="divider"')!==false) {?>
														<li class="divider"></li>
													<?php } else { ?>
														<li><?php echo $_smarty_tpl->tpl_vars['option']->value;?>
</li>
													<?php }?>
												<?php }?>
											<?php } ?>
										</ul>
									<?php } elseif (isset($_smarty_tpl->tpl_vars['module']->value->not_on_disk)) {?>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
											<span class="caret">&nbsp;</span>
										</button>
										<ul class="dropdown-menu">
											<?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences)&&isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']==1) {?>
												<li>
													<a class="action_module action_unfavorite toggle_favorite" data-module="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" data-value="0" href="#">
														<i class="icon-star"></i> <?php echo smartyTranslate(array('s'=>'Remove from Favorites'),$_smarty_tpl);?>

													</a>
													<a class="action_module action_favorite toggle_favorite" data-module="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" data-value="1" href="#" style="display: none;">
														<i class="icon-star"></i> <?php echo smartyTranslate(array('s'=>'Mark as Favorite'),$_smarty_tpl);?>

													</a>
												</li>
											<?php } else { ?>
												<li>
													<a class="action_module action_unfavorite toggle_favorite" data-module="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" data-value="0" href="#" style="display: none;">
														<i class="icon-star"></i> <?php echo smartyTranslate(array('s'=>'Remove from Favorites'),$_smarty_tpl);?>

													</a>
													<a class="action_module action_favorite toggle_favorite" data-module="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" data-value="1" href="#">
														<i class="icon-star"></i> <?php echo smartyTranslate(array('s'=>'Mark as Favorite'),$_smarty_tpl);?>

													</a>
												</li>
											<?php }?>
										</ul>
									<?php } else { ?>
										&nbsp;
									<?php }?>
								<?php }?>
							</div>
						</div>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="btn-group pull-left">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<?php echo smartyTranslate(array('s'=>'bulk actions'),$_smarty_tpl);?>

			 <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li>
			 	<a href="#" onclick="modules_management('install')">
					<i class="icon-plus-sign-alt"></i>&nbsp;
					<?php echo smartyTranslate(array('s'=>'Install the selection'),$_smarty_tpl);?>

				</a>
			</li>
			<li>
				<a href="#" onclick="modules_management('uninstall')">
					<i class="icon-minus-sign-alt"></i>&nbsp;
					<?php echo smartyTranslate(array('s'=>'Uninstall the selection'),$_smarty_tpl);?>

				</a>
			</li>
		</ul>
	</div>
	<?php } else { ?>
		<tbody>
			<tr>
				<td colspan="4" class="list-empty">
					<div class="list-empty-msg">
						<i class="icon-warning-sign list-empty-icon"></i> <?php echo smartyTranslate(array('s'=>'No modules available in this section.'),$_smarty_tpl);?>

					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php }?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.fancybox-quick-view').fancybox({
			type: 'ajax',
			autoDimensions: false,
			autoSize: false,
			width: 600,
			height: 'auto',
			helpers: {
				overlay: {
					locked: false
				}
			}
		});
	});
</script>
<?php }} ?>
