{*
* Copyright since 2007 Webkul.
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
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($conf)}
	<div class="bootstrap">
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{$conf}
		</div>
	</div>
{/if}
{if isset($errors) && count($errors) && current($errors) != '' && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
	<div class="bootstrap">
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($errors) == 1}
			{reset($errors)}
		{else }
			{l s='%d errors' sprintf=$errors|count}
			<br/>
			<ol>
				{foreach $errors as $error}
					<li>{$error}</li>
				{/foreach}
			</ol>
		{/if}
		</div>
	</div>
{/if}
{if isset($informations) && count($informations) && $informations}
	<div class="bootstrap">
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<ul id="infos_block" class="list-unstyled">
				{foreach $informations as $info}
					<li>{$info}</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}
{if isset($confirmations) && count($confirmations) && $confirmations}
	<div class="bootstrap">
		<div class="alert alert-success" style="display:block;">
			{foreach $confirmations as $conf}
				{$conf}
			{/foreach}
		</div>
	</div>
{/if}
{if isset($warnings) && count($warnings)}
	<div class="bootstrap">
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{if count($warnings) > 1}
				<h4>{l s='There are %d warnings:' sprintf=count($warnings)}</h4>
			{/if}
			<ul class="list-unstyled">
				{foreach $warnings as $warning}
					<li>{$warning}</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}
