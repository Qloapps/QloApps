<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 09:47:42
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin980qbfmdx/themes/default/template/controllers/products/configuration.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10847740615638be549883e5-60854287%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c0a5b048ef11052b8763db0f7d61dff71d54accd' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin980qbfmdx/themes/default/template/controllers/products/configuration.tpl',
      1 => 1446562055,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10847740615638be549883e5-60854287',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5638be54a30660_65937718',
  'variables' => 
  array (
    'product' => 0,
    'htl_room_type' => 0,
    'htl_full_info' => 0,
    'htl_info' => 0,
    'htl_dtl' => 0,
    'htl_room_info' => 0,
    'info' => 0,
    'rm_status' => 0,
    'room_stauts' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5638be54a30660_65937718')) {function content_5638be54a30660_65937718($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)) {?>
<div id="product-configuration" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Configuration" />
	<h3 class="tab"> <i class="icon-AdminAdmin"></i> <?php echo smartyTranslate(array('s'=>'Configuration','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</h3>
	
	<?php if (isset($_smarty_tpl->tpl_vars['htl_room_type']->value)) {?>
		<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['htl_room_type']->value['id'];?>
" name="wk_id_room_type">
	<?php }?>

	<div class="form-group">
		<?php if (isset($_smarty_tpl->tpl_vars['htl_room_type']->value)) {?>
			<label class="control-label col-sm-2" for="hotel_place">
				<?php echo smartyTranslate(array('s'=>'Hotel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['htl_full_info']->value['hotel_name'];?>
" readonly>
				<input type="hidden" name="id_hotel" value="<?php echo $_smarty_tpl->tpl_vars['htl_room_type']->value['id_hotel'];?>
">
			</div>
		<?php } else { ?>
			<label class="control-label col-sm-2" for="hotel_place">
				<?php echo smartyTranslate(array('s'=>'Select Hotel','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</label>
			<div class="col-sm-4">
				<select name="id_hotel" id="hotel_place" class="form-control">
					<?php  $_smarty_tpl->tpl_vars['htl_dtl'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['htl_dtl']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['htl_info']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['htl_dtl']->key => $_smarty_tpl->tpl_vars['htl_dtl']->value) {
$_smarty_tpl->tpl_vars['htl_dtl']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['htl_dtl']->value['id'];?>
" ><?php echo $_smarty_tpl->tpl_vars['htl_dtl']->value['hotel_name'];?>
</option>
					<?php } ?>
				</select>
			</div>
		<?php }?>
	</div>
	
	<div class="form-group">
		<label class="control-label col-sm-2" for="num_adults">
			<?php echo smartyTranslate(array('s'=>'Adults','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

		</label>
		<div class="col-sm-4">
			<input id="num_adults" type="text" name="num_adults" class="form-control" <?php if (isset($_smarty_tpl->tpl_vars['htl_room_type']->value)) {?>value="<?php echo $_smarty_tpl->tpl_vars['htl_room_type']->value['adult'];?>
"<?php }?>>
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-sm-2" for="num_child">
			<?php echo smartyTranslate(array('s'=>'Childrens','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

		</label>
		<div class="col-sm-4">
			<input id="num_child" type="text" name="num_child" class="form-control" <?php if (isset($_smarty_tpl->tpl_vars['htl_room_type']->value)) {?>value="<?php echo $_smarty_tpl->tpl_vars['htl_room_type']->value['children'];?>
"<?php }?>>
		</div>
	</div>

	<div class="from-group table-responsive-row clearfix">
		<table class="table hotel-room">
			<thead>
				<tr class="nodrag nodrop">
					<th class="col-sm-1 center">
						<span><?php echo smartyTranslate(array('s'=>'Room No.','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</th>
					<th class="col-sm-2 center">
						<span><?php echo smartyTranslate(array('s'=>'Floor','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</th>
					<th class="col-sm-2">
						<span><?php echo smartyTranslate(array('s'=>'Status','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</th>
					<th class="col-sm-7 center">
						<span><?php echo smartyTranslate(array('s'=>'Comments','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</span>
					</th>
				</tr>
				<?php if (isset($_smarty_tpl->tpl_vars['htl_room_info']->value)&&$_smarty_tpl->tpl_vars['htl_room_info']->value) {?>
					<?php  $_smarty_tpl->tpl_vars['info'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['info']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['htl_room_info']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['info']->key => $_smarty_tpl->tpl_vars['info']->value) {
$_smarty_tpl->tpl_vars['info']->_loop = true;
?>
						<tr class="room_data_values">
							<td class="col-sm-1 center">
								<input class="form-control" type="text" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['room_num'];?>
" name="room_num[]">
							</td>
							<td class="col-sm-2 center">
								<input class="form-control" type="text" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['floor'];?>
" name="room_floor[]">
							</td>
							<td class="col-sm-2 center">
								<select class="form-control" name="room_status[]">
									<?php  $_smarty_tpl->tpl_vars['room_stauts'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['room_stauts']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rm_status']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['room_stauts']->key => $_smarty_tpl->tpl_vars['room_stauts']->value) {
$_smarty_tpl->tpl_vars['room_stauts']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['room_stauts']->value['id'];?>
" <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['room_stauts']->value['id'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_smarty_tpl->tpl_vars['info']->value['id_status']==$_tmp1) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['room_stauts']->value['status'];?>
</option>
									<?php } ?>
								</select>
							</td>
							<td class="center col-sm-6">
								<input type="text" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['comment'];?>
" name="room_comment[]">
							</td>
							<td class="center col-sm-1">
								<a href="#" class="rm_htl_room btn btn-default" data-id-htl-info="<?php echo $_smarty_tpl->tpl_vars['info']->value['id'];?>
"><i class="icon-trash"></i></a>
								<input type="hidden" name="id_room_info[]" value="<?php echo $_smarty_tpl->tpl_vars['info']->value['id'];?>
">
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<?php $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['k']->step = 1;$_smarty_tpl->tpl_vars['k']->total = (int) ceil(($_smarty_tpl->tpl_vars['k']->step > 0 ? 2+1 - (1) : 1-(2)+1)/abs($_smarty_tpl->tpl_vars['k']->step));
if ($_smarty_tpl->tpl_vars['k']->total > 0) {
for ($_smarty_tpl->tpl_vars['k']->value = 1, $_smarty_tpl->tpl_vars['k']->iteration = 1;$_smarty_tpl->tpl_vars['k']->iteration <= $_smarty_tpl->tpl_vars['k']->total;$_smarty_tpl->tpl_vars['k']->value += $_smarty_tpl->tpl_vars['k']->step, $_smarty_tpl->tpl_vars['k']->iteration++) {
$_smarty_tpl->tpl_vars['k']->first = $_smarty_tpl->tpl_vars['k']->iteration == 1;$_smarty_tpl->tpl_vars['k']->last = $_smarty_tpl->tpl_vars['k']->iteration == $_smarty_tpl->tpl_vars['k']->total;?>
						<tr>
							<td class="col-sm-1 center">
								<input class="form-control" type="text" name="room_num[]">
							</td>
							<td class="col-sm-2 center">
								<input class="form-control" type="text" name="room_floor[]">
							</td>
							<td class="col-sm-2 center">
								<select class="form-control" name="room_status[]">
									<?php  $_smarty_tpl->tpl_vars['room_stauts'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['room_stauts']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rm_status']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['room_stauts']->key => $_smarty_tpl->tpl_vars['room_stauts']->value) {
$_smarty_tpl->tpl_vars['room_stauts']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['room_stauts']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['room_stauts']->value['status'];?>
</option>
									<?php } ?>
								</select>
							</td>
							<td class="center col-sm-6">
								<input type="text" class="form-control" name="room_comment[]">
							</td>
						</tr>
					<?php }} ?>
				<?php }?>
			</thead>
		</table>
		<div class="form-group">
			<div class="col-sm-12">
				<button id="add-more-rooms-button" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
					<i class="icon-folder-open"></i>
					<?php echo smartyTranslate(array('s'=>'Add More Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

				</button>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>&amp;submitFilterproduct=<?php echo intval($_REQUEST['page']);?>
<?php }?>" class="btn btn-default">
			<i class="process-icon-cancel"></i>
			<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

		</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled">
			<i class="process-icon-loading"></i>
			<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>

		</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled">
			<i class="process-icon-loading"></i>
				<?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>

		</button>
	</div>
</div>
<?php }?>

<style>
	
	.hotel-room
	{
		border: 1px solid #f2f2f2;
		margin-top: 10px;
	}

</style>


<script>
	var prod_link = "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts');?>
";
	var rm_status = <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['rm_status']->value);?>
;

	$(document).ready(function()
	{
		$('#add-more-rooms-button').on('click',function()
		{
			html = '<tr class="room_data_values">';
				html += '<td class="col-sm-1 center">';
					html += '<input class="form-control" type="text" name="room_num[]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<input class="form-control" type="text" name="room_floor[]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<select class="form-control" name="room_status[]">';
						$.each(rm_status, function(key, value)
						{
							html += '<option value="'+value.id+'"">'+value.status+'</option>';
						});
					html += '</select>';
				html += '</td>';
				html += '<td class="center col-sm-6">';
					html += '<input type="text" class="form-control" name="room_comment[]">';
				html += '</td>';
				html += '<td class="center col-sm-1">';
					html += '<a href="#" class="remove-rooms-button btn btn-default"><i class="icon-trash"></i></a>';
				html += '</td>';
			html += '</tr>';

			$('.hotel-room').append(html);
		});

		$('.rm_htl_room').on('click',function(e) 
		{
			e.preventDefault();

			var id_htl_info = $(this).attr('data-id-htl-info');
			$.ajax(
			{
	            url: prod_link,
	            type: 'POST',
	            dataType: 'text',
	            data: {
	            	ajax:true,
	            	action:'deleteHotelRoom',
	            	id: id_htl_info,
	            },
	            success: function (result)
	            {
	            	if (parseInt(result) == 1)
	            	{
		               	showSuccessMessage("<?php echo smartyTranslate(array('s'=>'Remove successful'),$_smarty_tpl);?>
");
	            	}
	            }
	        });
			$(this).closest(".room_data_values").remove();
		});

		$(document).on('click','.remove-rooms-button',function(e) 
		{
			e.preventDefault();
			$(this).closest(".room_data_values").remove();
		});
	});

</script><?php }} ?>
