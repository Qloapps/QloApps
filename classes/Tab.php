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

class TabCore extends ObjectModel
{
    /** @var string Displayed name*/
    public $name;

    /** @var string Class and file name*/
    public $class_name;

    public $module;

    /** @var int parent ID */
    public $id_parent;

    /** @var int position */
    public $position;

    /** @var bool active */
    public $active = true;

    /** @var int hide_host_mode */
    public $hide_host_mode = false;

    const TAB_MODULE_LIST_URL = _PS_TAB_MODULE_LIST_URL_;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'tab',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => array(
            'id_parent' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'module' =>    array('type' => self::TYPE_STRING, 'validate' => 'isTabName', 'size' => 64),
            'class_name' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 64),
            'active' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'hide_host_mode' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            /* Lang fields */
            'name' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isTabName', 'size' => 64),
        ),
    );

    protected static $_getIdFromClassName = null;

    /**
     * additionnal treatments for Tab when creating new one :
     * - generate a new position
     * - add access for admin profile
     *
     * @param bool $autodate
     * @param bool $null_values
     * @return int id_tab
     */
    public function add($autodate = true, $null_values = false)
    {
        // @retrocompatibility with old menu (before 1.5.0.9)
        $retro = array(
            'AdminPayment' => 'AdminParentModules',
            'AdminOrders' => 'AdminParentOrders',
            'AdminCustomers' => 'AdminParentCustomer',
            'AdminShipping' => 'AdminParentShipping',
            'AdminPreferences' => 'AdminParentPreferences',
            'AdminStats' => 'AdminParentStats',
            'AdminEmployees' => 'AdminAdmin',
        );

        $class_name = Tab::getClassNameById($this->id_parent);
        if (isset($retro[$class_name])) {
            $this->id_parent = Tab::getIdFromClassName($retro[$class_name]);
        }
        self::$_cache_tabs = array();

        // Set good position for new tab
        $this->position = Tab::getNewLastPosition($this->id_parent);
        $this->module = Tools::strtolower($this->module);

        // Add tab
        if (parent::add($autodate, $null_values)) {
            //forces cache to be reloaded
            self::$_getIdFromClassName = null;
            return Tab::initAccess($this->id);
        }
        return false;
    }

    public function save($null_values = false, $autodate = true)
    {
        self::$_getIdFromClassName = null;
        return parent::save();
    }

    /** When creating a new tab $id_tab, this add default rights to the table access
     *
     * @todo this should not be public static but protected
     * @param int $id_tab
     * @param Context $context
     * @return bool true if succeed
     */
    public static function initAccess($id_tab, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$context->employee || !$context->employee->id_profile) {
            return false;
        }

        /* Profile selection */
        $profiles = Db::getInstance()->executeS('SELECT `id_profile` FROM '._DB_PREFIX_.'profile WHERE `id_profile` != 1');
        if (!$profiles || empty($profiles)) {
            return true;
        }

        /* Query definition */
        $query = 'REPLACE INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ';
        $query .= '(1, '.(int)$id_tab.', 1, 1, 1, 1),';

        foreach ($profiles as $profile) {
            $rights = $profile['id_profile'] == $context->employee->id_profile ? 1 : 0;
            $query .= '('.(int)$profile['id_profile'].', '.(int)$id_tab.', '.(int)$rights.', '.(int)$rights.', '.(int)$rights.', '.(int)$rights.'),';
        }
        $query = trim($query, ', ');
        return Db::getInstance()->execute($query);
    }

    public function delete()
    {
        if (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'access WHERE `id_tab` = '.(int)$this->id) && parent::delete()) {
            if (is_array(self::$_getIdFromClassName) && isset(self::$_getIdFromClassName[strtolower($this->class_name)])) {
                self::$_getIdFromClassName = null;
            }
            return $this->cleanPositions($this->id_parent);
        }
        return false;
    }

    /**
     * Get tab id
     *
     * @return int tab id
     */
    public static function getCurrentTabId()
    {
        $id_tab = Tab::getIdFromClassName(Tools::getValue('controller'));
        // retro-compatibility 1.4/1.5
        if (empty($id_tab)) {
            $id_tab = Tab::getIdFromClassName(Tools::getValue('tab'));
        }
        return $id_tab;
    }

    /**
     * Get tab parent id
     *
     * @return int tab parent id
     */
    public static function getCurrentParentId()
    {
        $cache_id = 'getCurrentParentId_'.Tools::strtolower(Tools::getValue('controller'));
        if (!Cache::isStored($cache_id)) {
            $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_parent`
			FROM `'._DB_PREFIX_.'tab`
			WHERE LOWER(class_name) = \''.pSQL(Tools::strtolower(Tools::getValue('controller'))).'\'');
            if (!$value) {
                $value = -1;
            }
            Cache::store($cache_id, $value);
            return $value;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get tab
     *
     * @return array tab
     */
    public static function getTab($id_lang, $id_tab)
    {
        $cache_id = 'Tab::getTab_'.(int)$id_lang.'-'.(int)$id_tab;
        if (!Cache::isStored($cache_id)) {
            /* Tabs selection */
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT *
				FROM `'._DB_PREFIX_.'tab` t
				LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl
					ON (t.`id_tab` = tl.`id_tab` AND tl.`id_lang` = '.(int)$id_lang.')
				WHERE t.`id_tab` = '.(int)$id_tab.(defined('_PS_HOST_MODE_') ? ' AND `hide_host_mode` = 0' : '')
            );
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Return the list of tab used by a module
     *
     * @return array
     */
    public static function getModuleTabList()
    {
        $list = array();

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT t.`class_name`, t.`module`
			FROM `'._DB_PREFIX_.'tab` t
			WHERE t.`module` IS NOT NULL AND t.`module` != ""');

        if (is_array($result)) {
            foreach ($result as $detail) {
                $list[strtolower($detail['class_name'])] = $detail;
            }
        }
        return $list;
    }

    /**
     * Get tabs
     *
     * @return array tabs
     */
    protected static $_cache_tabs = array();
    public static function getTabs($id_lang, $id_parent = null)
    {
        if (!isset(self::$_cache_tabs[$id_lang])) {
            self::$_cache_tabs[$id_lang] = array();
            // Keep t.*, tl.name instead of only * because if translations are missing, the join on tab_lang will overwrite the id_tab in the results
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT t.*, tl.name
				FROM `'._DB_PREFIX_.'tab` t
				LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl ON (t.`id_tab` = tl.`id_tab` AND tl.`id_lang` = '.(int)$id_lang.')
				WHERE 1 '.(defined('_PS_HOST_MODE_') ? ' AND `hide_host_mode` = 0' : '').'
				ORDER BY t.`position` ASC'
            );

            if (is_array($result)) {
                foreach ($result as $row) {
                    if (!isset(self::$_cache_tabs[$id_lang][$row['id_parent']])) {
                        self::$_cache_tabs[$id_lang][$row['id_parent']] = array();
                    }
                    self::$_cache_tabs[$id_lang][$row['id_parent']][] = $row;
                }
            }
        }
        if ($id_parent === null) {
            $array_all = array();
            foreach (self::$_cache_tabs[$id_lang] as $array_parent) {
                $array_all = array_merge($array_all, $array_parent);
            }
            return $array_all;
        }

        return (isset(self::$_cache_tabs[$id_lang][$id_parent]) ? self::$_cache_tabs[$id_lang][$id_parent] : array());
    }

    /**
     * Get tab id from name
     *
     * @param string $class_name
     * @return int id_tab
     */
    public static function getIdFromClassName($class_name)
    {
        $class_name = strtolower($class_name);
        if (self::$_getIdFromClassName === null) {
            self::$_getIdFromClassName = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_tab, class_name FROM `'._DB_PREFIX_.'tab`', true, false);

            if (is_array($result)) {
                foreach ($result as $row) {
                    self::$_getIdFromClassName[strtolower($row['class_name'])] = $row['id_tab'];
                }
            }
        }
        return (isset(self::$_getIdFromClassName[$class_name]) ? (int)self::$_getIdFromClassName[$class_name] : false);
    }

    /**
     * Get collection from module name
     *
     * @param $module string Module name
     * @param null $id_lang integer Language ID
     * @return array|PrestaShopCollection Collection of tabs (or empty array)
     */
    public static function getCollectionFromModule($module, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        if (!Validate::isModuleName($module)) {
            return array();
        }

        $tabs = new PrestaShopCollection('Tab', (int)$id_lang);
        $tabs->where('module', '=', $module);
        return $tabs;
    }

    /**
     * Enabling tabs for module
     *
     * @param $module string Module Name
     * @return bool Status
     */
    public static function enablingForModule($module)
    {
        $tabs = Tab::getCollectionFromModule($module);
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
                $tab->active = 1;
                $tab->save();
            }
            return true;
        }
        return false;
    }

    /**
     * Disabling tabs for module
     *
     * @param $module string Module name
     * @return bool Status
     */
    public static function disablingForModule($module)
    {
        $tabs = Tab::getCollectionFromModule($module);
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
                $tab->active = 0;
                $tab->save();
            }
            return true;
        }
        return false;
    }

    /**
     * Get Instance from tab class name
     *
     * @param $class_name string Name of tab class
     * @param $id_lang integer id_lang
     * @return Tab Tab object (empty if bad id or class name)
     */
    public static function getInstanceFromClassName($class_name, $id_lang = null)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        return new Tab($id_tab, $id_lang);
    }

    public static function getNbTabs($id_parent = null)
    {
        return (int)Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'tab` t
			'.(!is_null($id_parent) ? 'WHERE t.`id_parent` = '.(int)$id_parent : '')
        );
    }

    /**
     * return an available position in subtab for parent $id_parent
     *
     * @param mixed $id_parent
     * @return int
     */
    public static function getNewLastPosition($id_parent)
    {
        return (Db::getInstance()->getValue('
			SELECT IFNULL(MAX(position),0)+1
			FROM `'._DB_PREFIX_.'tab`
			WHERE `id_parent` = '.(int)$id_parent
        ));
    }

    public function move($direction)
    {
        $nb_tabs = Tab::getNbTabs($this->id_parent);
        if ($direction != 'l' && $direction != 'r') {
            return false;
        }
        if ($nb_tabs <= 1) {
            return false;
        }
        if ($direction == 'l' && $this->position <= 1) {
            return false;
        }
        if ($direction == 'r' && $this->position >= $nb_tabs) {
            return false;
        }

        $new_position = ($direction == 'l') ? $this->position - 1 : $this->position + 1;
        Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'tab` t
			SET position = '.(int)$this->position.'
			WHERE id_parent = '.(int)$this->id_parent.'
				AND position = '.(int)$new_position
        );
        $this->position = $new_position;
        return $this->update();
    }

    public function cleanPositions($id_parent)
    {
        $result = Db::getInstance()->executeS('
			SELECT `id_tab`
			FROM `'._DB_PREFIX_.'tab`
			WHERE `id_parent` = '.(int)$id_parent.'
			ORDER BY `position`
		');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'tab`
				SET `position` = '.($i + 1).'
				WHERE `id_tab` = '.(int)$result[$i]['id_tab']
            );
        }
        return true;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT t.`id_tab`, t.`position`, t.`id_parent`
			FROM `'._DB_PREFIX_.'tab` t
			WHERE t.`id_parent` = '.(int)$this->id_parent.'
			ORDER BY t.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $tab) {
            if ((int)$tab['id_tab'] == (int)$this->id) {
                $moved_tab = $tab;
            }
        }

        if (!isset($moved_tab) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'tab`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                ? '> '.(int)$moved_tab['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_tab['position'].' AND `position` >= '.(int)$position).'
			AND `id_parent`='.(int)$moved_tab['id_parent'])
        && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'tab`
			SET `position` = '.(int)$position.'
			WHERE `id_parent` = '.(int)$moved_tab['id_parent'].'
			AND `id_tab`='.(int)$moved_tab['id_tab']));
        return $result;
    }

    public static function checkTabRights($id_tab)
    {
        static $tabAccesses = null;

        if (Context::getContext()->employee->id_profile == _PS_ADMIN_PROFILE_) {
            return true;
        }

        if ($tabAccesses === null) {
            $tabAccesses = Profile::getProfileAccesses(Context::getContext()->employee->id_profile);
        }

        if (isset($tabAccesses[(int)$id_tab]['view'])) {
            return ($tabAccesses[(int)$id_tab]['view'] === '1');
        }
        return false;
    }

    public static function recursiveTab($id_tab, $tabs)
    {
        $admin_tab = Tab::getTab((int)Context::getContext()->language->id, $id_tab);
        $tabs[] = $admin_tab;
        if ($admin_tab['id_parent'] > 0) {
            $tabs = Tab::recursiveTab($admin_tab['id_parent'], $tabs);
        }
        return $tabs;
    }

    /**
     * Overrides update to set position to last when changing parent tab
     *
     * @see ObjectModel::update
     * @param bool $null_values
     * @return bool
     */
    public function update($null_values = false)
    {
        $current_tab = new Tab($this->id);
        if ($current_tab->id_parent != $this->id_parent) {
            $this->position = Tab::getNewLastPosition($this->id_parent);
        }

        self::$_cache_tabs = array();
        return parent::update($null_values);
    }

    public static function getTabByIdProfile($id_parent, $id_profile)
    {
        return Db::getInstance()->executeS('
			SELECT t.`id_tab`, t.`id_parent`, tl.`name`, a.`id_profile`
			FROM `'._DB_PREFIX_.'tab` t
			LEFT JOIN `'._DB_PREFIX_.'access` a
				ON (a.`id_tab` = t.`id_tab`)
			LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl
				ON (t.`id_tab` = tl.`id_tab` AND tl.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE a.`id_profile` = '.(int)$id_profile.'
			AND t.`id_parent` = '.(int)$id_parent.'
			AND a.`view` = 1
			AND a.`edit` = 1
			AND a.`delete` = 1
			AND a.`add` = 1
			AND t.`id_parent` != 0 AND t.`id_parent` != -1
			'.(defined('_PS_HOST_MODE_') ? ' AND `hide_host_mode` = 0' : '').'
			ORDER BY t.`id_parent` ASC
		');
    }

    /**
     * @since 1.5.0
     */
    public static function getClassNameById($id_tab)
    {
        return Db::getInstance()->getValue('SELECT class_name FROM '._DB_PREFIX_.'tab WHERE id_tab = '.(int)$id_tab);
    }

    public static function getTabModulesList($id_tab)
    {
        $modules_list = array('default_list' => array(), 'slider_list' => array());
        $xml_tab_modules_list = false;

        if (file_exists(_PS_ROOT_DIR_.Module::CACHE_FILE_TAB_MODULES_LIST)) {
            $xml_tab_modules_list = @simplexml_load_file(_PS_ROOT_DIR_.Module::CACHE_FILE_TAB_MODULES_LIST);
        }

        $class_name = null;
        $display_type = 'default_list';
        if ($xml_tab_modules_list) {
            foreach ($xml_tab_modules_list->tab as $tab) {
                foreach ($tab->attributes() as $key => $value) {
                    if ($key == 'class_name') {
                        $class_name = (string)$value;
                    }
                }

                if (Tab::getIdFromClassName((string)$class_name) == $id_tab) {
                    foreach ($tab->attributes() as $key => $value) {
                        if ($key == 'display_type') {
                            $display_type = (string)$value;
                        }
                    }

                    foreach ($tab->children() as $module) {
                        $modules_list[$display_type][(int)$module['position']] = (string)$module['name'];
                    }
                    ksort($modules_list[$display_type]);
                }
            }
        }

        return $modules_list;
    }
}
