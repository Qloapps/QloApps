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

<tr class="qlo-img-row" id="qlo_img_{$img.id_header_image|intval}"
	data-id="{$img.id_header_image|intval}"
	data-thumb-src="{$imgBaseUrl|escape:'htmlall':'UTF-8'}{$img.name|escape:'htmlall':'UTF-8'}"
	data-tag-lines="{$img.tag_lines_json|escape:'htmlall':'UTF-8'}"
	data-tag-line-color="{$img.tag_line_color|default:'#ffffff'|escape:'htmlall':'UTF-8'}"
	data-tag-line-font-size="{$img.tag_line_font_size|default:16|intval}"
	data-tag-line-font-weight="{$img.tag_line_font_weight|default:'400'|escape:'htmlall':'UTF-8'}">
	<td class="row-selector text-center">
		<input type="checkbox" name="htl_header_imageBox[]" class="noborder qlo-img-checkbox" value="{$img.id_header_image|intval}">
	</td>
	<td>
		{if $img.thumb}{$img.thumb nofilter}{else}<img src="{$imgBaseUrl|escape:'html':'UTF-8'}{$img.name|escape:'html':'UTF-8'}" class="img-thumbnail" width="100" alt="">{/if}
	</td>
	<td class="qlo-img-tagline-cell">
		{if $img.tag_line}{$img.tag_line|truncate:50:'...'|escape:'html':'UTF-8'}{else}&mdash;{/if}
	</td>
	<td class="pointer dragHandle center positionImage" id="td_qlo_img_{$img.id_header_image|intval}">
		<div class="dragGroup">
			<div class="positions">{$position|intval}</div>
		</div>
	</td>
	<td class="center">
		<a class="list-action-enable ajax_table_link {if $img.active}action-enabled{else}action-disabled{/if}"
		   href="{$current|escape:'html':'UTF-8'}&amp;ajax=1&amp;action=toggle_image_active&amp;id_header_image={$img.id_header_image|intval}&amp;active={if $img.active}0{else}1{/if}&amp;token={$token|escape:'html':'UTF-8'}"
		   title="{if $img.active}{l s='Enabled' mod='hotelreservationsystem'}{else}{l s='Disabled' mod='hotelreservationsystem'}{/if}">
			<i class="icon-check{if !$img.active} hidden{/if}"></i>
			<i class="icon-remove{if $img.active} hidden{/if}"></i>
		</a>
	</td>
	<td class="center">
		<a class="list-action-enable ajax_table_link js-toggle-hotel-name {if $img.show_hotel_name}action-enabled{else}action-disabled{/if}"
		   href="{$current|escape:'html':'UTF-8'}&amp;ajax=1&amp;action=toggle_image_hotel_name&amp;id_header_image={$img.id_header_image|intval}&amp;show_hotel_name={if $img.show_hotel_name}0{else}1{/if}&amp;token={$token|escape:'html':'UTF-8'}"
		   title="{if $img.show_hotel_name}{l s='Enabled' mod='hotelreservationsystem'}{else}{l s='Disabled' mod='hotelreservationsystem'}{/if}">
			<i class="icon-check{if !$img.show_hotel_name} hidden{/if}"></i>
			<i class="icon-remove{if $img.show_hotel_name} hidden{/if}"></i>
		</a>
	</td>
	<td class="text-right">
		<div class="btn-group-action">
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-default btn qlo-edit-img"
						data-id="{$img.id_header_image|intval}">
					<i class="icon-pencil"></i>&nbsp;{l s='Edit' mod='hotelreservationsystem'}
				</button>
				<button class="btn btn-default btn dropdown-toggle" data-toggle="dropdown">
					<i class="icon-caret-down"></i>&nbsp;
				</button>
				<ul class="dropdown-menu">
					<li>
						<a href="javascript:void(0);" class="qlo-delete-img"
						   data-id="{$img.id_header_image|intval}">
							<i class="icon-trash"></i>&nbsp;{l s='Delete image' mod='hotelreservationsystem'}
						</a>
					</li>
				</ul>
			</div>
		</div>
	</td>
</tr>
