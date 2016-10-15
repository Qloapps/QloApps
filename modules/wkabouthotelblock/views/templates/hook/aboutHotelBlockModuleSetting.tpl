<div class="btn-group setting-link-div col-sm-3 col-xs-12">
	<a type="button" href="{$link->getAdminLink('AdminAboutHotelBlockSetting')|escape:'html':'UTF-8'}" class="setting-link btn btn-default col-sm-10 col-xs-10">
		<span class="col-sm-2 col-xs-2"><i class="icon-file-text"></i></span>
		<span class="setting-title col-sm-10 col-xs-10">{l s='Hotel Interior Block Setting' mod='wkabouthotelblock'}</span>
	</a>
	<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='About Hotel Setting' mod='wkabouthotelblock'}" data-content="{l s='Configure Hotel Interior block. You can display hotel interior images using this block. This block will be displayed in home page' mod='wkabouthotelblock'}" data-placement="bottom">
		<i class="icon-question-circle"></i>
	</a>
</div>