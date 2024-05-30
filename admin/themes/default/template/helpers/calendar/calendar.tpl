<div id="datepicker" class="row hide">
	<div class="col-lg-12">
		<div class="daterangepicker-days">
			<div class="row">
				{if $is_rtl}
				<div class="col-sm-6 col-lg-4">
					<div class="datepicker2" data-date="{$date_to}" data-date-format="{$date_format}">
						<div class='form-date-actions'>
							<button class='btn btn-link' type='button' id="datepicker-cancel" tabindex="7">
								<i class='icon-remove'></i>
								{l s='Cancel'}
							</button>
							<button class='btn btn-default pull-right' type='submit' name="submitDateRange" tabindex="6">
								<i class='icon-ok text-success'></i>
								{l s='Apply'}
							</button>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-lg-4 hide">
					<div class="datepicker1" data-date="{$date_from}" data-date-format="{$date_format}"></div>
				</div>
				{else}
				<div class="col-sm-6 col-lg-4 hide">
					<div class="datepicker1" data-date="{$date_from}" data-date-format="{$date_format}"></div>
				</div>
				<div class="col-sm-6 col-lg-4">
					<div class="datepicker2" data-date="{$date_to}" data-date-format="{$date_format}">
						<div class='form-date-actions'>
							<button class='btn btn-link' type='button' id="datepicker-cancel" tabindex="7">
								<i class='icon-remove'></i>
								{l s='Cancel'}
							</button>
							<button class='btn btn-default pull-right' type='submit' name="submitDateRange" tabindex="6">
								<i class='icon-ok text-success'></i>
								{l s='Apply'}
							</button>
						</div>
					</div>
				</div>
				{/if}
				<div class="col-xs-12 col-sm-6 col-lg-4 pull-right hide">
					<div id='datepicker-form' class='form-inline'>
						<div id='date-range' class='form-date-group'>
							<div  class='form-date-heading'>
								<span class="title">{l s='Date range'}</span>
								{if isset($actions) && $actions|count > 0}
									{if $actions|count > 1}
									<button class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown' type="button">
										{l s='Custom'}
										<i class='icon-angle-down'></i>
									</button>
									<ul class='dropdown-menu'>
										{foreach from=$actions item=action}
										<li><a{if isset($action.href)} href="{$action.href}"{/if}{if isset($action.class)} class="{$action.class}"{/if}>{if isset($action.icon)}<i class="{$action.icon}"></i> {/if}{$action.label}</a></li>
										{/foreach}
									</ul>
									{else}
									<a{if isset($actions[0].href)} href="{$actions[0].href}"{/if} class="btn btn-default btn-xs pull-right{if isset($actions[0].class)} {$actions[0].class}{/if}">{if isset($actions[0].icon)}<i class="{$actions[0].icon}"></i> {/if}{$actions[0].label}</a>
									{/if}
								{/if}
							</div>
							<div class='form-date-body'>
								<label>{l s='From'}</label>
								<input class='date-input form-control' id='date-start' placeholder='Start' type='text' name="date_from" value="{$date_from}" data-date-format="{$date_format}" tabindex="1" />
								<label>{l s='to'}</label>
								<input class='date-input form-control' id='date-end' placeholder='End' type='text' name="date_to" value="{$date_to}" data-date-format="{$date_format}" tabindex="2" />
							</div>
						</div>
						<div id="date-compare" class='form-date-group'>
							<div class='form-date-heading'>
								<span class="checkbox-title">
									<label >
										<input type='checkbox' id="datepicker-compare" name="datepicker_compare"{if isset($compare_date_from) && isset($compare_date_to)} checked="checked"{/if} tabindex="3">
										{l s='Compare to'}
									</label>
								</span>
								<select id="compare-options" class="form-control fixed-width-lg pull-right" name="compare_date_option"{if is_null($compare_date_from) || is_null($compare_date_to)} disabled="disabled"{/if}>
									<option value="1" {if $compare_option == 1}selected="selected"{/if} label="{l s='Previous period'}">{l s='Previous period'}</option>
									<option value="2" {if $compare_option == 2}selected="selected"{/if} label="{l s='Previous Year'}">{l s='Previous year'}</option>
									<option value="3" {if $compare_option == 3}selected="selected"{/if} label="{l s='Custom'}">{l s='Custom'}</option>
								</select>
							</div>
							<div class="form-date-body" id="form-date-body-compare"{if is_null($compare_date_from) || is_null($compare_date_to)} style="display: none;"{/if}>
								<label>{l s='From'}</label>
								<input id="date-start-compare" class="date-input form-control" type="text" placeholder="Start" name="compare_date_from" value="{$compare_date_from}" data-date-format="{$date_format}" tabindex="4" />
								<label>{l s='to'}</label>
								<input id="date-end-compare" class="date-input form-control" type="text" placeholder="End" name="compare_date_to" value="{$compare_date_to}" data-date-format="{$date_format}"
								tabindex="5" />
							</div>
						</div>
						<div class='form-date-actions'>
							<button class='btn btn-link' type='button' id="datepicker-cancel" tabindex="7">
								<i class='icon-remove'></i>
								{l s='Cancel'}
							</button>
							<button class='btn btn-default pull-right' type='submit' name="submitDateRange" tabindex="6">
								<i class='icon-ok text-success'></i>
								{l s='Apply'}
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	translated_dates = {
		days: ['{l s='Sunday' js=1}', '{l s='Monday' js=1}', '{l s='Tuesday' js=1}', '{l s='Wednesday' js=1}', '{l s='Thursday' js=1}', '{l s='Friday' js=1}', '{l s='Saturday' js=1}', '{l s='Sunday' js=1}'],
		daysShort: ['{l s='Sun' js=1}', '{l s='Mon' js=1}', '{l s='Tue' js=1}', '{l s='Wed' js=1}', '{l s='Thu' js=1}', '{l s='Fri' js=1}', '{l s='Sat' js=1}', '{l s='Sun' js=1}'],
		daysMin: ['{l s='Su' js=1}', '{l s='Mo' js=1}', '{l s='Tu' js=1}', '{l s='We' js=1}', '{l s='Th' js=1}', '{l s='Fr' js=1}', '{l s='Sa' js=1}', '{l s='Su' js=1}'],
		months: ['{l s='January' js=1}', '{l s='February' js=1}', '{l s='March' js=1}', '{l s='April' js=1}', '{l s='May' js=1}', '{l s='June' js=1}', '{l s='July' js=1}', '{l s='August' js=1}', '{l s='September' js=1}', '{l s='October' js=1}', '{l s='November' js=1}', '{l s='December' js=1}'],
		monthsShort: ['{l s='Jan' js=1}', '{l s='Feb' js=1}', '{l s='Mar' js=1}', '{l s='Apr' js=1}', '{l s='May ' js=1}', '{l s='Jun' js=1}', '{l s='Jul' js=1}', '{l s='Aug' js=1}', '{l s='Sep' js=1}', '{l s='Oct' js=1}', '{l s='Nov' js=1}', '{l s='Dec' js=1}']
	};
</script>
