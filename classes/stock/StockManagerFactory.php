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

/*
 * StockManagerFactory : factory of stock manager
 * @since 1.5.0
 */
class StockManagerFactoryCore
{
    /**
     * @var $stock_manager : instance of the current StockManager.
     */
    protected static $stock_manager;

    /**
     * Returns a StockManager
     *
     * @return StockManagerInterface
     */
    public static function getManager()
    {
        if (!isset(StockManagerFactory::$stock_manager)) {
            $stock_manager = StockManagerFactory::execHookStockManagerFactory();
            if (!($stock_manager instanceof StockManagerInterface)) {
                $stock_manager = new StockManager();
            }
            StockManagerFactory::$stock_manager = $stock_manager;
        }
        return StockManagerFactory::$stock_manager;
    }

    /**
     *  Looks for a StockManager in the modules list.
     *
     *  @return StockManagerInterface
     */
    public static function execHookStockManagerFactory()
    {
        $modules_infos = Hook::getModulesFromHook(Hook::getIdByName('stockManager'));
        $stock_manager = false;

        foreach ($modules_infos as $module_infos) {
            $module_instance = Module::getInstanceByName($module_infos['name']);

            if (is_callable(array($module_instance, 'hookStockManager'))) {
                $stock_manager = $module_instance->hookStockManager();
            }

            if ($stock_manager) {
                break;
            }
        }

        return $stock_manager;
    }
}
