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

{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.name == 'vat_number'}
		<div id="vat_area" style="display: visible">
	{/if}
	{$smarty.block.parent}
{/block}

{block name="input"}
	{if $input.type == 'text_customer'}
		{if isset($customer)}
			<a class="btn btn-default" href="?tab=AdminCustomers&amp;id_customer={$customer->id|intval}&amp;viewcustomer&amp;token={$tokenCustomer}">
				<i class="icon-eye-open"></i> {$customer->lastname} {$customer->firstname} ({$customer->email})
			</a>
			<input type="hidden" name="id_customer" value="{$customer->id}" />
			<input type="hidden" name="email" value="{$customer->email}" />
		{/if}
	{else if $input.type == 'select' && $input.name == 'id_customer'}
		{$smarty.block.parent}
		<input type="hidden" name="email" id="email" value="">
		<script type="text/javascript">
			$('#id_customer').on('change', function(e)
			{
				var id_customer = parseInt($(this).val());
				$('#email').val('');
				if (!isNaN(id_customer)) {
					var email = $(this).find('[value="'+id_customer+'"]').text();
					$('#email').val(email);
					var data = {};
					data.email = email;
					data.token = "{$token|escape:'html':'UTF-8'}";
					data.ajax = 1;
					data.controller = "AdminAddresses";
					data.action = "loadNames";
					$.ajax({
						type: "POST",
						url: "ajax-tab.php",
						data: data,
						dataType: 'json',
						async : true,
						success: function(msg) {
							if (msg) {
								var infos = msg.infos.replace("\\'", "'").split('_');
								$('input[name=firstname]').val(infos[0]);
								$('input[name=lastname]').val(infos[1]);
								$('input[name=company]').val(infos[2]);
								$('input[name=id_customer]').val(infos[3]);
								$('input[name=phone]').val(infos[4]);
							} else {
								resetCustomerRelatedAddressFields();
							}
						}
					});
				} else {
					resetCustomerRelatedAddressFields();
				}
			});
			function resetCustomerRelatedAddressFields() {
				$('input[name=firstname]').val('');
				$('input[name=lastname]').val('');
				$('input[name=company]').val('');
				$('input[name=id_customer]').val('');
			}
		</script>
	{else}
		{$smarty.block.parent}
	{/if}
	{if $input.name == 'vat_number'}
		</div>
	{/if}
{/block}
