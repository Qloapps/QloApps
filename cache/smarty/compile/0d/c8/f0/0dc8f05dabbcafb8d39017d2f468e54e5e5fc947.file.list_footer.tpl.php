<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:45:01
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/states/helpers/list/list_footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:207356637856ab3ba5739bc2-59673512%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0dc8f05dabbcafb8d39017d2f468e54e5e5fc947' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/states/helpers/list/list_footer.tpl',
      1 => 1454062128,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '207356637856ab3ba5739bc2-59673512',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'bulk_actions' => 0,
    'params' => 0,
    'key' => 0,
    'zones' => 0,
    'z' => 0,
    'table' => 0,
    'simple_header' => 0,
    'list_total' => 0,
    'selected_pagination' => 0,
    'pagination' => 0,
    'value' => 0,
    'page' => 0,
    'p' => 0,
    'total_pages' => 0,
    'token' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba57df0d5_23077491',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba57df0d5_23077491')) {function content_56ab3ba57df0d5_23077491($_smarty_tpl) {?>

				</table>
			</div>
			<div class="row">
				<div class="col-lg-8">
				<?php if ($_smarty_tpl->tpl_vars['bulk_actions']->value) {?>
					<?php if (count($_smarty_tpl->tpl_vars['bulk_actions']->value)>1) {?>
					<div class="form-group bulk-actions">
						<div class="col-lg-6">
							<select id="select_submitBulk" name="select_submitBulk" >
								<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['bulk_actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value) {
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
									<?php if ($_smarty_tpl->tpl_vars['params']->value['text']=='divider') {?>
										<option value="" disabled="disabled" class="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['params']->value['text'], ENT_QUOTES, 'UTF-8', true);?>
">--</option>
									<?php } else { ?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"<?php if (isset($_smarty_tpl->tpl_vars['params']->value['confirm'])) {?> data-confirm="<?php echo $_smarty_tpl->tpl_vars['params']->value['confirm'];?>
"<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['params']->value['text'], ENT_QUOTES, 'UTF-8', true);?>
</option>
									<?php }?>
								<?php } ?>
							</select>
						</div>
						<div class="col-lg-6">
							<input type="submit" class="btn btn-default" name="submitBulk" id="submitBulk" value="<?php echo smartyTranslate(array('s'=>'Apply'),$_smarty_tpl);?>
" />
						</div>
					</div>
					<?php } else { ?>
						<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['bulk_actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value) {
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
						<div class="form-group bulk-actions">
							<div class="col-lg-6">
							<?php if ($_smarty_tpl->tpl_vars['key']->value=='affectzone') {?>
								<select id="zone_to_affect" name="zone_to_affect">
									<?php  $_smarty_tpl->tpl_vars['z'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['z']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['zones']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['z']->key => $_smarty_tpl->tpl_vars['z']->value) {
$_smarty_tpl->tpl_vars['z']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['z']->value['id_zone'];?>
"><?php echo $_smarty_tpl->tpl_vars['z']->value['name'];?>
</option>
									<?php } ?>
								</select>									
							<?php }?>
							</div>
							<div class="col-lg-6">
								<input type="submit" class="btn btn-default" name="submitBulk<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['params']->value['text'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['params']->value['confirm'])) {?>onclick="return confirm('<?php echo $_smarty_tpl->tpl_vars['params']->value['confirm'];?>
');"<?php }?> />
							</div>
						</div>
						<?php } ?>
					<?php }?>
				<?php }?>
				</div>			
				<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value&&$_smarty_tpl->tpl_vars['list_total']->value>20) {?>
				<div class="col-lg-4">
					
					<div class="pagination">
						<?php echo smartyTranslate(array('s'=>'Display'),$_smarty_tpl);?>
 
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<?php echo $_smarty_tpl->tpl_vars['selected_pagination']->value;?>

							<i class="icon-caret-down"></i>
						</button>
						<ul class="dropdown-menu">
						<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pagination']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
							<li>
								<a href="javascript:void(0);" class="pagination-items-page" data-items="<?php echo intval($_smarty_tpl->tpl_vars['value']->value);?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</a>
							</li>
						<?php } ?>
						</ul>
						/ <?php echo $_smarty_tpl->tpl_vars['list_total']->value;?>
 <?php echo smartyTranslate(array('s'=>'result(s)'),$_smarty_tpl);?>

						<input type="hidden" id="pagination-items-page" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_pagination" value="<?php echo intval($_smarty_tpl->tpl_vars['selected_pagination']->value);?>
" />
					</div>
					<script type="text/javascript">
						$('.pagination-items-page').on('click',function(e){
							e.preventDefault();
							$('#pagination-items-page').val($(this).data("items")).closest("form").submit();
						});
					</script>
					<ul class="pagination pull-right">
						<li <?php if ($_smarty_tpl->tpl_vars['page']->value<=1) {?>class="disabled"<?php }?>>
							<a href="javascript:void(0);" class="pagination-link" data-page="1">
								<i class="icon-double-angle-left"></i>
							</a>
						</li>
						<li <?php if ($_smarty_tpl->tpl_vars['page']->value<=1) {?>class="disabled"<?php }?>>
							<a href="javascript:void(0);" class="pagination-link" data-page="<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
">
								<i class="icon-angle-left"></i>
							</a>
						</li>
						<?php $_smarty_tpl->tpl_vars['p'] = new Smarty_variable(0, null, 0);?>
						<?php while ($_smarty_tpl->tpl_vars['p']->value++<$_smarty_tpl->tpl_vars['total_pages']->value) {?>
							<?php if ($_smarty_tpl->tpl_vars['p']->value<$_smarty_tpl->tpl_vars['page']->value-2) {?>
								<li class="disabled">
									<a href="javascript:void(0);">&hellip;</a>
								</li>
								<?php $_smarty_tpl->tpl_vars['p'] = new Smarty_variable($_smarty_tpl->tpl_vars['page']->value-3, null, 0);?>
							<?php } elseif ($_smarty_tpl->tpl_vars['p']->value>$_smarty_tpl->tpl_vars['page']->value+2) {?>
								<li class="disabled">
									<a href="javascript:void(0);">&hellip;</a>
								</li>
								<?php $_smarty_tpl->tpl_vars['p'] = new Smarty_variable($_smarty_tpl->tpl_vars['total_pages']->value, null, 0);?>
							<?php } else { ?>
								<li <?php if ($_smarty_tpl->tpl_vars['p']->value==$_smarty_tpl->tpl_vars['page']->value) {?>class="active"<?php }?>>
									<a href="javascript:void(0);" class="pagination-link" data-page="<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a>
								</li>
							<?php }?>
						<?php }?>
						<li <?php if ($_smarty_tpl->tpl_vars['page']->value>$_smarty_tpl->tpl_vars['total_pages']->value) {?>class="disabled"<?php }?>>
							<a href="javascript:void(0);" class="pagination-link" data-page="<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
">
								<i class="icon-angle-right"></i>
							</a>
						</li>
						<li <?php if ($_smarty_tpl->tpl_vars['page']->value>$_smarty_tpl->tpl_vars['total_pages']->value) {?>class="disabled"<?php }?>>
							<a href="javascript:void(0);" class="pagination-link" data-page="<?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>
">
								<i class="icon-double-angle-right"></i>
							</a>
						</li>
					</ul>
					<script type="text/javascript">
						$('.pagination-link').on('click',function(e){
							e.preventDefault();
							$('#submitFilter'+'<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').val($(this).data("page")).closest("form").submit();
						});
					</script>
				</div>
				<?php }?>
			</div>
</div>

<input type="hidden" name="token" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
" />
</form>

<script type="text/javascript">
	$(document).ready(function(){
		<?php if (count($_smarty_tpl->tpl_vars['bulk_actions']->value)>1) {?>
			$('#submitBulk').click(function(){
				if ($('#select_submitBulk option:selected').data('confirm') !== undefined)
					return confirm($('#select_submitBulk option:selected').data('confirm'));
				else
					return true;
			});
			$('#select_submitBulk').change(function(){
				if ($(this).val() == 'affectzone')
					loadZones();
				else if (loaded)
					$('#zone_to_affect').fadeOut('slow');
			});
		<?php }?>
	});
	var loaded = false;
	function loadZones()
	{
		if (!loaded)
		{
			$.ajax({
				type: 'POST',
				url: 'ajax.php',
				data: 'getZones=true&token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
',
				async : true,
				cache: false,
				dataType: 'json',
				success: function(data) {
					var html = $(data.data);
					html.hide();
					$('#select_submitBulk').after(html);
					html.fadeIn('slow');
				}
			});
			loaded = true;
		}
		else
		{
			$('#zone_to_affect').fadeIn('slow');
		}
	}
</script>
<?php }} ?>
