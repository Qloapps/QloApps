{*
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($edit) && $edit}
			<i class="icon-pencil"></i> {l s='Edit Navigation Link' mod='blocknavigationmenu'}
		{else}
			<i class="icon-plus"></i> {l s='Add Navigation Link' mod='blocknavigationmenu'}
		{/if}
	</div>
	<div class="panel-content">
		<form id="{$table}_form" action="{$link->getAdminLink('AdminCustomNavigationLinkSetting')}" class="form-horizontal" method="post">
			{if isset($edit) && $edit}
				<input type="hidden" id="id_navigation_link" name="id_navigation_link" value="{$navigationLinkInfo['id']}">
			{/if}
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enable, If you want to add a link from a cms page. Disable, if you want to add anavigation link or a page link.' mod='blocknavigationmenu'}">
						{l s='Add link from cms pages' mod='blocknavigationmenu'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="is_cms_block_link" id="is_cms_block_link_on" value="1" {if isset($smarty.post.is_cms_block_link)}{if $smarty.post.is_cms_block_link}checked="checked"{/if}{elseif isset($navigationLinkInfo['id_cms']) && $navigationLinkInfo['id_cms']}checked="checked"{/if}/>

						<label for="is_cms_block_link_on">{l s='Yes' mod='blocknavigationmenu'}</label>

						<input type="radio" name="is_cms_block_link" id="is_cms_block_link_off" value="0" {if isset($smarty.post.is_cms_block_link)} {if !$smarty.post.is_cms_block_link}checked="checked"{/if}{elseif isset($navigationLinkInfo['id_cms'])}{if !$navigationLinkInfo['id_cms']}checked="checked"{/if}{else}checked="checked"{/if}/>

						<label for="is_cms_block_link_off">{l s='No' mod='blocknavigationmenu'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			{* Feilds of cms page *}
			<div id="cms_block_content" class="{if isset($smarty.post.is_cms_block_link)}{if !$smarty.post.is_cms_block_link}hidden{/if}{elseif !isset($navigationLinkInfo['id_cms']) || !$navigationLinkInfo['id_cms']}hidden{/if}">
				{*Display CMS Pages*}
				{if isset($cmsPages)}
					<div class="form-group">
						<label class="control-label col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip"
							title="{l s='Select the CMS page you want to attach with this navigation link. CMS page link will be used as redirect url and meta title of the CMS page will be considered as the name of footer navigation link.' mod='blocknavigationmenu'}">
								{l s='Select CMS Page' mod='blocknavigationmenu'}
							</span>
						</label>
						<div class="col-lg-9">
							<table class="table table-bordered" style="width:40%;">
								<thead>
									<tr>
										<th class="fixed-width-xs">
										</th>
										<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='blocknavigationmenu'}</span></th>
										<th>
											<span class="title_box">
												{l s='CMS Page Name' mod='blocknavigationmenu'}
											</span>
										</th>
									</tr>
								</thead>
								<tbody>
									{foreach $cmsPages as $cmsPage}
										<tr>
											<td><input type="radio" value="{$cmsPage.id_cms|escape:'htmlall':'UTF-8'}" name="id_cms" {if isset($navigationLinkInfo['id_cms']) && $navigationLinkInfo['id_cms'] == $cmsPage.id_cms}checked="checked"{/if}>
											</td>
											<td>{$cmsPage.id_cms|escape:'htmlall':'UTF-8'}</td>
											<td><label for="groupBox_{$cmsPage.id_cms|escape:'htmlall':'UTF-8'}">{$cmsPage.meta_title}</label></td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</div>
				{/if}
			</div>
			{* Feilds of non cms page *}
			<div id="non_cms_block_content" class="{if isset($smarty.post.is_cms_block_link)}{if $smarty.post.is_cms_block_link}hidden{/if}{elseif isset($navigationLinkInfo['id_cms']) && $navigationLinkInfo['id_cms']}hidden{/if}">
				<div class="form-group">
					<label class="col-sm-3 control-label required" for="navigation_link_name">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Enter the name for the link. This name will appear for created link.' mod='blocknavigationmenu'}">
							{l s='Name' mod='blocknavigationmenu'}
						</span>
					</label>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-lg-10">
								{foreach from=$languages item=language}
									{assign var="navigation_link_name" value="navigation_link_name_`$language.id_lang`"}
									<input type="text" id="{$navigation_link_name}" name="{$navigation_link_name}" value="{if isset($navigationLinkInfo.name[$language.id_lang]) && $navigationLinkInfo.name[$language.id_lang]}{$navigationLinkInfo.name[$language.id_lang]}{else if isset($smarty.post.$navigation_link_name)}{$smarty.post.$navigation_link_name}{/if}" data-lang-name="{$language.name}" placeholder="{l s='Enter exlore link name' mod='blocknavigationmenu'}" class="form-control navigation_link_name_all" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}/>
								{/foreach}
							</div>
							{if $languages|@count > 1}
								<div class="col-lg-2">
									<button type="button" id="navigation_link_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										{$currentLang.iso_code}
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=language}
											<li>
												<a href="javascript:void(0)" onclick="showNavigationLinkLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
											</li>
										{/foreach}
									</ul>
								</div>
							{/if}
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Enable, If you want to add a manual navigation redirect Url for navigation link. Disable, if you want to select a front end page.' mod='blocknavigationmenu'}">
							{l s='Set manual navigation redirect Url' mod='blocknavigationmenu'}
						</span>
					</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="is_custom_redirect_link" id="is_custom_redirect_link_on" value="1" {if isset($smarty.post.is_custom_redirect_link)}{if $smarty.post.is_custom_redirect_link}checked="checked"{/if}{elseif isset($navigationLinkInfo['is_custom_link']) && $navigationLinkInfo['is_custom_link']}checked="checked"{/if}/>

							<label for="is_custom_redirect_link_on">{l s='Yes' mod='blocknavigationmenu'}</label>

							<input type="radio" name="is_custom_redirect_link" id="is_custom_redirect_link_off" value="0" {if isset($smarty.post.is_custom_redirect_link)} {if !$smarty.post.is_custom_redirect_link}checked="checked"{/if}{elseif isset($navigationLinkInfo['is_custom_link'])}{if !$navigationLinkInfo['is_custom_link']}checked="checked"{/if}{else}checked="checked"{/if}/>

							<label for="is_custom_redirect_link_off">{l s='No' mod='blocknavigationmenu'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="form-group custom_redirect_link_div {if isset($smarty.post.is_custom_redirect_link)}{if $smarty.post.is_custom_redirect_link}hidden{/if}{elseif isset($navigationLinkInfo['is_custom_link']) && !$navigationLinkInfo['is_custom_link']}hidden{elseif !isset($navigationLinkInfo['is_custom_link'])}hidden{/if}">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='enter the url to which you want to redirect your guests for this nevigation link.' mod='blocknavigationmenu'}">
							{l s='Custom redirect url' mod='blocknavigationmenu'}
						</span>
					</label>
					<div class="col-lg-5">
						<input type="text" name="link" {if isset($smarty.post.link)}value="{$smarty.post.link}"{elseif isset($navigationLinkInfo['link'])}value="{$navigationLinkInfo['link']}"{/if}>
					</div>
				</div>
				<div class="form-group custom_redirect_page_div {if isset($smarty.post.is_custom_redirect_link)}{if $smarty.post.is_custom_redirect_link}hidden{/if}{elseif isset($navigationLinkInfo['is_custom_link']) && $navigationLinkInfo['is_custom_link']}hidden{/if}">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
						title="{l s='Select front end page of the theme. Guests will be redirected to this url for this navigation link.' mod='blocknavigationmenu'}">
							{l s='Theme Pages' mod='blocknavigationmenu'}
						</span>
					</label>
					<div class="col-sm-2">
						{if isset($themePages) && $themePages}
							<select class="form-control" name="redirect_page">
								{foreach $themePages as $page}
									<option value="{$page['page']}" {if isset($edit)} {if $navigationLinkInfo['link'] == "{$page['page']}"}selected{/if}{/if}>{$page['page']}</option>
								{/foreach}
							</select>
						{else}
							{l s='No pages found' mod='blocknavigationmenu'}
						{/if}
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enable, If you want to show this link to the navigation menu.' mod='blocknavigationmenu'}">
						{l s='Show at navigation menu' mod='blocknavigationmenu'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="show_at_navigation" id="show_at_navigation_on" value="1" checked="checked"/>
						<label for="show_at_navigation_on">{l s='Yes' mod='blocknavigationmenu'}</label>

						<input type="radio" name="show_at_navigation" id="show_at_navigation_off" value="0" {if isset($smarty.post.show_at_navigation)}{if !$smarty.post.show_at_navigation}checked="checked"{/if}
						{elseif isset($navigationLinkInfo['show_at_navigation']) && !$navigationLinkInfo['show_at_navigation']}checked="checked"{/if}/>
						<label for="show_at_navigation_off">{l s='No' mod='blocknavigationmenu'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enable, If you want to show this link to the footer block.' mod='blocknavigationmenu'}">
						{l s='Show at footer block' mod='blocknavigationmenu'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="show_at_footer" id="show_at_footer_on" value="1" checked="checked"/>
						<label for="show_at_footer_on">{l s='Yes' mod='blocknavigationmenu'}</label>

						<input type="radio" name="show_at_footer" id="show_at_footer_off" value="0" {if isset($smarty.post.show_at_footer)}{if !$smarty.post.show_at_footer}checked="checked"{/if}
						{elseif isset($navigationLinkInfo['show_at_footer']) && !$navigationLinkInfo['show_at_footer']}checked="checked"{/if}/>
						<label for="show_at_footer_off">{l s='No' mod='blocknavigationmenu'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Disabled navigation links will not be displayed anywhere.' mod='blocknavigationmenu'}">
						{l s='Enable' mod='blocknavigationmenu'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="active" id="active_on" value="1" checked="checked"/>
						<label for="active_on">{l s='Yes' mod='blocknavigationmenu'}</label>

						<input type="radio" name="active" id="active_off" value="0" {if isset($smarty.post.active)}{if !$smarty.post.active}checked="checked"{/if}
						{elseif isset($navigationLinkInfo['active']) && !$navigationLinkInfo['active']}checked="checked"{/if}/>
						<label for="active_off">{l s='No' mod='blocknavigationmenu'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminCustomNavigationLinkSetting')}" class="btn btn-default">
					<i class="process-icon-cancel"></i>{l s='Cancel' mod='blocknavigationmenu'}
				</a>
				<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right">
					<i class="process-icon-save"></i>{l s='Save' mod='blocknavigationmenu'}
				</button>
				<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='blocknavigationmenu'}
				</button>
			</div>
		</form>
	</div>
</div>