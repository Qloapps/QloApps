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

<li class="tree-folder">
	<span class="tree-folder-name{if isset($node['disabled']) && $node['disabled'] == true} tree-folder-name-disable{/if}">
		{if isset($node['id_feature'])}
			<input type="checkbox" class="select_feature_parent" name="{$input_name}[]" value="{$node['id_feature']}"{if isset($node['disabled']) && $node['disabled'] == true} disabled="disabled"{/if} />
		{/if}
		<i class="icon-folder-close"></i>
		<label class="tree-toggler">{if isset($node['name'])}{$node['name']|escape:'html':'UTF-8'}{/if}{if isset($node['selected_childs']) && (int)$node['selected_childs'] > 0} {l s='(%s selected)' sprintf=$node['selected_childs']}{/if}</label>
	</span>
	<ul class="tree">
		{$children|escape:'UTF-8'}
	</ul>
</li>
