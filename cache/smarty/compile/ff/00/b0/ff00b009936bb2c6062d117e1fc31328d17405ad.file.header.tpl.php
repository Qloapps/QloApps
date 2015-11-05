<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 08:52:40
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin980qbfmdx/themes/default/template/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8542135675638bc2821ed49-98720534%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff00b009936bb2c6062d117e1fc31328d17405ad' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin980qbfmdx/themes/default/template/header.tpl',
      1 => 1446454904,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8542135675638bc2821ed49-98720534',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'iso' => 0,
    'img_dir' => 0,
    'meta_title' => 0,
    'shop_name' => 0,
    'display_header_javascript' => 0,
    'controller_name' => 0,
    'iso_user' => 0,
    'full_language_code' => 0,
    'country_iso_code' => 0,
    'round_mode' => 0,
    'shop_context' => 0,
    'autorefresh_notifications' => 0,
    'token' => 0,
    'currentIndex' => 0,
    'default_language' => 0,
    'link' => 0,
    'tab_modules_list' => 0,
    'css_files' => 0,
    'css_uri' => 0,
    'js_def' => 0,
    'js_files' => 0,
    'displayBackOfficeHeader' => 0,
    'brightness' => 0,
    'display_header' => 0,
    'employee' => 0,
    'collapse_menu' => 0,
    'default_tab_link' => 0,
    'version' => 0,
    'is_multishop' => 0,
    'shop_list' => 0,
    'multishop_context' => 0,
    'show_new_orders' => 0,
    'show_new_customers' => 0,
    'show_new_messages' => 0,
    'quick_access' => 0,
    'quick' => 0,
    'matchQuickLink' => 0,
    'quick_access_current_link_name' => 0,
    'quick_access_current_link_icon' => 0,
    'logged_on_addons' => 0,
    'display_addons_connection' => 0,
    'base_url' => 0,
    'base_url_tc' => 0,
    'maintenance_mode' => 0,
    'host_mode' => 0,
    'login_link' => 0,
    'displayBackOfficeTop' => 0,
    'bootstrap' => 0,
    'page_header_toolbar' => 0,
    'modal_module_list' => 0,
    'install_dir_exists' => 0,
    'lite_display' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5638bc28409542_73269701',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5638bc28409542_73269701')) {function content_5638bc28409542_73269701($_smarty_tpl) {?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 lt-ie6 " lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html lang="fr" class="no-js ie9" lang="en"> <![endif]-->
<html lang="<?php echo $_smarty_tpl->tpl_vars['iso']->value;?>
">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=0.75, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" type="image/x-icon" href="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
favicon.ico" />
	<link rel="apple-touch-icon" href="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
app_icon.png" />

	<meta name="robots" content="NOFOLLOW, NOINDEX">
	<title><?php if ($_smarty_tpl->tpl_vars['meta_title']->value!='') {?><?php echo $_smarty_tpl->tpl_vars['meta_title']->value;?>
 • <?php }?><?php echo $_smarty_tpl->tpl_vars['shop_name']->value;?>
</title>
	<?php if (!isset($_smarty_tpl->tpl_vars['display_header_javascript']->value)||$_smarty_tpl->tpl_vars['display_header_javascript']->value) {?>
	<script type="text/javascript">
		var help_class_name = '<?php echo addcslashes($_smarty_tpl->tpl_vars['controller_name']->value,'\'');?>
';
		var iso_user = '<?php echo addcslashes($_smarty_tpl->tpl_vars['iso_user']->value,'\'');?>
';
		var full_language_code = '<?php echo addcslashes($_smarty_tpl->tpl_vars['full_language_code']->value,'\'');?>
';
		var country_iso_code = '<?php echo addcslashes($_smarty_tpl->tpl_vars['country_iso_code']->value,'\'');?>
';
		var _PS_VERSION_ = '<?php echo addcslashes(@constant('_PS_VERSION_'),'\'');?>
';
		var roundMode = <?php echo intval($_smarty_tpl->tpl_vars['round_mode']->value);?>
