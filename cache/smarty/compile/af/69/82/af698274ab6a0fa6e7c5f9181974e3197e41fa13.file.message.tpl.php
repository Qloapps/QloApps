<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:33
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/customer_threads/message.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2090485952568df21de3ece1-43310744%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af698274ab6a0fa6e7c5f9181974e3197e41fa13' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/customer_threads/message.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2090485952568df21de3ece1-43310744',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'email' => 0,
    'message' => 0,
    'employee_avatar' => 0,
    'PS_SHOP_NAME' => 0,
    'file_name' => 0,
    'is_valid_order_id' => 0,
    'current' => 0,
    'token' => 0,
    'contacts' => 0,
    'contact' => 0,
    'id_employee' => 0,
    'thread_url' => 0,
    'PS_CUSTOMER_SERVICE_SIGNATURE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df21df3cdd7_43391260',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df21df3cdd7_43391260')) {function content_568df21df3cdd7_43391260($_smarty_tpl) {?>

<?php if (!$_smarty_tpl->tpl_vars['email']->value) {?>
	<div class="panel">
		<h3>
			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['employee_name'])) {?>
				<i><?php echo $_smarty_tpl->tpl_vars['employee_avatar']->value;?>
</i>
					 (<?php echo $_smarty_tpl->tpl_vars['message']->value['employee_name'];?>
) - <?php echo $_smarty_tpl->tpl_vars['PS_SHOP_NAME']->value;?>
 
			<?php } else { ?>
				<i class="icon-user"></i> 
				<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_customer'])) {?>
					<a href="index.php?tab=AdminCustomers&amp;id_customer=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer'];?>
&amp;viewcustomer&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'View customer'),$_smarty_tpl);?>
">
						<?php echo $_smarty_tpl->tpl_vars['message']->value['customer_name'];?>

					</a>
				<?php } else { ?>
					<?php echo $_smarty_tpl->tpl_vars['message']->value['email'];?>

				<?php }?>
			<?php }?>
		</h3>
		<div class="infoCustomer">
			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_customer'])&&empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Customer ID:'),$_smarty_tpl);?>
</dt> 
				<dd>
					<a href="index.php?tab=AdminCustomers&amp;id_customer=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer'];?>
&amp;viewcustomer&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'View customer'),$_smarty_tpl);?>
">
						<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer'];?>
 <i class="icon-search"></i>
					</a>
				</dd>
			</dl>
			<?php }?>
			<dl class="dl-horizontal">			
				<dt><?php echo smartyTranslate(array('s'=>'Sent on:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['date_add'];?>
&nbsp;</dd>
			</dl>
			<?php if (empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Browser:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['user_agent'];?>
&nbsp;</dd>
			</dl>
			<?php }?>
			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['file_name'])&&$_smarty_tpl->tpl_vars['file_name']->value) {?>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'File attachment'),$_smarty_tpl);?>
</dt> 
				<dd>
					<a href="index.php?tab=AdminCustomerThreads&amp;id_customer_thread=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_thread'];?>
&amp;viewcustomer_thread&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomerThreads'),$_smarty_tpl);?>
&amp;filename=<?php echo $_smarty_tpl->tpl_vars['message']->value['file_name'];?>
"
					title="<?php echo smartyTranslate(array('s'=>'View file'),$_smarty_tpl);?>
">
						<i class="icon-search"></i>
					</a>
				</dd>
			</dl>
			<?php }?>
			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_order'])&&$_smarty_tpl->tpl_vars['is_valid_order_id']->value&&empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
				<dl class="dl-horizontal">
					<dt><?php echo smartyTranslate(array('s'=>'Order #'),$_smarty_tpl);?>
</dt> 
					<dd><a href="index.php?tab=AdminOrders&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_order'];?>
&amp;vieworder&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'View order'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['message']->value['id_order'];?>
 <img src="../img/admin/search.gif" alt="<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
" /></a>
					</dd>
				</dl>
			<?php }?>
			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_product'])&&empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
				<dl class="dl-horizontal">
					<dt><?php echo smartyTranslate(array('s'=>'Product #'),$_smarty_tpl);?>
</dt> 
					<dd><a href="index.php?tab=AdminProducts&amp;id_product=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_product'];?>
&amp;updateproduct&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'View order'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['message']->value['id_product'];?>
 <img src="../img/admin/search.gif" alt="<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
" /></a></dd>
				</dl>
			<?php }?>
			
			<form class="form-inline" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;id_customer_thread=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_thread'];?>
&amp;viewcustomer_thread" method="post" >
				<input type="hidden" name="id_customer_message" value="<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_message'];?>
" />
				<div class="form-group">
					<dl class="dl-horizontal">
						<dt><?php echo smartyTranslate(array('s'=>'Subject:'),$_smarty_tpl);?>
