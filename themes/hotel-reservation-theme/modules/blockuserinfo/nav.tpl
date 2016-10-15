<!-- Block user information module NAV  -->
{if !isset($ajaxCustomerLogin)}
<div class="nav_menu_padding header_user_info pull-right hidden-xs">
{/if}
	<ul class="navbar-nav userlogin_block hidden-xs">
	{if $logged}
		<li class="dropdown account_info_cont">
			<button class="btn dropdown-toggle" type="button" id="user_info_acc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<span class="account_user_name hide_xs">{$cookie->customer_firstname}</span>
				{*<span class="account_user_name visi_xs"><i class="icon-user"></i></span>*}
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" aria-labelledby="user_info_acc">
				<li><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}">{l s='Accounts'  mod='blockuserinfo'}</a></li>
				<li><a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='View my orders' mod='blockuserinfo'}">{l s='Orders'}</a></li>
				<li><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}"  title="{l s='Log me out' mod='blockuserinfo'}">{l s='Logout'}</a></li>
			</ul>
		</li>
	{else}
	    <li class="active">
	    	<a class="user_login navigation-link" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">
	    		<span class="hide_xs">{l s='Sign in' mod='blockuserinfo'}</span>
	    		{*<span class="visi_xs"><i class="icon-user"></i></span>*}
	    	</a>
	    </li>
	{/if}
	</ul>
{if !isset($ajaxCustomerLogin)}
</div>
{/if}
<!-- /Block user information module NAV -->
