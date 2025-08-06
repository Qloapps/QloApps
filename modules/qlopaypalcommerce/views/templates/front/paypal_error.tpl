{**
* 2010-2021 Webkul.
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
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
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
