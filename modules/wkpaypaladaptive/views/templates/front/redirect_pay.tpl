{**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="col-md-12 text-center">
	<h1>{l s='If you are not redirected within 10 seconds...' mod='mppaypaladaptive'}</h1>
	<a class="btn btn-primary" id="paypalredirect" href="{$payPalURL|escape:'html':'UTF-8'}">
		<span>{l s='Click here' mod='mppaypaladaptive'}</span>
	</a>
</div>
<script type="text/javascript">
	function redirect(){
		document.getElementById("paypalredirect").click();
	}
	setTimeout(redirect, 2000);
</script>