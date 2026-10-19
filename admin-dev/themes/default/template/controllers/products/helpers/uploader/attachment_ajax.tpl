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
<div class="col-lg-8">
	<input id="{$id|escape:'html':'UTF-8'}" type="file" name="{$name|escape:'html':'UTF-8'}"{if isset($url)} data-url="{$url|escape:'html':'UTF-8'}"{/if} class="hide" />
	<button class="btn btn-default" data-style="expand-right" data-size="s" type="button" id="{$id|escape:'html':'UTF-8'}-add-button">
		<i class="icon-plus-sign"></i> {l s='Add file'}
	</button>
<!--
	<div class="alert alert-success" id="{$id|escape:'html':'UTF-8'}-success" style="display:none">{l s='Upload successful'}</div>
	<div class="alert alert-danger" id="{$id|escape:'html':'UTF-8'}-errors" style="display:none"></div>
-->
</div>

<script type="text/javascript">
	function humanizeSize(bytes)
	{
		if (typeof bytes !== 'number') {
			return '';
		}

		if (bytes >= 1000000000) {
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}

		if (bytes >= 1000000) {
			return (bytes / 1000000).toFixed(2) + ' MB';
		}

		return (bytes / 1000).toFixed(2) + ' KB';
	}

	$( document ).ready(function() {
		var {$id|escape:'html':'UTF-8'}_add_button = Ladda.create( document.querySelector('#{$id|escape:'html':'UTF-8'}-add-button' ));
		var {$id|escape:'html':'UTF-8'}_total_files = 0;
		var success_message = '{l s='Upload successful' js=1}';

		$('#{$id|escape:'html':'UTF-8'}').fileupload({
			dataType: 'json',
			autoUpload: true,
			singleFileUploads: true,
			maxFileSize: {$post_max_size},
			success: function (e) {
				//showSuccessMessage(success_message);
			},
			start: function (e) {				
				{$id|escape:'html':'UTF-8'}_add_button.start();
			},
			fail: function (e, data) {
				showErrorMessage(data.errorThrown.message);
			},
			done: function (e, data) {
				if (data.result) {
					if (typeof data.result.attachment_file !== 'undefined') {
						if (typeof data.result.attachment_file.error !== 'undefined' && data.result.attachment_file.error.length > 0)
							$.each(data.result.attachment_file.error, function(index, error) {
								showErrorMessage(data.result.attachment_file.name + ' : ' + error);
							});
						else {
							showSuccessMessage(success_message);
							$('#selectAttachment2').append('<option value="'+data.result.attachment_file.id_attachment+'">'+data.result.attachment_file.filename+'</option>');
						}
					}
				}
			},
		}).on('fileuploadalways', function (e, data) {
			{$id|escape:'html':'UTF-8'}_add_button.stop();
		}).on('fileuploadprocessalways', function (e, data) {
			var index = data.index,	file = data.files[index];
			//if (file.error)
				//$('#{$id|escape:'html':'UTF-8'}-errors').append('<div class="row"><strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error+'</div>').show();
		}).on('fileuploadsubmit', function (e, data) {
			var params = new Object();

			$('input[id^="attachment_name_"]').each(function()
			{
				id = $(this).prop("id").replace("attachment_name_", "attachment_name[") + "]";
				params[id] = $(this).val();
			});

			$('textarea[id^="attachment_description_"]').each(function()
			{
				id = $(this).prop("id").replace("attachment_description_", "attachment_description[") + "]";
				params[id] = $(this).val();
			});


			data.formData = params;			
		});

		$('#{$id|escape:'html':'UTF-8'}-add-button').on('click', function() {
			//$('#{$id|escape:'html':'UTF-8'}-success').hide();
			//$('#{$id|escape:'html':'UTF-8'}-errors').html('').hide();
			{$id|escape:'html':'UTF-8'}_total_files = 0;
			$('#{$id|escape:'html':'UTF-8'}').trigger('click');
		});
	});
</script>
