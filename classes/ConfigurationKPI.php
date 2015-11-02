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

class ConfigurationKPICore extends Configuration
{
    public static $definition_backup;

    public static function setKpiDefinition()
    {
        ConfigurationKPI::$definition_backup = Configuration::$definition;
        Configuration::$definition['table'] = 'configuration_kpi';
        Configuration::$definition['primary'] = 'id_configuration_kpi';
    }

    public static function unsetKpiDefinition()
    {
        Configuration::$definition = ConfigurationKPI::$definition_backup;
    }

    public static function getIdByName($key, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::getIdByName($key, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function loadConfiguration()
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::loadConfiguration();
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function get($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::get($key, $id_lang, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function getGlobalValue($key, $id_lang = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::getGlobalValue($key, $id_lang);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function getInt($key, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::getInt($key, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function getMultiple($keys, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::getMultiple($keys, $id_lang, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function hasKey($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::hasKey($key, $id_lang, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function set($key, $values, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::set($key, $values, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function updateGlobalValue($key, $values, $html = false)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::updateGlobalValue($key, $values, $html);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function updateValue($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::updateValue($key, $values, $html, $id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function deleteByName($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::deleteByName($key);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function deleteFromContext($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::deleteFromContext($key);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function hasContext($key, $id_lang, $context)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::hasContext($key, $id_lang, $context);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function isOverridenByCurrentContext($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::isOverridenByCurrentContext($key);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    public static function isLangKey($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::isLangKey($key);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }

    protected static function sqlRestriction($id_shop_group, $id_shop)
    {
        ConfigurationKPI::setKpiDefinition();
        $r = parent::sqlRestriction($id_shop_group, $id_shop);
        ConfigurationKPI::unsetKpiDefinition();
        return $r;
    }
}
