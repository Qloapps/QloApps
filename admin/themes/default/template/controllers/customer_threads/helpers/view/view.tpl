{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
{include file="./modal.tpl" }
<div class="panel">
	<div class="panel-heading">
		<i class="icon-comments"></i>
		{l s="Thread"}: <span class="badge">#{$id_customer_thread|intval}</span>
		{if isset($next_thread) && $next_thread}
			<a class="btn btn-default pull-right" href="{$next_thread.href|escape:'html':'UTF-8'}">
				{$next_thread.name} <i class="icon-forward"></i>
			</a> 
		{/if}
	</div>
	<div class="well">
		<form action="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;viewcustomer_thread&amp;id_customer_thread={$id_customer_thread|intval}" method="post" enctype="multipart/form-data" class="form-horizontal">
			{foreach $actions as $action}
				<button class="btn btn-default" name="{$action.name|escape:'html':'UTF-8'}" value="{$action.value|intval}">
					{if isset($action.icon)}<i class="{$action.icon|escape:'html':'UTF-8'}"></i>{/if}{$action.label}
				</button>
			{/foreach}
			<button class="btn btn-default" type="button" data-toggle="modal" data-target="#myModal">
				{l s="Forward this discussion to another employee"}
			</button>
		</form>
	</div>
	<div class="row">
		<div class="message-item-initial media">
			<a href="{if isset($customer->id)}{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&amp;id_customer={$customer->id|intval}&amp;viewcustomer&{else}#{/if}" class="avatar-lg pull-left"><i class="icon-user icon-3x"></i></a>
			<div class="media-body">
				<div class="row">
					<div class="col-sm-6">
					{if isset($customer->firstname)}
						<h2>
							<a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&amp;id_customer={$customer->id|intval}&amp;viewcustomer&">
							{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'} <small>({$customer->email|escape:'html':'UTF-8'})</small>
							</a>
						</h2>
					{else}
						<h2>{$thread->email|escape:'html':'UTF-8'}</h2>
					{/if}
					{if isset($contact) && trim($contact) != ''}
						<span>{l s="To:"} </span><span class="badge">{$contact|escape:'html':'UTF-8'}</span>
					{/if}
					</div>
					{if isset($customer->firstname)}
						<div class="col-sm-6">
							<p>
							{if $count_ok}
								{l s='[1]%1$d[/1] order(s) validated for a total amount of [2]%2$s[/2]' sprintf=[$count_ok, $total_ok] tags=['<span class="badge">', '<span class="badge badge-success">']}
							{else}
								{l s="No orders validated for the moment"}
							{/if}
							</p>
							<p class="text-muted">{l s="Customer since: %s" sprintf=[{dateFormat date=$customer->date_add full=0}]}</p>
						</div>
					{/if}
				</div>
				<div class="row">
					<div class="col-sm-12">
						{if !$first_message.id_employee}
							{include file="./message.tpl" message=$first_message initial=true}
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		{foreach $messages as $message}
			{include file="./message.tpl" message=$message initial=false}
		{/foreach}
	</div>
</div>
<div class="panel">
	<form action="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_customer_thread={$thread->id|intval}&amp;viewcustomer_thread" method="post" enctype="multipart/form-data" class="form-horizontal">
	<h3>{l s="Your answer to"} {if isset($customer->firstname)}{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'} {else} {$thread->email}{/if}</h3>
	<div class="row">
		<div class="media">
			<div class="pull-left">
				<span class="avatar-md">{if isset($current_employee->firstname)}<img src="{$current_employee->getImage()}" alt="">{/if}</span>
			</div>
			<div class="media-body">
				<textarea cols="30" rows="7" name="reply_message">{$PS_CUSTOMER_SERVICE_SIGNATURE|escape:'html':'UTF-8'}</textarea>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<!--
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<i class="icon-magic icon-2x"></i><br>
			{l s="Choose a template"}
		</button>
		-->		
		<button class="btn btn-default pull-right" name="submitReply"><i class="process-icon-mail-reply"></i> {l s="Send"}</button>
		<input type="hidden" name="id_customer_thread" value="{$thread->id|intval}" />
		<input type="hidden" name="msg_email" value="{$thread->email}" />
	</div>
	</form>
</div>

{if count($timeline_items)}
<div class="panel">
	<h3>
		<i class="icon-clock-o"></i>
		{l s="Orders and messages timeline"}
	</h3>
	<div class="timeline">
		{foreach $timeline_items as $dates}
			{foreach from=$dates key=date item=timeline_item}
				{include file="controllers/customer_threads/helpers/view/timeline_item.tpl" timeline_item=$timeline_item}
			{/foreach}
		{/foreach}
	</div>
</div>
{/if}
<script type="text/javascript">
	var timer;
		$(document).ready(function(){
			$('select[name=id_employee_forward]').change(function(){
				if ($(this).val() >= 0)
					$('#message_forward').show(400);
				else
					$('#message_forward').hide(200);
				if ($(this).val() == 0)
					$('#message_forward_email').show(200);
				else
					$('#message_forward_email').hide(200);
			});
			$('textarea[name=message_forward]').click(function(){
				if($(this).val() == '{l s='You can add a comment here.'}')
				{
					$(this).val('');
				}
			});
			timer = setInterval("markAsRead()", 3000);
		});
	
	function markAsRead()
	{
		$.ajax({
			type: 'POST',
			url: 'ajax-tab.php',
			async: true,
			dataType: 'json',
			data: {
				controller: 'AdminCustomerThreads',
				action: 'markAsRead',
				token : '{$token|escape:'html':'UTF-8'}',
				id_thread: {$id_customer_thread}
			}
		});
		clearInterval(timer);
		timer = null;
	}
</script>

{/block}


