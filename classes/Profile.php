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

class ProfileCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'profile',
        'primary' => 'id_profile',
        'multilang' => true,
        'fields' => array(
            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
        ),
    );

    protected static $_cache_accesses = array();

    /**
    * Get all available profiles
    *
    * @return array Profiles
    */
    public static function getProfiles($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT p.`id_profile`, `name`
		FROM `'._DB_PREFIX_.'profile` p
		LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (p.`id_profile` = pl.`id_profile` AND `id_lang` = '.(int)$id_lang.')
		ORDER BY `id_profile` ASC');
    }

    /**
    * Get the current profile name
    *
    * @return string Profile
    */
    public static function getProfile($id_profile, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `name`
			FROM `'._DB_PREFIX_.'profile` p
			LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (p.`id_profile` = pl.`id_profile`)
			WHERE p.`id_profile` = '.(int)$id_profile.'
			AND pl.`id_lang` = '.(int)$id_lang
        );
    }

    public function add($autodate = true, $null_values = false)
    {
        if (parent::add($autodate, true)) {
            $result = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'access (SELECT '.(int)$this->id.', id_tab, 0, 0, 0, 0 FROM '._DB_PREFIX_.'tab)');
            $result &= Db::getInstance()->execute('
				INSERT INTO '._DB_PREFIX_.'module_access
				(`id_profile`, `id_module`, `configure`, `view`, `uninstall`)
				(SELECT '.(int)$this->id.', id_module, 0, 1, 0 FROM '._DB_PREFIX_.'module)
			');
            return $result;
        }
        return false;
    }

    public function delete()
    {
        // check if any employee exists of this deleting profile before delete
        $profileEmployees = Employee::getEmployeesByProfile($this->id);
        if (empty($profileEmployees)) {
            if (parent::delete()) {
                return (
                    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'access` WHERE `id_profile` = '.(int)$this->id)
                    && Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_access` WHERE `id_profile` = '.(int)$this->id)
                );
            }
        }

        return false;
    }

    public static function getProfileAccess($id_profile, $id_tab)
    {
        // getProfileAccesses is cached so there is no performance leak
        $accesses = Profile::getProfileAccesses($id_profile);
        return (isset($accesses[$id_tab]) ? $accesses[$id_tab] : false);
    }

    public static function getProfileAccesses($id_profile, $type = 'id_tab')
    {
        if (!in_array($type, array('id_tab', 'class_name'))) {
            return false;
        }

        if (!isset(self::$_cache_accesses[$id_profile])) {
            self::$_cache_accesses[$id_profile] = array();
        }

        if (!isset(self::$_cache_accesses[$id_profile][$type])) {
            self::$_cache_accesses[$id_profile][$type] = array();
            // Super admin profile has full auth
            if ($id_profile == _PS_ADMIN_PROFILE_) {
                foreach (Tab::getTabs(Context::getContext()->language->id) as $tab) {
                    self::$_cache_accesses[$id_profile][$type][$tab[$type]] = array(
                        'id_profile' => _PS_ADMIN_PROFILE_,
                        'id_tab' => $tab['id_tab'],
                        'class_name' => $tab['class_name'],
                        'view' => '1',
                        'add' => '1',
                        'edit' => '1',
                        'delete' => '1',
                    );
                }
            } else {
                $result = Db::getInstance()->executeS('
				SELECT *
				FROM `'._DB_PREFIX_.'access` a
				LEFT JOIN `'._DB_PREFIX_.'tab` t ON t.id_tab = a.id_tab
				WHERE `id_profile` = '.(int)$id_profile);

                foreach ($result as $row) {
                    self::$_cache_accesses[$id_profile][$type][$row[$type]] = $row;
                }
            }
        }

        return self::$_cache_accesses[$id_profile][$type];
    }
}
