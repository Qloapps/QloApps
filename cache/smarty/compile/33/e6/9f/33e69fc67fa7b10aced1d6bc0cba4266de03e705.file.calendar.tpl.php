<?php /* Smarty version Smarty-3.1.19, created on 2016-01-30 01:03:39
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin652sof9ht/themes/default/template/helpers/calendar/calendar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:162789938056ab8d5b141195-25722595%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '33e69fc67fa7b10aced1d6bc0cba4266de03e705' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin652sof9ht/themes/default/template/helpers/calendar/calendar.tpl',
      1 => 1454062117,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '162789938056ab8d5b141195-25722595',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'is_rtl' => 0,
    'date_to' => 0,
    'date_format' => 0,
    'date_from' => 0,
    'actions' => 0,
    'action' => 0,
    'compare_date_from' => 0,
    'compare_date_to' => 0,
    'compare_option' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab8d5b2085f9_42378895',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab8d5b2085f9_42378895')) {function content_56ab8d5b2085f9_42378895($_smarty_tpl) {?><div id="datepicker" class="row row-padding-top hide">
	<div class="col-lg-12">
		<div class="daterangepicker-days">
			<div class="row">
				<?php if ($_smarty_tpl->tpl_vars['is_rtl']->value) {?>
				<div class="col-sm-6 col-lg-4">
					<div class="datepicker2" data-date="<?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
"></div>
				</div>
				<div class="col-sm-6 col-lg-4">
					<div class="datepicker1" data-date="<?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
"></div>
				</div>
				<?php } else { ?>
				<div class="col-sm-6 col-lg-4">
					<div class="datepicker1" data-date="<?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
"></div>
				</div>
				<div class="col-sm-6 col-lg-4">
					<div class="datepicker2" data-date="<?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
"></div>
				</div>
				<?php }?>
				<div class="col-xs-12 col-sm-6 col-lg-4 pull-right">
					<div id='datepicker-form' class='form-inline'>
						<div id='date-range' class='form-date-group'>
							<div  class='form-date-heading'>
								<span class="title"><?php echo smartyTranslate(array('s'=>'Date range'),$_smarty_tpl);?>
</span>
								<?php if (isset($_smarty_tpl->tpl_vars['actions']->value)&&count($_smarty_tpl->tpl_vars['actions']->value)>0) {?>
									<?php if (count($_smarty_tpl->tpl_vars['actions']->value)>1) {?>
									<button class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown' type="button">
										<?php echo smartyTranslate(array('s'=>'Custom'),$_smarty_tpl);?>

										<i class='icon-angle-down'></i>
									</button>
									<ul class='dropdown-menu'>
										<?php  $_smarty_tpl->tpl_vars['action'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['action']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['action']->key => $_smarty_tpl->tpl_vars['action']->value) {
$_smarty_tpl->tpl_vars['action']->_loop = true;
?>
										<li><a<?php if (isset($_smarty_tpl->tpl_vars['action']->value['href'])) {?> href="<?php echo $_smarty_tpl->tpl_vars['action']->value['href'];?>
"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['action']->value['class'])) {?> class="<?php echo $_smarty_tpl->tpl_vars['action']->value['class'];?>
"<?php }?>><?php if (isset($_smarty_tpl->tpl_vars['action']->value['icon'])) {?><i class="<?php echo $_smarty_tpl->tpl_vars['action']->value['icon'];?>
"></i> <?php }?><?php echo $_smarty_tpl->tpl_vars['action']->value['label'];?>
</a></li>
										<?php } ?>
									</ul>
									<?php } else { ?>
									<a<?php if (isset($_smarty_tpl->tpl_vars['actions']->value[0]['href'])) {?> href="<?php echo $_smarty_tpl->tpl_vars['actions']->value[0]['href'];?>
"<?php }?> class="btn btn-default btn-xs pull-right<?php if (isset($_smarty_tpl->tpl_vars['actions']->value[0]['class'])) {?> <?php echo $_smarty_tpl->tpl_vars['actions']->value[0]['class'];?>
<?php }?>"><?php if (isset($_smarty_tpl->tpl_vars['actions']->value[0]['icon'])) {?><i class="<?php echo $_smarty_tpl->tpl_vars['actions']->value[0]['icon'];?>
"></i> <?php }?><?php echo $_smarty_tpl->tpl_vars['actions']->value[0]['label'];?>
</a>
									<?php }?>
								<?php }?>
							</div>
							<div class='form-date-body'>
								<label><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</label>
								<input class='date-input form-control' id='date-start' placeholder='Start' type='text' name="date_from" value="<?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
" tabindex="1" />
								<label><?php echo smartyTranslate(array('s'=>'to'),$_smarty_tpl);?>
</label>
								<input class='date-input form-control' id='date-end' placeholder='End' type='text' name="date_to" value="<?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
" tabindex="2" />
							</div>
						</div>
						<div id="date-compare" class='form-date-group'>
							<div class='form-date-heading'>
								<span class="checkbox-title">
									<label >
										<input type='checkbox' id="datepicker-compare" name="datepicker_compare"<?php if (isset($_smarty_tpl->tpl_vars['compare_date_from']->value)&&isset($_smarty_tpl->tpl_vars['compare_date_to']->value)) {?> checked="checked"<?php }?> tabindex="3">
										<?php echo smartyTranslate(array('s'=>'Compare to'),$_smarty_tpl);?>

									</label>
								</span>
								<select id="compare-options" class="form-control fixed-width-lg pull-right" name="compare_date_option"<?php if (is_null($_smarty_tpl->tpl_vars['compare_date_from']->value)||is_null($_smarty_tpl->tpl_vars['compare_date_to']->value)) {?> disabled="disabled"<?php }?>>
									<option value="1" <?php if ($_smarty_tpl->tpl_vars['compare_option']->value==1) {?>selected="selected"<?php }?> label="<?php echo smartyTranslate(array('s'=>'Previous period'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Previous period'),$_smarty_tpl);?>
</option>
									<option value="2" <?php if ($_smarty_tpl->tpl_vars['compare_option']->value==2) {?>selected="selected"<?php }?> label="<?php echo smartyTranslate(array('s'=>'Previous Year'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Previous year'),$_smarty_tpl);?>
</option>
									<option value="3" <?php if ($_smarty_tpl->tpl_vars['compare_option']->value==3) {?>selected="selected"<?php }?> label="<?php echo smartyTranslate(array('s'=>'Custom'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Custom'),$_smarty_tpl);?>
</option>
								</select>
							</div>
							<div class="form-date-body" id="form-date-body-compare"<?php if (is_null($_smarty_tpl->tpl_vars['compare_date_from']->value)||is_null($_smarty_tpl->tpl_vars['compare_date_to']->value)) {?> style="display: none;"<?php }?>>
								<label><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</label>
								<input id="date-start-compare" class="date-input form-control" type="text" placeholder="Start" name="compare_date_from" value="<?php echo $_smarty_tpl->tpl_vars['compare_date_from']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
" tabindex="4" />
								<label><?php echo smartyTranslate(array('s'=>'to'),$_smarty_tpl);?>
</label>
								<input id="date-end-compare" class="date-input form-control" type="text" placeholder="End" name="compare_date_to" value="<?php echo $_smarty_tpl->tpl_vars['compare_date_to']->value;?>
" data-date-format="<?php echo $_smarty_tpl->tpl_vars['date_format']->value;?>
"
								tabindex="5" />
							</div>
						</div>
						<div class='form-date-actions'>
							<button class='btn btn-link' type='button' id="datepicker-cancel" tabindex="7">
								<i class='icon-remove'></i>
								<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

							</button>
							<button class='btn btn-default pull-right' type='submit' name="submitDateRange" tabindex="6">
								<i class='icon-ok text-success'></i>
								<?php echo smartyTranslate(array('s'=>'Apply'),$_smarty_tpl);?>

							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	translated_dates = {
		days: ['<?php echo smartyTranslate(array('s'=>'Sunday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Monday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Tuesday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Wednesday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Thursday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Friday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Saturday','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Sunday','js'=>1),$_smarty_tpl);?>
'],
		daysShort: ['<?php echo smartyTranslate(array('s'=>'Sun','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Mon','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Tue','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Wed','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Thu','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Fri','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Sat','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Sun','js'=>1),$_smarty_tpl);?>
'],
		daysMin: ['<?php echo smartyTranslate(array('s'=>'Su','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Mo','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Tu','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'We','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Th','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Fr','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Sa','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Su','js'=>1),$_smarty_tpl);?>
'],
		months: ['<?php echo smartyTranslate(array('s'=>'January','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'February','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'March','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'April','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'May','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'June','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'July','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'August','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'September','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'October','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'November','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'December','js'=>1),$_smarty_tpl);?>
'],
		monthsShort: ['<?php echo smartyTranslate(array('s'=>'Jan','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Feb','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Mar','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Apr','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'May ','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Jun','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Jul','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Aug','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Sep','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Oct','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Nov','js'=>1),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Dec','js'=>1),$_smarty_tpl);?>
']
	};
</script>
<?php }} ?>
