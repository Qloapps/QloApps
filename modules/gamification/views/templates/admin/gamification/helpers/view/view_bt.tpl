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
<script>
	var current_level_percent_tab = {$current_level_percent|intval};
	var current_level_tab = '{$current_level|intval}';
	var gamification_level_tab = '{l s='Level' mod='gamification' js=1}';
	$(document).ready( function () {	
		$('.gamification_badges_img').tooltip();
		$('#gamification_progressbar_tab').progressbar({
			change: function() {
		        if ({$current_level_percent})
		        	$( "#gamification_progress-label_tab" ).html( '{l s='Level' mod='gamification' js=1}'+' '+{$current_level|intval}+' : '+$('#gamification_progressbar_tab').progressbar( "value" ) + "%" );
		        else
		        	$( "#gamification_progress-label_tab" ).html('');
		      },
	 	});
		$('#gamification_progressbar_tab').progressbar("value", {$current_level_percent|intval} );
	});
	var admintab_gamification = true;

</script>

<div class="panel">
	<div id="intro_gamification">
		<div id="left_intro">
			<h4>{l s="Become an e-commerce expert in leaps and bounds!" mod='gamification'}</h4><br/>
			<p>
				{l s="With all of the great features and benefits that PrestaShop offers, it's important to keep up!" mod='gamification'}<br/><br/>
				{l s="The main goal of all of the features we offer is to make you succeed in the e-commerce world. In order to accomplish this, we have created a system of badges and points that make it easy to monitor your progress as a merchant. We have broken down the system into three levels, all of which are integral to success in the e-commerce world: (i) Your use of key e-commerce features on your store; (ii) Your sales performance; (iii) Your presence in international markets." mod='gamification'}<br/><br/>
				{l s="The more progress your store makes, the more badges and points you earn. No need to submit any information or fill out any forms; we know how busy you are, everything is automatic!" mod='gamification'}<br/><br/>
				{l s="Now, with the click of a button, you will be able to see sales-enhancing features that you may be missing out on. Take advantage and check it out below!" mod='gamification'}
			</p>
		</div>
		<div id="right_intro">
			<h4>{l s="Our team is available to help. Contact us today!" mod='gamification'}</h4><br/>
			<ul>
				<li>
					<img src="../modules/gamification/views/img/mail_icon.png" alt="{l s="Email" mod='gamification' mod='gamification'}" />
					<a href="http://www.prestashop.com/en/contact-us?utm_source=gamification">{l s="Fill out a contact form" mod='gamification'}</a>
				</li>
			</ul>
		</div>
	</div>
	<div id="completion_gamification">
		<h4>{l s='Completion level' mod='gamification'}</h4>
		<div id="gamification_progressbar_tab"></div>
		<span class="gamification_progress-label" id="gamification_progress-label_tab">{l s="Level" mod='gamification' mod='gamification'} {$current_level|intval} : {$current_level_percent|intval} %</span>
	</div>
	&nbsp;
</div>
<div class="clear"><br/></div>

{foreach from=$badges_type key=key item=type}
<div class="panel">
	<h3><i class="icon-bookmark"></i> {$type.name|escape:html:'UTF-8'}</h3>
	<div class="row">
		<div class="col-lg-2">
			{include file='./filters_bt.tpl' type=$key}
		</div>
		<div class="col-lg-10">
			<ul class="badge_list" id="list_{$key}" style="">
				{foreach from=$type.badges item=badge}
				<li class="badge_square badge_all {if $badge->validated}validated {else} not_validated{/if} group_{$badge->id_group} level_{$badge->group_position} " id="{$badge->id|intval}">
					<div class="gamification_badges_img" data-placement="top" data-toggle="tooltip" data-original-title="{$badge->description|escape:html:'UTF-8'}"><img src="{$badge->getBadgeImgUrl()}" alt="{$badge->name|escape:html:'UTF-8'}" /></div>
					<div class="gamification_badges_name">{$badge->name|escape:html:'UTF-8'}</div>
				</li>
				{foreachelse}
				<li>
					<div class="gamification_badges_name">{l s="No badge in this section" mod='gamification'}</div>
				</li>
				{/foreach}
			</ul>
		</div>
		<p id="no_badge_{$key}" class="gamification_badges_name" style="display:none;text-align:center">{l s="No badge in this section" mod='gamification'}</p>
	</div>
</div>
<div class="clear"><br/></div>
{/foreach}
