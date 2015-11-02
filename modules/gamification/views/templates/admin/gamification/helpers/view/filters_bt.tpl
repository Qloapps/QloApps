{if isset($type)}
<form class="form-horizontal well" role="form">
	{if $type == 'badges_feature' || $type == 'badges_achievement'}
		<div class="form-group">
			<label>{l s='Type:' mod='gamification'}</label>
			<select id="group_select_{$type}" onchange="filterBadge('{$type}');">
					<option value="badge_all">{l s='All' mod='gamification'}</option>
				{if isset($groups.$type)}
				{foreach from=$groups.$type key=id_group item=group}
					<option value="group_{$id_group}">{$group}</option>
				{/foreach}
				{/if}
			</select>
		</div>
	{/if}
		<div class="form-group">
			<label>{l s='Status:' mod='gamification'}</label>
			<select id="status_select_{$type}" onchange="filterBadge('{$type}');">
				<option value="badge_all">{l s='All' mod='gamification'}</option>
				<option value="validated">{l s='Validated' mod='gamification'}</option>
				<option value="not_validated">{l s='Not Validated' mod='gamification'}</option>
			</select>
		</div>
	{if $type == 'badges_feature' || $type == 'badges_achievement'}
		<div class="form-group">
			<label>{l s='Level:' mod='gamification'}</label>
				<select id="level_select_{$type}" onchange="filterBadge('{$type}');">
						<option value="badge_all">{l s='All' mod='gamification'}</option>
					{if isset($levels)}
					{foreach from=$levels key=id_level item=level}
						<option value="level_{$id_level}">{$level}</option>
					{/foreach}
					{/if}
				</select>
		</div>
	{/if}
</form>
<div class="clear"></div>
{/if}
