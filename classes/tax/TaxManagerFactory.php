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
* @since 1.5
*/
class TaxManagerFactoryCore
{
    protected static $cache_tax_manager;

    /**
    * Returns a tax manager able to handle this address
    *
    * @param Address $address
    * @param string $type
    *
    * @return TaxManagerInterface
    */
    public static function getManager(Address $address, $type)
    {
        $cache_id = TaxManagerFactory::getCacheKey($address).'-'.$type;
        if (!isset(TaxManagerFactory::$cache_tax_manager[$cache_id])) {
            $tax_manager = TaxManagerFactory::execHookTaxManagerFactory($address, $type);
            if (!($tax_manager instanceof TaxManagerInterface)) {
                $tax_manager = new TaxRulesTaxManager($address, $type);
            }

            TaxManagerFactory::$cache_tax_manager[$cache_id] = $tax_manager;
        }

        return TaxManagerFactory::$cache_tax_manager[$cache_id];
    }

    /**
    * Check for a tax manager able to handle this type of address in the module list
    *
    * @param Address $address
    * @param string $type
    *
    * @return TaxManagerInterface|false
    */
    public static function execHookTaxManagerFactory(Address $address, $type)
    {
        $modules_infos = Hook::getModulesFromHook(Hook::getIdByName('taxManager'));
        $tax_manager = false;

        foreach ($modules_infos as $module_infos) {
            $module_instance = Module::getInstanceByName($module_infos['name']);
            if (is_callable(array($module_instance, 'hookTaxManager'))) {
                $tax_manager = $module_instance->hookTaxManager(array(
                                                                'address' => $address,
                                                                'params' => $type
                                                            ));
            }

            if ($tax_manager) {
                break;
            }
        }

        return $tax_manager;
    }


    /**
    * Create a unique identifier for the address
    * @param Address
    */
    protected static function getCacheKey(Address $address)
    {
        return $address->id_country.'-'
                .(int)$address->id_state.'-'
                .$address->postcode.'-'
                .$address->vat_number.'-'
                .$address->dni;
    }
}
