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

{if isset($hotels_info)}
    <div class="modal-body text-center">
        <div class="row">
            <div class="col-lg-12">
                <form class="defaultForm form-horizontal duplicate-options" action="{$action|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="id_product" value="0" id="duplicate_id_product">
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                title="{l s="Select hotel to assign duplicated room type to."}">
                                {l s="Select hotel"}
                            </span>
                        </label>
                        <div class="col-lg-9">
                            <select name="id_hotel" class="fixed-width-xl" id="duplicate_id_hotel">
                                {foreach $hotels_info as $hotel}
                                    <option value="{$hotel.id_hotel}">
                                        {$hotel.hotel_name|escape:'html':'UTF-8'} - {$hotel.city|escape:'html':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                title="{l s="Choose whether to copy images to duplicated room type."}">
                                {l s="Duplicate images"}
                            </span>
                        </label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="noimage" id="noimage_on" value="0"
                                    {if $duplicate_images|intval}checked="checked"{/if}>
                                <label for="noimage_on">{l s="Yes"}</label>
                                <input type="radio" name="noimage" id="noimage_off" value="1"
                                    {if !$duplicate_images|intval}checked="checked"{/if}>
                                <label for="noimage_off">{l s="No"}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="submitDuplicate" value="1">
                </form>
            </div>
        </div>
    </div>
{/if}
