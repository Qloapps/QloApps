<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Configuration $object
 */
class AdminMaintenanceControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('General'),
                'fields' =>    array(
                    'PS_SHOP_ENABLE' => array(
                        'title' => $this->l('Enable Site'),
                        'desc' => $this->l('Activate or deactivate your site (It is a good idea to deactivate your site while you perform maintenance. Please note that the webservice will not be disabled).'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ALLOW_EMP' => array(
                        'title' => $this->l('Allow Employees'),
                        'desc' => $this->l('Allow the employees to access the site using email and password.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'form_group_class' => (Tools::getValue('PS_SHOP_ENABLE', Configuration::get('PS_SHOP_ENABLE'))) ? 'collapse' : '',
                    ),
                    'PS_ALLOW_EMP_MAX_ATTEMPTS' => array(
                        'title' => $this->l('Maximum Login Attempts'),
                        'validation' => 'isUnsignedInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'class' => 'fixed-width-xl',
                        'desc' => sprintf($this->l('Set the number of maximum login attempts allowed in %d minutes.'), MaintenanceAccess::LOGIN_ATTEMPTS_WINDOW),
                        'form_group_class' => ((Tools::getValue('PS_SHOP_ENABLE', Configuration::get('PS_SHOP_ENABLE'))) || !(Tools::getValue('PS_ALLOW_EMP', Configuration::get('PS_ALLOW_EMP')))) ? ' collapse' : '',
                    ),
                    'PS_MAINTENANCE_IP' => array(
                        'title' => $this->l('Maintenance IP'),
                        'hint' => $this->l('IP addresses allowed to access the front office even if the site is disabled. Please use a comma to separate them (e.g. 42.24.4.2,127.0.0.1,99.98.97.96)'),
                        'validation' => 'isGenericName',
                        'type' => 'maintenance_ip',
                        'default' => ''
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $shopEnable = Tools::getValue('PS_SHOP_ENABLE');
            $allowEmp = Tools::getValue('PS_ALLOW_EMP');
            $allowEmpMaxAttempts = trim(Tools::getValue('PS_ALLOW_EMP_MAX_ATTEMPTS'));
            $maintenanceIp = trim(Tools::getValue('PS_MAINTENANCE_IP'));

            // validations
            if (!$shopEnable && $allowEmp) {
                if ($allowEmpMaxAttempts === '') {
                    $this->errors[] = $this->l('Maximum Login Attempts is a required field.');
                } elseif ($allowEmpMaxAttempts === '0' || !Validate::isUnsignedInt($allowEmpMaxAttempts)) {
                    $this->errors[] = $this->l('Maximum Login Attempts is invalid.');
                }
            }

            // update values
            if (!count($this->errors)) {
                Configuration::updateValue('PS_SHOP_ENABLE', $shopEnable);
                Configuration::updateValue('PS_MAINTENANCE_IP', $maintenanceIp);

                if (!$shopEnable) {
                    Configuration::updateValue('PS_ALLOW_EMP', $allowEmp);

                    if ($allowEmp) {
                        Configuration::updateValue('PS_ALLOW_EMP_MAX_ATTEMPTS', $allowEmpMaxAttempts);
                    }
                }

                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=6');
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'maintenance.js');
    }
}
