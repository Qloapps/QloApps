<div id="filter_results" class="row block">
	<div class="col-sm-12">
		{if $product_comment_installed && isset($config) && $config['SHOW_RATTING_FILTER']}
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<div class="row margin-lr-0">
						<div class="pull-left lf_headingmain_wrapper">
							<span>{l s='Guest Rating' mod='wkhotelfilterblock'}</span>
							<hr class="theme-text-underline">
						</div>
						<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
					</div>
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
						<label style="background-image:url({$ratting_img});" class="ratting_img_style ratting_0">
						</label>
					</div>
				</div>
			</div>
		{/if}
		
		{if isset($config) && $config['SHOW_AMENITIES_FILTER']}
			<div class="row margin-lr-0 layered_filter_cont">
				<div class="col-sm-12 layered_filter_heading">
					<div class="row margin-lr-0">
						<div class="pull-left lf_headingmain_wrapper">
							<span>{l s='Amenities' mod='wkhotelfilterblock'}</span>
							<hr class="theme-text-underline">
						</div>
						<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
					</div>
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
					<div class="row margin-lr-0">
						<div class="pull-left lf_headingmain_wrapper">
							<span>{l s='Price' mod='wkhotelfilterblock'}</span>
							<hr class="theme-text-underline">
						</div>
						<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
					</div>
				</div>
				<div class="col-sm-12 lf_sub_cont">
					<div class="row margin-lr-0 price_filter_subcont">
						<span class="pull-left">{displayPrice price = $min_price}</span>
						<span class="pull-right">{displayPrice price = $max_price}</span>
					</div>
					<div id="filter_price_silder"></div>
				</div>
			</div>
		{/if}
	</div>
</div>
{strip}
	{addJsDef num_days = $num_days}
	{addJsDef date_from = $date_from}
	{addJsDef date_to = $date_to}
	
	{addJsDef cat_link = $cat_link}
	{addJsDef min_price = $min_price}
	{addJsDef max_price = $max_price}
	{addJsDef warning_num = $warning_num}

	{addJsDefL name=viewMoreTxt}{l s='View More' js=1 mod='wkhotelfilterblock'}{/addJsDefL}
	{addJsDefL name=bookNowTxt}{l s='Book Now' js=1 mod='wkhotelfilterblock'}{/addJsDefL}
{/strip}