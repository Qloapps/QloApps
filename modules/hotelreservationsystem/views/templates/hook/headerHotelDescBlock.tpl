<div class="header-desc-container">
	<div class="header-desc-wrapper">
		<div class="header-desc-primary">
			<div class="container">
				<div class="row">
					<div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
						<p class="header-desc-welcome">{l s='Welcome To' mod='hotelreservationsystem'}</p>
						<hr class="heasder-desc-hr-first"/>
						<div class="header-desc-inner-wrapper">
							<h1 class="header-hotel-name">{$WK_HTL_CHAIN_NAME}</h1>
							<p class="header-hotel-desc">{$WK_HTL_TAG_LINE}</p>
							<hr class="heasder-desc-hr-second"/>
						</div>
					</div>
				</div>
				{hook h="displayAfterHeaderHotelDesc"}
			</div>
		</div>
	</div>
</div>