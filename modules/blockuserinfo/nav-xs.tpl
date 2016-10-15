<ul class="nav nav-pills nav-stacked visible-xs wk-nav-style">
	{if $logged}
		<li>
			<a class="navigation-link" href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='View my orders' mod='blockuserinfo'}">{l s='Booking History' mod='blockuserinfo'}</a>
		</li>
		<li>
			<a class="navigation-link" href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}">{l s='Accounts Settings' mod='blockuserinfo'}</a>
		</li>
		<li>
			<a class="navigation-link" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}"  title="{l s='Log me out' mod='blockuserinfo'}">{l s='Sign Out' mod='blockuserinfo'}</a>
		</li>
	{else}
		<li>
			<a class="navigation-link" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">{l s='Sign in' mod='blockuserinfo'}</a>
		</li>
	{/if}
</ul>