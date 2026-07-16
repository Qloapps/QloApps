{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}
{extends file="helpers/list/list_header.tpl"}
{block name="override_form_extra"}
	{if isset($smarty.get.id_source_type)}
		<input type="hidden" name="id_source_type" value="{$smarty.get.id_source_type|intval}" />
	{/if}
{/block}
