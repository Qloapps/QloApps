{**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="row">
	<div class="col-lg-12">
		{if isset($paypal_errors)}
			{foreach $paypal_errors as $paypal_error}
				<div class="alert alert-danger">
					{l s='Error Code' mod='wkpaypaladaptive'} : {$paypal_error.errorId|intval}<br/>
					{l s='Message' mod='wkpaypaladaptive'} : {$paypal_error.message|escape:'htmlall':'UTF-8'}
				</div>
			{/foreach}
		{else if isset($exception)}
			<div class="alert alert-danger">
				{$exception}
			</div>
		{else}
			<div class="alert alert-danger">
				{l s='There is some error in payment please contact our customer service department.' mod='wkpaypaladaptive'}
			</div>
		{/if}
	</div>
</div>