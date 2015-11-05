<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:56
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/dashboard/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1313535789563b5650c8b7a1-96079097%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae20b5ebae7f24e81e903004593e01d8e1dca0a8' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/dashboard/helpers/view/view.tpl',
      1 => 1446729268,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1313535789563b5650c8b7a1-96079097',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'dashboard_use_push' => 0,
    'warning' => 0,
    'action' => 0,
    'preselect_date_range' => 0,
    'date_from' => 0,
    'date_to' => 0,
    'calendar' => 0,
    'hookDashboardZoneOne' => 0,
    'hookDashboardZoneTwo' => 0,
    'lang_iso' => 0,
    'host_mode' => 0,
    'new_version_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b5650d31050_61725025',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b5650d31050_61725025')) {function content_563b5650d31050_61725025($_smarty_tpl) {?>
<script>
	var dashboard_ajax_url = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminDashboard');?>
';
	var adminstats_ajax_url = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminStats');?>
';
	var no_results_translation = '<?php echo smartyTranslate(array('s'=>'No result','js'=>1),$_smarty_tpl);?>
';
	var dashboard_use_push = '<?php echo intval($_smarty_tpl->tpl_vars['dashboard_use_push']->value);?>
';
	var read_more = '<?php echo smartyTranslate(array('s'=>'Read more','js'=>1),$_smarty_tpl);?>
';
</script>

<div id="dashboard">
	<div class="row">
		<div class="col-lg-12">
<?php if ($_smarty_tpl->tpl_vars['warning']->value) {?>
			<div class="alert alert-warning"><?php echo $_smarty_tpl->tpl_vars['warning']->value;?>
</div>
<?php }?>
			<div id="calendar" class="panel">
				<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post" id="calendar_form" name="calendar_form" class="form-inline">
					<div class="btn-group">
						<button type="button" name="submitDateDay" class="btn btn-default submitDateDay<?php if (isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)&&$_smarty_tpl->tpl_vars['preselect_date_range']->value=='day') {?> active<?php }?>">
							<?php echo smartyTranslate(array('s'=>'Day'),$_smarty_tpl);?>

						</button>
						<button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth<?php if ((!isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)||!$_smarty_tpl->tpl_vars['preselect_date_range']->value)||(isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)&&$_smarty_tpl->tpl_vars['preselect_date_range']->value=='month')) {?> active<?php }?>">
							<?php echo smartyTranslate(array('s'=>'Month'),$_smarty_tpl);?>

						</button>
						<button type="button" name="submitDateYear" class="btn btn-default submitDateYear<?php if (isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)&&$_smarty_tpl->tpl_vars['preselect_date_range']->value=='year') {?> active<?php }?>">
							<?php echo smartyTranslate(array('s'=>'Year'),$_smarty_tpl);?>

						</button>
						<button type="button" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev<?php if (isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)&&$_smarty_tpl->tpl_vars['preselect_date_range']->value=='prev-day') {?> active<?php }?>">
							<?php echo smartyTranslate(array('s'=>'Day'),$_smarty_tpl);?>
-1
						</button>
						<button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev<?php if (isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)&&$_smarty_tpl->tpl_vars['preselect_date_range']->value=='prev-month') {?> active<?php }?>">
							<?php echo smartyTranslate(array('s'=>'Month'),$_smarty_tpl);?>
-1
						</button>
						<button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev<?php if (isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)&&$_smarty_tpl->tpl_vars['preselect_date_range']->value=='prev-year') {?> active<?php }?>">
							<?php echo smartyTranslate(array('s'=>'Year'),$_smarty_tpl);?>
-1
						</button>
						<!--
						<button type="submit" name="submitDateRealTime" class="hide btn btn-default submitDateRealTime <?php if ($_smarty_tpl->tpl_vars['dashboard_use_push']->value) {?>active<?php }?>" value="<?php echo !intval($_smarty_tpl->tpl_vars['dashboard_use_push']->value);?>
">
							<?php echo smartyTranslate(array('s'=>'Real Time'),$_smarty_tpl);?>

						</button> -->
					</div>
					<input type="hidden" name="datepickerFrom" id="datepickerFrom" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date_from']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="form-control">
					<input type="hidden" name="datepickerTo" id="datepickerTo" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date_to']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="form-control">
					<input type="hidden" name="preselectDateRange" id="preselectDateRange" value="<?php if (isset($_smarty_tpl->tpl_vars['preselect_date_range']->value)) {?><?php echo $_smarty_tpl->tpl_vars['preselect_date_range']->value;?>
