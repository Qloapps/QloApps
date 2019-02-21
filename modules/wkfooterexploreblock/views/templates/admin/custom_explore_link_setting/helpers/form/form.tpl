{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($edit) && $edit}
			<i class="icon-pencil"></i> {l s='Edit Custom Link' mod='wkfooterexploreblock'}
		{else}
			<i class="icon-plus"></i> {l s='Add Custom Link' mod='wkfooterexploreblock'}
		{/if}
	</div>
	<div class="panel-content">
		<form id="{$table}_form" action="{$link->getAdminLink('AdminCustomExploreLinkSetting')}" class="form-horizontal" method="post">
			{if isset($edit) && $edit}
				<input type="hidden" id="id_explore_link" name="id_explore_link" value="{$exploreLinkInfo['id']}">
			{/if}
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enable, If you want to add an custom link from a cms page. Disable, if you want to add a custom link.' mod='wkfooterexploreblock'}">
						{l s='Link From CMS Block' mod='wkfooterexploreblock'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="is_cms_block_link" id="is_cms_block_link_on" value="1" {if isset($smarty.post.is_cms_block_link)}{if $smarty.post.is_cms_block_link}checked="checked"{/if}{elseif isset($exploreLinkInfo['id_cms']) && $exploreLinkInfo['id_cms']}checked="checked"{/if}/>

						<label for="is_cms_block_link_on">{l s='Yes' mod='wkfooterexploreblock'}</label>

						<input type="radio" name="is_cms_block_link" id="is_cms_block_link_off" value="0" {if isset($smarty.post.is_cms_block_link)} {if !$smarty.post.is_cms_block_link}checked="checked"{/if}{elseif isset($exploreLinkInfo['id_cms'])}{if !$exploreLinkInfo['id_cms']}checked="checked"{/if}{else}checked="checked"{/if}/>

						<label for="is_cms_block_link_off">{l s='No' mod='wkfooterexploreblock'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			{* Feilds of cms page *}
			<div id="cms_block_content" class="{if isset($smarty.post.is_cms_block_link)}{if !$smarty.post.is_cms_block_link}hidden{/if}{elseif !isset($exploreLinkInfo['id_cms']) || !$exploreLinkInfo['id_cms']}hidden{/if}">
				{*Display CMS Pages*}
				{if isset($cmsPages)}
					<div class="form-group">
						<label class="control-label col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip"
							title="{l s='Select the CMS page you want to attach with this custom link. CMS page link will be used as redirect url and meta title of the CMS page will be considered as the name of footer custom link.' mod='wkfooterexploreblock'}">
								{l s='Select CMS Page' mod='wkfooterexploreblock'}
							</span>
						</label>
						<div class="col-lg-9">
							<table class="table table-bordered" style="width:40%;">
								<thead>
									<tr>
										<th class="fixed-width-xs">
										</th>
										<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='wkfooterexploreblock'}</span></th>
										<th>
											<span class="title_box">
												{l s='Page Name' mod='wkfooterexploreblock'}
											</span>
										</th>
									</tr>
								</thead>
								<tbody>
									{foreach $cmsPages as $cmsPage}
										<tr>
											<td><input type="radio" value="{$cmsPage.id_cms|escape:'htmlall':'UTF-8'}" name="id_cms" {if isset($exploreLinkInfo['id_cms']) && $exploreLinkInfo['id_cms'] == $cmsPage.id_cms}checked="checked"{/if}>
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
			<div id="non_cms_block_content" class="{if isset($smarty.post.is_cms_block_link)}{if $smarty.post.is_cms_block_link}hidden{/if}{elseif isset($exploreLinkInfo['id_cms']) && $exploreLinkInfo['id_cms']}hidden{/if}">
				<div class="form-group">
					<label class="col-sm-3 control-label required" for="explore_link_name">
						{l s='Name' mod='wkfooterexploreblock'}
					</label>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-lg-10">
								{foreach from=$languages item=language}
									{assign var="explore_link_name" value="explore_link_name_`$language.id_lang`"}
									<input type="text" id="{$explore_link_name}" name="{$explore_link_name}" value="{if isset($exploreLinkInfo.name[$language.id_lang]) && $exploreLinkInfo.name[$language.id_lang]}{$exploreLinkInfo.name[$language.id_lang]}{else if isset($smarty.post.$explore_link_name)}{$smarty.post.$explore_link_name}{/if}" data-lang-name="{$language.name}" placeholder="{l s='Enter exlore link name' mod='wkfooterexploreblock'}" class="form-control explore_link_name_all" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}/>
								{/foreach}
							</div>
							{if $languages|@count > 1}
								<div class="col-lg-2">
									<button type="button" id="explore_link_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										{$currentLang.iso_code}
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=language}
											<li>
												<a href="javascript:void(0)" onclick="showExploreLinkLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
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
						title="{l s='enter the url to which you want to redirect your guest for this custom link.' mod='wkfooterexploreblock'}">
							{l s='Redirect Url' mod='wkfooterexploreblock'}
						</span>
					</label>
					<div class="col-lg-5">
						<input type="text" name="link" {if isset($smarty.post.link)}value="{$smarty.post.link}"{elseif isset($exploreLinkInfo['link'])}value="{$exploreLinkInfo['link']}"{/if}>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enable, If you want to show this link to the navigation menu.' mod='wkfooterexploreblock'}">
						{l s='Show at navigation menu' mod='wkfooterexploreblock'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="show_at_navigation" id="show_at_navigation_on" value="1" checked="checked"/>
						<label for="show_at_navigation_on">{l s='Yes' mod='wkfooterexploreblock'}</label>

						<input type="radio" name="show_at_navigation" id="show_at_navigation_off" value="0" {if isset($smarty.post.show_at_navigation)}{if !$smarty.post.show_at_navigation}checked="checked"{/if}
						{elseif isset($exploreLinkInfo['show_at_navigation']) && !$exploreLinkInfo['show_at_navigation']}checked="checked"{/if}/>
						<label for="show_at_navigation_off">{l s='No' mod='wkfooterexploreblock'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enable, If you want to show this link to the footer block.' mod='wkfooterexploreblock'}">
						{l s='Show at footer block' mod='wkfooterexploreblock'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="show_at_footer" id="show_at_footer_on" value="1" checked="checked"/>
						<label for="show_at_footer_on">{l s='Yes' mod='wkfooterexploreblock'}</label>

						<input type="radio" name="show_at_footer" id="show_at_footer_off" value="0" {if isset($smarty.post.show_at_footer)}{if !$smarty.post.show_at_footer}checked="checked"{/if}
						{elseif isset($exploreLinkInfo['show_at_footer']) && !$exploreLinkInfo['show_at_footer']}checked="checked"{/if}/>
						<label for="show_at_footer_off">{l s='No' mod='wkfooterexploreblock'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Disabled custom links will not be displayed anywhere.' mod='wkfooterexploreblock'}">
						{l s='Enable' mod='wkfooterexploreblock'}
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="active" id="active_on" value="1" checked="checked"/>
						<label for="active_on">{l s='Yes' mod='wkfooterexploreblock'}</label>

						<input type="radio" name="active" id="active_off" value="0" {if isset($smarty.post.active)}{if !$smarty.post.active}checked="checked"{/if}
						{elseif isset($exploreLinkInfo['active']) && !$exploreLinkInfo['active']}checked="checked"{/if}/>
						<label for="active_off">{l s='No' mod='wkfooterexploreblock'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminCustomExploreLinkSetting')}" class="btn btn-default">
					<i class="process-icon-cancel"></i>{l s='Cancel' mod='wkfooterexploreblock'}
				</a>
				<button type="submit" name="submitAdd{$table}" class="btn btn-default pull-right">
					<i class="process-icon-save"></i>{l s='Save' mod='wkfooterexploreblock'}
				</button>
				<button type="submit" name="submitAdd{$table}AndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='wkfooterexploreblock'}
				</button>
			</div>
		</form>
	</div>
</div>