{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="input_row"}
	<style>
		#logo_image-images-thumbnails img {
			max-width: 80px;
			max-height: 80px;
			padding: 5px;
		}
	</style>
	{$smarty.block.parent}
	{if $input.name == 'name'}
		{hook h="displayFeatureForm" id_feature=$form_id}
	{/if}
	{if $input.name == 'logo_icon'}
		<script type="text/javascript">
			$(document).ready(function() {
				// the uploader renders its own nested .form-group, so pick the field row that holds the label
				function getFieldRow(selector) {
					return $(selector).parents('.form-group').filter(function() {
						return $(this).children('label.control-label').length > 0;
					}).first();
				}
				function toggleFeatureLogoType() {
					var isIcon = $('#logo_type').val() === 'icon';
					getFieldRow('#logo_image').toggle(!isIcon);
					getFieldRow('#logo_icon').toggle(isIcon);
				}
				$('#logo_type').on('change', toggleFeatureLogoType);
				toggleFeatureLogoType();
			});
		</script>
	{/if}
{/block}
