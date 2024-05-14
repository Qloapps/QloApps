{*
* Copyright since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<li class="tree-item{if isset($node['disabled']) && $node['disabled'] == true} tree-item-disable{/if}">
	<span class="tree-item-name{if isset($node['disabled']) && $node['disabled'] == true} tree-item-name-disable{/if}">
		<input type="checkbox" class="select_child_feature" name="{$input_name}[]" value="{$node['id']}"{if isset($node['disabled']) && $node['disabled'] == true} disabled="disabled"{/if}/>
		<i class="tree-dot"></i>
		<label class="tree-toggler">{$node['name']}</label>
	</span>
</li>