{**
* Since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

    <div class="panel-heading">
        {l s='Services' mod='qlostatsserviceproducts'}
    </div>
    {$grid_table_services}
    <a class="btn btn-default export-csv" href="{$export_link_services}">
        <i class="icon-cloud-download"></i> {l s='CSV Export' mod='qlostatsserviceproducts'}
    </a>
</div>
<div class="panel">
    <div class="panel-heading">
        {l s='Facilities' mod='qlostatsserviceproducts'}
    </div>

    {$grid_table_facilities}
    <a class="btn btn-default export-csv" href="{$export_link_facilities}">
        <i class="icon-cloud-download"></i> {l s='CSV Export' mod='qlostatsserviceproducts'}
    </a>