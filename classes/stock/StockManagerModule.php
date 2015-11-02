<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
abstract class StockManagerModuleCore extends Module
{
    public $stock_manager_class;

    public function install()
    {
        return (parent::install() && $this->registerHook('stockManager'));
    }

    public function hookStockManager()
    {
        $class_file = _PS_MODULE_DIR_.'/'.$this->name.'/'.$this->stock_manager_class.'.php';

        if (!isset($this->stock_manager_class) || !file_exists($class_file)) {
            die(sprintf(Tools::displayError('Incorrect Stock Manager class [%s]'), $this->stock_manager_class));
        }

        require_once($class_file);

        if (!class_exists($this->stock_manager_class)) {
            die(sprintf(Tools::displayError('Stock Manager class not found [%s]'), $this->stock_manager_class));
        }

        $class = $this->stock_manager_class;
        if (call_user_func(array($class, 'isAvailable'))) {
            return new $class();
        }

        return false;
    }
}
