{*
 * 2010-2023 Webkul.
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
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 *}

    <div class="panel-heading">
        <i class="icon-money"></i> {l s='Services' mod='statsserviceproducts'}
    </div>
    {$grid_table_services}
    <a class="btn btn-default export-csv" href="{$export_link_services}">
        <i class="icon-cloud-download"></i> {l s='CSV Export' mod='statsserviceproducts'}
    </a>
</div>
<div class="panel">
    <div class="panel-heading">
        <i class="icon-money"></i> {l s='Facilities' mod='statsserviceproducts'}
    </div>

    {$grid_table_facilities}
    <a class="btn btn-default export-csv" href="{$export_link_facilities}">
        <i class="icon-cloud-download"></i> {l s='CSV Export' mod='statsserviceproducts'}
    </a>