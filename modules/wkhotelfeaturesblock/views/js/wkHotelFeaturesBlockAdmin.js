/**
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
*/

$(document).ready(function(){
	$("#feature_image").on("change", function(event) {
		if (typeof this.files[0] != 'undefined') {
			if (this.files[0].size > maxSizeAllowed) {
				showErrorMessage(filesizeError);
				$('#feature_image').val(null);
			}
		}
    });
});