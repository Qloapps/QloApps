<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 16:50:29
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin464itamgr/themes/default/template/controllers/products/booking.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1051161513563b3b7d57eb69-85873703%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '622b45431898f1a2195d2eee7aa889c952282d5b' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin464itamgr/themes/default/template/controllers/products/booking.tpl',
      1 => 1446560570,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1051161513563b3b7d57eb69-85873703',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'htl_config' => 0,
    'date_from' => 0,
    'date_to' => 0,
    'rooms_info' => 0,
    'booking_data' => 0,
    'link' => 0,
    'b_key' => 0,
    'b_val' => 0,
    'data_v' => 0,
    'booking_calendar_data' => 0,
    'check_css_condition_var' => 0,
    'check_calendar_var' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b3b7d67ea39_41930099',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b3b7d67ea39_41930099')) {function content_563b3b7d67ea39_41930099($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
?><?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)&&isset($_smarty_tpl->tpl_vars['htl_config']->value)) {?>
	<div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="Booking"/>
		<h3 class="tab"> <i class="icon-info"></i> <?php echo smartyTranslate(array('s'=>'Booking Information','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</h3>

		<div class="form-group">
			<div class="col-sm-1"></div>
			<label for="from_date" class="control-label col-sm-1 required">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo smartyTranslate(array('s'=>'Booking Date starts from'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'From','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
			</label>
			<div class="col-sm-2">
				<input type="hidden" id="checkTabClick" value="0" name="checkTabClick">
				<input type="text" name="from_date" class="form-control" id="from_date" value="<?php echo $_smarty_tpl->tpl_vars['date_from']->value;?>
">
			</div>

			<label for="to_date" class="control-label col-sm-1 required">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo smartyTranslate(array('s'=>'Booking Date upto'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'To','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
			</label>
			<div class="col-sm-2">
				<input type="text" name="to_date" class="form-control" id="to_date" value="<?php echo $_smarty_tpl->tpl_vars['date_to']->value;?>
">
			</div>
				<input type="hidden" id="product_id" name="id_product" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
">
				<input type="hidden" id="hotel_id" name="id_hotel" value="<?php echo $_smarty_tpl->tpl_vars['rooms_info']->value['id_hotel'];?>
">
				<input type="hidden" id="num_adults" name="num_adults" value="<?php echo $_smarty_tpl->tpl_vars['rooms_info']->value['adult'];?>
">
				<input type="hidden" id="num_children" name="num_children" value="<?php echo $_smarty_tpl->tpl_vars['rooms_info']->value['children'];?>
">
		</div>

		<div class="form-group">
			<div class="col-sm-1"></div>
			<div class="hotel_date col-sm-6">
				<div class="row margin-leftrgt-0">
					<div class="col-sm-12 htl_date_header">
						<div class="col-sm-4">
							<p class="htl_date_disp"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date_from']->value,"%d");?>
</p>
							<span class="htl_month_disp"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date_from']->value,"%b");?>
</span>
						</div>
						<div class="col-sm-2">
							<p class="htl_date_disp">-</p>
						</div>
						<div class="col-sm-3">
							<p class="htl_date_disp"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date_to']->value,"%d");?>
</p>
							<span class="htl_month_disp"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date_to']->value,"%b");?>
</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-sm-5 htl_room_data_cont">
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data">
								<p class="room_cat_header"><?php echo smartyTranslate(array('s'=>'Total Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</p>
								<p class="room_cat_data"><?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['total_rooms'];?>
</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data no_border">
								<p class="room_cat_header"><?php echo smartyTranslate(array('s'=>'Partially Available','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</p>
								<p class="room_cat_data"><?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['num_part_avai'];?>
</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data">
								<p class="room_cat_header"><?php echo smartyTranslate(array('s'=>'Available Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</p>
								<p class="room_cat_data"><?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['num_avail'];?>
</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data no_border">
								<p class="room_cat_header"><?php echo smartyTranslate(array('s'=>'Booked Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</p>
								<p class="room_cat_data"><?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['num_booked'];?>
</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data">
								<p class="room_cat_header"><?php echo smartyTranslate(array('s'=>'Unavailable Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</p>
								<p class="room_cat_data"><?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['num_unavail'];?>
</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-green"></div>
						<span class="indi_label"><?php echo smartyTranslate(array('s'=>'Available Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</div>
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-yellow"></div>
						<span class="indi_label"><?php echo smartyTranslate(array('s'=>'Partially Available','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</div>
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-red"></div>
						<span class="indi_label"><?php echo smartyTranslate(array('s'=>'Unavailable Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</div>
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-gray"></div>
						<span class="indi_label"><?php echo smartyTranslate(array('s'=>'Hold For Maintenance','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-footer">
			<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>&amp;submitFilterproduct=<?php echo intval($_REQUEST['page']);?>
<?php }?>" class="btn btn-default">
				<i class="process-icon-cancel"></i>
				<?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</a>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled" id="stayBookingTab">
				<i class="process-icon-loading"></i>
					<?php echo smartyTranslate(array('s'=>'Display Bookings'),$_smarty_tpl);?>

			</button>
		</div>
	</div>

	<div class="panel">
		<h3 class="tab"> <i class="icon-list"></i> <?php echo smartyTranslate(array('s'=>'LIST OF HOTEL ROOMS','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</h3>
		<div class="form-group">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th><?php echo smartyTranslate(array('s'=>'Room No.','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Status','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Message','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</th>
						</tr>
					</thead>
					<tbody>
						<?php  $_smarty_tpl->tpl_vars['b_val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['b_val']->_loop = false;
 $_smarty_tpl->tpl_vars['b_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['booking_data']->value['rm_data'][0]['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['b_val']->key => $_smarty_tpl->tpl_vars['b_val']->value) {
$_smarty_tpl->tpl_vars['b_val']->_loop = true;
 $_smarty_tpl->tpl_vars['b_key']->value = $_smarty_tpl->tpl_vars['b_val']->key;
?>
							<?php if (($_smarty_tpl->tpl_vars['b_key']->value=='available')&&!empty($_smarty_tpl->tpl_vars['b_val']->value)) {?>
								<?php  $_smarty_tpl->tpl_vars['data_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data_v']->_loop = false;
 $_smarty_tpl->tpl_vars['data_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['b_val']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data_v']->key => $_smarty_tpl->tpl_vars['data_v']->value) {
$_smarty_tpl->tpl_vars['data_v']->_loop = true;
 $_smarty_tpl->tpl_vars['data_k']->value = $_smarty_tpl->tpl_vars['data_v']->key;
?>
									<tr>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['room_num'];?>
</td>
										<td><?php echo smartyTranslate(array('s'=>'Available','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</td>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['room_comment'];?>
</td>
									</tr>
								<?php } ?>
							<?php } elseif (($_smarty_tpl->tpl_vars['b_key']->value=='unavailable')&&!empty($_smarty_tpl->tpl_vars['b_val']->value)) {?>
								<?php  $_smarty_tpl->tpl_vars['data_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data_v']->_loop = false;
 $_smarty_tpl->tpl_vars['data_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['b_val']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data_v']->key => $_smarty_tpl->tpl_vars['data_v']->value) {
$_smarty_tpl->tpl_vars['data_v']->_loop = true;
 $_smarty_tpl->tpl_vars['data_k']->value = $_smarty_tpl->tpl_vars['data_v']->key;
?>
									<tr>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['room_num'];?>
</td>
										<td><?php echo smartyTranslate(array('s'=>'Unavailable','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</td>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['room_comment'];?>
</td>
									</tr>
								<?php } ?>
							<?php } elseif (($_smarty_tpl->tpl_vars['b_key']->value=='booked')&&!empty($_smarty_tpl->tpl_vars['b_val']->value)) {?>
								<?php  $_smarty_tpl->tpl_vars['data_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data_v']->_loop = false;
 $_smarty_tpl->tpl_vars['data_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['b_val']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data_v']->key => $_smarty_tpl->tpl_vars['data_v']->value) {
$_smarty_tpl->tpl_vars['data_v']->_loop = true;
 $_smarty_tpl->tpl_vars['data_k']->value = $_smarty_tpl->tpl_vars['data_v']->key;
?>
									<tr>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['room_num'];?>
</td>
										<td>
											<?php if ($_smarty_tpl->tpl_vars['data_v']->value['booking_status']==2) {?>
												<?php echo smartyTranslate(array('s'=>'Alloted','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

											<?php } elseif ($_smarty_tpl->tpl_vars['data_v']->value['booking_status']==3) {?>
												<?php echo smartyTranslate(array('s'=>'Checked-in','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

											<?php } elseif ($_smarty_tpl->tpl_vars['data_v']->value['booking_status']==4) {?>
												<?php echo smartyTranslate(array('s'=>'Checked-out','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

											<?php }?>
										</td>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['comment'];?>
</td>
									</tr>
								<?php } ?>
							<?php } elseif (($_smarty_tpl->tpl_vars['b_key']->value=='partially_available')&&!empty($_smarty_tpl->tpl_vars['b_val']->value)) {?>
								<?php  $_smarty_tpl->tpl_vars['data_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data_v']->_loop = false;
 $_smarty_tpl->tpl_vars['data_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['b_val']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data_v']->key => $_smarty_tpl->tpl_vars['data_v']->value) {
$_smarty_tpl->tpl_vars['data_v']->_loop = true;
 $_smarty_tpl->tpl_vars['data_k']->value = $_smarty_tpl->tpl_vars['data_v']->key;
?>
									<tr>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['room_num'];?>
</td>
										<td><?php echo smartyTranslate(array('s'=>'Partially Available','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</td>
										<td><?php echo $_smarty_tpl->tpl_vars['data_v']->value['comment'];?>
</td>
									</tr>
								<?php } ?>
							<?php }?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php }?>

<script type="text/javascript">

$(document).ready(function()
{	
	var booking_calendar_data = '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['booking_calendar_data']->value);?>
';
	var check_css_condition_var = '<?php echo $_smarty_tpl->tpl_vars['check_css_condition_var']->value;?>
';
	var check_calendar_var = '<?php echo $_smarty_tpl->tpl_vars['check_calendar_var']->value;?>
';

	$("#from_date, #to_date").datepicker(
	{
		dateFormat: 'yy-mm-dd'
	});
	if (booking_calendar_data != 'null' && check_css_condition_var && check_calendar_var)
	{
		if (typeof(booking_calendar_data) != 'undefined')
	    {
	        var calendar_data = JSON.parse(booking_calendar_data);

	        $(".hotel_date").datepicker(
	        {
	            defaultDate: new Date(),
	            dateFormat: 'yy-mm-dd',
	            onChangeMonthYear: function(year, month)
	            {
	                if (check_calendar_var)
	                    $.ajax({
	                        url: "<?php echo addslashes($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'));?>
",
	                        data: {
	                            ajax:true,
	                            action:'ProductRoomsBookingDetailsOnMonthChange',
	                            month:month,
	                            year:year,
	                            id_product:$('#product_id').val(),
	                            id_hotel:$('#hotel_id').val(),
	                            num_adults:$('#num_adults').val(),
	                            num_children:$('#num_children').val(),
	                        },
	                        method:'POST',
	                        async: false,
	                        success: function (result)
	                        {
	                            calendar_data = JSON.parse(result);
	                        },
	                        error: function(XMLHttpRequest, textStatus, errorThrown)
	                        {
	                            alert(textStatus);
	                        }
	                    });
	            },
	            beforeShowDay: function(date)
	            {
	                var currentMonth = date.getMonth() + 1;
	                var currentDate = date.getDate();
	                if (currentMonth < 10)
	                {
	                    currentMonth = '0' + currentMonth;
	                }
	                if (currentDate < 10)
	                {
	                    currentDate = '0' + currentDate;
	                }

	                dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
	                var flag = 0;

	                $.each(calendar_data, function(key, value)
	                {
	                    if (key === dmy)
	                    {
	                        msg = 'Total Available : '+value.stats.num_avail+'&#013;Total Partail Available : '+value.stats.num_part_avai+'&#013;Total Unvailable : '+value.stats.num_unavail+'&#013;Total Booked : '+value.stats.num_booked;
	                        flag = 1;
	                        return 1;
	                    }
	                });
	                if (flag)
	                {
	                    return [true, check_css_condition_var, msg];
	                }
	                else
	                    return [true];
	            }
	        });
	        
	        var count = $("."+check_css_condition_var).length;
	        //$("td."+check_css_condition_var).eq(0).css('border-radius','50% 0 0 50%');
	        $("td."+check_css_condition_var).eq(count-1).css('border-radius','0 50% 50% 0');    
	    }
	    else
	    {
	    	$(".hotel_date").datepicker(
	        {
	            dateFormat: 'yy-mm-dd'
	        });
	    }
	}

    $("#stayBookingTab").on("click", function()
    {
    	$("#checkTabClick").val(1);
    });
});
	
</script><?php }} ?>
