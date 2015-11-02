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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script>
var display_onboarding_modal= {$display_onboarding_modal|intval};
var current_step_onboarding = {$current_step|escape|intval};
var onboarding_ajax_url = "{$link->getAdminLink('AdminOnboarding')|escape:'javascript':'UTF-8'}";
</script>
{capture name="onboardingStepParagraph"}
	{if $current_step == 0}
		{l s='Hey %s, welcome on your own online shop.[1]Follow the guide and take the first steps with your online shop!' sprintf=[$employee->firstname] tags=['<br />'] mod='onboarding'}
	{else if $current_step == 1}
		{l s='Check out our catalog to get a new theme or customize the current default theme.[1]Add your logo, play on fonts and colors... Give this special look to your shop!' tags=['<br />'] mod='onboarding'}
	{else if $current_step == 2}
		{l s='Start your product catalog with a first product.[1]Make sure you cover the basics by setting its price, having a nice description and uploading a catchy image![1]If you already have your product base in a .CSV file, save time and make an import!' tags=['<br />'] mod='onboarding'}
	{else if $current_step == 3}
        {if $has_psp}
            {l s='Your shop runs with PrestaShop Payments by HiPay, so that you can accept payments by card right now. Other payment methods are available too, make sure you set everything up!' mod='onboarding'}
        {else}
		    {l s='Select which payment methods you want to offer to customers on your shop, and manage the various restrictions you can apply (per currency, country or group of customers).' mod='onboarding'}
        {/if}
	{else if $current_step == 4}
		{l s='If you feel you need more information, you can still have a look at PrestaShop Documentation: click on "Help" in the top right corner of your back office!' mod='onboarding'}
	{else if $current_step == 5}
		{l s='You have completed all the essential first steps to configure your online shop. You can repeat those steps if you have more products, payment methods or shipping partners to add.[1]To dive deeper in the configuration of your shop, you should read the [2]"First steps with PrestaShop 1.6"[/2] chapter of the PrestaShop User Guide.[1]Once you are certain that your shop is ready to sell your products, click on the Launch button to make your shop public.'  tags=['<br />', '<a href="http://doc.prestashop.com/display/PS16/First+steps+with+PrestaShop+1.6" class="_blank">'] mod='onboarding'}
	{/if}
{/capture}
{capture name="onboardingStepButton"}
	{if $current_step == 0}
		{l s='Let\'s start!' mod='onboarding'}
	{else if $current_step == 5}
		{l s='I\'m all good, let\'s launch!' mod='onboarding'}
	{else}
		{l s='I\'m done, take me to next step' mod='onboarding'}
	{/if}
{/capture}
{capture name="onboardingStepBannerTitle"}
	{if $current_step == 0}
		{l s='Take a tour: get started with PrestaShop' mod='onboarding'}
	{else if $current_step == 1}
		{l s='Customize your shop\'s look and feel' mod='onboarding'}
	{else if $current_step == 2}
		{l s='Add your first products' mod='onboarding'}
	{else if $current_step == 3}
		{l s='Get your shop ready for payments' mod='onboarding'}
	{else if $current_step == 4}
		{l s='You are now ready to launch your shop.' mod='onboarding'}
	{else if $current_step == 5}
		{l s='You are now ready to launch your shop.' mod='onboarding'}
	{/if}
{/capture}

