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
			<div class="row">
				<ul class="nav nav-pills nav-stacked wk-nav-style">
					<li>
						<a class="navigation-link" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">{l s='Home' mod='blocknevigationmenu'}</a>
					</li>
					{hook h="displayDefaultNavigationHook"}
					<li>
						<a class="navigation-link" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Contact' mod='blocknevigationmenu'}</a>
					</li>
				</ul>
				{hook h="displayExternalNavigationHook"}
			</div>
		</div>
	</div>
</div>