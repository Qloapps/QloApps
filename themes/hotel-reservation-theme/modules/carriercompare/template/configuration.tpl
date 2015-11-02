{if isset($display_error)}
	{if $display_error}
		<div class="error">{l s='An error occurred during form validation.' mod='carriercompare'}</div>
	{else}
		<div class="conf">{l s='Configuration updated' mod='carriercompare'}</div>
	{/if}
{/if}

<form method="post" action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}">
	<fieldset>
		<div class="warn">
			{l s='This module is only available during the standard five-step checkout process. The carrier list has already been defined for one-page checkout.' mod='carriercompare'}
		</div>
		<legend>{l s='Global Configuration' mod='carriercompare'}</legend>
		
		<label for="refresh_method">Refresh carrier list method</label>
		<div class="margin-form">
			<select id="refresh_method" name="refresh_method">
				<option value="0" {if $refresh_method == 0}selected{/if}>{l s='Anytime' mod='carriercompare'}</option>
				<option value="1" {if $refresh_method == 1}selected{/if}>{l s='The required information is set.' mod='carriercompare'}</option>
			</select>
			<p>{l s='How would you like to refresh information for a customer?' mod='carriercompare'}</p>
		</div>
		
		<div class="margin-form">
			<input name="setGlobalConfiguration" type="submit" class="button" value="{l s='Submit' mod='carriercompare'}">
		</div>
	</fieldset>
</form>
