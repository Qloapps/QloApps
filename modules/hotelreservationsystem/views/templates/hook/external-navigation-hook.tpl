{*
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

{if ($email != '') || ($phone != '')}
    <ul class="nav nav-pills nav-stacked visible-xs wk-nav-style">
        {if $email != ''}
            <li>
                <a href="mailto:{$email}">
                    <i class="icon-envelope-o"></i>
                    {$email}
                </a>
            </li>
        {/if}
        {if $phone != ''}
            <li>
                <a href="tel:{$phone}">
                    <i class="icon-phone"></i>
                    {$phone}
                </a>
            </li>
        {/if}
    </ul>
{/if}
