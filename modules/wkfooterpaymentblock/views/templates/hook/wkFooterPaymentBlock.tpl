{*
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
	<section class="col-xs-12 col-sm-12">
		<div class="row margin-lr-0 footer-section-heading">
			<p>{l s='payment accepted' mod='wkfooterpaymentblock'}</p>
			<hr/>
		</div>
		<div class="row margin-lr-0 footer-payment-block">
			{if isset($allPaymentBlocks) && $allPaymentBlocks}
				{foreach $allPaymentBlocks as $paymentBlock}
					<img src="{$link->getMediaLink("`$module_dir`views/img/payment_img/`$paymentBlock['id_payment_block']`.jpg")}">
				{/foreach}
			{/if}
		</div>
	</section>
</div>