<div id="opc_new_account" class="opc-main-block">
	<div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
	<form action="{$link->getPageLink('authentication', true, NULL, "back=order-opc")|escape:'html':'UTF-8'}" method="post" id="login_form">
		<fieldset>
			<div class="already_registered_block">
				<p>
					{l s='Already have an account? '} <a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" id="openLoginFormBlock"> {l s='Login Now'}</a> {l s='to make check process faster and time saving.'}
				</p>
				<p>{l s='Or'}</p>
			</div>
			<p><a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" id="openLoginFormBlock"></a></p>
			<div id="login_form_content" style="display:none;">
				<!-- Error return block -->
				<div id="opc_login_errors" class="alert alert-danger" style="display:none;"></div>
				<!-- END Error return block -->
				<p class="form-group">
					<label for="login_email">{l s='Email address'}</label>
					<input type="email" class="form-control validate" id="login_email" name="email" data-validate="isEmail" />
				</p>
				<p class="form-group">
					<label for="login_passwd">{l s='Password'}</label>
					<input class="form-control validate" type="password" id="login_passwd" name="login_passwd" data-validate="isPasswd" />
				</p>
				<a href="{$link->getPageLink('password', true)|escape:'html':'UTF-8'}" class="lost_password pull-right">{l s='Forgot your password?'}</a>
				<div style="clear:both"></div>
				<p class="submit">
					{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
					<button type="submit" id="SubmitLogin" name="SubmitLogin" class="button btn btn-default button-medium pull-right"><span><i class="icon-lock left"></i>{l s='Sign in'}</span></button>
				</p>
			</div>
		</fieldset>
	</form>
	<form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" method="post" id="new_account_form" class="std" autocomplete="on" autofill="on">
		<div id="opc_account_choice" class="row">
			<div class="col-xs-12">
				<div class="opc-button">
					<span>
						<button type="submit" class="opc-button-small opc-btn-primary" id="opc_guestCheckout"><span>{l s='Guest checkout'}</span></button>
					</span>
					<span>
						<button type="submit" class="opc-button-small opc-btn-default" id="opc_createAccount"><span>{l s='Create an account'}</span></button>
					</span>
				</div>
			</div>
		</div>
		<div id="opc_account_form" class="unvisible">
			{$HOOK_CREATE_ACCOUNT_TOP}
			<div style="display: none;" id="opc_account_saved" class="alert alert-success">
				{l s='Account information saved successfully.'}
			</div>
			<!-- Error return block -->
			<div id="opc_account_errors" class="alert alert-danger" style="display:none;"></div>
			<!-- END Error return block -->
			<!-- Account -->
			<input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
			<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && isset($guestInformations.id_customer) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
			<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
			<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
			<p class="required"><sup>*</sup>{l s='Required field'}</p>

			<div class="row">
				<div class="required clearfix gender-line col-sm-2">
					<label>{l s='Social title'}</label>
					<select name="id_gender" id="id_gender">
						{foreach from=$genders key=k item=gender}
							<option value="{$gender->id_gender}"{if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id_gender || (isset($guestInformations) && $guestInformations.id_gender == $gender->id_gender)} selected="selected"{/if}>{$gender->name}</option>
						{/foreach}
					</select>
				</div>
				<div class="required form-group col-sm-5">
					<label for="firstname">{l s='First name'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_firstname) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
				</div>
				<div class="required form-group col-sm-5">
					<label for="lastname">{l s='Last name'} <sup>*</sup></label>
					<input type="text" class="form-control validate" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_lastname) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{/if}" />
				</div>
			</div>

			<div class="row">
				<div class="required text form-group col-sm-6">
					<label for="email">{l s='Email'} <sup>*</sup></label>
					<input type="email" class="text form-control validate" id="email" name="email" data-validate="isEmail" value="{if isset($guestInformations) && isset($guestInformations.email) && $guestInformations.email}{$guestInformations.email}{/if}" />
				</div>
			</div>
			<div class="row">
				<div class="required password is_customer_param form-group col-sm-6">
					<label for="passwd">{l s='Password'} <sup>*</sup></label>
					<input type="password" class="text form-control validate" name="passwd" id="passwd" data-validate="isPasswd" />
					<span class="form_info">{l s='(five characters min.)'}</span>
				</div>
			</div>
			<div class="row">
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-sm-6">
					<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input type="text" class="text form-control validate" name="phone_mobile" id="phone_mobile" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{/if}" />
				</div>
			</div>
			{if $PS_CUSTOMER_ADDRESS_CREATION}
				<div class="row">
					<div class="select form-group date-select col-sm-12">
						<label>{l s='Date of Birth'}</label>
						<div class="row">
							<div class="col-xs-4">
								<select id="days" name="days">
									<option value="">-</option>
									{foreach from=$days item=day}
									<option value="{$day|escape:'html':'UTF-8'}" {if isset($guestInformations) && isset($guestInformations.sl_day) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
									{/foreach}
								</select>
							</div>
							<div class="col-xs-4">
								<select id="months" name="months">
									<option value="">-</option>
									{foreach from=$months key=k item=month}
									<option value="{$k|escape:'html':'UTF-8'}" {if isset($guestInformations) && isset($guestInformations.sl_month) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
									{/foreach}
								</select>
							</div>
							<div class="col-xs-4">
								<select id="years" name="years">
									<option value="">-</option>
									{foreach from=$years item=year}
									<option value="{$year|escape:'html':'UTF-8'}" {if isset($guestInformations) && isset($guestInformations.sl_year) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
				</div>

				{if isset($newsletter) && $newsletter}
					<div class="checkbox">
						<label for="newsletter">
						<input type="checkbox" name="newsletter" id="newsletter" value="1"{if isset($guestInformations) && isset($guestInformations.newsletter) && $guestInformations.newsletter} checked="checked"{/if} autocomplete="off"/>
						{l s='Sign up for our newsletter!'}</label>
						{if array_key_exists('newsletter', $field_required)}
							<sup> *</sup>
						{/if}
					</div>
				{/if}
				{if isset($optin) && $optin}
					<div class="checkbox">
						<label for="optin">
						<input type="checkbox" name="optin" id="optin" value="1"{if isset($guestInformations) && isset($guestInformations.optin) && $guestInformations.optin} checked="checked"{/if} autocomplete="off"/>
						{l s='Receive special offers from our partners!'}</label>
						{if array_key_exists('optin', $field_required)}
							<sup> *</sup>
						{/if}
					</div>
				{/if}

				<p class="block-small-header margin-top-20 margin-btm-10">{l s='RESIDENTIAL ADDRESS'}</p>
				{$stateExist = false}
				{$postCodeExist = false}
				{$dniExist = false}
				<div class="row">
					{foreach from=$dlv_all_fields item=field_name}
						{if $field_name eq "firstname"}
							<div class="required text form-group col-sm-6">
								<label for="firstname">{l s='First name'} <sup>*</sup></label>
								<input type="text" class="text form-control validate" id="firstname" name="firstname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
							</div>
						{elseif $field_name eq "lastname"}
							<div class="required text form-group col-sm-6">
								<label for="lastname">{l s='Last name'} <sup>*</sup></label>
								<input type="text" class="text form-control validate" id="lastname" name="lastname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.lastname) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" />
							</div>
						{elseif $field_name eq "address1"}
							<div class="required text form-group col-sm-6">
								<label for="address1">{l s='Address'} <sup>*</sup></label>
								<input type="text" class="text form-control validate" name="address1" id="address1" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1) && isset($guestInformations) && isset($guestInformations.address1) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
							</div>
						{elseif $field_name eq "address2"}
							<div class="text{if !in_array($field_name, $required_fields)} is_customer_param{/if} form-group col-sm-6">
								<label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="text form-control validate" name="address2" id="address2" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2) && isset($guestInformations) && isset($guestInformations.address2) && $guestInformations.address2}{$guestInformations.address2}{/if}" />
							</div>
						{elseif $field_name eq "city"}
							<div class="required text form-group col-sm-6">
								<label for="city">{l s='City'} <sup>*</sup></label>
								<input type="text" class="text form-control validate" name="city" id="city" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city) && $guestInformations.city}{$guestInformations.city}{/if}" />
							</div>
						{elseif $field_name eq "postcode"}
							{$postCodeExist = true}
							<div class="required postcode text form-group col-sm-6">
								<label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
								<input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}"/>
							</div>
						{elseif $field_name eq "company"}
							<div class="text form-group col-sm-6">
								<label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="text form-control validate" id="company" name="company" data-validate="isGenericName" value="{if isset($guestInformations) && isset($guestInformations.company) && $guestInformations.company}{$guestInformations.company}{/if}" />
							</div>
						{elseif $field_name eq "vat_number"}
							<div id="vat_number_block" style="display:none;">
								<div class="form-group col-sm-6">
									<label for="vat_number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
									<input type="text" class="text form-control" name="vat_number" id="vat_number" value="{if isset($guestInformations) && isset($guestInformations.vat_number) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
								</div>
							</div>
						{elseif $field_name eq "dni"}
							{assign var='dniExist' value=true}
							<div class="required dni form-group col-sm-6">
								<label for="dni">{l s='Identification number'} <sup>*</sup></label>
								<input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
								<span class="form_info">{l s='DNI / NIF / NIE'}</span>
							</div>
						{elseif $field_name eq "country" || $field_name eq "Country:name"}
							<div class="required select form-group col-sm-6">
								<label for="id_country">{l s='Country'} <sup>*</sup></label>
								<select name="id_country" id="id_country">
									{foreach from=$countries item=v}
									<option value="{$v.id_country}"{if (isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country == $v.id_country) || (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
									{/foreach}
								</select>
							</div>
						{elseif $field_name eq "state" || $field_name eq 'State:name'}
							{$stateExist = true}
							<div class="required id_state form-group col-sm-6" style="display:none;">
								<label for="id_state">{l s='State'} <sup>*</sup></label>
								<select name="id_state" id="id_state">
									<option value="">-</option>
								</select>
							</div>
						{/if}
					{/foreach}
					{if !$postCodeExist}
						<div class="required postcode form-group col-sm-6 unvisible">
							<label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
							<input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}"/>
						</div>
					{/if}
					{if !$stateExist}
						<div class="required id_state form-group col-sm-6 unvisible">
							<label for="id_state">{l s='State'} <sup>*</sup></label>
							<select name="id_state" id="id_state">
								<option value="">-</option>
							</select>
						</div>
					{/if}
					{if !$dniExist}
						<div class="required dni form-group col-sm-6">
							<label for="dni">{l s='Identification number'} <sup>*</sup></label>
							<input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
					{/if}
					<div class="form-group is_customer_param col-sm-6">
						<label for="phone">{l s='Home phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
						<input type="text" class="text form-control validate" name="phone" id="phone" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
					</div>
					<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-sm-6">
						<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
						<input type="text" class="text form-control validate" name="phone_mobile" id="phone_mobile" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{/if}" />
					</div>
					<div class="form-group is_customer_param col-sm-6">
						<label for="other">{l s='Additional information'}</label>
						<textarea class="form-control" name="other" id="other" cols="26" rows="7"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						{if isset($one_phone_at_least) && $one_phone_at_least}
							{assign var="atLeastOneExists" value=true}
							<p class="inline-infos required">** {l s='You must register at least one phone number.'}</p>
						{/if}
						<input type="hidden" name="alias" id="alias" value="{l s='My address'}"/>

						{* <div class="checkbox">
							<label for="invoice_address">
							<input type="checkbox" name="invoice_address" id="invoice_address"{if (isset($smarty.post.invoice_address) && $smarty.post.invoice_address) || (isset($guestInformations) && isset($guestInformations.invoice_address) && $guestInformations.invoice_address)} checked="checked"{/if} autocomplete="off"/>
							{l s='Please use another address for invoice'}</label>
						</div> *}
						<p class="required opc-required">
							<sup>*</sup>{l s='Required field'}
						</p>
					</div>
				</div>
			{/if}
			{* <div id="opc_invoice_address" class="is_customer_param">
				{assign var=stateExist value=false}
				{assign var=postCodeExist value=false}
				{assign var='dniExist' value=false}
				<h3 class="page-subheading top-indent">{l s='Additional address'}</h3>
				{foreach from=$inv_all_fields item=field_name}
					{if $field_name eq "company"}
						<div class="form-group">
							<label for="company_invoice">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
							<input type="text" class="text form-control validate" id="company_invoice" name="company_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.company_invoice) && $guestInformations.company_invoice}{$guestInformations.company_invoice}{/if}" />
						</div>
					{elseif $field_name eq "vat_number"}
						<div id="vat_number_block_invoice" class="is_customer_param" style="display:none;">
							<div class="form-group">
								<label for="vat_number_invoice">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="form-control" id="vat_number_invoice" name="vat_number_invoice" value="{if isset($guestInformations) && isset($guestInformations.vat_number_invoice) && $guestInformations.vat_number_invoice}{$guestInformations.vat_number_invoice}{/if}" />
							</div>
						</div>
					{elseif $field_name eq "dni"}
						{assign var='dniExist' value=true}
						<div class="required form-group dni_invoice">
							<label for="dni_invoice">{l s='Identification number'} <sup>*</sup></label>
							<input type="text" class="text form-control validate" name="dni_invoice" id="dni_invoice" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
					{elseif $field_name eq "firstname"}
						<div class="required form-group">
							<label for="firstname_invoice">{l s='First name'} <sup>*</sup></label>
							<input type="text" class="form-control validate" id="firstname_invoice" name="firstname_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname_invoice) && $guestInformations.firstname_invoice}{$guestInformations.firstname_invoice}{/if}" />
						</div>
					{elseif $field_name eq "lastname"}
						<div class="required form-group">
							<label for="lastname_invoice">{l s='Last name'} <sup>*</sup></label>
							<input type="text" class="form-control validate" id="lastname_invoice" name="lastname_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.lastname_invoice) && $guestInformations.lastname_invoice}{$guestInformations.lastname_invoice}{/if}" />
						</div>
					{elseif $field_name eq "address1"}
						<div class="required form-group">
							<label for="address1_invoice">{l s='Address'} <sup>*</sup></label>
							<input type="text" class="form-control validate" name="address1_invoice" id="address1_invoice" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1_invoice) && isset($guestInformations) && isset($guestInformations.address1_invoice) && $guestInformations.address1_invoice}{$guestInformations.address1_invoice}{/if}" />
						</div>
					{elseif $field_name eq "address2"}
						<div class="form-group{if !in_array($field_name, $required_fields)} is_customer_param{/if}">
							<label for="address2_invoice">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
							<input type="text" class="form-control address" name="address2_invoice" id="address2_invoice" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2_invoice) && isset($guestInformations) && isset($guestInformations.address2_invoice) && $guestInformations.address2_invoice}{$guestInformations.address2_invoice}{/if}" />
						</div>
					{elseif $field_name eq "postcode"}
						{$postCodeExist = true}
						<div class="required postcode_invoice form-group">
							<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
							<input type="text" class="form-control validate" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode_invoice) && $guestInformations.postcode_invoice}{$guestInformations.postcode_invoice}{/if}"/>
						</div>
					{elseif $field_name eq "city"}
						<div class="required form-group">
							<label for="city_invoice">{l s='City'} <sup>*</sup></label>
							<input type="text" class="form-control validate" name="city_invoice" id="city_invoice" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city_invoice) && $guestInformations.city_invoice}{$guestInformations.city_invoice}{/if}" />
						</div>
					{elseif $field_name eq "country" || $field_name eq "Country:name"}
						<div class="required form-group">
							<label for="id_country_invoice">{l s='Country'} <sup>*</sup></label>
							<select name="id_country_invoice" id="id_country_invoice" class="form-control">
								<option value="">-</option>
								{foreach from=$countries item=v}
								<option value="{$v.id_country}"{if (isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice == $v.id_country) || (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					{elseif $field_name eq "state" || $field_name eq 'State:name'}
						{$stateExist = true}
						<div class="required id_state_invoice form-group" style="display:none;">
							<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
							<select name="id_state_invoice" id="id_state_invoice" class="form-control">
								<option value="">-</option>
							</select>
						</div>
					{/if}
				{/foreach}
				{if !$postCodeExist}
					<div class="required postcode_invoice form-group unvisible">
						<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
						<input type="text" class="form-control validate" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode_invoice) && $guestInformations.postcode_invoice}{$guestInformations.postcode_invoice}{/if}"/>
					</div>
				{/if}
				{if !$stateExist}
					<div class="required id_state_invoice form-group unvisible">
						<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice" class="form-control">
							<option value="">-</option>
						</select>
					</div>
				{/if}
				{if !$dniExist}
					<div class="required form-group dni_invoice">
						<label for="dni">{l s='Identification number'} <sup>*</sup></label>
						<input type="text" class="text form-control validate" name="dni_invoice" id="dni_invoice" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</div>
				{/if}
					<div class="form-group is_customer_param">
						<label for="other_invoice">{l s='Additional information'}</label>
						<textarea class="form-control" name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
					</div>
				{if isset($one_phone_at_least) && $one_phone_at_least}
					<p class="inline-infos required is_customer_param">{l s='You must register at least one phone number.'}</p>
				{/if}
				<div class="form-group is_customer_param">
					<label for="phone_invoice">{l s='Home phone'}</label>
					<input type="text" class="form-control validate" name="phone_invoice" id="phone_invoice" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_invoice) && $guestInformations.phone_invoice}{$guestInformations.phone_invoice}{/if}" />
				</div>
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
					<label for="phone_mobile_invoice">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
					<input type="text" class="form-control validate" name="phone_mobile_invoice" id="phone_mobile_invoice" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile_invoice) && $guestInformations.phone_mobile_invoice}{$guestInformations.phone_mobile_invoice}{/if}" />
				</div>
				<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" />
			</div> *}
			{$HOOK_CREATE_ACCOUNT_FORM}
			<div class="submit opc-add-save clearfix">
				<button type="submit" name="submitAccount" id="submitAccount" class="btn btn-default button button-medium pull-right"><span>{l s='Save'}<i class="icon-chevron-right right"></i></span></button>

			</div>
		<!-- END Account -->
		</div>
	</form>
</div>
{strip}
{if isset($guestInformations) && isset($guestInformations.id_state) && $guestInformations.id_state}
	{addJsDef idSelectedState=$guestInformations.id_state|intval}
{else}
	{addJsDef idSelectedState=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_state_invoice) && $guestInformations.id_state_invoice}
	{addJsDef idSelectedStateInvoice=$guestInformations.id_state_invoice|intval}
{else}
	{addJsDef idSelectedStateInvoice=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country}
	{addJsDef idSelectedCountry=$guestInformations.id_country|intval}
{else}
	{addJsDef idSelectedCountry=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice}
	{addJsDef idSelectedCountryInvoice=$guestInformations.id_country_invoice|intval}
{else}
	{addJsDef idSelectedCountryInvoice=false}
{/if}
{if isset($countries)}
	{addJsDef countries=$countries}
{/if}
{if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
	{addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
{/if}
{/strip}
