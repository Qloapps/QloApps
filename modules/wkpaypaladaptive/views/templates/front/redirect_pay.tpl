<div class="col-md-12 text-center">
	<h1>{l s='If you are not redirected within 10 seconds...' mod='wkpaypaladaptive'}</h1>
	<a class="btn btn-primary" id="paypalredirect" href="{$payPalURL|escape:'html':'UTF-8'}">
		<span>{l s='Click here' mod='wkpaypaladaptive'}</span>
	</a>
</div>
<script type="text/javascript">
	function redirect(){
		document.getElementById("paypalredirect").click();
	}
	setTimeout(redirect, 2000);
</script>