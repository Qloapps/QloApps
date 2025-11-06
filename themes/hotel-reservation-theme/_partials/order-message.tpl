{*
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

<div class="message {if isset($message.id_employee) && $message.id_employee}management{else}customer{/if}">
    <div class="profile">
        <div class="row">
            <div class="col-sm-6">
                <strong>
                    {if $message.id_employee}
                        {if isset($obj_hotel_branch_information->hotel_name) && $obj_hotel_branch_information->hotel_name}
                            {$obj_hotel_branch_information->hotel_name|escape:'html':'UTF-8'}
                        {elseif isset($message.elastname) && $message.elastname}
                            {$message.efirstname|escape:'html':'UTF-8'} {$message.elastname|escape:'html':'UTF-8'}
                        {elseif $message.clastname}
                            {$message.cfirstname|escape:'html':'UTF-8'} {$message.clastname|escape:'html':'UTF-8'}
                        {else}
                            {$shop_name|escape:'html':'UTF-8'}
                        {/if}
                    {else}
                        {$message.cfirstname|escape:'html':'UTF-8'} {$message.clastname|escape:'html':'UTF-8'}
                    {/if}
                </strong>
            </div>
            <div class="col-sm-6 text-right">
                {dateFormat date=$message.date_add full=1}
            </div>
        </div>
    </div>
    <div class="message-content">
        {$message.message|escape:'html':'UTF-8'|nl2br}
    </div>
</div>
