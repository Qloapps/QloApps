{**
 * 2010-2022 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2022 Webkul IN
 * @license LICENSE.txt
 *}

{extends file='helpers/form/form.tpl'}

{block name='fieldset'}
	<div class="panel" id="general">
		<div class="panel-heading">
			<i class="icon-info-circle"></i>
			{l s='Link Information' mod='hotelreservationsystem'}
		</div>

		<div class="form-wrapper" id="fields-meta-tags-rule">
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Enable to show this link.' mod='hotelreservationsystem'}">
						{l s='Enabled' mod='hotelreservationsystem'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="active" id="active_on" value="1"
							{if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if}>
						<label for="active_on">{l s='Yes' mod='hotelreservationsystem'}</label>
						<input type="radio" name="active" id="active_off" value="0"
							{if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if}>
						<label for="active_off">{l s='No' mod='hotelreservationsystem'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Enter a memorable name for this link.' mod='hotelreservationsystem'}">
						{l s='Name' mod='hotelreservationsystem'}
					</span>
				</label>
				<div class="col-lg-9">
					{if $languages|count > 0}
						{foreach $languages as $language}
							{assign var='value_text' value=$currentTab->getFieldValue($currentObject, 'name', $language.id_lang)}
							<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
								<div class="col-lg-6">
									<input type="text" name="name_{$language.id_lang}" class="form-control" value="{$value_text|escape:'html':'UTF-8'}">
								</div>
								{if $languages|count > 1}
									<div class="col-lg-2">
										<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
											{$language.iso_code}
											<i class="icon-caret-down"></i>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
											{/foreach}
										</ul>
									</div>
								{/if}
							</div>
						{/foreach}
					{/if}
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Enter a hint for this link.' mod='hotelreservationsystem'}">
						{l s='Hint' mod='hotelreservationsystem'}
					</span>
				</label>
				<div class="col-lg-9">
					{if $languages|count > 0}
						{foreach $languages as $language}
							{assign var='value_text' value=$currentTab->getFieldValue($currentObject, 'hint', $language.id_lang)}
							<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
								<div class="col-lg-6">
									<input type="text" name="hint_{$language.id_lang}" class="form-control" value="{$value_text|escape:'html':'UTF-8'}">
								</div>
								{if $languages|count > 1}
									<div class="col-lg-2">
										<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
											{$language.iso_code}
											<i class="icon-caret-down"></i>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
												<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
											{/foreach}
										</ul>
									</div>
								{/if}
							</div>
						{/foreach}
					{/if}
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Set icon class for this link.' mod='hotelreservationsystem'}">
						{l s='Icon' mod='hotelreservationsystem'}
					</span>
				</label>
				<div class="col-lg-5">
					<input name="icon" class="form-control" placeholder="{l s='Eg. icon-user' mod='hotelreservationsystem'}" value="{$currentTab->getFieldValue($currentObject, 'icon')|escape}">
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='Refer to the following link for available icons.' mod='hotelreservationsystem'}
						<a href="https://fontawesome.com/v4/cheatsheet" target="_blank">
							https://fontawesome.com/v4/cheatsheet <i class="icon icon-external-link"></i>
						</a>
						{l s=' Note: Replace fa- with icon- in icon name.' mod='hotelreservationsystem'}
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Set URL of the desired page. If it is a Back office URL please remove the token from it.' mod='hotelreservationsystem'}">
						{l s='URL' mod='hotelreservationsystem'}
					</span>
				</label>
				<div class="col-lg-5">
					<input name="link" class="form-control" value="{$currentTab->getFieldValue($currentObject, 'link')|escape}">
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Enable to open the link in a new window.' mod='hotelreservationsystem'}">
						{l s='Open in new window' mod='hotelreservationsystem'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="new_window" id="new_window_on" value="1"
							{if $currentTab->getFieldValue($currentObject, 'new_window')|intval}checked="checked"{/if}>
						<label for="new_window_on">{l s='Yes' mod='hotelreservationsystem'}</label>
						<input type="radio" name="new_window" id="new_window_off" value="0"
							{if !$currentTab->getFieldValue($currentObject, 'new_window')|intval}checked="checked"{/if}>
						<label for="new_window_off">{l s='No' mod='hotelreservationsystem'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button type="submit" value="1" name="submitHotelSettingsLink" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>
				{l s='Save' mod='hotelreservationsystem'}
			</button>
			<button type="submit" value="1" name="submitHotelSettingsLinkAndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>
				{l s='Save and stay' mod='hotelreservationsystem'}
			</button>
			<a href="{$link->getAdminLink('AdminHotelConfigurationSetting')}&display=list" class="btn btn-default" onclick="window.history.back();">
				<i class="process-icon-back"></i>
				{l s='Back to list' mod='hotelreservationsystem'}
			</a>
		</div>
	</div>
{/block}
