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

<div id="referrersContainer">
	<div id="calendar">
			<form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}{if $action && $table}&amp;{$action}{$table}{/if}{if $identifier && $id}&amp;{$identifier}={$id|escape:'html':'UTF-8'}{/if}" method="post" id="calendar_form" name="calendar_form" class="form-horizontal">
				<div class="panel">
					<input type="submit" name="submitDateDay" class="btn btn-default submitDateDay" value="{$translations.Day}" />
					<input type="submit" name="submitDateMonth" class="btn btn-default submitDateMonth" value="{$translations.Month}" />
					<input type="submit" name="submitDateYear" class="btn btn-default submitDateYear" value="{$translations.Year}" />
					<input type="submit" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev" value="{$translations.Day}-1" />
					<input type="submit" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev" value="{$translations.Month}-1" />
					<input type="submit" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev" value="{$translations.Year}-1" />
					<p>
						<span>{if isset($translations.From)}{$translations.From}{else}{l s='From:'}{/if}</span>
						<input type="text" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" class="datepicker" />
					</p>
					<p>
						<span>{if isset($translations.To)}{$translations.To}{else}<span>{l s='To:'}</span>{/if}</span>
						<input type="text" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" class="datepicker" />
					</p>
					<button type="submit" name="submitDatePicker" id="submitDatePicker" class="btn btn-default">
						<i class="icon-save"></i> {if isset($translations.Save)}{$translations.Save}{else}{l s='Save'}{/if}
					</button>
				</div>
			</form>

			<script type="text/javascript">
				$(document).ready(function() {
					if ($("form#calendar_form .datepicker").length > 0)
						$("form#calendar_form .datepicker").datepicker({
							prevText: '',
							nextText: '',
							dateFormat: 'yy-mm-dd'
						});
				});
			</script>
	</div>