;
<?php if (isset($_smarty_tpl->tpl_vars['shop_context']->value)) {?>
	<?php if ($_smarty_tpl->tpl_vars['shop_context']->value==Shop::CONTEXT_ALL) {?>
		var youEditFieldFor = '<?php echo smartyTranslate(array('s'=>'This field will be modified for all your shops.','js'=>1),$_smarty_tpl);?>
';
	<?php } elseif ($_smarty_tpl->tpl_vars['shop_context']->value==Shop::CONTEXT_GROUP) {?>
		var youEditFieldFor = '<?php echo smartyTranslate(array('s'=>'This field will be modified for all shops in this shop group:','js'=>1),$_smarty_tpl);?>
 <b><?php echo addcslashes($_smarty_tpl->tpl_vars['shop_name']->value,'\'');?>
</b>';
	<?php } else { ?>
		var youEditFieldFor = '<?php echo smartyTranslate(array('s'=>'This field will be modified for this shop:','js'=>1),$_smarty_tpl);?>
 <b><?php echo addcslashes($_smarty_tpl->tpl_vars['shop_name']->value,'\'');?>
</b>';
	<?php }?>
<?php } else { ?>
		var youEditFieldFor = '';
<?php }?>
		var autorefresh_notifications = '<?php echo addcslashes($_smarty_tpl->tpl_vars['autorefresh_notifications']->value,'\'');?>
';
		var new_order_msg = '<?php echo smartyTranslate(array('s'=>'A new order has been placed on your shop.','js'=>1),$_smarty_tpl);?>
';
		var order_number_msg = '<?php echo smartyTranslate(array('s'=>'Order number:','js'=>1),$_smarty_tpl);?>
 ';
		var total_msg = '<?php echo smartyTranslate(array('s'=>'Total:','js'=>1),$_smarty_tpl);?>
 ';
		var from_msg = '<?php echo smartyTranslate(array('s'=>'From:','js'=>1),$_smarty_tpl);?>
 ';
		var see_order_msg = '<?php echo smartyTranslate(array('s'=>'View this order','js'=>1),$_smarty_tpl);?>
';
		var new_customer_msg = '<?php echo smartyTranslate(array('s'=>'A new customer registered on your shop.','js'=>1),$_smarty_tpl);?>
';
		var customer_name_msg = '<?php echo smartyTranslate(array('s'=>'Customer name:','js'=>1),$_smarty_tpl);?>
 ';
		var new_msg = '<?php echo smartyTranslate(array('s'=>'A new message was posted on your shop.','js'=>1),$_smarty_tpl);?>
';
		var see_msg = '<?php echo smartyTranslate(array('s'=>'Read this message','js'=>1),$_smarty_tpl);?>
';
		var token = '<?php echo addslashes($_smarty_tpl->tpl_vars['token']->value);?>
';
		var token_admin_orders = '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
';
		var token_admin_customers = '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
';
		var token_admin_customer_threads = '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomerThreads'),$_smarty_tpl);?>
';
		var currentIndex = '<?php echo addcslashes($_smarty_tpl->tpl_vars['currentIndex']->value,'\'');?>
';
		var employee_token = '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminEmployees'),$_smarty_tpl);?>
';
		var choose_language_translate = '<?php echo smartyTranslate(array('s'=>'Choose language','js'=>1),$_smarty_tpl);?>
';
		var default_language = '<?php echo intval($_smarty_tpl->tpl_vars['default_language']->value);?>
';
		var admin_modules_link = '<?php echo addslashes($_smarty_tpl->tpl_vars['link']->value->getAdminLink("AdminModules"));?>
';
		var tab_modules_list = '<?php if (isset($_smarty_tpl->tpl_vars['tab_modules_list']->value)&&$_smarty_tpl->tpl_vars['tab_modules_list']->value) {?><?php echo addslashes($_smarty_tpl->tpl_vars['tab_modules_list']->value);?>
<?php }?>';
		var update_success_msg = '<?php echo smartyTranslate(array('s'=>'Update successful','js'=>1),$_smarty_tpl);?>
';
		var errorLogin = '<?php echo smartyTranslate(array('s'=>'PrestaShop was unable to log in to Addons. Please check your credentials and your Internet connection.','js'=>1),$_smarty_tpl);?>
';
		var search_product_msg = '<?php echo smartyTranslate(array('s'=>'Search for a product','js'=>1),$_smarty_tpl);?>
