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

{if isset($errors)}
    <p>
        {if count($errors) > 1}
            {l s='There are %d errors:' sprintf=[count($errors)]}
        {else}
            {l s='There is 1 error:'}
        {/if}
    </p>
    <ol>
        {foreach from=$errors item=error}
            <li>{$error}</li>
        {/foreach}
    </ol>
{/if}
