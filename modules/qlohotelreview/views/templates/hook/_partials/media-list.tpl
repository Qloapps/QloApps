{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{if is_array($images) && count($images)}
    <div class="media-list-wrap">
        <h3 class="title">{l s='Media shared by guests' mod='qlohotelreview'}</h3>
        <div class="media-list clearfix">
            {for $i = 0 to 5}
                {if isset($images[$i])}
                    <div class="media-item-wrap">
                        <div class="media-item">
                            <img class="img img-responsive img-fancybox" src="{$images[$i]}" data-index="{$i}" />
                        </div>
                    </div>
                {/if}
            {/for}
        </div>
        <a class="btn btn-primary btn-primary-review view-all">
            {l s='VIEW ALL IMAGES' mod='qlohotelreview'}
        </a>
    </div>
{/if}