{capture name="onboardingStepModalTitle"}
	{if $current_step == 1}
		{l s='A few steps before launching!' mod='onboarding'}
	{else if $current_step == 2}
		{l s='Let\'s create your first products' mod='onboarding'}
	{else if $current_step == 3}
		{l s='Get your shop ready for payments' mod='onboarding'}
	{else if $current_step == 4}
		{l s='Choose your shipping options' mod='onboarding'}
	{else if $current_step == 5}
		{l s='Hurrah!' mod='onboarding'}
	{/if}
{/capture}
{capture name="onboardingComplete"}
	{if $current_step == 1}
	{else if $current_step == 2}
		{l s='1/4 complete' mod='onboarding'}
	{else if $current_step == 3}
		{l s='2/4 complete' mod='onboarding'}
	{else if $current_step == 4}
		{l s='3/4 complete' mod='onboarding'}
	{else if $current_step == 5}
		{l s='4/4 complete' mod='onboarding'}
	{/if}
{/capture}
{capture name="onboardingCompletePercentage"}
	{if $current_step == 1}
	10%%
	{else if $current_step == 2}
	25%%
	{else if $current_step == 3}
	50%%
	{else if $current_step == 4}
	75%%
	{else if $current_step == 5}
	100%%
	{/if}
{/capture}

