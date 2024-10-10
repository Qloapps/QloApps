{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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