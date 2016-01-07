<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:45:27
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wkhotelfilterblock/views/templates/hook/htlfilterblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1393763400568df46fbbb3e6-48249857%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bed41ee40b4faa0f4f9e49701ff5f7b6f7b0868b' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wkhotelfilterblock/views/templates/hook/htlfilterblock.tpl',
      1 => 1452142879,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1393763400568df46fbbb3e6-48249857',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'config' => 0,
    'ratting_img' => 0,
    'all_feat' => 0,
    'feat' => 0,
    'currency' => 0,
    'min_price' => 0,
    'max_price' => 0,
    'max_adult' => 0,
    'foo' => 0,
    'max_child' => 0,
    'num_days' => 0,
    'date_from' => 0,
    'date_to' => 0,
    'cat_link' => 0,
    'warning_num' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df46fc1daa9_56767510',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df46fc1daa9_56767510')) {function content_568df46fc1daa9_56767510($_smarty_tpl) {?><div id="filter_results" class="row block">
	<div class="col-sm-12">
		<?php if (isset($_smarty_tpl->tpl_vars['config']->value)&&$_smarty_tpl->tpl_vars['config']->value['SHOW_RATTING_FILTER']) {?>
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<span><?php echo smartyTranslate(array('s'=>'Guest Rating','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
					<span class="pull-right clear_filter"><?php echo smartyTranslate(array('s'=>'Clear Filter','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="5">
						<label style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);" class="ratting_img_style ratting_5">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="4">
						<label style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);" class="ratting_img_style ratting_4">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="3">
						<label style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);" class="ratting_img_style ratting_3">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="2">
						<label style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);" class="ratting_img_style ratting_2">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="1">
						<label style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);" class="ratting_img_style ratting_1">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="0">
						<label style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);" class="ratting_img_style ratting_0">
						</label>
					</div>
				</div>
			</div>
		<?php }?>
		
		<?php if (isset($_smarty_tpl->tpl_vars['config']->value)&&$_smarty_tpl->tpl_vars['config']->value['SHOW_AMENITIES_FILTER']) {?>
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<span><?php echo smartyTranslate(array('s'=>'Amenities','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
					<span class="pull-right clear_filter"><?php echo smartyTranslate(array('s'=>'Clear Filter','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					<?php  $_smarty_tpl->tpl_vars['feat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['feat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['all_feat']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['feat']->key => $_smarty_tpl->tpl_vars['feat']->value) {
$_smarty_tpl->tpl_vars['feat']->_loop = true;
?>
						<div class="layered_filt">
							<input type="checkbox" class="filter" data-type="amenities" value="<?php echo $_smarty_tpl->tpl_vars['feat']->value['id_feature'];?>
">
							<span class="filters_name"><?php echo $_smarty_tpl->tpl_vars['feat']->value['name'];?>
</span>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }?>

		<?php if (isset($_smarty_tpl->tpl_vars['config']->value)&&$_smarty_tpl->tpl_vars['config']->value['SHOW_PRICE_FILTER']) {?>
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<span><?php echo smartyTranslate(array('s'=>'Price','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
					<span class="pull-right clear_filter"><?php echo smartyTranslate(array('s'=>'Clear Filter','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					<div class="row margin-lr-0 price_filter_subcont">
						<span class="pull-left"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
 <span id="filter_price_from"><?php echo $_smarty_tpl->tpl_vars['min_price']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
						<span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>
 <span id="filter_price_to"><?php echo $_smarty_tpl->tpl_vars['max_price']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>
</span>
					</div>
					<div id="filter_price_silder"></div>
				</div>
			</div>
		<?php }?>
		
		<!-- Adults , children filters are disable for now -->
		
		<!-- <div class="row margin-lr-0 layered_filter_cont">
			<div class="col-sm-12 layered_filter_heading">
				<span><?php echo smartyTranslate(array('s'=>'Adults','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
			</div>
			<div class="col-sm-12 lf_sub_cont">
				<?php $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['foo']->step = 1;$_smarty_tpl->tpl_vars['foo']->total = (int) ceil(($_smarty_tpl->tpl_vars['foo']->step > 0 ? $_smarty_tpl->tpl_vars['max_adult']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['max_adult']->value)+1)/abs($_smarty_tpl->tpl_vars['foo']->step));
if ($_smarty_tpl->tpl_vars['foo']->total > 0) {
for ($_smarty_tpl->tpl_vars['foo']->value = 1, $_smarty_tpl->tpl_vars['foo']->iteration = 1;$_smarty_tpl->tpl_vars['foo']->iteration <= $_smarty_tpl->tpl_vars['foo']->total;$_smarty_tpl->tpl_vars['foo']->value += $_smarty_tpl->tpl_vars['foo']->step, $_smarty_tpl->tpl_vars['foo']->iteration++) {
$_smarty_tpl->tpl_vars['foo']->first = $_smarty_tpl->tpl_vars['foo']->iteration == 1;$_smarty_tpl->tpl_vars['foo']->last = $_smarty_tpl->tpl_vars['foo']->iteration == $_smarty_tpl->tpl_vars['foo']->total;?>
				    <div class="layered_filt">
						<input type="checkbox" class="filter" data-type="adult" value="<?php echo $_smarty_tpl->tpl_vars['foo']->value;?>
">
						<span class="filters_name"><?php echo $_smarty_tpl->tpl_vars['foo']->value;?>
</span>
					</div>
				<?php }} ?>
			</div>
		</div> -->
		<!-- <div class="row margin-lr-0 layered_filter_cont">
			<div class="col-sm-12 layered_filter_heading">
				<span><?php echo smartyTranslate(array('s'=>'Children','mod'=>'wkhotelfilterblock'),$_smarty_tpl);?>
</span>
			</div>
			<div class="col-sm-12 lf_sub_cont">
				<?php $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['foo']->step = 1;$_smarty_tpl->tpl_vars['foo']->total = (int) ceil(($_smarty_tpl->tpl_vars['foo']->step > 0 ? $_smarty_tpl->tpl_vars['max_child']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['max_child']->value)+1)/abs($_smarty_tpl->tpl_vars['foo']->step));
if ($_smarty_tpl->tpl_vars['foo']->total > 0) {
for ($_smarty_tpl->tpl_vars['foo']->value = 1, $_smarty_tpl->tpl_vars['foo']->iteration = 1;$_smarty_tpl->tpl_vars['foo']->iteration <= $_smarty_tpl->tpl_vars['foo']->total;$_smarty_tpl->tpl_vars['foo']->value += $_smarty_tpl->tpl_vars['foo']->step, $_smarty_tpl->tpl_vars['foo']->iteration++) {
$_smarty_tpl->tpl_vars['foo']->first = $_smarty_tpl->tpl_vars['foo']->iteration == 1;$_smarty_tpl->tpl_vars['foo']->last = $_smarty_tpl->tpl_vars['foo']->iteration == $_smarty_tpl->tpl_vars['foo']->total;?>
				    <div class="layered_filt">
						<input type="checkbox" class="filter" data-type="children" value="<?php echo $_smarty_tpl->tpl_vars['foo']->value;?>
">
						<span class="filters_name"><?php echo $_smarty_tpl->tpl_vars['foo']->value;?>
</span>
					</div>
				<?php }} ?>
			</div>
		</div> -->
	</div>
</div>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('num_days'=>$_smarty_tpl->tpl_vars['num_days']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('date_from'=>$_smarty_tpl->tpl_vars['date_from']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('date_to'=>$_smarty_tpl->tpl_vars['date_to']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('cat_link'=>$_smarty_tpl->tpl_vars['cat_link']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('min_price'=>$_smarty_tpl->tpl_vars['min_price']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('max_price'=>$_smarty_tpl->tpl_vars['max_price']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('warning_num'=>$_smarty_tpl->tpl_vars['warning_num']->value),$_smarty_tpl);?>
<?php }} ?>
