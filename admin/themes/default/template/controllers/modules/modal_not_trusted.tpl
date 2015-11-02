{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="untrusted-content-action">

	<div class="modal-body">
		<div class="alert alert-warning">
			<h3>{l s='Do you want to install this module that could not be verified by PrestaShop?'}</h3>

			<p>{l s="This generally happens when the module isn't distributed through our official marketplace, PrestaShop Addons - or when your server failed to communicate with PrestaShop Addons."}</p>
		</div>

		<div class="row">
			<div class="col-sm-2" style="text-align: center;">
				<img id="untrusted-module-logo" class="" src="" alt="">
			</div>
			<div class="col-sm-10">
				<table class="table">
					<tr>
						<td>{l s='Module'}</td>
						<td><strong><span class="module-display-name-placeholder"></span></strong></td>
					</tr>
					<tr>
						<td>{l s='Author'}</td>
						<td><strong><span class="author-name-placeholder"></span></strong></td>
					</tr>
				</table>
			</div>

			<div class="col-sm-12" style="text-align: center; padding-top: 12px;">
				<a id="proceed-install-anyway" href="#" class="btn btn-warning">{l s='Proceed with the installation'}</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Back to modules list'}</button>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<div class="alert alert-info">
			<p>
				{l s='Since you may not have downloaded this module from PrestaShop Addons, we cannot assert that the module is not adding some undisclosed functionalities. We advise you to install it only if you trust the source of the content.'}
				<a id="untrusted-show-risk" href="#"><strong>{l s="What's the risk?"}</strong></a>
			</p>
		</div>
	</div>

</div>

<div class="untrusted-content-more-info" style="display:none;">

	<div class="modal-body">
		<h4>{l s='Am I at Risk?'}</h4>

		<p>{l s="A module that hasn't been verified may be dangerous and could add hidden functionalities like backdoors, ads, hidden links, spam, etc. Don’t worry, this alert is simply a warning."}</p>

		<p>{l s="PrestaShop, being an open-source software, has an awesome community with a long history of developing and sharing high quality modules. Before installing this module, making sure its author is a known community member is always a good idea (by checking [1]our forum[/1] for instance)." tags=['<a href="https://www.prestashop.com/forums/">']}</p>

		<h4>{l s='What Should I Do?'}</h4>
		
		<p>{l s="If you trust or find the author of this module to be an active community member, you can proceed with the installation."}

		<p>{l s="Otherwise you can look for similar modules on the official marketplace. [1]Click here to browse PrestaShop Addons[/1]." tags=['<a class="catalog-link" href="#">']}

	</div>

	<div class="modal-footer">
		<a id="untrusted-show-action" class="btn btn-default" href="#">{l s='Back'}</a>
	</div>

</div>
