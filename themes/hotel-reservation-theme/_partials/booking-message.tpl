{*
* 2010-2024 Webkul.
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
* @copyright 2010-2024 Webkul IN
* @license LICENSE.txt
*}

<div class="message {if isset($message.id_employee) && $message.id_employee}management{else}customer{/if}">
    <div class="profile">
        <div class="row">
            <div class="col-sm-6">
                <strong>
                    {if $message.id_employee}
                        {$obj_hotel_branch_information->hotel_name|escape:'html':'UTF-8'}
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
