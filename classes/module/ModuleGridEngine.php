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

abstract class ModuleGridEngineCore extends Module
{
    protected $_type;

    public function __construct($type)
    {
        $this->_type = $type;
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }
        return Configuration::updateValue('PS_STATS_GRID_RENDER', $this->name);
    }

    public static function getGridEngines()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
	    	SELECT m.`name`
	    	FROM `'._DB_PREFIX_.'module` m
	    	LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
	    	LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
	    	WHERE h.`name` = \'displayAdminStatsGridEngine\'
	    ');

        $array_engines = array();
        foreach ($result as $module) {
            $instance = Module::getInstanceByName($module['name']);
            if (!$instance) {
                continue;
            }
            $array_engines[$module['name']] = array($instance->displayName, $instance->description);
        }

        return $array_engines;
    }

    abstract public function setValues($values);
    abstract public function setTitle($title);
    abstract public function setSize($width, $height);
    abstract public function setTotalCount($total_count);
    abstract public function setLimit($start, $limit);
    abstract public function render();
}
