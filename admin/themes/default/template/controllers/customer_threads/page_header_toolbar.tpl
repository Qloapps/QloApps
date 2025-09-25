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

{extends file="page_header_toolbar.tpl"}

{block name=pageTitle}
    {if isset($display) && $display == 'view'}
        <h2 class="page-title">
            {if is_array($title)}
                {$title|end|strip_tags}
            {else}
                <span title="{$title}">
                    {$title|strip_tags|truncate:40}
                </span>
            {/if}
        </h2>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}