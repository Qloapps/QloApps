{**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($status) && $status == '1'}
	<p class="alert alert-success">
		{l s='Your order on %s is complete.' sprintf=$shop_name mod='qlopaypalcommerce'}
	</p>
	<div class="box order-confirmation">
		<p>
			-{l s='Total Amount Paid' mod='qlopaypalcommerce'} <span class="price"> <strong>{$total_to_pay|escape:'html':'UTF-8'}</strong></span>
			<br/>-{l s='An email has been sent with this information.' mod='qlopaypalcommerce'}
			<br/><strong>-{l s='Your order will be sent as soon as possible.' mod='qlopaypalcommerce'}</strong>
			<br/>-{l s='If you have questions, comments or concerns, please contact our' mod='qlopaypalcommerce'} <a href="{$link->getPageLink('contact', true)|escape:'htmlall':'UTF-8'}">{l s='expert customer support team' mod='qlopaypalcommerce'}</a>.
		</p>
	</div>
{else}
	<p class="warning">
		{l s='We noticed a problem with your payment. Please contact our' mod='qlopaypalcommerce'}
		 <a href="{$link->getPageLink('contact', true)|escape:'htmlall':'UTF-8'}">{l s='expert customer support team' mod='qlopaypalcommerce'}</a>.
	</p>

{/if}