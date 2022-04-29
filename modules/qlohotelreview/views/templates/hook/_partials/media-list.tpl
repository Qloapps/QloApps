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

{if is_array($images) && count($images)}
    <div class="media-list-wrap">
        <h3 class="title">{l s='Media shared by guests' mod='qlohotelreview'}</h3>
        <div class="row-no-gutters media-list clearfix">
            {for $i = 0 to 5}
                {if isset($images[$i])}
                    <div class="col-xs-2">
                        <div class="media-item">
                            <img class="img img-responsive img-fancybox" src="{$images[$i]}" data-index="{$i}" />
                        </div>
                    </div>
                {/if}
            {/for}
        </div>
        <a class="btn btn-primary btn-primary-review view-all">
            {l s='VIEW ALL' mod='qlohotelreview'}
        </a>
    </div>
{/if}