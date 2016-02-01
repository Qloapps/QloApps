<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:59
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/orders/current_cart_details_data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:92996897256ab3ba36c95d4-56134865%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '202bd09096c1a26b673be5fb2db7948747d309dd' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/orders/current_cart_details_data.tpl',
      1 => 1454062119,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '92996897256ab3ba36c95d4-56134865',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cart' => 0,
    'cart_detail_data' => 0,
    'data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba370a0e3_69855598',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba370a0e3_69855598')) {function content_56ab3ba370a0e3_69855598($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotelcommerce-master/tools/smarty/plugins/modifier.date_format.php';
?><div class="panel form-horizontal" id="customer_cart_details">
		<div class="panel-heading">
			<i class="icon-shopping-cart"></i>
			<?php echo smartyTranslate(array('s'=>'Cart Details'),$_smarty_tpl);?>

		</div>
		<div class="row">
			<div class="col-lg-12">
				<table class="table" id="customer_cart_details_table">
					<thead>
						<tr>
							<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Room No.'),$_smarty_tpl);?>
</span></th>
							<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Room Image'),$_smarty_tpl);?>
</th>
							<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Room Type'),$_smarty_tpl);?>
</span></th>
							<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Duration'),$_smarty_tpl);?>
</span></th>
							<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Price'),$_smarty_tpl);?>
</span></th>
							<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</span></th>
						</tr>
					</thead>
					<tbody>
					<?php $_smarty_tpl->tpl_vars['curr_id'] = new Smarty_variable(intval($_smarty_tpl->tpl_vars['cart']->value->id_currency), null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['cart_detail_data']->value) {?>
						<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart_detail_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
							<tr>
								<td><?php echo $_smarty_tpl->tpl_vars['data']->value['room_num'];?>
</td>
								<td><img src="<?php echo $_smarty_tpl->tpl_vars['data']->value['image_link'];?>
" title="Room image" /></td>
								<td><?php echo $_smarty_tpl->tpl_vars['data']->value['room_type'];?>
</td>
								<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['date_from'],"%d %b %Y");?>
&nbsp-&nbsp <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['date_to'],"%d %b %Y");?>
</td>
								<td id="cart_detail_data_price_<?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['data']->value['amt_with_qty']),$_smarty_tpl);?>
</td>
								<td>
									<button class="btn btn-primary delete_hotel_cart_data" data-id_room=<?php echo $_smarty_tpl->tpl_vars['data']->value['id_room'];?>
 data-id_product=<?php echo $_smarty_tpl->tpl_vars['data']->value['id_product'];?>
 data-id = <?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
 data-id_cart = <?php echo $_smarty_tpl->tpl_vars['data']->value['id_cart'];?>
 data-date_to = <?php echo $_smarty_tpl->tpl_vars['data']->value['date_to'];?>
 data-date_from = <?php echo $_smarty_tpl->tpl_vars['data']->value['date_from'];?>
><i class="icon-trash"></i>&nbsp<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</button>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td><?php echo smartyTranslate(array('s'=>'No Data Found.'),$_smarty_tpl);?>
</td>
						</tr>
					<?php }?>
					</tbody>
				</table>
			</div>
		</div>
	</div><?php }} ?>
