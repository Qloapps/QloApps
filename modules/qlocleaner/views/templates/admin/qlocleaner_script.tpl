{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<script type="text/javascript">
	$(document).ready(function(){
		$("#submitTruncateCatalog").click(function(){
			if ($(\'#checkTruncateCatalog_on\').attr(\'checked\') != "checked")
			{
				alert({l s='Please read the disclaimer and click "Yes" above' mod='qlocleaner'});
				return false;
			}
			if (confirm({l s='Are you sure that you want to delete all catalog data?' mod='qlocleaner'})
				return true;
			return false;
		});
		$("#submitTruncateSales").click(function(){
			if ($(\'#checkTruncateSales_on\').attr(\'checked\') != "checked")
			{
				alert({l s='Please read the disclaimer and click "Yes" above' mod='qlocleaner'});
				return false;
			}
			if (confirm({l s='Are you sure that you want to delete all booking data?' mod='qlocleaner'})
				return true;
			return false;
		});
	});
</script>