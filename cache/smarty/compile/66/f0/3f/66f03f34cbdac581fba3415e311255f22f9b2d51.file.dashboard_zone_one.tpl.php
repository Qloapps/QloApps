<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 05:22:26
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/modules/dashactivity/views/templates/hook/dashboard_zone_one.tpl" */ ?>
<?php /*%%SmartyHeaderCode:99497532456ab3d6251f5a0-78623872%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '66f03f34cbdac581fba3415e311255f22f9b2d51' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/modules/dashactivity/views/templates/hook/dashboard_zone_one.tpl',
      1 => 1454061953,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '99497532456ab3d6251f5a0-78623872',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'allow_push' => 0,
    'dashactivity_config_form' => 0,
    'link' => 0,
    'DASHACTIVITY_VISITOR_ONLINE' => 0,
    'DASHACTIVITY_CART_ACTIVE' => 0,
    'stock_management' => 0,
    'gapi_mode' => 0,
    'date_subtitle' => 0,
    'date_format' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3d625b0558_93836934',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3d625b0558_93836934')) {function content_56ab3d625b0558_93836934($_smarty_tpl) {?>
<section id="dashactivity" class="panel widget<?php if ($_smarty_tpl->tpl_vars['allow_push']->value) {?> allow_push<?php }?>">
	<div class="panel-heading">
		<i class="icon-time"></i> <?php echo smartyTranslate(array('s'=>'Activity overview','mod'=>'dashactivity'),$_smarty_tpl);?>

		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashactivity'); return false;" title="<?php echo smartyTranslate(array('s'=>'Configure','mod'=>'dashactivity'),$_smarty_tpl);?>
">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashactivity'); return false;" title="<?php echo smartyTranslate(array('s'=>'Refresh','mod'=>'dashactivity'),$_smarty_tpl);?>
">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</div>
	<section id="dashactivity_config" class="dash_config hide">
		<header><i class="icon-wrench"></i> <?php echo smartyTranslate(array('s'=>'Configuration','mod'=>'dashactivity'),$_smarty_tpl);?>
</header>
		<?php echo $_smarty_tpl->tpl_vars['dashactivity_config_form']->value;?>

	</section>
	<section id="dash_live" class="loading">
		<ul class="data_list_large">
			<li>
				<span class="data_label size_l">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminStats'), ENT_QUOTES, 'UTF-8', true);?>
&amp;module=statslive"><?php echo smartyTranslate(array('s'=>'Online Visitors','mod'=>'dashactivity'),$_smarty_tpl);?>
</a>
					<small class="text-muted"><br/>
						<?php echo smartyTranslate(array('s'=>'in the last %d minutes','sprintf'=>intval($_smarty_tpl->tpl_vars['DASHACTIVITY_VISITOR_ONLINE']->value),'mod'=>'dashactivity'),$_smarty_tpl);?>

					</small>
				</span>
				<span class="data_value size_xxl">
					<span id="online_visitor"></span>
				</span>
			</li>
			<li>
				<span class="data_label size_l">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Active Shopping Carts','mod'=>'dashactivity'),$_smarty_tpl);?>
</a>
					<small class="text-muted"><br/>
						<?php echo smartyTranslate(array('s'=>'in the last %d minutes','sprintf'=>intval($_smarty_tpl->tpl_vars['DASHACTIVITY_CART_ACTIVE']->value),'mod'=>'dashactivity'),$_smarty_tpl);?>

					</small>
				</span>
				<span class="data_value size_xxl">
					<span id="active_shopping_cart"></span>
				</span>
			</li>
		</ul>
	</section>
	<section id="dash_pending" class="loading">
		<header><i class="icon-time"></i> <?php echo smartyTranslate(array('s'=>'Currently Pending','mod'=>'dashactivity'),$_smarty_tpl);?>
</header>
		<ul class="data_list">
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Orders','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_l">
					<span id="pending_orders"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminReturn'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Return/Exchanges','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_l">
					<span id="return_exchanges"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Abandoned Carts','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_l">
					<span id="abandoned_cart"></span>
				</span>
			</li>
			<?php if (isset($_smarty_tpl->tpl_vars['stock_management']->value)&&$_smarty_tpl->tpl_vars['stock_management']->value) {?>
				<li>
					<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminTracking'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'Out of Stock Products','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
					<span class="data_value size_l">
						<span id="products_out_of_stock"></span>
					</span>
				</li>
			<?php }?>
		</ul>
	</section>
	<section id="dash_notifications" class="loading">
		<header><i class="icon-exclamation-sign"></i> <?php echo smartyTranslate(array('s'=>'Notifications','mod'=>'dashactivity'),$_smarty_tpl);?>
</header>
		<ul class="data_list_vertical">
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomerThreads'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'New Messages','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_l">
					<span id="new_messages"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules'), ENT_QUOTES, 'UTF-8', true);?>
&amp;configure=productcomments&amp;tab_module=front_office_features&amp;module_name=productcomments"><?php echo smartyTranslate(array('s'=>'Product Reviews','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_l">
					<span id="product_reviews"></span>
				</span>
			</li>
		</ul>
	</section>
	<section id="dash_customers" class="loading">
		<header><i class="icon-user"></i> <?php echo smartyTranslate(array('s'=>'Customers & Newsletters','mod'=>'dashactivity'),$_smarty_tpl);?>
 <span class="subtitle small" id="customers-newsletters-subtitle"></span></header>
		<ul class="data_list">
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers'), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'New Customers','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_md">
					<span id="new_customers"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminStats'), ENT_QUOTES, 'UTF-8', true);?>
&amp;module=statsnewsletter"><?php echo smartyTranslate(array('s'=>'New Subscriptions','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_md">
					<span id="new_registrations"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules'), ENT_QUOTES, 'UTF-8', true);?>
&amp;configure=blocknewsletter&amp;module_name=blocknewsletter"><?php echo smartyTranslate(array('s'=>'Total Subscribers','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_md">
					<span id="total_suscribers"></span>
				</span>
			</li>
		</ul>
	</section>
	<section id="dash_traffic" class="loading">
		<header>
			<i class="icon-globe"></i> <?php echo smartyTranslate(array('s'=>'Traffic','mod'=>'dashactivity'),$_smarty_tpl);?>
 <span class="subtitle small" id="traffic-subtitle"></span>
		</header>
		<ul class="data_list">
			<?php if ($_smarty_tpl->tpl_vars['gapi_mode']->value) {?>
				<li>
					<span class="data_label">
						<img src="../modules/dashactivity/gapi-logo.gif" width="16" height="16" alt=""/> <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules'), ENT_QUOTES, 'UTF-8', true);?>
&amp;<?php echo $_smarty_tpl->tpl_vars['gapi_mode']->value;?>
=gapi"><?php echo smartyTranslate(array('s'=>'Link to your Google Analytics account','mod'=>'dashactivity'),$_smarty_tpl);?>
</a>
					</span>
				</li>
			<?php }?>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminStats'), ENT_QUOTES, 'UTF-8', true);?>
&amp;module=statsforecast"><?php echo smartyTranslate(array('s'=>'Visits','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_md">
					<span id="visits"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminStats'), ENT_QUOTES, 'UTF-8', true);?>
&amp;module=statsvisits"><?php echo smartyTranslate(array('s'=>'Unique Visitors','mod'=>'dashactivity'),$_smarty_tpl);?>
</a></span>
				<span class="data_value size_md">
					<span id="unique_visitors"></span>
				</span>
			</li>
			<li>
				<span class="data_label"><?php echo smartyTranslate(array('s'=>'Traffic Sources','mod'=>'dashactivity'),$_smarty_tpl);?>
</span>
				<ul class="data_list_small" id="dash_traffic_source">
				</ul>
				<div id="dash_traffic_chart2" class='chart with-transitions'>
					<svg></svg>
				</div>
			</li>
		</ul>
	</section>
</section>
<script type="text/javascript">
	date_subtitle = "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date_subtitle']->value, ENT_QUOTES, 'UTF-8', true);?>
";
	date_format   = "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date_format']->value, ENT_QUOTES, 'UTF-8', true);?>
";
</script>
<?php }} ?>
