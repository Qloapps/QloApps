<div id="filter_results" class="row block">
	<div class="col-sm-12">
		{if isset($config) && $config['SHOW_RATTING_FILTER']}
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<span>{l s='Guest Ratting' mod='wkhotelfilterblock'}</span>
					<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="5">
						<label style="background-image:url({$ratting_img});" class="ratting_img_style ratting_5">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="4">
						<label style="background-image:url({$ratting_img});" class="ratting_img_style ratting_4">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="3">
						<label style="background-image:url({$ratting_img});" class="ratting_img_style ratting_3">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="2">
						<label style="background-image:url({$ratting_img});" class="ratting_img_style ratting_2">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="1">
						<label style="background-image:url({$ratting_img});" class="ratting_img_style ratting_1">
						</label>
					</div>
					<div class="layered_filt">
						<input type="checkbox" class="filter" data-type="ratting" value="0">
						<span class="filters_name">{l s='No Ratting' mod='wkhotelfilterblock'}</span>
					</div>
				</div>
			</div>
		{/if}
		
		{if isset($config) && $config['SHOW_AMENITIES_FILTER']}
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<span>{l s='Amenities' mod='wkhotelfilterblock'}</span>
					<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					{foreach $all_feat as $feat}
						<div class="layered_filt">
							<input type="checkbox" class="filter" data-type="amenities" value="{$feat.id_feature}">
							<span class="filters_name">{$feat.name}</span>
						</div>
					{/foreach}
				</div>
			</div>
		{/if}

		{if isset($config) && $config['SHOW_PRICE_FILTER']}
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<span>{l s='Price' mod='wkhotelfilterblock'}</span>
					<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					<div class="row margin-lr-0 price_filter_subcont">
						<span class="pull-left">{$currency->prefix} <span id="filter_price_from">{$min_price}</span> {$currency->suffix}</span>
						<span class="pull-right">{$currency->prefix} <span id="filter_price_to">{$max_price}</span> {$currency->suffix}</span>
					</div>
					<div id="filter_price_silder"></div>
				</div>
			</div>
		{/if}
		
		<!-- Adults , children filters are disable for now -->
		
		<!-- <div class="row margin-lr-0 layered_filter_cont">
			<div class="col-sm-12 layered_filter_heading">
				<span>{l s='Adults' mod='wkhotelfilterblock'}</span>
			</div>
			<div class="col-sm-12 lf_sub_cont">
				{for $foo=1 to $max_adult}
				    <div class="layered_filt">
						<input type="checkbox" class="filter" data-type="adult" value="{$foo}">
						<span class="filters_name">{$foo}</span>
					</div>
				{/for}
			</div>
		</div> -->
		<!-- <div class="row margin-lr-0 layered_filter_cont">
			<div class="col-sm-12 layered_filter_heading">
				<span>{l s='Children' mod='wkhotelfilterblock'}</span>
			</div>
			<div class="col-sm-12 lf_sub_cont">
				{for $foo=1 to $max_child}
				    <div class="layered_filt">
						<input type="checkbox" class="filter" data-type="children" value="{$foo}">
						<span class="filters_name">{$foo}</span>
					</div>
				{/for}
			</div>
		</div> -->
	</div>
</div>
{strip}
	{addJsDef num_days = $num_days}
	{addJsDef date_from = $date_from}
	{addJsDef date_to = $date_to}
	
	{addJsDef cat_link = $cat_link}
	{addJsDef min_price = $min_price}
	{addJsDef max_price = $max_price}
{/strip}