';
	</script>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['css_files']->value)) {?>
<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_smarty_tpl->tpl_vars['css_uri'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['css_files']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value) {
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['css_uri']->value = $_smarty_tpl->tpl_vars['media']->key;
?>
	<link href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['css_uri']->value, ENT_QUOTES, 'UTF-8', true);?>
" rel="stylesheet" type="text/css"/>
<?php } ?>
<?php }?>
	<?php if ((isset($_smarty_tpl->tpl_vars['js_def']->value)&&count($_smarty_tpl->tpl_vars['js_def']->value)||isset($_smarty_tpl->tpl_vars['js_files']->value)&&count($_smarty_tpl->tpl_vars['js_files']->value))) {?>
		<?php echo $_smarty_tpl->getSubTemplate ((@constant('_PS_ALL_THEMES_DIR_')).("javascript.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['displayBackOfficeHeader']->value)) {?>
		<?php echo $_smarty_tpl->tpl_vars['displayBackOfficeHeader']->value;?>

	<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['brightness']->value)) {?>
	<!--
		// @todo: multishop color
		<style type="text/css">
			div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {color:<?php echo $_smarty_tpl->tpl_vars['brightness']->value;?>
}
		</style>
	-->
	<?php }?>
</head>

<?php if ($_smarty_tpl->tpl_vars['display_header']->value) {?>
	<body class="ps_back-office<?php if ($_smarty_tpl->tpl_vars['employee']->value->bo_menu) {?> page-sidebar<?php if ($_smarty_tpl->tpl_vars['collapse_menu']->value) {?> page-sidebar-closed<?php }?><?php } else { ?> page-topbar<?php }?> <?php echo strtolower(htmlspecialchars($_GET['controller'], ENT_QUOTES, 'UTF-8', true));?>
">
	
	<header id="header" class="bootstrap">
		<nav id="header_infos" role="navigation">
			<div class="navbar-header">
				<button id="header_nav_toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-primary">
					<i class="icon-reorder"></i>
				</button>
				<a id="header_shopversion" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['default_tab_link']->value, ENT_QUOTES, 'UTF-8', true);?>
">
					<span id="shop_version"><?php echo $_smarty_tpl->tpl_vars['version']->value;?>
</span>
				</a>
				
				<?php if (isset($_smarty_tpl->tpl_vars['is_multishop']->value)&&$_smarty_tpl->tpl_vars['is_multishop']->value&&$_smarty_tpl->tpl_vars['shop_list']->value&&(isset($_smarty_tpl->tpl_vars['multishop_context']->value)&&$_smarty_tpl->tpl_vars['multishop_context']->value&Shop::CONTEXT_GROUP||$_smarty_tpl->tpl_vars['multishop_context']->value&Shop::CONTEXT_SHOP)) {?>
					<ul id="header_shop">
						<li class="dropdown">
							<?php echo $_smarty_tpl->tpl_vars['shop_list']->value;?>

						</li>
					</ul>
				<?php } else { ?>
					<a id="header_shopname" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['default_tab_link']->value, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->tpl_vars['shop_name']->value;?>
</a>
				<?php }?>
				<ul id="header_notifs_icon_wrapper">
<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['show_new_orders']->value;?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==1) {?>
					<li id="orders_notif" class="dropdown" data-type="order">
						<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
							<i class="icon-shopping-cart"></i>
							<span id="orders_notif_number_wrapper" class="notifs_badge hide">
								<span id="orders_notif_value">0</span>
							</span>
						</a>
						<div class="dropdown-menu notifs_dropdown">
							<section id="orders_notif_wrapper" class="notifs_panel">
								<div class="notifs_panel_header">
									<h3><?php echo smartyTranslate(array('s'=>'Latest Orders'),$_smarty_tpl);?>
</h3>
								</div>
								<div id="list_orders_notif" class="list_notif">
									<span class="no_notifs">
										<?php echo smartyTranslate(array('s'=>'No new orders have been placed on your shop.'),$_smarty_tpl);?>

									</span>
								</div>
								<div class="notifs_panel_footer">
									<a href="index.php?controller=AdminOrders&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Show all orders'),$_smarty_tpl);?>
</a>
								</div>
							</section>
						</div>
					</li>
<?php }?>
<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['show_new_customers']->value;?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2==1) {?>
					<li id="customers_notif" class="dropdown" data-type="customer">
						<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
							<i class="icon-user"></i>
							<span id="customers_notif_number_wrapper" class="notifs_badge hide">
								<span id="customers_notif_value">0</span>
							</span>
						</a>
						<div class="dropdown-menu notifs_dropdown">
							<section id="customers_notif_wrapper" class="notifs_panel">
								<div class="notifs_panel_header">
									<h3><?php echo smartyTranslate(array('s'=>'Latest Registrations'),$_smarty_tpl);?>
</h3>
								</div>
								<div id="list_customers_notif" class="list_notif">
									<span class="no_notifs">
										<?php echo smartyTranslate(array('s'=>'No new customers have registered on your shop.'),$_smarty_tpl);?>

									</span>
								</div>
								<div class="notifs_panel_footer">
									<a href="index.php?controller=AdminCustomers&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Show all customers'),$_smarty_tpl);?>
</a>
								</div>
							</section>
						</div>
					</li>
<?php }?>
<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['show_new_messages']->value;?>
<?php $_tmp3=ob_get_clean();?><?php if ($_tmp3==1) {?>
					<li id="customer_messages_notif" class="dropdown" data-type="customer_message">
						<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
							<i class="icon-envelope"></i>
							<span id="customer_messages_notif_number_wrapper" class="notifs_badge hide">
								<span id="customer_messages_notif_value" >0</span>
							</span>
						</a>
						<div class="dropdown-menu notifs_dropdown">
							<section id="customer_messages_notif_wrapper" class="notifs_panel">
								<div class="notifs_panel_header">
									<h3><?php echo smartyTranslate(array('s'=>'Latest Messages'),$_smarty_tpl);?>
</h3>
								</div>
								<div id="list_customer_messages_notif" class="list_notif">
									<span class="no_notifs">
										<?php echo smartyTranslate(array('s'=>'No new messages have been posted on your shop.'),$_smarty_tpl);?>

									</span>
								</div>
								<div class="notifs_panel_footer">
									<a href="index.php?controller=AdminCustomerThreads&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomerThreads'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Show all messages'),$_smarty_tpl);?>
</a>
								</div>
							</section>
						</div>
					</li>
<?php }?>
				</ul>
