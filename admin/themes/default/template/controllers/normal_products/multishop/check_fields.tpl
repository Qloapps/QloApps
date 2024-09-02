{*
* Copyright since 2007 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
	<div class="panel clearfix">
		<label class="control-label col-lg-3">
			<i class="icon-sitemap"></i> {l s='Multistore'}
		</label>
		<div class="col-lg-9">
			<div class="row">
				<div class="col-lg-4">
					<span class="switch prestashop-switch">
						<input type="radio" name="multishop_{$product_tab}" id="multishop_{$product_tab}_on" value="1" onclick="$('#product-tab-content-{$product_tab} input[name^=\'multishop_check[\']').attr('checked', true); ProductMultishop.checkAll{$product_tab}()">
						<label for="multishop_{$product_tab}_on">
							{l s='Yes'}
						</label>
						<input type="radio" name="multishop_{$product_tab}" id="multishop_{$product_tab}_off" value="0" checked="checked" onclick="$('#product-tab-content-{$product_tab} input[name^=\'multishop_check[\']').attr('checked', false); ProductMultishop.checkAll{$product_tab}()">
						<label for="multishop_{$product_tab}_off">
							{l s='No'}
						</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<p class="help-block">
						<strong>{l s='Check / Uncheck all'}</strong> {l s='(If you are editing this page for several shops, some fields may be disabled. If you need to edit them, you will need to check the box for each field)'}
					</p>
				</div>
			</div>
		</div>
	</div>
{/if}