</dt>
						<dd>
							<select name="id_contact" class="fixed-width-lg" onchange="this.form.submit();">
								<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value) {
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['contact']->value['id_contact'];?>
" <?php if ($_smarty_tpl->tpl_vars['contact']->value['id_contact']==$_smarty_tpl->tpl_vars['message']->value['id_contact']) {?>selected="selected"<?php }?>>
										<?php echo $_smarty_tpl->tpl_vars['contact']->value['name'];?>

									</option>
								<?php } ?>
							</select>
						</dd>
					</dl>
				</div>
			</form>
		</div>
<?php } else { ?>
		<div class="infoEmployee">
			<?php if ($_smarty_tpl->tpl_vars['id_employee']->value) {?>
				<a class="btn btn-default pull-right" href="<?php echo $_smarty_tpl->tpl_vars['thread_url']->value;?>
">
					<?php echo smartyTranslate(array('s'=>'View this thread'),$_smarty_tpl);?>

				</a>
			<?php }?>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Sent by:'),$_smarty_tpl);?>
</dt>
				<dd>
					<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['customer_name'])) {?>
						<?php echo $_smarty_tpl->tpl_vars['message']->value['customer_name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['message']->value['email'];?>
)
					<?php } else { ?>
						<?php echo $_smarty_tpl->tpl_vars['message']->value['email'];?>

					<?php }?>
				</dd>
			</dl>

			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_customer'])&&empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
				<!--<dl class="dl-horizontal">
					<dt><?php echo smartyTranslate(array('s'=>'Customer ID:'),$_smarty_tpl);?>
</dt>
					<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer'];?>
</dd>
				</dl>-->
			<?php }?>

			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_order'])&&empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
				<!--<dl class="dl-horizontal">
					<dt><?php echo smartyTranslate(array('s'=>'Order #'),$_smarty_tpl);?>
:</dt>
					<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['id_order'];?>
</dd>
				</dl>-->
			<?php }?>

			<?php if (!empty($_smarty_tpl->tpl_vars['message']->value['id_product'])&&empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
				<!--<dl class="dl-horizontal">
					<dt><?php echo smartyTranslate(array('s'=>'Product #'),$_smarty_tpl);?>
:</dt>
					<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['id_product'];?>
</dd>
				</dl>-->
			<?php }?>

			<!--<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Subject:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['subject'];?>
</dd>
			</dl>-->
<?php }?>
<?php if (!$_smarty_tpl->tpl_vars['email']->value) {?>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Thread ID:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_thread'];?>
</dd>
			</dl>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Message ID:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_message'];?>
</dd>
			</dl>
<?php }?>
			<dl class="dl-horizontal">
				<dt><?php echo smartyTranslate(array('s'=>'Message:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['message'], ENT_QUOTES, 'UTF-8', true));?>
</dd>
			</dl>
		</div>	
<?php if (!$_smarty_tpl->tpl_vars['email']->value) {?>
	</div>
	<?php if (empty($_smarty_tpl->tpl_vars['message']->value['id_employee'])) {?>
		<div class="panel">
			<button class="btn btn-default"
				onclick="$('#reply_to_<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_message'];?>
').show(500); $(this).parent().hide();">
				<i class="icon-mail-reply"></i> <?php echo smartyTranslate(array('s'=>'Reply to this message'),$_smarty_tpl);?>

			</button>
		</div>
	<?php }?>
	<div id="reply_to_<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_message'];?>
" style="display: none;">
		<div class="panel">
			<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomerThreads'),$_smarty_tpl);?>
&amp;id_customer_thread=<?php echo intval($_smarty_tpl->tpl_vars['message']->value['id_customer_thread']);?>
&amp;viewcustomer_thread=1" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="panel-heading">
					<?php echo smartyTranslate(array('s'=>'Please type your reply below:'),$_smarty_tpl);?>

				</div>
				<div class="row row-margin-bottom">
					<textarea class="col-lg-12" rows="6" name="reply_message"><?php echo $_smarty_tpl->tpl_vars['PS_CUSTOMER_SERVICE_SIGNATURE']->value;?>
</textarea>
				</div>
				<div class="row">
					<p class="pull-right"><?php echo smartyTranslate(array('s'=>'Your reply will be sent to:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['message']->value['email'];?>
</p>
				</div>
				<div class="row row-margin-bottom">
					<input type="file" name="joinFile"/>
				</div>
				<div class="row">
					<button type="submit" class="btn btn-default" name="submitReply">
						<i class="icon-check"></i> <?php echo smartyTranslate(array('s'=>'Send my reply'),$_smarty_tpl);?>
</button>
					<input type="hidden" name="id_customer_thread" value="<?php echo intval($_smarty_tpl->tpl_vars['message']->value['id_customer_thread']);?>
" />
					<input type="hidden" name="msg_email" value="<?php echo $_smarty_tpl->tpl_vars['message']->value['email'];?>
" />
				</div>				
			</form>
		</div>
	</div>
<?php }?>
<?php }} ?>
