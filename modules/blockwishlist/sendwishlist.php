<?php
/*
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
*/

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/WishList.php');
require_once(dirname(__FILE__).'/blockwishlist.php');

$context = Context::getContext();

// Instance of module class for translations
$module = new BlockWishList();

if (Configuration::get('PS_TOKEN_ENABLE') == 1 AND
	strcmp(Tools::getToken(false), Tools::getValue('token')) AND
	$context->customer->isLogged() === true
)
	exit($module->l('invalid token', 'sendwishlist'));

if ($context->customer->isLogged())
{
	$id_wishlist = (int)Tools::getValue('id_wishlist');
	if (empty($id_wishlist) === true)
		exit($module->l('Invalid wishlist', 'sendwishlist'));
	for ($i = 1; empty($_POST['email'.strval($i)]) === false; ++$i)
	{
		$to = Tools::getValue('email'.$i);
		$wishlist = WishList::exists($id_wishlist, $context->customer->id, true);
		if ($wishlist === false)
			exit($module->l('Invalid wishlist', 'sendwishlist'));
		if (WishList::addEmail($id_wishlist, $to) === false)
			exit($module->l('Wishlist send error', 'sendwishlist'));
		$toName = strval(Configuration::get('PS_SHOP_NAME'));
		$customer = $context->customer;
		if (Validate::isLoadedObject($customer))
			Mail::Send(
				$context->language->id,
				'wishlist',
				sprintf(Mail::l('Message from %1$s %2$s', $context->language->id), $customer->lastname, $customer->firstname),
				array(
					'{lastname}' => $customer->lastname,
					'{firstname}' => $customer->firstname,
					'{wishlist}' => $wishlist['name'],
					'{message}' => $context->link->getModuleLink('blockwishlist', 'view', array('token' => $wishlist['token']))
				),
				$to, $toName, $customer->email, $customer->firstname.' '.$customer->lastname, null, null, dirname(__FILE__).'/mails/'
			);
	}
}
