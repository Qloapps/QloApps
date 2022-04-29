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

<div class="row">
    <label class="control-label col-lg-3">{l s='Customer' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        <p class="form-control-static">
            <a href="{$link->getAdminLink('AdminCustomers')}&viewcustomer&id_customer={$obj_customer->id}"
                target="_blank">
                {$obj_customer->firstname} {$obj_customer->lastname} (#{$obj_customer->id})
            </a>
        </p>
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Hotel' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        <p class="form-control-static">
            <a href="{$link->getAdminLink('AdminAddHotel')}&updatehtl_branch_info&id={$currentObject->id_hotel}"
                target="_blank">
                {$obj_hotel->hotel_name} (#{$obj_hotel->id})
            </a>
        </p>
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Subject' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        <p class="form-control-static">{$currentObject->subject}</p>
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Description' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        <p class="form-control-static">{$currentObject->description}</p>
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Overall Rating' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        <p class="form-control-static">
            {if $currentObject->rating|floatval < 2}
                <span class="badge badge-danger">{$currentObject->rating|string_format:'%.1f'}</span>
            {elseif $currentObject->rating|floatval < 3.5}
                <span class="badge badge-danger">{$currentObject->rating|string_format:'%.1f'}</span>
            {else}
                <span class="badge badge-danger">{$currentObject->rating|string_format:'%.1f'}</span>
            {/if}
        </p>
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Category Ratings' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        {if count($currentObject->category_ratings)}
            {foreach from=$currentObject->category_ratings item=category}
                <div class="row">
                    <p class="form-control-static">
                        <b>{$category.name|escape:'html':'UTF-8'}:</b>&nbsp;
                        {if $category.rating|floatval < 2}
                            <span class="badge badge-danger">{$category.rating|string_format:'%.1f'}</span>
                        {elseif $category.rating|floatval < 3.5}
                            <span class="badge badge-warning">{$category.rating|string_format:'%.1f'}</span>
                        {else}
                            <span class="badge badge-success">{$category.rating|string_format:'%.1f'}</span>
                        {/if}
                    </p>
                </div>
            {/foreach}
        {else}
            <p class="form-control-static">{l s='No categories available.'}</p>
        {/if}
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Images' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        {if count($images)}
            <div class="row image-row">
                {foreach from=$images item=image}
                    <div class="col-lg-2">
                        <div class="image-wrapp">
                            <div class="image-wrap">
                                <a href="{$image}" target="_blank">
                                    <img class="img img-thumbnail" src="{$image}">
                                </a>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        {else}
            <p class="form-control-static">--</p>
        {/if}
    </div>
</div>

<div class="row">
    <label class="control-label col-lg-3">{l s='Date Added' mod='qlohotelreview'}</label>
    <div class="col-lg-9">
        <p class="form-control-static">{$currentObject->date_add}</p>
    </div>
</div>