{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !$email}
	<div class="panel">
		<h3>
			{if !empty($message.employee_name)}
				<i>{$employee_avatar}</i>
					 ({$message.employee_name}) - {$PS_SHOP_NAME} 
			{else}
				<i class="icon-user"></i> 
				{if !empty($message.id_customer)}
					<a href="index.php?tab=AdminCustomers&amp;id_customer={$message.id_customer}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" title="{l s='View customer'}">
						{$message.customer_name}
					</a>
				{else}
					{$message.email}
				{/if}
			{/if}
		</h3>
		<div class="infoCustomer">
			{if !empty($message.id_customer) && empty($message.id_employee)}
			<dl class="dl-horizontal">
				<dt>{l s='Customer ID:'}</dt> 
				<dd>
					<a href="index.php?tab=AdminCustomers&amp;id_customer={$message.id_customer}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" title="{l s='View customer'}">
						{$message.id_customer} <i class="icon-search"></i>
					</a>
				</dd>
			</dl>
			{/if}
			<dl class="dl-horizontal">			
				<dt>{l s='Sent on:'}</dt>
				<dd>{$message.date_add}&nbsp;</dd>
			</dl>
			{if empty($message.id_employee)}
			<dl class="dl-horizontal">
				<dt>{l s='Browser:'}</dt>
				<dd>{$message.user_agent}&nbsp;</dd>
			</dl>
			{/if}
			{if !empty($message.file_name) && $file_name}
			<dl class="dl-horizontal">
				<dt>{l s='File attachment'}</dt> 
				<dd>
					<a href="index.php?tab=AdminCustomerThreads&amp;id_customer_thread={$message.id_customer_thread}&amp;viewcustomer_thread&amp;token={getAdminToken tab='AdminCustomerThreads'}&amp;filename={$message.file_name}"
					title="{l s='View file'}">
						<i class="icon-search"></i>
					</a>
				</dd>
			</dl>
			{/if}
			{if !empty($message.id_order) && $is_valid_order_id && empty($message.id_employee)}
				<dl class="dl-horizontal">
					<dt>{l s='Order #'}</dt> 
					<dd><a href="index.php?tab=AdminOrders&amp;id_order={$message.id_order}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}" title="{l s='View order'}">{$message.id_order} <img src="../img/admin/search.gif" alt="{l s='View'}" /></a>
					</dd>
				</dl>
			{/if}
			{if !empty($message.id_product) && empty($message.id_employee)}
				<dl class="dl-horizontal">
					<dt>{l s='Product #'}</dt> 
					<dd><a href="index.php?tab=AdminProducts&amp;id_product={$message.id_product}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}" title="{l s='View order'}">{$message.id_product} <img src="../img/admin/search.gif" alt="{l s='View'}" /></a></dd>
				</dl>
			{/if}
			
			<form class="form-inline" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;id_customer_thread={$message.id_customer_thread}&amp;viewcustomer_thread" method="post" >
				<input type="hidden" name="id_customer_message" value="{$message.id_customer_message}" />
				<div class="form-group">
					<dl class="dl-horizontal">
						<dt>{l s='Subject:'}</dt>
						<dd>
							<select name="id_contact" class="fixed-width-lg" onchange="this.form.submit();">
								{foreach $contacts as $contact}
									<option value="{$contact.id_contact}" {if $contact.id_contact == $message.id_contact}selected="selected"{/if}>
										{$contact.name}
									</option>
								{/foreach}
							</select>
						</dd>
					</dl>
				</div>
			</form>
		</div>
{else}
		<div class="infoEmployee">
			{if $id_employee}
				<a class="btn btn-default pull-right" href="{$thread_url}">
					{l s='View this thread'}
				</a>
			{/if}
			<dl class="dl-horizontal">
				<dt>{l s='Sent by:'}</dt>
				<dd>
					{if !empty($message.customer_name)}
						{$message.customer_name} ({$message.email})
					{else}
						{$message.email}
					{/if}
				</dd>
			</dl>

			{if !empty($message.id_customer) && empty($message.id_employee)}
				<!--<dl class="dl-horizontal">
					<dt>{l s='Customer ID:'}</dt>
					<dd>{$message.id_customer}</dd>
				</dl>-->
			{/if}

			{if !empty($message.id_order) && empty($message.id_employee)}
				<!--<dl class="dl-horizontal">
					<dt>{l s='Order #'}:</dt>
					<dd>{$message.id_order}</dd>
				</dl>-->
			{/if}

			{if !empty($message.id_product) && empty($message.id_employee)}
				<!--<dl class="dl-horizontal">
					<dt>{l s='Product #'}:</dt>
					<dd>{$message.id_product}</dd>
				</dl>-->
			{/if}

			<!--<dl class="dl-horizontal">
				<dt>{l s='Subject:'}</dt>
				<dd>{$message.subject}</dd>
			</dl>-->
{/if}
{if !$email}
			<dl class="dl-horizontal">
				<dt>{l s='Thread ID:'}</dt>
				<dd>{$message.id_customer_thread}</dd>
			</dl>
			<dl class="dl-horizontal">
				<dt>{l s='Message ID:'}</dt>
				<dd>{$message.id_customer_message}</dd>
			</dl>
{/if}
			<dl class="dl-horizontal">
				<dt>{l s='Message:'}</dt>
				<dd>{$message.message|escape:'html':'UTF-8'|nl2br}</dd>
			</dl>
		</div>	
{if !$email}
	</div>
	{if empty($message.id_employee)}
		<div class="panel">
			<button class="btn btn-default"
				onclick="$('#reply_to_{$message.id_customer_message}').show(500); $(this).parent().hide();">
				<i class="icon-mail-reply"></i> {l s='Reply to this message'}
			</button>
		</div>
	{/if}
	<div id="reply_to_{$message.id_customer_message}" style="display: none;">
		<div class="panel">
			<form action="{$current|escape:'html':'UTF-8'}&amp;token={getAdminToken tab='AdminCustomerThreads'}&amp;id_customer_thread={$message.id_customer_thread|intval}&amp;viewcustomer_thread=1" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="panel-heading">
					{l s='Please type your reply below:'}
				</div>
				<div class="row row-margin-bottom">
					<textarea class="col-lg-12" rows="6" name="reply_message">{$PS_CUSTOMER_SERVICE_SIGNATURE}</textarea>
				</div>
				<div class="row">
					<p class="pull-right">{l s='Your reply will be sent to:'} {$message.email}</p>
				</div>
				<div class="row row-margin-bottom">
					<input type="file" name="joinFile"/>
				</div>
				<div class="row">
					<button type="submit" class="btn btn-default" name="submitReply">
						<i class="icon-check"></i> {l s='Send my reply'}</button>
					<input type="hidden" name="id_customer_thread" value="{$message.id_customer_thread|intval}" />
					<input type="hidden" name="msg_email" value="{$message.email}" />
				</div>				
			</form>
		</div>
	</div>
{/if}
