{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}
<div id="pagenotfound">
	<div class="pagenotfound">
		<h1 style="font-size: 85px;"><i class="icon-ban text-danger"></i></h1>
		<h1>{l s='Access denied'}</h1>

		<p>
			{l s='You do not have permission to access this page.'}
		</p>

		<div class="buttons">
			<a class="btn btn-primary" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Home'}">
				<span>{l s='Home Page'}</span>
			</a>
		</div>
	</div>
</div>