<?php }?>" class="form-control">
					<div class="form-group pull-right">
						<button id="datepickerExpand" class="btn btn-default" type="button">
							<i class="icon-calendar-empty"></i>
							<span class="hidden-xs">
								<?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>

								<strong class="text-info" id="datepicker-from-info"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date_from']->value, ENT_QUOTES, 'UTF-8', true);?>
</strong>
								<?php echo smartyTranslate(array('s'=>'To'),$_smarty_tpl);?>

								<strong class="text-info" id="datepicker-to-info"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date_to']->value, ENT_QUOTES, 'UTF-8', true);?>
</strong>
								<strong class="text-info" id="datepicker-diff-info"></strong>
							</span>
							<i class="icon-caret-down"></i>
						</button>
					</div>
					<?php echo $_smarty_tpl->tpl_vars['calendar']->value;?>

				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-lg-3" id="hookDashboardZoneOne">
			<?php echo $_smarty_tpl->tpl_vars['hookDashboardZoneOne']->value;?>

		</div>
		<div class="col-md-8 col-lg-7" id="hookDashboardZoneTwo">
			<?php echo $_smarty_tpl->tpl_vars['hookDashboardZoneTwo']->value;?>

			<div id="dashaddons" class="row-margin-bottom">
				<a href="http://addons.prestashop.com/en/209-dashboards?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank">
					<i class="icon-plus"></i> <?php echo smartyTranslate(array('s'=>'Add more dashboard modules'),$_smarty_tpl);?>

				</a>
			</div>
		</div>
		<div class="col-md-12 col-lg-2">
			<section class="dash_news panel">
				<h3><i class="icon-rss"></i> <?php echo smartyTranslate(array('s'=>'PrestaShop News'),$_smarty_tpl);?>
</h3>
				<div class="dash_news_content"></div>
				<div class="text-center"><h4><a href="http://www.prestashop.com/blog/" onclick="return !window.open(this.href);"><?php echo smartyTranslate(array('s'=>'Find more news'),$_smarty_tpl);?>
</a></h4></div>
			</section>
			<section id="dash_version" class="visible-lg">
				<iframe style="overflow:hidden;border:none" src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['new_version_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" ></iframe>
			</section>
			<section class="dash_links panel">
				<h3><i class="icon-link"></i> <?php echo smartyTranslate(array('s'=>"Useful links"),$_smarty_tpl);?>
</h3>
					<dl>
						<dt><a href="http://doc.prestashop.com/display/PS16?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><?php echo smartyTranslate(array('s'=>"Official Documentation"),$_smarty_tpl);?>
</a></dt>
						<dd><?php echo smartyTranslate(array('s'=>"User, Developer and Designer Guides"),$_smarty_tpl);?>
</dd>
					</dl>
					<dl>
						<dt><a href="http://www.prestashop.com/forums?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><?php echo smartyTranslate(array('s'=>"PrestaShop Forum"),$_smarty_tpl);?>
</a></dt>
						<dd><?php echo smartyTranslate(array('s'=>"Connect with the PrestaShop community"),$_smarty_tpl);?>
</dd>
					</dl>
					<dl>
						<dt><a href="http://addons.prestashop.com?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><?php echo smartyTranslate(array('s'=>"PrestaShop Addons"),$_smarty_tpl);?>
</a></dt>
						<dd><?php echo smartyTranslate(array('s'=>"Enhance your store with templates & modules"),$_smarty_tpl);?>
</dd>
					</dl>
					<dl>
						<dt><a href="http://forge.prestashop.com?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><?php echo smartyTranslate(array('s'=>"The Forge"),$_smarty_tpl);?>
</a></dt>
						<dd><?php echo smartyTranslate(array('s'=>"Report issues in the Bug Tracker"),$_smarty_tpl);?>
</dd>
					</dl>
					<dl>
						<dt><a href="http://www.prestashop.com/en/contact-us?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang_iso']->value, 'UTF-8');?>
&amp;utm_content=<?php if ($_smarty_tpl->tpl_vars['host_mode']->value) {?>cloud<?php } else { ?>download<?php }?>" class="_blank"><?php echo smartyTranslate(array('s'=>"Contact Us!"),$_smarty_tpl);?>
</a></dt>
						<dd></dd>
					</dl>
			</section>
		</div>
	</div>
</div>
<?php }} ?>
