<script>
	var current_id_tab = {$current_id_tab|intval};
	var current_level_percent = {$current_level_percent|intval};
	var current_level = {$current_level|intval};
	var gamification_level = '{l s='Level' mod='gamification' js=1}';
	var advice_hide_url = '{$advice_hide_url}';
	var hide_advice = '{l s='Do you really want to hide this advice?' mod='gamification' js=1}';

	$('#dropdown_gamification .notifs_panel_header, #dropdown_gamification .tab-content').click(function () {
		return false;
	});

	$('#dropdown_gamification .panel-footer').click(function (elt) {
		window.location.href = '{$link->getAdminLink('AdminGamification')}';
	});

	$('#gamification_tab li').click(function () {
		gamificationDisplayTab($(this).children('a'));
		return false;
	});

	function gamificationDisplayTab(elt)
	{
		$('#gamification_tab li, .gamification-tab-pane').removeClass('active');
		$(elt).parent('li').addClass('active');
		$('#'+$(elt).data('target')).addClass('active');
	}

</script>
<li id="gamification_notif" style="background:none" class="dropdown">
	<a href="javascript:void(0);" class="dropdown-toggle gamification_notif" data-toggle="dropdown">
		<i class="icon-trophy"></i>
		<span id="gamification_notif_number_wrapper" class="notifs_badge">
			<span id="gamification_notif_value">{$notification|intval}</span>
		</span>
	</a>
	<div class="dropdown-menu notifs_dropdown" id="dropdown_gamification">
		<section id="gamification_notif_wrapper" class="notifs_panel" style="width:325px">
			<header class="notifs_panel_header">
				<h3>{l s='Your Merchant Expertise' mod='gamification'}
					<span class="label label-default" style="float:right">{l s='Level' mod='gamification'} {$current_level|intval} : {$current_level_percent|intval} %</span>
				</h3>
			</header>
			<div class="progress" style="margin: 10px">
				<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{$current_level_percent|intval}" aria-valuemin="0" aria-valuemax="100" style="width: {$current_level_percent|intval}%;">
				<span style="color:#FFF">{l s='Level' mod='gamification'} {$current_level|intval} : {$current_level_percent|intval} %</span>
				</div>
			</div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" id="gamification_tab" style="margin-left:10px">
				<li class="active">
					<a href="#home" data-toggle="tab" data-target="gamification_1" onclick="gamificationDisplayTab(this); return false;">{l s='Last badge :' mod='gamification'}</a>
				</li>
				<li>
					<a href="#profile" data-toggle="tab" data-target="gamification_2" onclick="gamificationDisplayTab(this); return false;">{l s='Next badge :' mod='gamification'}</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane gamification-tab-pane active" id="gamification_1">
					<ul id="gamification_badges_list" style="{if $badges_to_display|count <= 2} height:170px;{/if} padding-left:0">
					{foreach from=$unlock_badges name=badge_list item=badge key="i"}
						{if $badge->id}
							<li class="{if $badge->validated} unlocked {else} locked {/if}" style="float:left;">
								<span class="{if $badge->validated} unlocked_img {else} locked_img {/if}" {if $badge->validated}style="left: 12px;"{/if}></span>
								<div class="gamification_badges_title"><span>{if $badge->validated} {l s='Last badge :' mod='gamification'} {else} {l s='Next badge :' mod='gamification'} {/if}</span></div>
								<div class="gamification_badges_img" data-placement="{if $i <= 1}bottom{else}top{/if}" data-original-title="{$badge->description|escape:html:'UTF-8'}"><img src="{$badge->getBadgeImgUrl()}"></div>
								<div class="gamification_badges_name">{$badge->name|escape:html:'UTF-8'}</div>
							</li>
						{/if}
					{/foreach}
					</ul>
				</div>
				<div class="tab-pane gamification-tab-pane" id="gamification_2">
					<ul id="gamification_badges_list" style="{if $badges_to_display|count <= 2} height:170px;{/if} padding-left:0">
					{foreach from=$next_badges name=badge_list item=badge key="i"}
						{if $badge->id && !$badge->awb}
							<li class="{if $badge->validated} unlocked {else} locked {/if}" style="float:left;">
								<span class="{if $badge->validated} unlocked_img {else} locked_img {/if}" {if $badge->validated}style="left: 12px;"{/if}></span>
								<div class="gamification_badges_title"><span>{if $badge->validated} {l s='Last badge :' mod='gamification'} {else} {l s='Next badge :' mod='gamification'} {/if}</span></div>
								<div class="gamification_badges_img" data-placement="{if $i <= 1}bottom{else}top{/if}"data-toggle="tooltip" data-original-title="{$badge->description|escape:html:'UTF-8'}"><img src="{$badge->getBadgeImgUrl()}"></div>
								<div class="gamification_badges_name">{$badge->name|escape:html:'UTF-8'}</div>
							</li>
						{/if}
					{/foreach}
					</ul>
				</div>
			</div>

			<footer class="panel-footer">
				<a href="{$link->getAdminLink('AdminGamification')}">{l s='View my complete profile' mod='gamification'}</a>
			</footer>
		</section>
	</div>
</li>
