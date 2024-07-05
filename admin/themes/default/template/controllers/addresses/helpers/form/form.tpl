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

	{if $input.type == 'text_customer' && !isset($customer)}
		<label class="control-label col-lg-3 required" for="email">{l s='Customer email'}</label>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="field"}
	{if $input.type == 'text_customer'}
		{if isset($customer)}
			<div class="col-lg-9">
				<a class="btn btn-default" href="?tab=AdminCustomers&amp;id_customer={$customer->id|intval}&amp;viewcustomer&amp;token={$tokenCustomer}">
					<i class="icon-eye-open"></i> {$customer->lastname} {$customer->firstname} ({$customer->email})
				</a>
			</div>
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
					data.id_customer = id_customer;
					data.token = "{$token|escape:'html':'UTF-8'}";
					data.ajax = 1;
					data.controller = "AdminAddresses";
					data.action = "loadCustomer";
					$.ajax({
						type: "POST",
						url: "ajax-tab.php",
						data: data,
						dataType: 'json',
						async : true,
						success: function(response) {
							if (response.status) {
								$('input[name=firstname]').val(response.firstname);
								$('input[name=lastname]').val(response.lastname);
								$('input[name=company]').val(response.company);
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
