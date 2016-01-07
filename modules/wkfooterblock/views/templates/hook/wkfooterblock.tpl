<div id="footer_block" class="row margin-lr-0">
	<div class="footer_logo_block">
		<img class="img img-responsive" src="{$logo_url}">
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 footer_links_block hidden-xs">
		<a href="{$base_dir}" class="footer_links"><span>{l s='Home' mod='wkfooterblock'}</span></a>
		<a href="{if ($page_name == index)}#{else}{$base_dir}#htmlcontent_home{/if}" class="footer_links footer_our_rooms_link" id="htl_sss_link"><span>{l s='Our Rooms' mod='wkfooterblock'}</span></a>
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" class="footer_links footer_contact_link"><span>{l s='Contact' mod='wkfooterblock'}</span></a>
		<a href="{$redirect_link_about}" class="footer_links"><span>{l s='About us' mod='wkfooterblock'}</span></a>
		<a href="{$redirect_link_terms}" class="footer_links"><span>{l s='Terms and Conditions' mod='wkfooterblock'}</span></a>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 footer_links_block visible-xs">
		<p>
			<a href="{$base_dir}" class="footer_links"><span>{l s='Home' mod='wkfooterblock'}</span></a>
		</p>
		<p>
			<a href="#" class="footer_links footer_our_rooms_link" id="htl_sss_link"><span>{l s='Our Rooms' mod='wkfooterblock'}</span></a>
		</p>
		<p>
			<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" class="footer_links footer_contact_link"><span>{l s='Contact' mod='wkfooterblock'}</span></a>
		</p>
		<p>
			<a href="{$redirect_link_about}" class="footer_links"><span>{l s='About us' mod='wkfooterblock'}</span></a>
		</p>
		<p>
			<a href="{$redirect_link_terms}" class="footer_links"><span>{l s='Terms and Conditions' mod='wkfooterblock'}</span></a>
		</p>
	</div>
	<div class="copyright_block row margin-lr-0">
		&copy; {$hotel_establish_year}-{'Y'|date} <a class="webkul_link_footer" href="{$base_dir}">&nbsp;{$hotel_chain_name}.</a>&nbsp;{l s=' All rights reserved.' mod='wkfooterblock'}
	</div>
</div>