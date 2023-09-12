{**
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

<a class="list-action-enable{if $enabled} action-enabled{else} action-disabled{/if}" href="{$url_enable|escape:'html':'UTF-8'}" title="{if $enabled}{l s='Enabled'}{else}{l s='Disabled'}{/if}">
    <i class="icon-check{if !$enabled} hidden{/if}"></i>
    <i class="icon-remove{if $enabled} hidden{/if}"></i>
</a>
