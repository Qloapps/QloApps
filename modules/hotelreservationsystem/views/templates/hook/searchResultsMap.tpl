{**
* 2010-2022 Webkul.
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
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*}

<div class="row margin-lr-0 layered_filter_cont" id="search-results-wrap">
    <div class="col-sm-12 layered_filter_heading">
        <div class="row margin-lr-0">
            <div class="pull-left lf_headingmain_wrapper">
                <span>{l s='Directions' mod='hotelreservationsystem'}</span>
                <hr class="theme-text-underline">
            </div>
        </div>
    </div>
    <div class="col-sm-12 lf_sub_cont">
        {if ($hotel->latitude|floatval != 0 && $hotel->longitude|floatval != 0)}
            <div class="map-wrap"></div>
        {/if}
    </div>
</div>
