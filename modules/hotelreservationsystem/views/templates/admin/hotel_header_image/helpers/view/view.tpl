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

<form id="qlo-header-media-form" class="form-horizontal"
	  action="{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}"
	  method="post" enctype="multipart/form-data">
	<input type="hidden" name="submitHeaderMedia" value="1">
	<input type="hidden" name="confirm_delete_images" id="qlo-confirm-delete-images" value="0">

	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>&nbsp;{l s='Header Media Settings' mod='hotelreservationsystem'}
		</div>
		<div class="panel-body">

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Header Media Type' mod='hotelreservationsystem'}
				</label>
				<div class="col-lg-9">
					<select name="QLO_HEADER_MEDIA_TYPE" id="qlo-media-type" class="fixed-width-lg">
						<option value="{HotelHeaderImage::MEDIA_TYPE_IMAGE}"{if $config.QLO_HEADER_MEDIA_TYPE == HotelHeaderImage::MEDIA_TYPE_IMAGE} selected{/if}>{l s='Images' mod='hotelreservationsystem'}</option>
						<option value="{HotelHeaderImage::MEDIA_TYPE_VIDEO}"{if $config.QLO_HEADER_MEDIA_TYPE == HotelHeaderImage::MEDIA_TYPE_VIDEO} selected{/if}>{l s='Video' mod='hotelreservationsystem'}</option>
					</select>
					<p class="help-block">{l s='Choose whether the home page header shows a slideshow of images or a background video.' mod='hotelreservationsystem'}</p>
				</div>
			</div>

			<div id="qlo-image-settings"{if $config.QLO_HEADER_MEDIA_TYPE != HotelHeaderImage::MEDIA_TYPE_IMAGE} style="display:none"{/if}>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Navigation Type' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<select name="QLO_HEADER_SLIDER_NAV_TYPE" class="fixed-width-lg">
							<option value="{HotelHeaderImage::NAV_TYPE_DOTS}"{if $config.QLO_HEADER_SLIDER_NAV_TYPE == HotelHeaderImage::NAV_TYPE_DOTS} selected{/if}>{l s='Dots' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::NAV_TYPE_ARROWS}"{if $config.QLO_HEADER_SLIDER_NAV_TYPE == HotelHeaderImage::NAV_TYPE_ARROWS} selected{/if}>{l s='Arrows' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::NAV_TYPE_BOTH}"{if $config.QLO_HEADER_SLIDER_NAV_TYPE == HotelHeaderImage::NAV_TYPE_BOTH} selected{/if}>{l s='Both (Dots + Arrows)' mod='hotelreservationsystem'}</option>
						</select>
						<p class="help-block">{l s='Visible only when 2 or more active images exist.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Auto Slide' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="QLO_HEADER_SLIDER_AUTO_PLAY" id="auto_play_on" value="1"{if $config.QLO_HEADER_SLIDER_AUTO_PLAY} checked{/if}>
							<label for="auto_play_on">{l s='Yes' mod='hotelreservationsystem'}</label>
							<input type="radio" name="QLO_HEADER_SLIDER_AUTO_PLAY" id="auto_play_off" value="0"{if !$config.QLO_HEADER_SLIDER_AUTO_PLAY} checked{/if}>
							<label for="auto_play_off">{l s='No' mod='hotelreservationsystem'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>

				<div class="form-group" id="qlo-slide-interval-group"{if !$config.QLO_HEADER_SLIDER_AUTO_PLAY} style="display:none"{/if}>
					<label class="control-label col-lg-3">{l s='Slide Interval' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<div class="input-group fixed-width-lg">
							<input type="text" name="QLO_HEADER_SLIDER_INTERVAL"
								   class="form-control"
								   value="{$config.QLO_HEADER_SLIDER_INTERVAL|intval}">
							<span class="input-group-addon">ms</span>
						</div>
						<p class="help-block">{l s='Milliseconds between slides. Min: 500. Example: 5000 = 5 s.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				<div class="form-group" id="qlo-slide-anim-group"{if !$config.QLO_HEADER_SLIDER_AUTO_PLAY} style="display:none"{/if}>
					<label class="control-label col-lg-3">{l s='Slide Animation' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<select name="QLO_HEADER_SLIDER_ANIM_TYPE" class="fixed-width-lg">
							<option value="{HotelHeaderImage::ANIM_TYPE_SLIDE}"{if $config.QLO_HEADER_SLIDER_ANIM_TYPE == HotelHeaderImage::ANIM_TYPE_SLIDE} selected{/if}>{l s='Slide' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::ANIM_TYPE_FADE}"{if $config.QLO_HEADER_SLIDER_ANIM_TYPE == HotelHeaderImage::ANIM_TYPE_FADE} selected{/if}>{l s='Fade' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::ANIM_TYPE_ZOOM}"{if $config.QLO_HEADER_SLIDER_ANIM_TYPE == HotelHeaderImage::ANIM_TYPE_ZOOM} selected{/if}>{l s='Zoom' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::ANIM_TYPE_BLUR}"{if $config.QLO_HEADER_SLIDER_ANIM_TYPE == HotelHeaderImage::ANIM_TYPE_BLUR} selected{/if}>{l s='Blur' mod='hotelreservationsystem'}</option>
						</select>
						<p class="help-block">{l s='Transition effect between slides.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Show Hotel Name' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="QLO_HOTEL_NAME_ENABLE" id="hotel_name_on" value="1"{if $config.QLO_HOTEL_NAME_ENABLE} checked{/if}>
							<label for="hotel_name_on">{l s='Yes' mod='hotelreservationsystem'}</label>
							<input type="radio" name="QLO_HOTEL_NAME_ENABLE" id="hotel_name_off" value="0"{if !$config.QLO_HOTEL_NAME_ENABLE} checked{/if}>
							<label for="hotel_name_off">{l s='No' mod='hotelreservationsystem'}</label>
							<a class="slide-button btn"></a>
						</span>
						<p class="help-block">{l s='Display the hotel name on the header image slideshow.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Content Alignment' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<select name="QLO_HEADER_CONTENT_ALIGN" class="fixed-width-lg">
							<option value="{HotelHeaderImage::CONTENT_ALIGN_LEFT}"{if $config.QLO_HEADER_CONTENT_ALIGN == HotelHeaderImage::CONTENT_ALIGN_LEFT} selected{/if}>{l s='Left' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::CONTENT_ALIGN_CENTER}"{if $config.QLO_HEADER_CONTENT_ALIGN == HotelHeaderImage::CONTENT_ALIGN_CENTER || !$config.QLO_HEADER_CONTENT_ALIGN} selected{/if}>{l s='Center' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderImage::CONTENT_ALIGN_RIGHT}"{if $config.QLO_HEADER_CONTENT_ALIGN == HotelHeaderImage::CONTENT_ALIGN_RIGHT} selected{/if}>{l s='Right' mod='hotelreservationsystem'}</option>
						</select>
						<p class="help-block">{l s='Horizontal alignment of the hotel name and tagline on the header.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

			</div>

			<div id="qlo-video-settings"{if $config.QLO_HEADER_MEDIA_TYPE != HotelHeaderImage::MEDIA_TYPE_VIDEO} style="display:none"{/if}>

				{if $videoItem}{assign var="currentSourceType" value=$videoItem.source_type}{else}{assign var="currentSourceType" value='upload'}{/if}

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Video Source' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<select name="source_type" id="qlo-source-type" class="fixed-width-lg">
							<option value="upload"{if $currentSourceType != 'url'} selected{/if}>{l s='Upload video file' mod='hotelreservationsystem'}</option>
							<option value="url"{if $currentSourceType == 'url'} selected{/if}>{l s='External video URL' mod='hotelreservationsystem'}</option>
						</select>
					</div>
				</div>

				<div id="qlo-vid-file-wrap"{if $currentSourceType == 'url'} style="display:none"{/if}>
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Video File' mod='hotelreservationsystem'}</label>
						<div class="col-lg-6">
							<input type="file" name="header_video_file" id="qlo-video-file-input" accept=".mp4,.webm,.ogg"
								   style="width:0;height:0;overflow:hidden;position:absolute;">
							<button class="btn btn-default" type="button" id="qlo-vid-file-add-btn">
								<i class="icon-folder-open"></i>&nbsp;{l s='Choose file...' mod='hotelreservationsystem'}
							</button>
							<span class="qlo-vid-filename help-block" style="display:none;margin-top:4px;"></span>
							<p class="help-block">
								{l s='Formats: .mp4, .webm, .ogg' mod='hotelreservationsystem'}
								&mdash; {l s='Max:' mod='hotelreservationsystem'} {$maxUpload|escape:'html':'UTF-8'}
							</p>
							{if $videoItem && $videoItem.source_type == 'upload'}
							<p class="help-block">{l s='Selecting a new file will replace the current video.' mod='hotelreservationsystem'}</p>
							{/if}
						</div>
					</div>
				</div>

				<div id="qlo-vid-url-wrap"{if $currentSourceType != 'url'} style="display:none"{/if}>
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='External Video URL' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<input type="text" name="video_url" id="qlo-video-url-input" class="form-control"
								   placeholder="https://example.com/video.mp4"
								   value="{if $videoItem && $videoItem.source_type == 'url'}{$videoItem.name|escape:'html':'UTF-8'}{/if}">
							<p class="help-block">{l s='Enter a direct URL to an MP4, WebM, or OGG video file (server-hosted only). Streaming links (e.g., YouTube, Vimeo) are not supported.' mod='hotelreservationsystem'}</p>
							{if $videoItem && $videoItem.source_type == 'url'}
							<p class="help-block">{l s='Entering a new URL will replace the current video.' mod='hotelreservationsystem'}</p>
							{/if}
							<div class="qlo-video-preview-card" id="qlo-video-url-preview-wrap"{if !($videoItem && $videoItem.source_type == 'url')} style="display:none"{/if}>
								<video controls muted preload="metadata" class="qlo-video-preview-player" id="qlo-video-url-preview-player">
									<source id="qlo-video-url-preview-source"
											src="{if $videoItem && $videoItem.source_type == 'url'}{$videoItem.name|escape:'html':'UTF-8'}{/if}"
											type="{$videoMimeType|escape:'html':'UTF-8'}">
									{l s='Your browser does not support the video tag.' mod='hotelreservationsystem'}
								</video>
							</div>
						</div>
					</div>
				</div>

				{if $videoItem && $videoItem.source_type == 'upload'}
				<div id="qlo-current-video-wrap">
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Current Video' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<div class="qlo-video-preview-card">
								<video controls muted preload="metadata" class="qlo-video-preview-player">
									<source src="{$imgBaseUrl|escape:'html':'UTF-8'}{$videoItem.name|escape:'html':'UTF-8'}"
											type="{$videoMimeType|escape:'html':'UTF-8'}">
									{l s='Your browser does not support the video tag.' mod='hotelreservationsystem'}
								</video>
							</div>
						</div>
					</div>
				</div>
				{/if}

			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>&nbsp;{l s='Save' mod='hotelreservationsystem'}
			</button>
		</div>
	</div>
</form>

{* Confirm switch-to-video modal — triggered when saving with Video selected while 2+ images exist *}
<div class="modal fade" id="qlo-confirm-switch-video-modal" tabindex="-1" role="dialog" aria-labelledby="qloConfirmSwitchVideoModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="qloConfirmSwitchVideoModalLabel">
					<i class="icon-exclamation-triangle"></i>&nbsp;{l s='Confirm Switch to Video' mod='hotelreservationsystem'}
				</h4>
			</div>
			<div class="modal-body">
				<p>{l s='Switching to Video will delete all images except the first one. This cannot be undone. Continue?' mod='hotelreservationsystem'}</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="qlo-confirm-switch-video-btn">{l s='Continue' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>
</div>

{* Confirm bulk-delete images modal — triggered by the "Delete selected" bulk action *}
<div class="modal fade" id="qlo-confirm-bulk-delete-modal" tabindex="-1" role="dialog" aria-labelledby="qloConfirmBulkDeleteModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="qloConfirmBulkDeleteModalLabel">
					<i class="icon-exclamation-triangle"></i>&nbsp;{l s='Confirm Delete' mod='hotelreservationsystem'}
				</h4>
			</div>
			<div class="modal-body">
				<p>{l s='Delete selected images? This cannot be undone.' mod='hotelreservationsystem'}</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="qlo-confirm-bulk-delete-btn">{l s='Delete' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>
</div>

<div id="qlo-image-panel"{if $config.QLO_HEADER_MEDIA_TYPE != HotelHeaderImage::MEDIA_TYPE_IMAGE} style="display:none"{/if}>
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-picture"></i>&nbsp;{l s='Header Images' mod='hotelreservationsystem'}
			&nbsp;<span class="badge" id="qlo-image-count">{$imageItems|count}</span>
			<span class="panel-heading-action">
			    <a id="qlo-add-image-btn" class="btn btn-primary" href="javascript:void(0);" style="margin-right:170px;">
					<i class="icon-plus-sign"></i>&nbsp;{l s='Add Images' mod='hotelreservationsystem'}
				</a>
				<button type="button" class="btn btn-default" data-toggle="modal" data-target="#qlo-bulk-tagline-modal" style="margin-right:15px;">
					<i class="icon-pencil"></i>&nbsp;{l s='Set tag line for all images' mod='hotelreservationsystem'}
				</button>
			</span>
		</div>

		<div class="alert alert-info" id="qlo-slider-info"{if $config.QLO_HEADER_MEDIA_TYPE != HotelHeaderImage::MEDIA_TYPE_IMAGE} style="display:none"{/if}>
	<b>
	{l s='Slider navigation and auto-slide controls take effect only when 2 or more images are active. With a single active image these settings have no visible impact on the front end.' mod='hotelreservationsystem'}</b>
</div>

		<hr>

		<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}" id="qlo-bulk-form">

		<div class="table-responsive">
			<table class="table" id="qlo-image-table">
				<thead>
					<tr class="nodrag nodrop">
						<th class="center fixed-width-xs"></th>
						<th>{l s='Image' mod='hotelreservationsystem'}</th>
						<th>{l s='Tag Line' mod='hotelreservationsystem'}</th>
						<th class="center fixed-width-xs">{l s='Position' mod='hotelreservationsystem'}</th>
						<th class="center">{l s='Active' mod='hotelreservationsystem'}</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="qlo-image-tbody">
					{foreach from=$imageItems item=img}
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
							{if $img.thumb}{$img.thumb nofilter}{else}<img src="{$imgBaseUrl|escape:'html':'UTF-8'}{$img.name|escape:'html':'UTF-8'}" class="imgm img-thumbnail" alt="">{/if}
						</td>
						<td class="qlo-img-tagline-cell">
							{if $img.tag_line}{$img.tag_line|truncate:50:'...'|escape:'html':'UTF-8'}{else}&mdash;{/if}
						</td>
						<td class="pointer dragHandle center positionImage" id="td_qlo_img_{$img.id_header_image|intval}">
							<div class="dragGroup">
								<div class="positions">{$img@iteration}</div>
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
					{foreachelse}
					<tr class="list-empty-tr" id="qlo-no-images">
						<td class="list-empty" colspan="6">
							<div class="list-empty-msg">
								<i class="icon-warning-sign list-empty-icon"></i>
								{l s='No Image Found' mod='hotelreservationsystem'}
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

		<div class="row" id="qlo-bulk-actions-row"{if $imageItems|count <= 1} style="display:none"{/if}>
			<div class="col-lg-6">
				<div class="btn-group bulk-actions dropup">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{l s='Bulk actions' mod='hotelreservationsystem'} <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'htl_header_imageBox[]', true);return false;">
								<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='hotelreservationsystem'}
							</a>
						</li>
						<li>
							<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'htl_header_imageBox[]', false);return false;">
								<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='hotelreservationsystem'}
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="#" onclick="sendBulkAction($(this).closest('form').get(0), 'submitBulkenableSelectionhtl_header_image');return false;">
								<i class="icon-power-off text-success"></i>&nbsp;{l s='Enable selection' mod='hotelreservationsystem'}
							</a>
						</li>
						<li>
							<a href="#" onclick="sendBulkAction($(this).closest('form').get(0), 'submitBulkdisableSelectionhtl_header_image');return false;">
								<i class="icon-power-off text-danger"></i>&nbsp;{l s='Disable selection' mod='hotelreservationsystem'}
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="#" class="qlo-bulk-delete-images-trigger" data-action="submitBulkdeletehtl_header_image">
								<i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='hotelreservationsystem'}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		</form>

	</div>
</div>

{* Bulk tag line modal — triggered by the "Set tag line for all images" button above *}
<div class="modal fade" id="qlo-bulk-tagline-modal" tabindex="-1" role="dialog" aria-labelledby="qloBulkTaglineModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="qloBulkTaglineModalLabel">
					<i class="icon-pencil"></i>&nbsp;{l s='Set tag line for all images' mod='hotelreservationsystem'}
				</h4>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Tag Line (All Images)' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							{foreach from=$languages item=lang}
							{if count($languages) > 1}
							<div class="translatable-field row lang-{$lang.id_lang}"{if $lang.id_lang != $defaultLangId} style="display:none"{/if}>
								<div class="col-lg-10">
							{/if}
									<input type="text"
										   class="form-control qlo-bulk-tagline-field"
										   data-lang="{$lang.id_lang}"
										   placeholder="{l s='Tag line for all images...' mod='hotelreservationsystem'}" />
							{if count($languages) > 1}
								</div>
								<div class="col-lg-2">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
										{$lang.iso_code|escape:'html':'UTF-8'} <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=lang2}
										<li><a href="javascript:hideOtherLanguage({$lang2.id_lang});">{$lang2.name|escape:'html':'UTF-8'}</a></li>
										{/foreach}
									</ul>
								</div>
							</div>
							{/if}
							{/foreach}
							<p class="help-block">{l s='This will overwrite the tag line for all images in every language.' mod='hotelreservationsystem'}</p>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="qlo-bulk-tagline-apply">{l s='Apply to All' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="qlo-img-form-modal" tabindex="-1" role="dialog" aria-labelledby="qloImgFormModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="qloImgFormModalLabel">
					<i class="icon-picture"></i>&nbsp;<span id="qlo-img-modal-title-add">{l s='Add Images' mod='hotelreservationsystem'}</span><span id="qlo-img-modal-title-edit" style="display:none">{l s='Edit Image' mod='hotelreservationsystem'}</span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="form-group" id="qlo-img-edit-preview-group" style="display:none">
						<label class="control-label col-lg-3">{l s='Editing Image' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<img id="qlo-img-edit-thumb" src="" class="img-thumbnail" alt=""
								 style="max-width:160px;max-height:100px;object-fit:cover;">
						</div>
					</div>

					<div class="form-group" id="qlo-img-form-file-group">
						<label class="control-label col-lg-3 file_upload_label">
							{l s='Add Images' mod='hotelreservationsystem'}
						</label>
						<div class="col-lg-9">
							<input type="file" id="qlo-img-form-file" multiple
								   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
								   style="width:0;height:0;overflow:hidden;position:absolute;">
							<button class="btn btn-default" data-style="expand-right" data-size="s"
									type="button" id="qlo-img-file-add-btn">
								<i class="icon-folder-open"></i> {l s='Add files...' mod='hotelreservationsystem'}
							</button>
							<ul id="qlo-img-files-list" class="qlo-files-list" style="display:none"></ul>
							<p class="help-block">
								{l s='Formats: .jpg, .jpeg, .png, .webp, .gif' mod='hotelreservationsystem'}
								&mdash; {l s='Recommended: 1920×600 px' mod='hotelreservationsystem'}
								&mdash; {l s='Max:' mod='hotelreservationsystem'} {$maxImageUpload|escape:'html':'UTF-8'}
							</p>
						</div>
					</div>

					<div class="form-group" id="qlo-img-form-add-active-group">
						<label class="control-label col-lg-3">{l s='Enable' mod='hotelreservationsystem'}</label>
						<div class="col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="qlo_img_active_add" id="qlo_img_active_add_on" value="1" checked>
								<label for="qlo_img_active_add_on">{l s='Yes' mod='hotelreservationsystem'}</label>
								<input type="radio" name="qlo_img_active_add" id="qlo_img_active_add_off" value="0">
								<label for="qlo_img_active_add_off">{l s='No' mod='hotelreservationsystem'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					<div class="form-group" id="qlo-img-form-edit-group" style="display:none">
						<label class="control-label col-lg-3">{l s='Enable Image' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="qlo_img_active_edit" id="qlo_img_active_edit_on" value="1">
								<label for="qlo_img_active_edit_on">{l s='Yes' mod='hotelreservationsystem'}</label>
								<input type="radio" name="qlo_img_active_edit" id="qlo_img_active_edit_off" value="0">
								<label for="qlo_img_active_edit_off">{l s='No' mod='hotelreservationsystem'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Tag Line' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							{foreach from=$languages item=lang}
							{if count($languages) > 1}
							<div class="translatable-field row lang-{$lang.id_lang}"{if $lang.id_lang != $defaultLangId} style="display:none"{/if}>
								<div class="col-lg-10">
							{/if}
									<input type="text"
										   id="qlo-form-tagline-{$lang.id_lang}"
										   class="form-control qlo-form-tagline-field"
										   data-lang="{$lang.id_lang}"
										   placeholder="{l s='Tag line...' mod='hotelreservationsystem'}" />
							{if count($languages) > 1}
								</div>
								<div class="col-lg-2">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
										{$lang.iso_code|escape:'html':'UTF-8'} <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=lang2}
										<li><a href="javascript:hideOtherLanguage({$lang2.id_lang});">{$lang2.name|escape:'html':'UTF-8'}</a></li>
										{/foreach}
									</ul>
								</div>
							</div>
							{/if}
							{/foreach}
							<p class="help-block">{l s='Short text overlay shown on this image. Leave empty for no overlay.' mod='hotelreservationsystem'}</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Tag Line Color' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<div class="input-group fixed-width-lg">
								<input type="color" id="qlo-img-tl-color" name="tag_line_color"
									   class="color mColorPickerInput" data-hex="true" value="#ffffff">
							</div>
							<p class="help-block">{l s='Text color for the tag line overlay.' mod='hotelreservationsystem'}</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Tag Line Font Size' mod='hotelreservationsystem'}</label>
						<div class="col-lg-3">
							<div class="input-group">
								<input type="number" id="qlo-img-tl-font-size" name="tag_line_font_size"
									   class="form-control" value="16" min="8" max="72">
								<span class="input-group-addon">px</span>
							</div>
							<p class="help-block">{l s='Default: 16px. Recommended: 12px–48px.' mod='hotelreservationsystem'}</p>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Tag Line Font Weight' mod='hotelreservationsystem'}</label>
						<div class="col-lg-3">
							<select id="qlo-img-tl-font-weight" name="tag_line_font_weight" class="fixed-width-lg">
								<option value="300">{l s='300 (Light)' mod='hotelreservationsystem'}</option>
								<option value="400" selected="selected">{l s='400 (Normal)' mod='hotelreservationsystem'}</option>
								<option value="600">{l s='600 (Semi-Bold)' mod='hotelreservationsystem'}</option>
								<option value="700">{l s='700 (Bold)' mod='hotelreservationsystem'}</option>
							</select>
						</div>
					</div>

					<div class="form-group" id="qlo-form-upload-progress" style="display:none">
						<div class="col-lg-offset-3 col-lg-9">
							<div class="alert alert-info" style="margin:0">
								<i class="icon-spinner icon-spin"></i>&nbsp;{l s='Uploading, please wait...' mod='hotelreservationsystem'}
							</div>
						</div>
					</div>

					<input type="hidden" id="qlo-img-form-id" value="">

				</div>
			</div>
			<div class="modal-footer" id="qlo-img-add-footer">
				<button type="button" id="qlo-img-form-upload-btn" class="btn btn-primary">{l s='Upload' mod='hotelreservationsystem'}</button>
			</div>
			<div class="modal-footer" id="qlo-img-edit-footer" style="display:none">
				<button type="button" id="qlo-img-form-save-btn" class="btn btn-primary">{l s='Save' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>
</div>
