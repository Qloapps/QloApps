<?php
/**
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
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_3_0($module)
{
	$objUpgrade = new upgradeBlocknewsletter230($module);
	if (!$objUpgrade->registerHooks()
        || !$objUpgrade->callInstallTab()
    ) {
        return false;
    }

    return true;
}

class upgradeBlocknewsletter230
{
	public function __construct($module)
    {
        $this->module = $module;
    }

    public function registerHooks()
    {
        if (!$this->module->registerHook('actionObjectCustomerUpdateBefore')
            || !$this->module->registerHook('actionCustomerCartRulesModifier')
            || !$this->module->registerHook('actionValidateCartRule')
            || !$this->module->registerHook('actionObjectCustomerDeleteAfter')
        ) {
            return false;
        }

        return true;
	}

    public function callInstallTab()
    {
        $result = $this->installTab('AdminParentNewsletter', 'Newsletter', false, true);
        $result &= $this->installTab('AdminNewsletter', 'Configuration', 'AdminParentNewsletter', true);

        return $result;
    }

    public function installTab($className, $tabName, $tabParentName = false, $hidden = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } elseif ($hidden) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->module->name;

        return $tab->add();
    }
}
