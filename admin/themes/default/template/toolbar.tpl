{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="{$table}_toolbar" class="toolbar-placeholder">
	<div class="toolbarBox {if $toolbar_scroll}toolbarHead{/if}">
		{block name=toolbarBox}
			<ul>
				{foreach from=$toolbar_btn item=btn key=k}
					<li>
						<a id="desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="toolbar_btn{if isset($btn.target) && $btn.target} _blank{/if}"{if isset($btn.href)} href="{$btn.href}"{/if} title="{$btn.desc}"{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>
							<span class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}{if isset($btn.class)} {$btn.class}{/if}"></span>
							<div{if isset($btn.force_desc) && $btn.force_desc == true } class="locked"{/if}>{$btn.desc}</div>
						</a>
						{if $k == 'modules-list'}
							<div id="modules_list_container" style="display:none">
							<div style="float:right;margin:5px">
								<a href="#" onclick="$('#modules_list_container').slideUp();return false;"><img alt="X" src="../img/admin/close.png" /></a>
							</div>
							<div id="modules_list_loader"><img src="../img/loader.gif" alt="" border="0" /></div>
							<div id="modules_list_container_tab_modal" style="display:none;"></div>
							</div>
						{/if}
					</li>
				{/foreach}
			</ul>

			<script type="text/javascript">
			//<![CDATA[
				var submited = false
				var modules_list_loaded = false;
				$(function() {
					//get reference on save link
					btn_save = $('#{$table}_toolbar span[class~="process-icon-save"]').parent();

					//get reference on form submit button
					btn_submit = $('#{$table}_form_submit_btn');

					if (btn_save.length > 0 && btn_submit.length > 0)
					{
						//get reference on save and stay link
						btn_save_and_stay = $('span[class~="process-icon-save-and-stay"]').parent();

						//get reference on current save link label
						lbl_save = $('#desc-{$table}-save div');

						//override save link label with submit button value
						if (btn_submit.val().length > 0)
							lbl_save.html(btn_submit.attr("value"));

						if (btn_save_and_stay.length > 0)
						{
							//get reference on current save link label
							lbl_save_and_stay = $('#desc-{$table}-save-and-stay div');

							//override save and stay link label with submit button value
							if (btn_submit.val().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
								lbl_save_and_stay.html(btn_submit.val() + " {l s='and stay'} ");
						}

						//hide standard submit button
						btn_submit.hide();
						//bind enter key press to validate form
						$('#{$table}_form').keypress(function (e) {
							if (e.which == 13 && e.target.localName != 'textarea' && !e.target.hasClass('tagify'))
								$('#desc-{$table}-save').click();
						});
						//submit the form
						{block name=formSubmit}
							btn_save.click(function() {
								// Vars
								var btn_submit = $('#{$table}_form_submit_btn');

								// Avoid double click
								if (submited)
									return false;
								submited = true;

								//add hidden input to emulate submit button click when posting the form -> field name posted
								btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

								$('#{$table}_form').submit();
								return false;
							});

							if (btn_save_and_stay)
							{
								btn_save_and_stay.click(function() {
									//add hidden input to emulate submit button click when posting the form -> field name posted
									btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');

									$('#{$table}_form').submit();
									return false;
								});
							}
						{/block}
					}
					{if isset($tab_modules_open) && $tab_modules_open}
						$('#modules_list_container').slideDown();
						openModulesList();
					{/if}
				});
				{if isset($tab_modules_list) && $tab_modules_list}
					$('.process-icon-modules-list').parent('a').unbind().bind('click', function (){
						$('#modules_list_container').slideDown();
						openModulesList();
					});
				{/if}
			//]]>
			</script>
		{/block}
		<div class="pageTitle">
			<h3>{block name=pageTitle}
				<span id="current_obj" style="font-weight: normal;">
					{if $title}
						{foreach $title as $key => $item name=title}
							{* Use strip_tags because if the string already has been through htmlentities using escape will break it *}
							<span class="breadcrumb item-{$key} ">{$item|strip_tags}
								{if !$smarty.foreach.title.last}
									<img alt="&gt;" style="margin-right:5px" src="../img/admin/separator_breadcrumb.png" />
								{/if}
							</span>
						{/foreach}
					{else}
						&nbsp;
					{/if}
				</span>
				{/block}
			</h3>
		</div>
	</div>
</div>
