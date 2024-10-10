{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE ReferralProgram -->
<p id="referralprogram">
	<i class="icon-flag"></i>
	{l s='You have earned a voucher worth %s thanks to your sponsor!' sprintf=$discount_display mod='referralprogram'}
	{l s='Enter voucher name %s to receive the reduction on this order.' sprintf=$discount->name mod='referralprogram'}
	<a href="{$link->getModuleLink('referralprogram', 'program', [], true)|escape:'html':'UTF-8'}" title="{l s='Referral program' mod='referralprogram'}" rel="nofollow">{l s='View your referral program.' mod='referralprogram'}</a>
</p>
<br />
<!-- END : MODULE ReferralProgram -->