<?php if (count($_smarty_tpl->tpl_vars['quick_access']->value)>=0) {?>
				<ul id="header_quick">
					<li class="dropdown">
						<a href="javascript:void(0)" id="quick_select" class="dropdown-toggle" data-toggle="dropdown"><?php echo smartyTranslate(array('s'=>'Quick Access'),$_smarty_tpl);?>
 <i class="icon-caret-down"></i></a>
						<ul class="dropdown-menu">
							<?php  $_smarty_tpl->tpl_vars['quick'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['quick']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['quick_access']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['quick']->key => $_smarty_tpl->tpl_vars['quick']->value) {
$_smarty_tpl->tpl_vars['quick']->_loop = true;
?>
								<li <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['quick']->value['link'];?>
<?php $_tmp4=ob_get_clean();?><?php if ($_smarty_tpl->tpl_vars['link']->value->matchQuickLink($_tmp4)) {?><?php $_smarty_tpl->tpl_vars["matchQuickLink"] = new Smarty_variable($_smarty_tpl->tpl_vars['quick']->value['id_quick_access'], null, 0);?>class="active"<?php }?>>
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['quick']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
"<?php if ($_smarty_tpl->tpl_vars['quick']->value['new_window']) {?> class="_blank"<?php }?>>
										<?php if (isset($_smarty_tpl->tpl_vars['quick']->value['icon'])) {?>
											<i class="icon-<?php echo $_smarty_tpl->tpl_vars['quick']->value['icon'];?>
 icon-fw"></i>
										<?php } else { ?>
											<i class="icon-chevron-right icon-fw"></i>
										<?php }?>
										<?php echo $_smarty_tpl->tpl_vars['quick']->value['name'];?>

									</a>
								</li>
							<?php } ?>
							<li class="divider"></li>
							<?php if (isset($_smarty_tpl->tpl_vars['matchQuickLink']->value)) {?>
								<li>
									<a href="javascript:void(0);" class="ajax-quick-link" data-method="remove" data-quicklink-id="<?php echo $_smarty_tpl->tpl_vars['matchQuickLink']->value;?>
">
										<i class="icon-minus-circle"></i>
										<?php echo smartyTranslate(array('s'=>'Remove from QuickAccess'),$_smarty_tpl);?>

									</a>
								</li>
							<?php }?>
							<li <?php if (isset($_smarty_tpl->tpl_vars['matchQuickLink']->value)) {?>class="hide"<?php }?>>
								<a href="javascript:void(0);" class="ajax-quick-link" data-method="add">
									<i class="icon-plus-circle"></i>
									<?php echo smartyTranslate(array('s'=>'Add current page to QuickAccess'),$_smarty_tpl);?>

								</a>
							</li>
						</ul>
					</li>
				</ul>
				<?php $_smarty_tpl->tpl_vars['quick_access_current_link_name'] = new Smarty_variable(explode(" - ",$_smarty_tpl->tpl_vars['quick_access_current_link_name']->value), null, 0);?>
				<script>
					$(function() {
						$('.ajax-quick-link').on('click', function(e){
							e.preventDefault();

							var method = $(this).data('method');

							if(method == 'add')
								var name = prompt('<?php echo smartyTranslate(array('s'=>'Please name this shortcut:','js'=>1),$_smarty_tpl);?>
', '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['quick_access_current_link_name']->value[0],32);?>
');

							if(method == 'add' && name || method == 'remove')
							{
								$.ajax({
									type: 'POST',
									headers: { "cache-control": "no-cache" },
									async: false,
									url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminQuickAccesses');?>
" + "&action=GetUrl" + "&rand=<?php echo rand(1,200);?>
" + "&ajax=1" + "&method=" + method + ( $(this).data('quicklink-id') ? "&id_quick_access=" + $(this).data('quicklink-id') : ""),
									data: {
										"url": "<?php echo $_smarty_tpl->tpl_vars['link']->value->getQuickLink($_SERVER['REQUEST_URI']);?>
",
										"name": name,
										"icon": "<?php echo $_smarty_tpl->tpl_vars['quick_access_current_link_icon']->value;?>
"
									},
									dataType: "json",
									success: function(data) {
										var quicklink_list ='';
										$.each(data, function(index,value){
											if (typeof data[index]['name'] !== 'undefined')
												quicklink_list += '<li><a href="' + data[index]['link'] + '&token=' + data[index]['token'] + '"><i class="icon-chevron-right"></i> ' + data[index]['name'] + '</a></li>';
										});

										if (typeof data['has_errors'] !== 'undefined' && data['has_errors'])
											$.each(data, function(index, value)
											{
												if (typeof data[index] == 'string')
													$.growl.error({ title: "", message: data[index]});
											});
										else if (quicklink_list)
										{
											$("#header_quick ul.dropdown-menu").html(quicklink_list);
											showSuccessMessage(update_success_msg);
										}
									}
								});
							}
						});
					});
				</script>
<?php }?>
				<ul id="header_employee_box">
					<?php if ((!isset($_smarty_tpl->tpl_vars['logged_on_addons']->value)||!$_smarty_tpl->tpl_vars['logged_on_addons']->value)&&(isset($_smarty_tpl->tpl_vars['display_addons_connection']->value)&&$_smarty_tpl->tpl_vars['display_addons_connection']->value)) {?>
						<li class="hidden-sm hidden-xs">
							<a href="#" class="addons_connect toolbar_btn" data-toggle="modal" data-target="#modal_addons_connect" title="<?php echo smartyTranslate(array('s'=>'Connect to PrestaShop Marketplace account'),$_smarty_tpl);?>
">
								<i class="icon-chain-broken"></i>
								<span class="string-long"><?php echo smartyTranslate(array('s'=>'Connect to PrestaShop Marketplace account'),$_smarty_tpl);?>
</span>
								<span class="string-short"><?php echo smartyTranslate(array('s'=>'PrestaShop Marketplace'),$_smarty_tpl);?>
</span>
							</a>
						</li>
					<?php }?>
