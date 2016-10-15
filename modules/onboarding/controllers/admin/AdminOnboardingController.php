<?php
/**
* 2007-2016 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminOnboardingController extends ModuleAdminController
{
	public function postProcess()
	{
		if ((int)Tools::getValue('remove') == 1)
			$this->module->uninstall();

		$current_step = (int)Tools::getValue('current_step');

		$links = array(
			0 => $this->context->link->getAdminLink('AdminDashboard').'&onboarding',
			1 => $this->context->link->getAdminLink('AdminThemes').'&onboarding',
			2 => $this->context->link->getAdminLink('AdminProducts').'&onboarding&addproduct',
			3 => $this->context->link->getAdminLink('AdminPayment').'&onboarding',
			4 => $this->context->link->getAdminLink('AdminCarriers').'&onboarding&onboarding_carrier',
		);

		if ($current_step < 6)
			Configuration::updateValue('PS_ONBOARDING_CURRENT_STEP', $current_step);

		if ($current_step > 6)
			$this->module->uninstall();

		Tools::redirectAdmin(isset($links[$current_step]) ? $links[$current_step] : Context::getContext()->link->getAdminLink('AdminDashboard')
			.'&onboarding');
	}
}
