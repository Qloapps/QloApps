{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
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

<div class="input-group col-lg-5">
    <input type="text" value="{$productName|escape:'htmlall':'UTF-8'}" name="productName" id="productName" class="form-control" autocomplete="off">
    <input type="hidden" value="{$idProduct|escape:'htmlall':'UTF-8'}" name="id_product" id="id_product" class="form-control">
    <span class="input-group-addon"><i class="icon-search"></i></span>
    <ul class="list-unstyled prod_suggest_ul"></ul>
</div>