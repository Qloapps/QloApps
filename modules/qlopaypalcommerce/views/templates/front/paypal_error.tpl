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


<section id="main">
	{if isset($err_msg)}
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-danger">
					{l s='Your payment has been failed due to following reason:' mod='qlopaypalcommerce'}
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<td>{l s='Error:' mod='qlopaypalcommerce'}</td>
							<td>{$err_name|escape:'html':'UTF-8'}</td>
						</tr>
						<tr>
							<td>{l s='Error Description:' mod='qlopaypalcommerce'}</td>
							<td>{$err_msg|escape:'html':'UTF-8'}</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p class="cart_navigation clearfix" id="cart_navigation">
					<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" class="button-exclusive btn btn-default">
						<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='qlopaypalcommerce'}
					</a>
				</p>
			</div>
			<div class="clearfix"></div><br/>
		</div>
	{/if}
</section>
