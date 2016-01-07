<div class="pull-right clearfix nav_menu_padding">
	<button type="button" class="nav_toggle">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
</div>

<div id="menu_cont" class="menu_cont_right">
	<div class="row margin-lr-0">
		<div class="col-xs-12 col-sm-12">
			<div class="row margin-lr-0">
				<span class="pull-right close_navbar"><i class="icon-close"></i></span>
			</div>
			<div class="row margin-lr-0 margin-top-20">
				<ul class="nav nav-pills nav-stacked">
					<li>
						<a class="navigation-link" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">{l s='Home' mod='blocknevigationmenu'}</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_our_rooms_link" href="{if ($page_name == index)}#{else}{$base_dir}#htmlcontent_home{/if}">{l s='Our Rooms' mod='blocknevigationmenu'}</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_features_link" href="{if ($page_name == index)}#{else}{$base_dir}#features_block{/if}">{l s='Features' mod='blocknevigationmenu'}</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" id="htl_testimonial_link" href="{if ($page_name == index)}#{else}{$base_dir}#testimonial_block{/if}">{l s='Testimonials' mod='blocknevigationmenu'}</a>
						<hr class="upper">
						<hr class="lower">
					</li>
					<li>
						<a class="navigation-link" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Contact' mod='blocknevigationmenu'}</a>
						<hr class="upper">
						<hr class="lower">
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>