<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
<?php $_tmp5=ob_get_clean();?><?php if ($_tmp5) {?>
					<li>
						<a href="<?php if (isset($_smarty_tpl->tpl_vars['base_url_tc']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['base_url_tc']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['base_url']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" id="header_foaccess" class="_blank" title="<?php echo smartyTranslate(array('s'=>'View my shop'),$_smarty_tpl);?>
">
							<span class="string-long"><?php echo smartyTranslate(array('s'=>'My shop'),$_smarty_tpl);?>
</span>
							<span class="string-short"><?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>
</span>
						</a>
						<?php if (isset($_smarty_tpl->tpl_vars['maintenance_mode']->value)&&$_smarty_tpl->tpl_vars['maintenance_mode']->value==true) {?>
							<span class="maintenance-mode">
								&mdash;
								<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" data-html="true"
								title="<p class='text-left text-nowrap'><strong><?php echo smartyTranslate(array('s'=>'Your shop is in maintenance.'),$_smarty_tpl);?>
</strong></p><p class='text-left'><?php echo smartyTranslate(array('s'=>'Your visitors and customers cannot access your shop while in maintenance mode.%s To manage the maintenance settings, go to Preferences > Maintenance.','sprintf'=>'<br />'),$_smarty_tpl);?>
</p>"><?php echo smartyTranslate(array('s'=>'Maintenance mode'),$_smarty_tpl);?>
</span>
							</span>
						<?php }?>
					</li>
<?php }?>
					<li id="employee_infos" class="dropdown">
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminEmployees'), ENT_QUOTES, 'UTF-8', true);?>
&amp;id_employee=<?php echo intval($_smarty_tpl->tpl_vars['employee']->value->id);?>
&amp;updateemployee" class="employee_name dropdown-toggle" data-toggle="dropdown">
							<span class="employee_avatar_small">
								<?php if (isset($_smarty_tpl->tpl_vars['employee']->value)) {?>
								<img class="imgm img-thumbnail" alt="" src="<?php echo $_smarty_tpl->tpl_vars['employee']->value->getImage();?>
" width="32" height="32" />
								<?php }?>
							</span>
							<span class="string-long"><?php echo $_smarty_tpl->tpl_vars['employee']->value->firstname;?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['employee']->value->lastname;?>
