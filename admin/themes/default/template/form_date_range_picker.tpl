<div id="calendar" class="panel">
	<form action="{$action|escape}" method="post" id="calendar_form" name="calendar_form" class="form-inline">
		<div class="row">
			<div class="col-lg-6">
				<div class="btn-group">
					<button type="submit" name="submitDateDay" class="btn btn-default submitDateDay">{$translations.Day}</button>
					<button type="submit" name="submitDateMonth" class="btn btn-default submitDateMonth">{$translations.Month}</button>
					<button type="submit" name="submitDateYear" class="btn btn-default submitDateYear">{$translations.Year}</button>
					<button type="submit" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev">{$translations.Day}-1</button>
					<button type="submit" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev">{$translations.Month}-1</button>
					<button type="submit" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev">{$translations.Year}-1</button>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-xs-6">
								<div class="input-group">
									<label class="input-group-addon">{if isset($translations.From)}{$translations.From}{else}{l s='From:'}{/if}</label>
									<input type="text" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" class="datepicker form-control" autocomplete="off" onfocus="this.blur();">
								</div>
							</div>
							<div class="col-xs-6">
								<div class="input-group">
									<label class="input-group-addon">{if isset($translations.To)}{$translations.To}{else}{l s='From:'}{/if}</label>
									<input type="text" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" class="datepicker form-control" autocomplete="off" onfocus="this.blur();">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="row">
							<button type="submit" name="submitDatePicker" id="submitDatePicker" class="btn btn-default"><i class="icon-save"></i> {l s='Save'}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		if ($('form#calendar_form .datepicker').length > 0) {
			$('form#calendar_form .datepicker#datepickerFrom').datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				onClose: function() {
					let dateFrom = $('form#calendar_form .datepicker#datepickerFrom').val().trim();
					let dateTo = $('form#calendar_form .datepicker#datepickerTo').val().trim();

					if (dateFrom >= dateTo) {
						let objDateFrom = $.datepicker.parseDate('yy-mm-dd', dateFrom);
						let objDateToMin = objDateFrom;
						objDateToMin.setDate(objDateToMin.getDate() + 1);

						$('form#calendar_form .datepicker#datepickerTo').datepicker('option', 'minDate', objDateToMin);
						$('form#calendar_form .datepicker#datepickerTo').val($.datepicker.formatDate('yy-mm-dd', objDateToMin));
						$('form#calendar_form .datepicker#datepickerTo').datepicker('show');
					}
				},
			});

			$('form#calendar_form .datepicker#datepickerTo').datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				beforeShow: function() {
					let dateFrom = $('form#calendar_form .datepicker#datepickerFrom').val().trim();

					if (typeof dateFrom != 'undefined' && dateFrom != '') {
						let objDateFrom = $.datepicker.parseDate('yy-mm-dd', dateFrom);
						let objDateToMin = objDateFrom;
						objDateToMin.setDate(objDateToMin.getDate() + 1);

						$('form#calendar_form .datepicker#datepickerTo').datepicker('option', 'minDate', objDateToMin);
					} else {
						let objDateToMin = new Date();
						objDateToMin.setDate(objDateToMin.getDate() + 1);

						$('form#calendar_form .datepicker#datepickerTo').datepicker('option', 'minDate', objDateToMin);
					}
				},
			});
		}
	});
</script>