<div class="onboarding minimized">
	<div class="overlay"></div>
	<div class="panel onboarding-steps">
		<div id="onboarding-starter" class="hide">
			<div class="row">
				<div class="col-md-12">
					<h3>{l s='Getting Started with PrestaShop' mod='onboarding'}</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-3 col-md-2 col-md-offset-2">
					<div class="onboarding-step step-first {if $current_step == 0}step-todo{elseif $current_step == 1}step-in-progress active{elseif $current_step > 1}active step-success{/if}"></div>
				</div>
				<div class="col-xs-3 col-md-2">
					<div class="onboarding-step {if $current_step <= 1}step-todo{elseif $current_step == 2}step-in-progress active{elseif $current_step > 2}active step-success{/if}"></div>
				</div>
				<div class="col-xs-3 col-md-2">
					<div class="onboarding-step {if $current_step <= 2}step-todo{elseif $current_step == 3}step-in-progress active{elseif $current_step > 3}active step-success{/if}"></div>
				</div>
				<div class="col-xs-3 col-md-2">
					<div class="onboarding-step step-final {if $current_step <= 3}step-todo{elseif $current_step == 4}step-in-progress active{elseif $current_step > 4}active step-success{/if}"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-3 col-md-2 col-md-offset-2 text-center">
					<a style="{if $current_step < 1} color:gray; text-decoration:none {/if}"{if $current_step >= 1} href="{$continue_editing_links.theme}"{/if}>{l s='Customize your shop' mod='onboarding'}</a>
				</div>
				<div class="col-xs-3 col-md-2 text-center">
					<a style="{if $current_step < 2} color:gray; text-decoration:none {/if}"{if $current_step >= 2} href="{$continue_editing_links.product}"{/if}>{l s='Add products' mod='onboarding'}</a>
				</div>
				<div class="col-xs-3 col-md-2 text-center">
					<a style="{if $current_step < 3} color:gray; text-decoration:none {/if}"{if $current_step >= 3} href="{$continue_editing_links.payment}"{/if}>{l s='Configure payments' mod='onboarding'}</a>
				</div>
				<div class="col-xs-3 col-md-2 text-center">
					<a style="{if $current_step < 4} color:gray; text-decoration:none {/if}"{if $current_step >= 4} href="{$continue_editing_links.carrier}"{/if}>{l s='Choose your shipping options' mod='onboarding'}</a>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-lg-8">
					<h4>{$smarty.capture.onboardingStepBannerTitle|escape:'html':'UTF-8'}</h4>
					<p>{$smarty.capture.onboardingStepParagraph|escape:'UTF-8'}</p>
				</div>
				<div class="col-lg-4 onboarding-action-container">
					<a href="{$next_step_link|escape:'html':'UTF-8'}" class="btn btn-default btn-lg quick-start-button pull-right">
						{$smarty.capture.onboardingStepButton|escape:'html':'UTF-8'}&nbsp;&nbsp;
						<i class="icon icon-angle-right icon-lg"></i>
					</a>
					<a class="btn btn-default btn-lg pull-right" href="#" id="onboarding-close">
						{l s='No thanks!' mod='onboarding'}&nbsp;&nbsp;
						<i class="icon icon-times icon-lg"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="onboarding-intro">
			<h3 class="text-center">
			{$smarty.capture.onboardingStepModalTitle|escape:'html':'UTF-8'}
			</h3>
			<a class="close-button" href="#" id="quick-start-button">
				<i class="icon icon-times-circle"></i>
			</a>
		</div>

		<div class="steps-list-container">
			<ul class="steps-list">
				<li {if $current_step > 1}class="checked"{/if}{if $current_step == 1}class="active"{/if}{if $current_step < 1}class="inactive"{/if}>
					<span class="title">{l s='Customize your shop\'s look and feel' mod='onboarding'}</span>
					{if $current_step == 1}
						<p class="desc">
						{l s='Give your shop its own identity based on your brand.' mod='onboarding'}<br/>
						{l s='You can change your theme or install a new one, and make sure to upload your own logo to make your shop truly unique.' mod='onboarding'}<br/><br/>
							<a class="btn btn-primary continue_editing" href="#">
							<i class="icon icon-pencil icon-lg"></i>
							{l s='OK, take me to my theme' mod='onboarding'}</a>
						</p>
					{else if $current_step > 1}
						<p class="desc">
							<a class="continue_editing" href="{$continue_editing_links.theme|escape:'html':'UTF-8'}">{l s='Continue editing' mod='onboarding'}</a>
						</p>
					{/if}
				</li>
				<li {if $current_step > 2}class="checked"{/if}{if $current_step == 2}class="active"{/if}{if $current_step < 2}class="inactive"{/if}>
					<span class="title">{l s='Add products to your catalog' mod='onboarding'}</span>
					{if $current_step == 2}
						<p class="desc">
							{l s='Start your product catalog with a first product.' mod='onboarding'}
							<br/>
							{l s='Make sure you cover the basics by setting its price, having a nice description and uploading a catchy image!' mod='onboarding'}
							{assign "onboardingstep2importcsv" value=$continue_editing_links.import var="onboardingstep2importcsv"}
							{l s='If you already have your product base in a .CSV file, save time and make an import!' tags=["<a href='$onboardingstep2importcsv&amp;addproduct'>"] mod='onboarding'}
							<br/><br/>
							<a class="btn btn-primary continue_editing" href="#">
							<i class="icon icon-book icon-lg"></i>
							{l s='Ok, Go to my catalog' mod='onboarding'}</a>
						</p>
					{else if $current_step > 2}
						<p class="desc">
							<a class="" href="{$continue_editing_links.product|escape:'html':'UTF-8'}">{l s='Continue adding products' mod='onboarding'}</a>
						</p>
					{/if}
				</li>
				<li {if $current_step > 3}class="checked"{/if}{if $current_step == 3}class="active"{/if}{if $current_step < 3}class="inactive"{/if}>
					<span class="title">{l s='Set up your payment methods' mod='onboarding'}</span>
					{if $current_step == 3}
						<p class="desc">
                            {if $has_psp}
                                {l s='Your shop runs with PrestaShop Payments by HiPay, so that you can accept payments by card right now. Other payment methods are available too, make sure you set everything up!' mod='onboarding'}
                            {else}
                                {l s='Select which payment methods you want to offer to customers on your shop, and manage the various restrictions you can apply (per currency, country or group of customers).' mod='onboarding'}
                            {/if}
							<br/><br/>
							<a class="btn btn-primary continue_editing" href="#">
								<i class="icon icon-credit-card icon-lg"></i>
								{l s='Show me payment methods' mod='onboarding'}
							</a>
						</p>
					{else if $current_step > 3}
						<p class="desc">
							<a class="" href="{$continue_editing_links.payment|escape:'html':'UTF-8'}">{l s='Continue selecting payment methods' mod='onboarding'}</a>
						</p>
					{/if}
				</li>
				<li {if $current_step > 4}class="checked"{/if}{if $current_step == 4}class="active"{/if}{if $current_step < 4}class="inactive"{/if}>
					<span class="title" >{l s='Set up your shipping methods' mod='onboarding'}</span>
					{if $current_step == 4}
					<p class="desc">
						{l s='Unless you are only selling virtual products, you must register your shipping partners into PrestaShop.' mod='onboarding'}<br/>
						{l s='Without this your customers won\'t be able to enjoy your products!' mod='onboarding'}
						<br/>
						<br/>
						<a class="btn btn-primary continue_editing" href="#">
							<i class="icon icon-truck icon-lg"></i>
							{l s='Let\'s see about shipping' mod='onboarding'}
						</a>
					</p>
					{else if $current_step > 4}
						<p class="desc">
							<a class="" href="{$continue_editing_links.carrier|escape:'html':'UTF-8'}">{l s='Continue selecting shipping methods' mod='onboarding'}</a>
						</p>
					{/if}
				</li>
			</ul>
			{if $current_step == 5}
				<div class="step-launch">
					<button id="onboarding-launch" class="btn btn-block btn-lg btn-primary">
						<i class="icon icon-check icon-lg"></i>
						{l s='Launch' mod='onboarding'}
					</button>
				</div>
			{else}
				<a href="#" class="skip">{l s='Skip Tutorial' mod='onboarding'}</a>
			{/if}
		</div>
		<div class="steps-animation-container">
			{if $current_step == 1}
				<img src="{$module_dir|escape:'html':'UTF-8'}img/step0.jpg" alt="Step 1">
			{/if}
			{if $current_step == 2}
				<img src="{$module_dir|escape:'html':'UTF-8'}img/step1.jpg" alt="Step 2">
			{/if}
			{if $current_step == 3}
				<img src="{$module_dir|escape:'html':'UTF-8'}img/step2.jpg" alt="Step 3">
			{/if}
			{if $current_step == 4}
				<img src="{$module_dir|escape:'html':'UTF-8'}img/step3.jpg" alt="Step 4">
			{/if}
			{if $current_step == 5}
				{include file="./launch_animation.tpl"}
			{/if}
			<div class="step-before-launch text-center">
				{if $current_step == 4}
					{l s='Last step before launch!' mod='onboarding'}
				{else if $current_step == 5}
					{l s='You are all set!' mod='onboarding'}
				{else}
					{l s='You are only %s steps away from launch!' sprintf=[5-(int)$current_step] mod='onboarding'}
				{/if}
			</div>
		</div>
	</div>
	<div class="panel final" style="display: none;">
		<div class="onboarding-intro">
			<h3 class="text-center">
			{$smarty.capture.onboardingStepModalTitle|escape:'html':'UTF-8'}
			</h3>
			<a class="close-button" href="" id="final-button">
				<i class="icon icon-times-circle"></i>
			</a>
		</div>
		<div class="final-container">
			<span class="title">
				{l s='You are now ready to launch your shop. If you feel you need more information, you can still have a look at PrestaShop Documentation:' mod='onboarding'}
				<br />
				{l s='click on "Help" in the top right corner of your back office!' mod='onboarding'}
			</span>
			<br />
			<textarea name="social-text" id="social-text">{l s='I just launched my online shop with @PrestaShop. Check it out!' mod='onboarding'}</textarea>
			<br />
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_facebook_click();">
					<i class="icon icon-facebook icon-4x icon-fw"></i>
				</a>
			</div>
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_twitter_click($('#social-text').text());">
					<i class="icon icon-twitter icon-4x icon-fw"></i>
				</a>
			</div>
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_linkedin_click();">
					<i class="icon icon-linkedin icon-4x icon-fw"></i>
				</a>
			</div>
			<div class="col-lg-3 text-center">
				<a href="#" class="btn btn-default" onclick="share_google_click();">
					<i class="icon icon-google-plus icon-4x icon-fw"></i>
				</a>
			</div>
		</div>
	</div>
</div>
