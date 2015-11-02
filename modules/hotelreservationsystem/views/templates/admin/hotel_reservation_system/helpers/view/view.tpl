<div id="filter-box">
	<div class="row">
		<div class="col-lg-12">
			<div id="calendar" class="panel col-lg-12">
				<form action="" method="post" id="calendar_form" name="calendar_form" class="form-inline">
					<div class="form-group col-lg-10">
						<select name="room_type" id="room_type" class="col-lg-12 pull-right">
							<option value="1">{l s='Dealux' mod='hotelreservationsystem'}</option>
							<option value="2">{l s='Presidential' mod='hotelreservationsystem'}</option>
							<option value="3">{l s='Executive' mod='hotelreservationsystem'}</option>
						</select>
					</div>
					<div class="form-group col-lg-1">
						<select name="hotel_place" id="hotel_place" class="col-lg-12 pull-right">
							<option value="1">{l s='Delhi' mod='hotelreservationsystem'}</option>
							<option value="2">{l s='Noida' mod='hotelreservationsystem'}</option>
							<option value="3">{l s='Gazhiabaad' mod='hotelreservationsystem'}</option>
						</select>
					</div>
					<div class="form-group col-lg-1">
						<button id="datepickerExpand" class="btn btn-default col-lg-12 pull-right" type="button">
							<i class="icon-calendar-empty"></i>
							<span class="hidden-xs">
								{l s='From'}
								<strong class="text-info" id="datepicker-from-info"></strong>
								{l s='To'}
								<strong class="text-info" id="datepicker-to-info"></strong>
								<strong class="text-info" id="datepicker-diff-info"></strong>
							</span>
							<i class="icon-caret-down"></i>
						</button>
					</div>
					{$calendar}
				</form>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12 col-lg-12" id="">
	<div class="hotel_date col-md-12" class="row-margin-bottom"></div>
</div>


<style>
    .ui-datepicker
    {
        font-size:20px;
        margin: 0 auto!important;
    }
</style>