</span>
							<span class="string-short"><?php echo smartyTranslate(array('s'=>'Me'),$_smarty_tpl);?>
</span>
							<i class="caret"></i>
						</a>
						<ul id="employee_links" class="dropdown-menu">
							<li>
								<span class="employee_avatar">
									<img class="imgm img-thumbnail" alt="" src="<?php echo $_smarty_tpl->tpl_vars['employee']->value->getImage();?>
" width="96" height="96" />
								</span>
							</li>
							<li class="text-center text-nowrap"><?php echo $_smarty_tpl->tpl_vars['employee']->value->firstname;?>
 <?php echo $_smarty_tpl->tpl_vars['employee']->value->lastname;?>
</li>
							<li class="divider"></li>
							<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminEmployees'), ENT_QUOTES, 'UTF-8', true);?>
&amp;id_employee=<?php echo intval($_smarty_tpl->tpl_vars['employee']->value->id);?>
&amp;updateemployee"><i class="icon-wrench"></i> <?php echo smartyTranslate(array('s'=>'My preferences'),$_smarty_tpl);?>
</a></li>
							<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>
							<li><a href="https://www.prestashop.com/cloud/" class="_blank"><i class="icon-wrench"></i> <?php echo smartyTranslate(array('s'=>'My PrestaShop account'),$_smarty_tpl);?>
</a></li>
							<?php }?>
							<li class="divider"></li>
							<li><a id="header_logout" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['login_link']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;logout"><i class="icon-signout"></i> <?php echo smartyTranslate(array('s'=>'Sign out'),$_smarty_tpl);?>
</a></li>
						</ul>
					</li>
				</ul>

				<span id="ajax_running">
					<i class="icon-refresh icon-spin icon-fw"></i>
				</span>

	<?php if (isset($_smarty_tpl->tpl_vars['displayBackOfficeTop']->value)) {?><?php echo $_smarty_tpl->tpl_vars['displayBackOfficeTop']->value;?>
<?php }?>
			</div>
		</nav>
	</header>

	<div id="main">
		<?php echo $_smarty_tpl->getSubTemplate ('nav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>


		<div id="content" class="<?php if (!$_smarty_tpl->tpl_vars['bootstrap']->value) {?>nobootstrap<?php } else { ?>bootstrap<?php }?>">
			<?php if (isset($_smarty_tpl->tpl_vars['page_header_toolbar']->value)) {?><?php echo $_smarty_tpl->tpl_vars['page_header_toolbar']->value;?>
<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['modal_module_list']->value)) {?><?php echo $_smarty_tpl->tpl_vars['modal_module_list']->value;?>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['install_dir_exists']->value) {?>
			<div class="alert alert-warning">
				<?php echo smartyTranslate(array('s'=>'For security reasons, you must also delete the /install folder.'),$_smarty_tpl);?>

			</div>
<?php }?>

			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminAfterHeader'),$_smarty_tpl);?>





<?php } else { ?>
	<body<?php if (isset($_smarty_tpl->tpl_vars['lite_display']->value)&&$_smarty_tpl->tpl_vars['lite_display']->value) {?> class="ps_back-office display-modal"<?php }?>>
		<div id="main">
			<div id="content" class="<?php if (!$_smarty_tpl->tpl_vars['bootstrap']->value) {?>nobootstrap<?php } else { ?>bootstrap<?php }?>">
<?php }?>
<?php }} ?>
