<!-- Block user information module NAV  -->
{if $logged}
<div class="header_user_info">
	<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
</div>
{/if}
<div class="header_user_info">
	{if $logged}
		<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">{l s='Sign out' mod='blockuserinfo'}</a>
	{else}
	<div class="col-sm-2 clearfix" id="userlogin_block_top">
	  <ul class="nav navbar-nav userlogin_block">
	    <li class="active"><a class="user_login" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">{l s='Sign in' mod='blockuserinfo'}</a></li>
	  </ul>
	</div> 
	{/if}
</div>
<!-- /Block user information module NAV -->
